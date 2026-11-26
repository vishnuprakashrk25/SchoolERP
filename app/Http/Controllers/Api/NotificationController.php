<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\DeviceToken;
use App\Services\PushNotificationService;

class NotificationController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/notifications",
     *      operationId="getNotificationsList",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Get list of Notifications",
     *      description="Returns list of notifications with optional user_id, unread, and limit filters",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="user_id", in="query", required=false, description="Filter by user ID (defaults to auth user if not admin)", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="unread_only", in="query", required=false, description="Filter only unread notifications", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="limit", in="query", required=false, description="Items per page", @OA\Schema(type="integer", default=20)),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="unread_count", type="integer", example=3),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $query = Notification::query()->with('sender:id,name,full_name,email');

        if ($request->filled('user_id')) {
            $query->where('user_id', $userId);
        } elseif ($request->boolean('mine') || auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        $limit = (int) $request->input('limit', 20);
        $notifications = $query->latest()->paginate($limit);

        $unreadCount = Notification::where('user_id', auth()->id() ?? $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $unreadCount,
            'data'         => $notifications->items(),
            'meta'         => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/my-notifications",
     *      operationId="getMyNotifications",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Get authenticated user's notifications",
     *      description="Returns the logged-in user's notification list and unread count",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", default=20)),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="unread_count", type="integer", example=2),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function myNotifications(Request $request)
    {
        $userId = auth()->id();
        $limit = (int) $request->input('limit', 20);

        $notifications = Notification::where('user_id', $userId)
            ->with('sender:id,name,full_name,email')
            ->latest()
            ->paginate($limit);

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $unreadCount,
            'data'         => $notifications->items(),
            'meta'         => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/notifications/unread-count",
     *      operationId="getUnreadNotificationCount",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Get unread notification count for current user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="unread_count", type="integer", example=4)
     *          )
     *      )
     * )
     */
    public function unreadCount(Request $request)
    {
        $userId = auth()->id() ?? $request->input('user_id');
        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $count
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/notifications/{id}/read",
     *      operationId="markNotificationAsRead",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Mark a notification as read",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Notification marked as read")
     *          )
     *      )
     * )
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data'    => $notification
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/notifications/mark-all-read",
     *      operationId="markAllNotificationsAsRead",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Mark all notifications as read for current user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="All notifications marked as read")
     *          )
     *      )
     * )
     */
    public function markAllAsRead(Request $request)
    {
        $userId = auth()->id() ?? $request->input('user_id');

        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/save-device-token",
     *      operationId="saveDeviceToken",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Register FCM device token for push notifications",
     *      description="Stores or updates user's mobile or web device token for Firebase Cloud Messaging",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token"},
     *              @OA\Property(property="token", type="string", example="fcm_token_string_here"),
     *              @OA\Property(property="device_type", type="string", enum={"android", "ios", "web"}, example="android")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Device token registered successfully")
     *          )
     *      )
     * )
     */
    public function saveDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'       => 'required|string',
            'device_type' => 'nullable|string|in:android,ios,web',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $userId = auth()->id() ?? $request->input('user_id');
        if (empty($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required to register device token'
            ], 401);
        }

        $token = DeviceToken::updateOrCreate(
            ['token' => $request->input('token')],
            [
                'user_id'     => $userId,
                'device_type' => $request->input('device_type', 'android'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token registered successfully',
            'data'    => $token
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/remove-device-token",
     *      operationId="removeDeviceToken",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Remove device token (e.g. on user logout)",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token"},
     *              @OA\Property(property="token", type="string", example="fcm_token_string_here")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Device token removed successfully")
     *          )
     *      )
     * )
     */
    public function removeDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        DeviceToken::where('token', $request->input('token'))->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device token removed successfully'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/notifications/send",
     *      operationId="sendTargetedNotification",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Send push & in-app notifications to target roles, class, or users",
     *      description="Dispatches push notifications and creates in-app database notifications. Targets can be 'role' (Admin, Teacher, Student, Parent, Staff), 'class', 'user', or 'all'.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "message", "target_type"},
     *              @OA\Property(property="title", type="string", example="School Annual Day Rehearsal"),
     *              @OA\Property(property="message", type="string", example="Please report to the auditorium at 10:00 AM."),
     *              @OA\Property(property="type", type="string", example="announcement"),
     *              @OA\Property(property="target_type", type="string", enum={"role", "class", "user", "all"}, example="role"),
     *              @OA\Property(property="roles", type="array", @OA\Items(type="string", example="Teacher"), description="Required if target_type is role"),
     *              @OA\Property(property="class_id", type="integer", example=5, description="Required if target_type is class"),
     *              @OA\Property(property="section_id", type="integer", example=2, description="Optional for target_type class"),
     *              @OA\Property(property="include_parents", type="boolean", example=true, description="If true, sends to students and parents of class"),
     *              @OA\Property(property="user_ids", type="array", @OA\Items(type="integer"), description="Required if target_type is user"),
     *              @OA\Property(property="data", type="object", example={"screen": "NoticeDetails", "id": 12})
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Notification dispatched successfully"),
     *              @OA\Property(property="recipients_count", type="integer", example=15)
     *          )
     *      )
     * )
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'           => 'required|string|max:255',
            'message'         => 'required|string',
            'target_type'     => 'required|string|in:role,class,user,all',
            'roles'           => 'required_if:target_type,role|array',
            'roles.*'         => 'string',
            'class_id'        => 'required_if:target_type,class|integer',
            'section_id'      => 'nullable|integer',
            'include_parents' => 'nullable|boolean',
            'user_ids'        => 'required_if:target_type,user|array',
            'user_ids.*'      => 'integer',
            'type'            => 'nullable|string|max:100',
            'data'            => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $title      = $request->input('title');
        $body       = $request->input('message');
        $targetType = $request->input('target_type');
        $type       = $request->input('type', 'general');
        $data       = $request->input('data', []);
        $senderId   = auth()->id();
        $count      = 0;

        if ($targetType === 'role') {
            $roles = (array) $request->input('roles', []);
            $count = PushNotificationService::sendToRoles($roles, $title, $body, $data, $type, $senderId);
        } elseif ($targetType === 'class') {
            $classId = (int) $request->input('class_id');
            $sectionId = $request->input('section_id');
            $includeParents = $request->boolean('include_parents', true);
            $count = PushNotificationService::sendToClassSection($classId, $sectionId, $title, $body, $data, $type, $senderId, $includeParents);
        } elseif ($targetType === 'user') {
            $userIds = (array) $request->input('user_ids', []);
            $count = PushNotificationService::sendToUsers($userIds, $title, $body, $data, $type, $senderId);
        } elseif ($targetType === 'all') {
            $count = PushNotificationService::sendBroadcast($title, $body, $data, $type, $senderId);
        }

        return response()->json([
            'success'          => true,
            'message'          => 'Notification dispatched successfully',
            'target_type'      => $targetType,
            'recipients_count' => $count
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/notifications",
     *      operationId="storeNotification",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Store single notification (Direct CRUD)",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"user_id","title","message"},
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="New Announcement"),
     *              @OA\Property(property="message", type="string", example="Notification message text"),
     *              @OA\Property(property="type", type="string", example="general"),
     *              @OA\Property(property="send_push", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'type'    => 'nullable|string|max:100',
            'data'    => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $userId = $request->input('user_id');
        $title = $request->input('title');
        $message = $request->input('message');
        $type = $request->input('type', 'general');
        $data = $request->input('data', []);
        $senderId = auth()->id();

        if ($request->boolean('send_push', true)) {
            $item = PushNotificationService::sendToUser($userId, $title, $message, $data, $type, $senderId);
        } else {
            $item = Notification::create([
                'user_id'   => $userId,
                'sender_id' => $senderId,
                'title'     => $title,
                'message'   => $message,
                'type'      => $type,
                'data'      => $data,
                'is_read'   => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/notifications/{id}",
     *      operationId="getNotificationById",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Get notification details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        $item = Notification::with('sender:id,name,full_name,email')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/notifications/{id}",
     *      operationId="updateNotification",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Update existing notification",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string"),
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="is_read", type="boolean")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $item = Notification::findOrFail($id);
        $item->update($request->only(['title', 'message', 'type', 'data', 'is_read']));

        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/notifications/{id}",
     *      operationId="deleteNotification",
     *      tags={"8. Communication & Utils - Notification"},
     *      summary="Delete existing notification",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function destroy($id)
    {
        $item = Notification::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
}
