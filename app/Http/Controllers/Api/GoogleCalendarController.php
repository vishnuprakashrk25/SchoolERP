<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Client;

class GoogleCalendarController extends Controller
{
    /**
     * Helper to configure Google Client
     */
     private function getGoogleClient()
     {
         $clientId = config('services.google.client_id');
         $clientSecret = config('services.google.client_secret');
         $redirectUri = config('services.google.redirect');

         if (empty($clientId) || empty($clientSecret)) {
             return null;
         }

         $client = new Client();
         $client->setClientId($clientId);
         $client->setClientSecret($clientSecret);
         $client->setRedirectUri($redirectUri);
         $client->addScope('https://www.googleapis.com/auth/calendar.events');
         $client->setAccessType('offline');
         $client->setPrompt('consent');

         return $client;
     }

    /**
     * @OA\Get(
     *      path="/api/google/redirect",
     *      operationId="redirectToGoogle",
     *      tags={"Google Calendar Integration"},
     *      summary="Get Google OAuth redirect URL",
     *      description="Generates the Google OAuth 2.0 authorization URL to connect user's Google Calendar.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="url", type="string", example="https://accounts.google.com/o/oauth2/v2/auth...")
     *          )
     *      )
     * )
     */
    public function redirectToGoogle(Request $request)
    {
        $client = $this->getGoogleClient();
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Google Calendar OAuth credentials (GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET) are not configured in .env'
            ], 422);
        }

        // Attach encrypted state with current user ID to preserve identity across external browser redirect
        $user = auth()->user();
        if ($user) {
            $state = \Illuminate\Support\Facades\Crypt::encryptString(json_encode([
                'user_id'   => $user->id,
                'timestamp' => now()->timestamp,
            ]));
            $client->setState($state);
        }

        $authUrl = $client->createAuthUrl();

        return response()->json([
            'success' => true,
            'url'     => $authUrl
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/google/callback",
     *      operationId="handleGoogleCallback",
     *      tags={"Google Calendar Integration"},
     *      summary="Handle Google OAuth callback",
     *      description="Exchanges Google authorization code for access and refresh tokens. Automatically called by Google redirect.",
     *      @OA\Parameter(
     *          name="code",
     *          in="query",
     *          required=true,
     *          description="Authorization code returned by Google OAuth flow",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="state",
     *          in="query",
     *          required=false,
     *          description="Encrypted user state token generated during redirect",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Google Calendar connected successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Google Calendar connected successfully!")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid or missing code",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Authorization code not provided")
     *          )
     *      )
     * )
     */
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->input('code');

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization code not provided'
            ], 400);
        }

        try {
            $client = $this->getGoogleClient();
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Calendar OAuth credentials are not configured in .env'
                ], 500);
            }

            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $token['error_description'] ?? 'Failed to fetch Google access token'
                ], 400);
            }

            // Authenticated user or resolve from encrypted state
            $user = auth()->user();
            if (!$user && $request->filled('state')) {
                try {
                    $stateData = json_decode(\Illuminate\Support\Facades\Crypt::decryptString($request->input('state')), true);
                    if (!empty($stateData['user_id'])) {
                        $user = \App\Models\User::find($stateData['user_id']);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Google OAuth state decryption failed: ' . $e->getMessage());
                }
            }

            if ($user) {
                $user->update([
                    'google_access_token'     => $token['access_token'] ?? null,
                    'google_refresh_token'    => $token['refresh_token'] ?? $user->google_refresh_token,
                    'google_token_expires_at' => isset($token['expires_in']) ? now()->addSeconds($token['expires_in']) : null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Google Calendar connected successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google OAuth failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/google/status",
     *      operationId="getGoogleCalendarStatus",
     *      tags={"Google Calendar Integration"},
     *      summary="Check Google Calendar connection status",
     *      description="Returns whether the logged-in user has connected their Google Calendar.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="connected", type="boolean", example=true)
     *          )
     *      )
     * )
     */
    public function status(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'success'   => true,
            'connected' => !empty($user->google_refresh_token)
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/google/disconnect",
     *      operationId="disconnectGoogleCalendar",
     *      tags={"Google Calendar Integration"},
     *      summary="Disconnect Google Calendar",
     *      description="Clears Google OAuth access and refresh tokens for the logged-in user.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Google Calendar disconnected successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Google Calendar disconnected successfully")
     *          )
     *      )
     * )
     */
    public function disconnect(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->update([
                'google_access_token'     => null,
                'google_refresh_token'    => null,
                'google_token_expires_at' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Google Calendar disconnected successfully'
        ]);
    }
}
