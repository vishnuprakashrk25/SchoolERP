<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ActivityLoggerService;

class LogUserActivity
{
    /**
     * Handle an incoming request and log user activity.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = $request->user() ?? auth('sanctum')->user();

            if ($user && !$this->shouldSkip($request)) {
                ActivityLoggerService::logApiCall(
                    $user,
                    $request,
                    $response->getStatusCode()
                );
            }
        } catch (\Throwable $e) {
            // Logging failure should never interrupt API response
            \Illuminate\Support\Facades\Log::error('Activity logging failed: ' . $e->getMessage());
        }

        return $response;
    }

    private function shouldSkip(Request $request): bool
    {
        $skipPatterns = [
            'api/user-activities*',
            'api/documentation*',
            'docs*',
            'sanctum/csrf-cookie',
        ];

        foreach ($skipPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}

