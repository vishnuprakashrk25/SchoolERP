<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->two_factor_confirmed_at && !$request->session()->has('2fa_verified')) {
            // For API, we might check a specific header or token ability if using Sanctum
            // But since this is a pure API, the state is usually handled by the token itself.
            // If the user has 2FA enabled, they should have been given a 'challenge' token first.
            // Only after verification should they get the full 'access' token.
            
            // However, if we want to enforce it via middleware on a full token:
            if (!$user->tokenCan('2fa-verified')) {
                 return response()->json([
                    'success' => false,
                    'two_factor_required' => true,
                    'message' => '2FA verification required'
                ], 403);
            }
        }

        return $next($request);
    }
}
