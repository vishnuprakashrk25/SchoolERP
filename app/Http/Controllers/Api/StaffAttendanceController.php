<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class StaffAttendanceController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/staff-attendances/monthly-report",
     *      operationId="getStaffMonthlyAttendanceReport",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Staff Attendance Monthly Report",
     *      description="Returns structured grid-ready monthly attendance report for staff members (P, L, A, F, H, Le, W) across all days of the selected month.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="year", in="query", required=false, description="Year (default current year)", @OA\Schema(type="integer", example=2026)),
     *      @OA\Parameter(name="month", in="query", required=false, description="Month 1-12 (default current month)", @OA\Schema(type="integer", example=11)),
     *      @OA\Parameter(name="role_id", in="query", required=false, description="Filter by staff role ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="role", in="query", required=false, description="Filter by staff role name (e.g. Teacher, Admin)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="staff_id", in="query", required=false, description="Filter for a specific staff member ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search staff name, code, email, or phone", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="month", type="integer", example=11),
     *              @OA\Property(property="year", type="integer", example=2026),
     *              @OA\Property(property="month_name", type="string", example="November 2026"),
     *              @OA\Property(property="title", type="string", example="Staff Attendance Report P:45 L:3 A:2 F:1 H:4 Le:1"),
     *              @OA\Property(property="summary", type="object"),
     *              @OA\Property(property="legend", type="object"),
     *              @OA\Property(property="days_header", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="total_staff", type="integer", example=15),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function monthlyReport(Request $request, $staffIdParam = null)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $staffId = $staffIdParam ?? $request->input('staff_id');

        $firstDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $daysInMonth = $firstDate->daysInMonth;

        // Build days header with day numbers and 3-letter weekday names (e.g. 1 Sun, 2 Mon...)
        $daysHeader = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $curDate = Carbon::createFromDate($year, $month, $d);
            $isWeekend = ($curDate->dayOfWeek === Carbon::SUNDAY);
            $daysHeader[] = [
                'day'        => $d,
                'day_name'   => $curDate->format('D'),
                'is_weekend' => $isWeekend,
                'date'       => $curDate->toDateString(),
            ];
        }

        // Query staff
        $staffQuery = Staff::query();

        if (!empty($staffId)) {
            $staffQuery->where('id', $staffId);
        } else {
            if ($request->filled('role_id')) {
                $roleObj = Role::find($request->input('role_id'));
                if ($roleObj) {
                    $staffQuery->where('role', $roleObj->name);
                }
            } elseif ($request->filled('role')) {
                $staffQuery->where('role', $request->input('role'));
            }

            if ($request->filled('search')) {
                $search = trim($request->input('search'));
                $staffQuery->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('staff_code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
        }

        $staffMembers = $staffQuery->orderBy('role')->orderBy('first_name')->get();

        if ($staffMembers->isEmpty() && !empty($staffId)) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found'
            ], 404);
        }

        $staffIds = $staffMembers->pluck('id')->toArray();

        // Fetch attendances for this month
        $attendances = StaffAttendance::whereIn('staff_id', $staffIds)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy('staff_id');

        // Global counters for the top title header: "Staff Attendance Report P:0 L:0 A:0 F:0 H:0 Le:0"
        $globalCounts = [
            'P'  => 0,
            'L'  => 0,
            'A'  => 0,
            'F'  => 0,
            'H'  => 0,
            'Le' => 0,
        ];

        $reportRows = [];

        foreach ($staffMembers as $member) {
            $memberAtts = $attendances->get($member->id, collect());
            $byDate = [];
            foreach ($memberAtts as $att) {
                $dStr = Carbon::parse($att->date)->toDateString();
                $byDate[$dStr] = $att;
            }

            $p = 0; $l = 0; $a = 0; $f = 0; $h = 0; $le = 0;
            $dailyAttendance = [];
            $dailyNotes = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $curDate = Carbon::createFromDate($year, $month, $d);
                $dateStr = $curDate->toDateString();
                $isWeekend = ($curDate->dayOfWeek === Carbon::SUNDAY);

                $attRecord = $byDate[$dateStr] ?? null;
                $statusCode = '';

                if ($attRecord) {
                    $rawStatus = strtolower(trim((string)$attRecord->status));
                    if ($rawStatus === 'present' || $rawStatus === 'p') {
                        $statusCode = 'P';
                        $p++;
                    } elseif ($rawStatus === 'late' || $rawStatus === 'l') {
                        $statusCode = 'L';
                        $l++;
                    } elseif ($rawStatus === 'absent' || $rawStatus === 'a') {
                        $statusCode = 'A';
                        $a++;
                    } elseif (str_contains($rawStatus, 'half') || $rawStatus === 'f') {
                        $statusCode = 'F';
                        $f++;
                    } elseif ($rawStatus === 'holiday' || $rawStatus === 'h') {
                        $statusCode = 'H';
                        $h++;
                    } elseif ($rawStatus === 'leave' || $rawStatus === 'le') {
                        $statusCode = 'Le';
                        $le++;
                    } else {
                        $statusCode = strtoupper(substr($rawStatus, 0, 1));
                    }
                } else {
                    if ($isWeekend) {
                        $statusCode = 'W';
                    } else {
                        $statusCode = '';
                    }
                }

                $dailyAttendance[$d] = $statusCode;
                $dailyNotes[$d] = $attRecord?->note ?? '';
            }

            $globalCounts['P']  += $p;
            $globalCounts['L']  += $l;
            $globalCounts['A']  += $a;
            $globalCounts['F']  += $f;
            $globalCounts['H']  += $h;
            $globalCounts['Le'] += $le;

            $workingDays = $p + $l + $a + $f + $le;
            if ($workingDays > 0) {
                $attendedPoints = $p + $l + ($f * 0.5);
                $percentage = round(($attendedPoints / $workingDays) * 100) . '%';
            } else {
                $percentage = '100%';
            }

            $fullName = $member->full_name ?: trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? ''));

            $reportRows[] = [
                'staff_id'        => $member->id,
                'name'            => $fullName,
                'staff_no'        => $member->staff_code ?? $member->staff_no,
                'staff_code'      => $member->staff_code ?? $member->staff_no,
                'role'            => $member->role,
                'role_id'         => $member->role_id,
                'designation'     => $member->designation,
                'photo'           => $member->photo ? url($member->photo) : null,
                'P'               => $p,
                'L'               => $l,
                'A'               => $a,
                'F'               => $f,
                'H'               => $h,
                'Le'              => $le,
                'percentage'      => $percentage,
                'days'            => $dailyAttendance,
                'notes'           => $dailyNotes,
            ];
        }

        $summaryText = sprintf(
            'Staff Attendance Report P:%d L:%d A:%d F:%d H:%d Le:%d',
            $globalCounts['P'],
            $globalCounts['L'],
            $globalCounts['A'],
            $globalCounts['F'],
            $globalCounts['H'],
            $globalCounts['Le']
        );

        return response()->json([
            'success'      => true,
            'month'        => $month,
            'year'         => $year,
            'month_name'   => Carbon::createFromDate($year, $month, 1)->format('F Y'),
            'title'        => $summaryText,
            'summary'      => $globalCounts,
            'legend'       => [
                'P'  => 'Present',
                'L'  => 'Late',
                'A'  => 'Absent',
                'F'  => 'Half Day',
                'H'  => 'Holiday',
                'Le' => 'Leave',
                'W'  => 'Weekend',
            ],
            'days_header'  => $daysHeader,
            'total_staff'  => count($reportRows),
            'data'         => $reportRows,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/staff-attendances/roster",
     *      operationId="getStaffAttendanceRoster",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="List staff roster with attendance status for taking daily attendance",
     *      description="Returns all staff members (with role/search filters) mapped with their attendance record for the given date (default today). If not yet marked, status defaults to 'Present'.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="date", in="query", required=false, description="Attendance date (YYYY-MM-DD, defaults to today)", @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="role_id", in="query", required=false, description="Filter staff by role ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="role", in="query", required=false, description="Filter staff by role name (e.g. Teacher, Admin)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search staff name, code, phone, or email", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status", in="query", required=false, description="Filter staff by attendance status", @OA\Schema(type="string")),
     *      @OA\Parameter(name="staff_id", in="query", required=false, description="Filter specific staff ID", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="date", type="string", format="date", example="2026-11-26"),
     *              @OA\Property(property="total_staff", type="integer", example=10),
     *              @OA\Property(property="marked_count", type="integer", example=5),
     *              @OA\Property(property="present_count", type="integer", example=4),
     *              @OA\Property(property="absent_count", type="integer", example=1),
     *              @OA\Property(property="late_count", type="integer", example=0),
     *              @OA\Property(property="half_day_count", type="integer", example=0),
     *              @OA\Property(property="holiday_count", type="integer", example=0),
     *              @OA\Property(property="leave_count", type="integer", example=0),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", nullable=true, example=1),
     *                      @OA\Property(property="staff_id", type="integer", example=3),
     *                      @OA\Property(property="staff_code", type="string", example="STF003"),
     *                      @OA\Property(property="name", type="string", example="Sarah Connor"),
     *                      @OA\Property(property="full_name", type="string", example="Sarah Connor"),
     *                      @OA\Property(property="role", type="string", example="Teacher"),
     *                      @OA\Property(property="role_id", type="integer", example=2),
     *                      @OA\Property(property="designation", type="string", example="Senior Teacher"),
     *                      @OA\Property(property="category", type="string", example="Teaching"),
     *                      @OA\Property(property="photo", type="string", nullable=true, example="http://localhost:8000/uploads/staff/sarah.jpg"),
     *                      @OA\Property(property="phone", type="string", example="9876543210"),
     *                      @OA\Property(property="email", type="string", example="sarah@school.com"),
     *                      @OA\Property(property="date", type="string", format="date", example="2026-11-26"),
     *                      @OA\Property(property="status", type="string", example="Present"),
     *                      @OA\Property(property="note", type="string", example=""),
     *                      @OA\Property(property="is_marked", type="boolean", example=true),
     *                      @OA\Property(property="staff", type="object")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function staffRoster(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $staffQuery = Staff::query();

        if ($request->filled('role_id')) {
            $roleObj = Role::find($request->input('role_id'));
            if ($roleObj) {
                $staffQuery->where('role', $roleObj->name);
            }
        } elseif ($request->filled('role')) {
            $staffQuery->where('role', $request->input('role'));
        }

        if ($request->filled('category')) {
            $staffQuery->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $staffQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('staff_id')) {
            $staffQuery->where('id', $request->input('staff_id'));
        }

        $staffMembers = $staffQuery->orderBy('role')->orderBy('first_name')->get();

        // Fetch existing attendance records for the given date
        $attendances = StaffAttendance::whereDate('date', $date)
            ->whereIn('staff_id', $staffMembers->pluck('id'))
            ->get()
            ->keyBy('staff_id');

        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $halfDayCount = 0;
        $holidayCount = 0;
        $leaveCount = 0;

        $targetStatus = $request->filled('status') ? strtolower(trim($request->input('status'))) : null;

        $result = $staffMembers->map(function ($member) use ($attendances, $date, &$presentCount, &$absentCount, &$lateCount, &$halfDayCount, &$holidayCount, &$leaveCount) {
            $att = $attendances->get($member->id);
            $status = $att?->status ?? 'Present';

            $stLower = strtolower(trim($status));
            if (in_array($stLower, ['p', 'present'])) {
                $presentCount++;
            } elseif (in_array($stLower, ['a', 'absent'])) {
                $absentCount++;
            } elseif (in_array($stLower, ['l', 'late'])) {
                $lateCount++;
            } elseif (in_array($stLower, ['f', 'half day', 'half_day', 'halfday'])) {
                $halfDayCount++;
            } elseif (in_array($stLower, ['h', 'holiday'])) {
                $holidayCount++;
            } elseif (in_array($stLower, ['le', 'leave'])) {
                $leaveCount++;
            }

            $photoUrl = null;
            if (!empty($member->photo)) {
                $photoUrl = str_starts_with($member->photo, 'http') ? $member->photo : url($member->photo);
            }

            return [
                'id'              => $att?->id,
                'staff_id'        => $member->id,
                'staff_code'      => $member->staff_code ?? $member->staff_no,
                'name'            => $member->full_name ?? trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')),
                'full_name'       => $member->full_name ?? trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')),
                'role'            => $member->role,
                'role_id'         => $member->role_id,
                'designation'     => $member->designation,
                'category'        => $member->category,
                'photo'           => $photoUrl,
                'phone'           => $member->phone,
                'email'           => $member->email,
                'date'            => $att?->date ?? $date,
                'status'          => $status,
                'note'            => $att?->note ?? '',
                'is_marked'       => $att !== null,
                'staff'           => $member,
            ];
        });

        if ($targetStatus !== null) {
            $result = $result->filter(function ($item) use ($targetStatus) {
                return strtolower(trim($item['status'])) === $targetStatus;
            })->values();
        }

        return response()->json([
            'success'         => true,
            'date'            => $date,
            'total_staff'     => $staffMembers->count(),
            'marked_count'    => $attendances->count(),
            'unmarked_count'  => max(0, $staffMembers->count() - $attendances->count()),
            'present_count'   => $presentCount,
            'absent_count'    => $absentCount,
            'late_count'      => $lateCount,
            'half_day_count'  => $halfDayCount,
            'holiday_count'   => $holidayCount,
            'leave_count'     => $leaveCount,
            'data'            => $result,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/staff-attendances",
     *      operationId="getStaffAttendanceList",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Get list of StaffAttendance (or full staff roster with attendance status)",
     *      description="By default, returns staff roster for taking daily attendance. Pass only_uploaded=true to query only stored records.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="date", in="query", required=false, description="Date for attendance (YYYY-MM-DD)", @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="role_id", in="query", required=false, description="Role ID filter", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="role", in="query", required=false, description="Role name filter", @OA\Schema(type="string")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search name, code, phone, or email", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status", in="query", required=false, description="Filter by status", @OA\Schema(type="string")),
     *      @OA\Parameter(name="only_uploaded", in="query", required=false, description="If true, return only existing attendance records from DB", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        // When explicitly requesting only stored records or querying raw paginated list
        if ($request->boolean('only_uploaded')) {
            $query = StaffAttendance::with(['staff']);
            if ($request->filled('staff_id')) {
                $query->where('staff_id', $request->staff_id);
            }
            if ($request->filled('date')) {
                $query->whereDate('date', $request->date);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            return $this->respondPaginatedOrAll($query->latest('date'), $request);
        }

        // Default: return the full staff roster with attendance status for the date
        return $this->staffRoster($request);
    }

    /**
     * @OA\Post(
     *      path="/api/staff-attendances",
     *      operationId="storeStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Store Staff Attendance (Single or Bulk Array)",
     *      description="Stores single attendance record or bulk roster attendance array. Uses updateOrCreate on staff_id and date.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="date", type="string", format="date", example="2026-11-26"),
     *              @OA\Property(
     *                  property="attendances",
     *                  type="array",
     *                  description="Bulk attendance items (optional, omit for single record)",
     *                  @OA\Items(
     *                      @OA\Property(property="staff_id", type="integer", example=1),
     *                      @OA\Property(property="staff_code", type="string", example="STF001"),
     *                      @OA\Property(property="date", type="string", format="date", example="2026-11-26"),
     *                      @OA\Property(property="status", type="string", example="Present"),
     *                      @OA\Property(property="note", type="string", example="On time")
     *                  )
     *              ),
     *              @OA\Property(property="staff_id", type="integer", example=1, description="For single record"),
     *              @OA\Property(property="staff_code", type="string", example="STF001", description="For single record"),
     *              @OA\Property(property="status", type="string", example="Present", description="For single record"),
     *              @OA\Property(property="note", type="string", example="Optional note", description="For single record")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Staff attendances saved successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $bulkItems = null;

        // 1. Check for {"attendances": [...]}
        if ($request->has('attendances') && is_array($request->attendances)) {
            $bulkItems = $request->attendances;
        } else {
            // 2. Check for raw JSON array [...]
            $rawJson = $request->json()->all();
            if (is_array($rawJson) && isset($rawJson[0]) && is_array($rawJson[0])) {
                $bulkItems = $rawJson;
            }
        }

        if ($bulkItems !== null) {
            $saved = [];
            $dateParam = $request->input('date', now()->toDateString());
            DB::beginTransaction();
            try {
                foreach ($bulkItems as $item) {
                    $staffId = $item['staff_id'] ?? null;
                    $staffCode = $item['staff_code'] ?? null;
                    $date = $item['date'] ?? $dateParam;
                    $status = $item['status'] ?? 'Present';
                    $note = $item['note'] ?? null;

                    $staff = null;
                    if (!empty($staffId)) {
                        $staff = Staff::find($staffId);
                    }
                    if (!$staff && !empty($staffCode)) {
                        $staff = Staff::where('staff_code', $staffCode)->orWhere('phone', $staffCode)->orWhere('email', $staffCode)->first();
                        if (!$staff && is_numeric($staffCode)) {
                            $staff = Staff::find($staffCode);
                        }
                    }

                    if (!$staff) {
                        continue;
                    }

                    $record = StaffAttendance::updateOrCreate(
                        [
                            'staff_id' => $staff->id,
                            'date'     => $date,
                        ],
                        [
                            'status'   => $status,
                            'note'     => $note,
                        ]
                    );
                    $record->load('staff');
                    $saved[] = $record;
                }
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Staff attendances saved successfully',
                    'count'   => count($saved),
                    'data'    => $saved,
                ], 200);
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save staff attendances: ' . $e->getMessage()
                ], 500);
            }
        }

        // Single item store
        $validator = Validator::make($request->all(), [
            'staff_id'   => 'required_without:staff_code|integer|nullable',
            'staff_code' => 'required_without:staff_id|string|nullable',
            'date'       => 'required',
            'status'     => 'required|string',
            'note'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $staffId = $request->input('staff_id');
        if (empty($staffId) && $request->filled('staff_code')) {
            $staff = Staff::where('staff_code', $request->input('staff_code'))->first();
            if ($staff) {
                $staffId = $staff->id;
            }
        }

        if (empty($staffId)) {
            return response()->json([
                'success' => false,
                'message' => 'Valid staff member is required'
            ], 422);
        }

        $item = StaffAttendance::updateOrCreate(
            [
                'staff_id' => $staffId,
                'date'     => $request->input('date'),
            ],
            [
                'status'   => $request->input('status'),
                'note'     => $request->input('note'),
            ]
        );
        $item->load('staff');

        return response()->json([
            'success' => true,
            'message' => 'Staff attendance saved successfully',
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/staff-attendances/{id}",
     *      operationId="getStaffAttendanceById",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Get StaffAttendance information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
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
        $item = StaffAttendance::with(['staff'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/staff-attendances/{id}",
     *      operationId="updateStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Update existing StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"staff_id","date","status"},
     *              @OA\Property(property="staff_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-08-09"),
     *              @OA\Property(property="status", type="string", example="present"),
     *              @OA\Property(property="note", type="string", example="Updated note")
     *          )
     *      ),
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
    public function update(Request $request, $id)
    {
        $item = StaffAttendance::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|integer',
            'date' => 'required',
            'status' => 'required',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item->update($request->all());
        $item->load('staff');
        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/staff-attendances/{id}",
     *      operationId="deleteStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Delete existing StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $item = StaffAttendance::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}