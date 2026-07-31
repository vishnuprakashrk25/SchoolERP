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
     *      tags={"0. Authentication - Auth"},
     *      summary="Setup 2FA",
     *      description="Generates a 2FA secret and QR code URL for the authenticated user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
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
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return response()->json([
            'success' => true,
            'data' => [
                'secret' => $user->two_factor_secret,
                'qr_code_url' => $qrCodeUrl,
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/2fa/confirm",
     *      operationId="confirm2fa",
     *      tags={"0. Authentication - TwoFactor"},
     *      summary="Confirm 2FA",
     *      description="Confirms 2FA setup by verifying a TOTP code",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"code"},
     *              @OA\Property(property="code", type="string", example="123456")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid code"
     *      )
     * )
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            $user->two_factor_confirmed_at = now();
            // Generate recovery codes if needed
            $user->two_factor_recovery_codes = encrypt(json_encode([
                Str::random(10), Str::random(10), Str::random(10), Str::random(10)
            ]));
            $user->save();

            return response()->json([
                'success' => true,
                'message' => '2FA confirmed successfully',
                'recovery_codes' => json_decode(decrypt($user->two_factor_recovery_codes))
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid 2FA code'
        ], 422);
    }

    /**
     * @OA\Post(
     *      path="/api/2fa/verify",
     *      operationId="verify2fa",
     *      tags={"0. Authentication - TwoFactor"},
     *      summary="Verify 2FA (during login)",
     *      description="Verifies TOTP code during the login flow",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"challenge_token", "code", "cf-turnstile-response"},
     *              @OA\Property(property="challenge_token", type="string"),
     *              @OA\Property(property="code", type="string"),
     *              @OA\Property(property="cf-turnstile-response", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful login"
     *      )
     * )
     */
    public function verify(Request $request)
    {
        $request->validate([
            'challenge_token' => 'required|string',
            'code' => 'required|string',
            // 'cf-turnstile-response' => ['required', new Turnstile(app(TurnstileClient::class))],
        ]);

        // In a real scenario, challenge_token would be a temporary Sanctum token or a cache key
        // For simplicity, we'll assume it's the user ID or a signed token.
        // Let's use a simple approach for this task: decrypting the challenge_token to get user ID.
        try {
            $userId = decrypt($request->challenge_token);
            $user = User::findOrFail($userId);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid challenge token'], 401);
        }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            $token = $user->createToken('erp_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'user'  => $user,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid 2FA code'
        ], 422);
    }

    /**
     * @OA\Post(
     *      path="/api/2fa/disable",
     *      operationId="disable2fa",
     *      tags={"0. Authentication - Auth"},
     *      summary="Disable 2FA",
     *      description="Disables 2FA for the authenticated user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function disable(Request $request)
    {
        $user = $request->user();
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
