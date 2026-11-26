<?php

namespace App\Services;

use App\Models\User;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Notification;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            // Role may not exist in Spatie or user has direct match
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
     * Dispatch push notifications via Firebase Cloud Messaging (FCM)
     */
    public static function sendFcmNotification(array $tokens, string $title, string $body, array $data = [], string $type = 'general'): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));
        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No device tokens provided'];
        }

        $serverKey = env('FCM_SERVER_KEY');
        if (empty($serverKey)) {
            Log::info("FCM Notification simulated (FCM_SERVER_KEY not set in .env): '{$title}' to " . count($tokens) . " devices.");
            return [
                'success' => true,
                'simulated' => true,
                'message' => 'FCM_SERVER_KEY not configured in .env. Notification recorded in database.',
                'token_count' => count($tokens)
            ];
        }

        $url = 'https://fcm.googleapis.com/fcm/send';

        $payload = [
            'registration_ids' => $tokens,
            'notification'     => [
                'title' => $title,
                'body'  => $body,
                'sound' => 'default',
                'badge' => 1,
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

            return [
                'success'  => true,
                'response' => $result
            ];
        } catch (\Throwable $e) {
            Log::error('FCM Send Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }
}
