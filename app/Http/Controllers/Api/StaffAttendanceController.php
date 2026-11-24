<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;
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
     *      path="/api/staff-attendances",
     *      operationId="getStaffAttendanceList",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Get list of StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="staff_id", type="integer"),
     *                      @OA\Property(property="date", type="string"),
     *                      @OA\Property(property="status", type="string"),
     *                      @OA\Property(property="note", type="string", example="On sick leave"),
     *                      @OA\Property(property="photo", type="string", example="http://localhost:8000/uploads/staff_photos/example.jpg"),
     *                      @OA\Property(property="staff", type="object")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        return $this->respondPaginatedOrAll(StaffAttendance::with(['staff'])->latest(), $request);
    }

    /**
     * @OA\Post(
     *      path="/api/staff-attendances",
     *      operationId="storeStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Store new StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"staff_id","date","status"},
     *              @OA\Property(property="staff_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-08-09"),
     *              @OA\Property(property="status", type="string", example="present"),
     *              @OA\Property(property="note", type="string", example="On sick leave")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
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
        $item = StaffAttendance::create($request->all());
        $item->load('staff');
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
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