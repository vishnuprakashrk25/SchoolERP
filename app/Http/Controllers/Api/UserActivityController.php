<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:super-admin|admin']);
    }

    /**
     * @OA\Get(
     *      path="/api/user-activities",
     *      operationId="getUserActivitiesList",
     *      tags={"1. Core System - User Activity"},
     *      summary="Get list of user activities and API calls",
     *      description="Returns paginated user activity logs with optional filters",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="user_id", in="query", required=false, description="Filter by user ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="activity_type", in="query", required=false, description="Filter by type (login, logout, api_call)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="method", in="query", required=false, description="Filter by HTTP method (GET, POST, PUT, DELETE)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="date", in="query", required=false, description="Filter by date (YYYY-MM-DD)", @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="date_from", in="query", required=false, description="Start date (YYYY-MM-DD)", @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="date_to", in="query", required=false, description="End date (YYYY-MM-DD)", @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by user name, email, IP, or URL", @OA\Schema(type="string")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=25)),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="meta", type="object")
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $query = UserActivityLog::with('user:id,name,email,photo')->latest('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->method));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name', 'like', "%{$s}%")
                  ->orWhere('user_email', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
                  ->orWhere('url', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $perPage = (int) $request->input('per_page', 25);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $logs->items(),
            'meta'    => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/user-activities/{id}",
     *      operationId="getUserActivityById",
     *      tags={"1. Core System - User Activity"},
     *      summary="Get detailed user activity log by ID",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Log not found")
     * )
     */
    public function show($id)
    {
        $log = UserActivityLog::with('user:id,name,email,photo')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $log
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/user-activities/login-history",
     *      operationId="getUserLoginHistory",
     *      tags={"1. Core System - User Activity"},
     *      summary="Get login and logout history of users",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="user_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=25)),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function loginHistory(Request $request)
    {
        $query = UserActivityLog::whereIn('activity_type', ['login', 'logout'])
            ->with('user:id,name,email,photo')
            ->latest('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $perPage = (int) $request->input('per_page', 25);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $logs->items(),
            'meta'    => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/user-activities/summary",
     *      operationId="getUserActivitySummary",
     *      tags={"1. Core System - User Activity"},
     *      summary="Get activity statistics and summary for dashboard",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function summary()
    {
        $today = Carbon::today();

        $totalCallsToday = UserActivityLog::whereDate('created_at', $today)->count();
        $totalLoginsToday = UserActivityLog::whereDate('created_at', $today)->where('activity_type', 'login')->count();
        $activeUsersToday = UserActivityLog::whereDate('created_at', $today)->distinct('user_id')->count('user_id');

        $topApis = UserActivityLog::where('activity_type', 'api_call')
            ->select('method', 'url', DB::raw('count(*) as total'))
            ->groupBy('method', 'url')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentLogins = UserActivityLog::where('activity_type', 'login')
            ->latest('id')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_activities_today' => $totalCallsToday,
                'total_logins_today'     => $totalLoginsToday,
                'active_users_today'     => $activeUsersToday,
                'top_apis'               => $topApis,
                'recent_logins'          => $recentLogins,
            ]
        ]);
    }
}

