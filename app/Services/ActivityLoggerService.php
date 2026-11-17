<?php

namespace App\Services;

use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLoggerService
{
    private static array $hiddenKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'cf_turnstile_response',
        'cf-turnstile-response',
        'two_factor_secret',
        'secret',
        'api_key'
    ];

    public static function sanitizePayload(array $payload): array
    {
        $sanitized = [];
        foreach ($payload as $key => $value) {
            if (in_array(strtolower($key), self::$hiddenKeys)) {
                $sanitized[$key] = '********';
            } elseif (is_array($value)) {
                $sanitized[$key] = self::sanitizePayload($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    public static function logLogin(User $user, Request $request): UserActivityLog
    {
        $roles = $user->getRoleNames()->join(', ') ?: 'user';

        return UserActivityLog::create([
            'user_id'         => $user->id,
            'user_name'       => $user->full_name ?? $user->name,
            'user_email'      => $user->email,
            'role'            => $roles,
            'activity_type'   => 'login',
            'description'     => "User {$user->name} logged in",
            'method'          => 'POST',
            'url'             => $request->fullUrl(),
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'status_code'     => 200,
            'request_payload' => ['email' => $user->email],
        ]);
    }

    public static function logLogout(User $user, Request $request): UserActivityLog
    {
        $roles = $user->getRoleNames()->join(', ') ?: 'user';

        return UserActivityLog::create([
            'user_id'         => $user->id,
            'user_name'       => $user->full_name ?? $user->name,
            'user_email'      => $user->email,
            'role'            => $roles,
            'activity_type'   => 'logout',
            'description'     => "User {$user->name} logged out",
            'method'          => 'POST',
            'url'             => $request->fullUrl(),
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'status_code'     => 200,
            'request_payload' => null,
        ]);
    }

    public static function logApiCall(?User $user, Request $request, int $statusCode, $payload = null): ?UserActivityLog
    {
        if (!$user) {
            return null;
        }

        $roles = $user->getRoleNames()->join(', ') ?: 'user';
        $method = strtoupper($request->method());
        $path = $request->path();

        $description = "Called {$method} /{$path}";

        $sanitizedData = null;
        if ($payload !== null && is_array($payload)) {
            $sanitizedData = self::sanitizePayload($payload);
        } elseif ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
            $sanitizedData = self::sanitizePayload($request->except(['password', 'password_confirmation', 'cf-turnstile-response']));
        }

        return UserActivityLog::create([
            'user_id'         => $user->id,
            'user_name'       => $user->full_name ?? $user->name,
            'user_email'      => $user->email,
            'role'            => $roles,
            'activity_type'   => 'api_call',
            'description'     => $description,
            'method'          => $method,
            'url'             => $request->fullUrl(),
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'status_code'     => $statusCode,
            'request_payload' => $sanitizedData,
        ]);
    }
}

