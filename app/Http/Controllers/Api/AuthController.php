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
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="admin@admin.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password")
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
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            // 'cf-turnstile-response' => ['required', new Turnstile(app(TurnstileClient::class))],
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
                'user'  => $user,
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
     *      description="Send a password reset link to the user's email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Reset link sent"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['success' => true, 'message' => __($status)]);
        }

        return response()->json(['success' => false, 'message' => __($status)], 422);
    }

    /**
     * @OA\Post(
     *      path="/api/reset-password",
     *      operationId="resetPassword",
     *      tags={"0. Authentication - Auth"},
     *      summary="Reset Password",
     *      description="Reset the user's password using a token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token", "email", "password", "password_confirmation", "cf-turnstile-response"},
     *              @OA\Property(property="token", type="string"),
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string", format="password"),
     *              @OA\Property(property="password_confirmation", type="string", format="password"),
     *              @OA\Property(property="cf-turnstile-response", type="string")
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
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            // 'cf-turnstile-response' => ['required', new Turnstile(app(TurnstileClient::class))],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['success' => true, 'message' => __($status)]);
        }

        return response()->json(['success' => false, 'message' => __($status)], 422);
    }
}
