<?php

namespace App\Services;

use App\Models\User;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Notification;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PushNotificationService
{
    /**
     * Send notification to a single user (In-app + FCM Push)
     */
    public static function sendToUser($userId, string $title, string $body, array $data = [], string $type = 'general', $senderId = null): ?Notification
    {
        if (empty($userId)) {
            return null;
        }

        // 1. Create in-app notification record in database
        $notification = Notification::create([
            'user_id'   => $userId,
            'sender_id' => $senderId,
            'title'     => $title,
            'message'   => $body,
            'type'      => $type,
            'data'      => $data,
            'is_read'   => false,
        ]);

        // 2. Fetch user's registered device tokens
        $tokens = DeviceToken::where('user_id', $userId)->pluck('token')->toArray();

        if (!empty($tokens)) {
            self::sendFcmNotification($tokens, $title, $body, $data, $type);
        }

        return $notification;
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToUsers(array $userIds, string $title, string $body, array $data = [], string $type = 'general', $senderId = null): int
    {
        $userIds = array_unique(array_filter($userIds));
        $count = 0;

        foreach ($userIds as $userId) {
            self::sendToUser($userId, $title, $body, $data, $type, $senderId);
            $count++;
        }

        return $count;
    }

    /**
     * Send notification to all users having a specific Role (e.g. Admin, Teacher, Staff, Student, Parent)
     */
    public static function sendToRole(string $roleName, string $title, string $body, array $data = [], string $type = 'general', $senderId = null): int
    {
        $userIds = [];

        // 1. Spatie Role lookup
        try {
            $roleUsers = User::role($roleName)->pluck('id')->toArray();
            $userIds = array_merge($userIds, $roleUsers);
        } catch (\Throwable $e) {
            // Role may not exist in Spatie
        }

        // 2. Staff table match (for Teacher, Admin, Accountant, etc.)
        $staffUserIds = Staff::where(function ($q) use ($roleName) {
            $q->where('role', 'like', "%{$roleName}%")
              ->orWhere('designation', 'like', "%{$roleName}%")
              ->orWhere('category', 'like', "%{$roleName}%");
        })->whereNotNull('user_id')->pluck('user_id')->toArray();

        $userIds = array_merge($userIds, $staffUserIds);

        // 3. If role is Student
        if (strtolower($roleName) === 'student') {
            $studentUserIds = Student::whereNotNull('user_id')->pluck('user_id')->toArray();
            $userIds = array_merge($userIds, $studentUserIds);
        }

        // 4. If role is Parent
        if (strtolower($roleName) === 'parent') {
            $parentUserIds = \App\Models\ParentModel::whereNotNull('user_id')->pluck('user_id')->toArray();
            $userIds = array_merge($userIds, $parentUserIds);
        }

        $userIds = array_unique(array_filter($userIds));
        return self::sendToUsers($userIds, $title, $body, $data, $type, $senderId);
    }

    /**
     * Send notification to multiple roles at once
     */
    public static function sendToRoles(array $roleNames, string $title, string $body, array $data = [], string $type = 'general', $senderId = null): int
    {
        $total = 0;
        foreach ($roleNames as $role) {
            $total += self::sendToRole($role, $title, $body, $data, $type, $senderId);
        }
        return $total;
    }

    /**
     * Send notification to all Students & optionally Parents of a specific Class and Section
     */
    public static function sendToClassSection($classId, $sectionId = null, string $title, string $body, array $data = [], string $type = 'general', $senderId = null, bool $includeParents = true): int
    {
        $query = Student::where('class_id', $classId);
        if (!empty($sectionId)) {
            $query->where('section_id', $sectionId);
        }

        $students = $query->with('parents')->get();
        $userIds = [];

        foreach ($students as $student) {
            if (!empty($student->user_id)) {
                $userIds[] = $student->user_id;
            }
            if ($includeParents && $student->parents) {
                foreach ($student->parents as $p) {
                    if (!empty($p->user_id)) {
                        $userIds[] = $p->user_id;
                    }
                }
            }
        }

        $userIds = array_unique(array_filter($userIds));
        return self::sendToUsers($userIds, $title, $body, $data, $type, $senderId);
    }

    /**
     * Send broadcast notification to all active users
     */
    public static function sendBroadcast(string $title, string $body, array $data = [], string $type = 'broadcast', $senderId = null): int
    {
        $allUserIds = User::where('is_active', true)->orWhereNull('is_active')->pluck('id')->toArray();
        return self::sendToUsers($allUserIds, $title, $body, $data, $type, $senderId);
    }

    /**
     * Dispatch push notifications via Firebase Cloud Messaging (Supports both FCM v1 and Legacy)
     */
    public static function sendFcmNotification(array $tokens, string $title, string $body, array $data = [], string $type = 'general'): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));
        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No device tokens provided'];
        }

        // 1. Check for Modern Firebase V1 Service Account Credentials
        $credentialsPath = self::getCredentialsPath();

        if ($credentialsPath && file_exists($credentialsPath)) {
            return self::sendViaFcmV1($tokens, $title, $body, $data, $type, $credentialsPath);
        }

        // 2. Fallback to Legacy FCM Server Key
        $serverKey = env('FCM_SERVER_KEY');
        if (!empty($serverKey)) {
            return self::sendViaLegacyFcm($tokens, $title, $body, $data, $type, $serverKey);
        }

        // 3. Fallback: Simulation mode
        Log::info("FCM Notification simulated (No Firebase credentials configured): '{$title}' to " . count($tokens) . " devices.");
        return [
            'success'     => true,
            'simulated'   => true,
            'message'     => 'Firebase credentials not found. In-app database notification saved.',
            'token_count' => count($tokens)
        ];
    }

    /**
     * Send via Firebase Cloud Messaging API (V1)
     */
    private static function sendViaFcmV1(array $tokens, string $title, string $body, array $data, string $type, string $credentialsPath): array
    {
        $credentials = json_decode(file_get_contents($credentialsPath), true);
        if (empty($credentials) || empty($credentials['project_id'])) {
            return ['success' => false, 'message' => 'Invalid firebase credentials file'];
        }

        $projectId = $credentials['project_id'];
        $accessToken = self::getGoogleAccessToken($credentials);

        if (empty($accessToken)) {
            Log::error('Failed to obtain Google OAuth2 access token for FCM v1');
            return ['success' => false, 'message' => 'Failed to obtain Google OAuth2 access token'];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $results = [];
        $successCount = 0;
        $failCount = 0;

        // Stringify all data payload values as required by FCM v1
        $stringData = ['title' => (string)$title, 'body' => (string)$body, 'type' => (string)$type];
        foreach ($data as $k => $v) {
            $stringData[(string)$k] = is_scalar($v) ? (string)$v : json_encode($v);
        }

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token'        => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $stringData,
                    'android' => [
                        'priority'     => 'high',
                        'notification' => [
                            'sound'        => 'default',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ]
                    ]
                ]
            ];

            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type'  => 'application/json',
                ])->timeout(10)->post($url, $payload);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failCount++;
                    $resJson = $response->json();
                    $errorStatus = $resJson['error']['status'] ?? null;

                    // Auto-delete stale/unregistered tokens
                    if (in_array($errorStatus, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        DeviceToken::where('token', $token)->delete();
                        Log::info("Removed invalid FCM token: {$token}");
                    }
                }
                $results[] = $response->json();
            } catch (\Throwable $e) {
                $failCount++;
                Log::error("FCM v1 send error for token {$token}: " . $e->getMessage());
            }
        }

        return [
            'success'       => true,
            'api_version'   => 'v1',
            'success_count' => $successCount,
            'fail_count'    => $failCount,
        ];
    }

    /**
     * Send via Legacy FCM HTTP API
     */
    private static function sendViaLegacyFcm(array $tokens, string $title, string $body, array $data, string $type, string $serverKey): array
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $payload = [
            'registration_ids' => $tokens,
            'notification'     => [
                'title' => $title,
                'body'  => $body,
                'sound' => 'default',
            ],
            'data' => array_merge($data, [
                'title'        => $title,
                'body'         => $body,
                'type'         => $type,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]),
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ])->timeout(10)->post($url, $payload);

            $result = $response->json();

            // Auto-clean invalid or unregistered device tokens
            if (!empty($result['results']) && is_array($result['results'])) {
                foreach ($result['results'] as $index => $item) {
                    if (isset($item['error']) && in_array($item['error'], ['NotRegistered', 'InvalidRegistration', 'MismatchSenderId'])) {
                        $badToken = $tokens[$index] ?? null;
                        if ($badToken) {
                            DeviceToken::where('token', $badToken)->delete();
                            Log::info("Removed stale FCM device token: {$badToken}");
                        }
                    }
                }
            }

            return ['success' => true, 'api_version' => 'legacy', 'response' => $result];
        } catch (\Throwable $e) {
            Log::error('FCM Legacy Send Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get or generate a Google OAuth2 access token using the Service Account JSON
     */
    private static function getGoogleAccessToken(array $credentials): ?string
    {
        $cacheKey = 'firebase_fcm_v1_access_token_' . md5($credentials['client_email']);

        return Cache::remember($cacheKey, 3300, function () use ($credentials) {
            $now = time();
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $claims = json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600
            ]);

            $b64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $b64UrlClaims = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claims));
            $signatureInput = $b64UrlHeader . '.' . $b64UrlClaims;
            $signature = '';

            if (!openssl_sign($signatureInput, $signature, $credentials['private_key'], 'SHA256')) {
                Log::error('Failed to sign JWT with Firebase private key');
                return null;
            }

            $b64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            $jwt = $signatureInput . '.' . $b64UrlSignature;

            $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt
            ]);

            return $response->json()['access_token'] ?? null;
        });
    }

    /**
     * Determine the path to the Firebase Service Account JSON
     */
    private static function getCredentialsPath(): ?string
    {
        $custom = env('FIREBASE_CREDENTIALS');
        if (!empty($custom) && file_exists($custom)) {
            return $custom;
        }

        $defaultApp = storage_path('app/firebase_credentials.json');
        if (file_exists($defaultApp)) {
            return $defaultApp;
        }

        $defaultFirebase = storage_path('app/firebase/firebase_credentials.json');
        if (file_exists($defaultFirebase)) {
            return $defaultFirebase;
        }

        return null;
    }
}
