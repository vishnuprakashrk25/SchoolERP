<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
use RyanChandler\LaravelCloudflareTurnstile\TurnstileClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\ResetPasswordNotification;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="loginUser",
     *      tags={"0. Authentication - Auth"},
     *      summary="User Login",
     *      description="Login User Here",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password","cf_turnstile_response"},
     *              @OA\Property(property="email", type="string", format="email", example="admin@admin.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *              @OA\Property(property="cf_turnstile_response", type="string", description="Cloudflare Turnstile verification token (also accepts cf-turnstile-response or CF-Turnstile-Response header)", example="1x00000000000000000000AA")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function login(Request $request)
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
            'email' => 'required|email',
            'password' => 'required',
            'cf-turnstile-response' => ['required', new Turnstile(app(TurnstileClient::class))],
        ];

        if (app()->environment('testing') && !$request->has('cf-turnstile-response')) {
            unset($rules['cf-turnstile-response']);
        }

        $request->validate($rules, [
            'cf-turnstile-response.required' => 'Cloudflare Turnstile CAPTCHA verification is required.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account disabled'
            ], 403);
        }

        // Check if 2FA is enabled
        if ($user->two_factor_confirmed_at) {
            return response()->json([
                'success' => true,
                'two_factor_required' => true,
                'challenge_token' => encrypt($user->id),
                'message' => '2FA verification required'
            ]);
        }

        $token = $user->createToken('erp_token')->plainTextToken;

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
                'token' => $token,
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'is_parent' => $isParent,
                'children_count' => $childrenCount,
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="logoutUser",
     *      tags={"0. Authentication - Auth"},
     *      summary="User Logout",
     *      description="Logout user and invalidate token",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET AUTH USER
    |--------------------------------------------------------------------------
    */
    /**
     * @OA\Get(
     *      path="/api/me",
     *      operationId="getAuthUser",
     *      tags={"0. Authentication - Auth"},
     *      summary="Get Authenticated User",
     *      description="Returns user profile, roles, and permissions",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function me(Request $request)
    {
        $user = $request->user();

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
            'data' => [
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'is_parent' => $isParent,
                'children_count' => $childrenCount,
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PASSWORD RESET
    |--------------------------------------------------------------------------
    */

    /**
     * @OA\Post(
     *      path="/api/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"0. Authentication - Auth"},
     *      summary="Forgot Password",
     *      description="Send a password reset OTP code or link to the user's email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password reset code generated and sent"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We cannot find a user with that email address.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate 6-digit numeric OTP code and token using cryptographically secure random_int
        $code = sprintf("%06d", random_int(100000, 999999));

        // Store hashed code in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => Hash::make($code),
                'created_at' => Carbon::now(),
            ]
        );

        try {
            $user->notify(new ResetPasswordNotification($code));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending error in forgot-password: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'If your email is registered, you will receive a password reset code shortly.'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/password/verify-token",
     *      operationId="verifyResetToken",
     *      tags={"0. Authentication - Auth"},
     *      summary="Verify Reset Password Token / Code",
     *      description="Checks if the password reset token or 6-digit code is valid and unexpired",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="token", type="string", example="123456"),
     *              @OA\Property(property="code", type="string", example="123456")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Token is valid"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid or expired token"
     *      )
     * )
     */
    public function verifyResetToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required_without:code|string',
            'code'  => 'required_without:token|string',
        ]);

        $email = $request->email;
        $token = $request->token ?? $request->code;

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired password reset token.'
            ], 422);
        }

        $expireMinutes = config('auth.passwords.users.expire', 60);
        if (Carbon::parse($record->created_at)->addMinutes($expireMinutes)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Password reset token has expired.'
            ], 422);
        }

        $user = User::where('email', $email)->first();
        $isValid = Hash::check($token, $record->token)
            || ($record->token === $token)
            || ($user && Password::broker()->tokenExists($user, $token));

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password reset token or code.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset token is valid.',
            'data' => [
                'email' => $email,
                'token' => $token
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/password/verify",
     *      operationId="verifyPassword",
     *      tags={"0. Authentication - Auth"},
     *      summary="Verify Password",
     *      description="Verifies the current password of an authenticated user or validates user credentials",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"password"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="secret123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password is valid"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid password"
     *      )
     * )
     */
    public function verifyPassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }
        } else {
            $request->validate([
                'password' => 'required|string',
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided password is incorrect.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password verified successfully.'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/reset-password",
     *      operationId="resetPassword",
     *      tags={"0. Authentication - Auth"},
     *      summary="Reset Password",
     *      description="Reset the user's password using a token or OTP code",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password", "password_confirmation"},
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="token", type="string", example="123456"),
     *              @OA\Property(property="code", type="string", example="123456"),
     *              @OA\Property(property="password", type="string", format="password"),
     *              @OA\Property(property="password_confirmation", type="string", format="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password reset successful"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|min:8|confirmed',
            'token'                 => 'required_without:code|string',
            'code'                  => 'required_without:token|string',
        ], [
            'email.exists' => 'We cannot find a user with that email address.',
        ]);

        $email = $request->email;
        $token = $request->token ?? $request->code;

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired password reset token.'
            ], 422);
        }

        $expireMinutes = config('auth.passwords.users.expire', 60);
        if (Carbon::parse($record->created_at)->addMinutes($expireMinutes)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Password reset token has expired.'
            ], 422);
        }

        $isValid = Hash::check($token, $record->token)
            || ($record->token === $token)
            || Password::broker()->tokenExists($user, $token);

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password reset token or code.'
            ], 422);
        }

        // Update password
        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Revoke all existing API tokens for security (SEC-07)
        $user->tokens()->delete();

        // Invalidate used reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.'
        ]);
    }
}
