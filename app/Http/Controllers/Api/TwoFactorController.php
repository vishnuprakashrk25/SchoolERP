<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
use RyanChandler\LaravelCloudflareTurnstile\TurnstileClient;

class TwoFactorController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/2fa/setup",
     *      operationId="setup2fa",
     *      tags={"0. Authentication - TwoFactor"},
     *      summary="Setup 2FA",
     *      description="Generates a 2FA TOTP secret key, otpauth URL, and base64 SVG QR code image for the authenticated user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="2FA setup details generated",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="secret", type="string", example="JBSWY3DPEHPK3PXP"),
     *                  @OA\Property(property="qr_code_url", type="string", example="otpauth://totp/ERP%20School:user@example.com?secret=JBSWY3DPEHPK3PXP&issuer=ERP+School"),
     *                  @OA\Property(property="qr_code_image", type="string", example="data:image/svg+xml;base64,PHN2ZyB4bWxucz0..."),
     *                  @OA\Property(property="enabled", type="boolean", example=false)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function setup(Request $request)
    {
        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');

        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'ERP School'),
            $user->email,
            $user->two_factor_secret
        );

        $qrCodeImage = null;
        try {
            $svgString = (string) QrCode::size(200)->generate($qrCodeUrl);
            $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($svgString);
        } catch (\Throwable $e) {
            \Log::warning('QR code SVG generation failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'secret'        => $user->two_factor_secret,
                'qr_code_url'   => $qrCodeUrl,
                'qr_code_image' => $qrCodeImage,
                'enabled'       => !empty($user->two_factor_confirmed_at),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/2fa/confirm",
     *      operationId="confirm2fa",
     *      tags={"0. Authentication - TwoFactor"},
     *      summary="Confirm & Activate 2FA",
     *      description="Confirms 2FA setup by verifying a 6-digit TOTP code and returns emergency recovery codes",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"code"},
     *              @OA\Property(property="code", type="string", example="123456", description="6-digit TOTP code from Authenticator app")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="2FA activated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="2FA confirmed successfully"),
     *              @OA\Property(
     *                  property="recovery_codes",
     *                  type="array",
     *                  @OA\Items(type="string"),
     *                  example={"AB3DE5GH1J", "KL7MN9PQ2R", "ST4UV6WX8Y", "ZA0BC2DE4F"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid 2FA code",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid 2FA code")
     *          )
     *      )
     * )
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if (empty($user->two_factor_secret)) {
            return response()->json([
                'success' => false,
                'message' => '2FA setup not initiated. Please call /api/2fa/setup first.'
            ], 422);
        }

        $google2fa = app('pragmarx.google2fa');

        // Use a window of 2 time slices (±60 seconds) to tolerate client clock drift
        $valid = $google2fa->verifyKey($user->two_factor_secret, trim($request->code), 2);

        if ($valid) {
            $user->two_factor_confirmed_at = now();

            // Generate 8 clean alphanumeric recovery codes
            $recoveryCodes = [
                Str::upper(Str::random(10)),
                Str::upper(Str::random(10)),
                Str::upper(Str::random(10)),
                Str::upper(Str::random(10)),
                Str::upper(Str::random(10)),
                Str::upper(Str::random(10)),
                Str::upper(Str::random(10)),
                Str::upper(Str::random(10)),
            ];

            $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
            $user->save();

            return response()->json([
                'success'        => true,
                'message'        => '2FA confirmed successfully',
                'recovery_codes' => $recoveryCodes
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid 2FA code. Please ensure your device clock is accurate and try again.'
        ], 422);
    }

    /**
     * @OA\Post(
     *      path="/api/2fa/verify",
     *      operationId="verify2fa",
     *      tags={"0. Authentication - TwoFactor"},
     *      summary="Verify 2FA (Login Step 2)",
     *      description="Verifies the TOTP code or emergency recovery code during the 2FA login challenge and completes login",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"challenge_token", "code"},
     *              @OA\Property(property="challenge_token", type="string", description="Encrypted challenge token returned by POST /api/login"),
     *              @OA\Property(property="code", type="string", example="123456", description="6-digit TOTP code or 10-character emergency recovery code"),
     *              @OA\Property(property="cf-turnstile-response", type="string", example="1x00000000000000000000AA", description="Cloudflare Turnstile CAPTCHA response token")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Login successful"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="token", type="string", example="1|plainTextToken..."),
     *                  @OA\Property(property="user", type="object"),
     *                  @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *                  @OA\Property(property="permissions", type="array", @OA\Items(type="string")),
     *                  @OA\Property(property="is_parent", type="boolean", example=false),
     *                  @OA\Property(property="children_count", type="integer", example=0),
     *                  @OA\Property(property="used_recovery_code", type="boolean", example=false)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid challenge token",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid or expired challenge token")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid 2FA code",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid 2FA code")
     *          )
     *      )
     * )
     */
    public function verify(Request $request)
    {
        // Normalize Turnstile token from cf_turnstile_response, cf-turnstile-response, or CF-Turnstile-Response header
        if (!$request->has('cf-turnstile-response')) {
            if ($request->filled('cf_turnstile_response')) {
                $request->merge(['cf-turnstile-response' => $request->input('cf_turnstile_response')]);
            } elseif ($request->header('CF-Turnstile-Response')) {
                $request->merge(['cf-turnstile-response' => $request->header('CF-Turnstile-Response')]);
            }
        }

        $rules = [
            'challenge_token' => 'required|string',
            'code'            => 'required|string',
        ];

        // Enforce Turnstile when provided or in production
        if ($request->has('cf-turnstile-response') || (!app()->environment('testing') && !empty(config('services.turnstile.secret')))) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile(app(TurnstileClient::class))];
        }

        $request->validate($rules, [
            'cf-turnstile-response.required' => 'Cloudflare Turnstile CAPTCHA verification is required.',
        ]);

        try {
            $userId = decrypt($request->challenge_token);
            $user = User::findOrFail($userId);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired 2FA challenge token. Please login again.'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account disabled'
            ], 403);
        }

        $cleanCode = trim($request->code);
        $google2fa = app('pragmarx.google2fa');

        // 1. Check TOTP Authenticator code (window = 2 allows ±60 seconds drift)
        $valid = $google2fa->verifyKey($user->two_factor_secret, $cleanCode, 2);
        $usedRecoveryCode = false;

        // 2. If TOTP fails, check if input matches an emergency recovery code
        if (!$valid && !empty($user->two_factor_recovery_codes)) {
            try {
                $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?: [];
                $upperCode = strtoupper($cleanCode);

                if (in_array($upperCode, $recoveryCodes)) {
                    $valid = true;
                    $usedRecoveryCode = true;

                    // Consume the used recovery code
                    $remainingCodes = array_values(array_diff($recoveryCodes, [$upperCode]));
                    $user->two_factor_recovery_codes = encrypt(json_encode($remainingCodes));
                    $user->save();
                }
            } catch (\Exception $e) {
                \Log::warning('Recovery code decryption failed: ' . $e->getMessage());
            }
        }

        if ($valid) {
            $token = $user->createToken('erp_token')->plainTextToken;

            // Multi-role parent detection matching AuthController::login
            $isParent = \App\Models\StudentParent::where('father_email', $user->email)
                ->orWhere('mother_email', $user->email)
                ->orWhere('guardian_email', $user->email)
                ->exists() || $user->hasRole('parent');

            $childrenCount = \App\Models\Student::whereHas('parents', function ($q) use ($user) {
                $q->where('father_email', $user->email)
                    ->orWhere('mother_email', $user->email)
                    ->orWhere('guardian_email', $user->email);
            })->count();

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token'              => $token,
                    'user'               => $user,
                    'roles'              => $user->getRoleNames(),
                    'permissions'        => $user->getAllPermissions()->pluck('name'),
                    'is_parent'          => $isParent,
                    'children_count'     => $childrenCount,
                    'used_recovery_code' => $usedRecoveryCode,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid 2FA code or recovery code'
        ], 422);
    }

    /**
     * @OA\Post(
     *      path="/api/2fa/disable",
     *      operationId="disable2fa",
     *      tags={"0. Authentication - TwoFactor"},
     *      summary="Disable 2FA",
     *      description="Disables 2FA and removes secret and recovery codes for the authenticated user",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"password"},
     *              @OA\Property(property="password", type="string", format="password", example="secret123"),
     *              @OA\Property(property="code", type="string", nullable=true, example="123456", description="Optional TOTP code")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="2FA disabled successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="2FA disabled successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid password or verification code"
     *      )
     * )
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'code'     => 'nullable|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password.'
            ], 422);
        }

        if (!empty($user->two_factor_secret) && !empty($user->two_factor_confirmed_at) && $request->filled('code')) {
            $google2fa = app('pragmarx.google2fa');
            $valid = $google2fa->verifyKey($user->two_factor_secret, trim($request->code), 2);
            if (!$valid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid 2FA code.'
                ], 422);
            }
        }

        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '2FA disabled successfully'
        ]);
    }
}

