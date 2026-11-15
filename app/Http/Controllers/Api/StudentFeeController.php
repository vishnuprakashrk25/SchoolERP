<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentFee\AssignStudentFeeRequest;
use App\Services\FeeAssignmentService;
use Illuminate\Http\Request;
use App\Models\StudentFee;
use App\Models\StudentFeeItem;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class StudentFeeController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/student-fees",
     *      operationId="getStudentFeesList",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Get list of Student Fees",
     *      description="Returns list of student fee assignments with status and balance filtering",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="student_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = StudentFee::with([
            'student:id,admission_no,first_name,last_name,class_id,section_id,academic_year_id',
            'feeStructure:id,name,class_id,total_amount',
            'academicYear:id,name',
            'items.feeType:id,name,code',
        ]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_id') || $request->filled('section_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                if ($request->filled('class_id')) {
                    $q->where('class_id', $request->class_id);
                }
                if ($request->filled('section_id')) {
                    $q->where('section_id', $request->section_id);
                }
            });
        }

        return $this->respondPaginatedOrAll($query->latest(), $request);
    }

    /**
     * @OA\Post(
     *      path="/api/student-fees/assign",
     *      operationId="assignStudentFeeStructure",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Assign fee structure to a class or student list",
     *      description="Automatically generates student fee obligations and itemized schedules",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"fee_structure_id"},
     *              @OA\Property(property="fee_structure_id", type="integer", example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", example=null),
     *              @OA\Property(property="student_ids", type="array", @OA\Items(type="integer"), example={1, 2, 3}),
     *              @OA\Property(property="due_date", type="string", format="date", example="2026-06-30")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Assignment completed"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function assign(AssignStudentFeeRequest $request, FeeAssignmentService $feeAssignmentService)
    {
        $structure = FeeStructure::with(['items.feeType', 'feeType', 'installments'])->findOrFail($request->fee_structure_id);
        $academicYearId = $request->academic_year_id ?? $structure->academic_year_id;

        $students = $feeAssignmentService->resolveStudents($structure, $request->only([
            'student_ids',
            'class_id',
            'section_id',
            'category_id',
        ]));

        if ($students->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No students found matching the assignment criteria'
            ], 404);
        }

        $result = $feeAssignmentService->assignToStudents(
            $students,
            $structure,
            $academicYearId ? (int) $academicYearId : null,
            $request->due_date ?? null,
            Auth::id()
        );

        return response()->json([
            'success' => true,
            'message' => "Fee structure assigned: {$result['assigned_count']} student(s) assigned, {$result['skipped_count']} already assigned/skipped.",
            'data' => $result,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/student-fees/student/{studentId}",
     *      operationId="getStudentFeeProfile",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Get comprehensive student fee profile and ledger",
     *      description="Returns complete financial profile: assigned structures, item schedule, payment history, and balance dues",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="studentId", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Student Not Found")
     * )
     */
    public function studentProfile($studentId)
    {
        $student = Student::with(['classes', 'section', 'academicYear'])->find($studentId);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $studentFees = StudentFee::with([
            'feeStructure:id,name,total_amount',
            'items.feeType:id,name,code,billing_cycle',
            'items.feeInstallment:id,name,due_date',
            'payments.items',
            'academicYear:id,name'
        ])
        ->where('student_id', $studentId)
        ->latest()
        ->get();

        $payments = Payment::with(['items.studentFeeItem', 'collector:id,name'])
            ->where('student_id', $studentId)
            ->latest()
            ->get();

        $totalAssigned = (float)$studentFees->sum('amount');
        $totalPaid = (float)$studentFees->sum('paid_amount');
        $totalDiscount = (float)$studentFees->sum('discount_amount');
        $totalFine = (float)$studentFees->sum('fine_amount');
        $totalBalance = (float)$studentFees->sum('balance_amount');

        $isClear = ($totalBalance <= 0 && $totalAssigned > 0);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'admission_no' => $student->admission_no,
                    'roll_no' => $student->roll_no,
                    'name' => $student->full_name,
                    'class' => optional($student->classes)->name,
                    'section' => optional($student->section)->name,
                    'academic_year' => optional($student->academicYear)->name,
                ],
                'summary' => [
                    'total_assigned' => $totalAssigned,
                    'total_discount' => $totalDiscount,
                    'total_fine' => $totalFine,
                    'total_paid' => $totalPaid,
                    'total_balance_due' => $totalBalance,
                    'clearance_status' => $isClear ? 'Clear' : ($totalPaid > 0 ? 'Partial' : 'Dues Pending'),
                ],
                'fee_structures' => $studentFees,
                'payment_history' => $payments,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/student-fees/dues",
     *      operationId="getStudentFeeDuesReport",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Get outstanding student fee dues list",
     *      description="Filters overdue and pending student fees class-wise and section-wise",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function duesReport(Request $request)
    {
        $query = StudentFee::with([
            'student:id,admission_no,first_name,last_name,full_name,class_id,section_id,phone',
            'student.classes:id,name',
            'student.section:id,name',
            'feeStructure:id,name'
        ])
        ->where('balance_amount', '>', 0);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('class_id') || $request->filled('section_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                if ($request->filled('class_id')) {
                    $q->where('class_id', $request->class_id);
                }
                if ($request->filled('section_id')) {
                    $q->where('section_id', $request->section_id);
                }
            });
        }

        $dues = $query->orderByDesc('balance_amount')->get();

        $totalDueAmount = (float)$dues->sum('balance_amount');
        $studentCount = $dues->pluck('student_id')->unique()->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'total_due_amount' => $totalDueAmount,
                'defaulter_student_count' => $studentCount,
            ],
            'data' => $dues
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/student-fees/{id}",
     *      operationId="getStudentFeeById",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Get Student Fee details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $fee = StudentFee::with([
            'student',
            'feeStructure.items.feeType',
            'items.feeType',
            'items.feeInstallment',
            'payments.items'
        ])->find($id);

        if (!$fee) {
            return response()->json([
                'success' => false,
                'message' => 'Student fee record not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $fee
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/student-fees",
     *      operationId="storeStudentFee",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Store individual student fee",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'fee_structure_id' => 'required|integer|exists:fee_structures,id',
            'academic_year_id' => 'nullable|integer',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $structure = FeeStructure::with('items.feeType')->findOrFail($request->fee_structure_id);
        $amount = $request->amount ?? ($structure->total_amount > 0 ? $structure->total_amount : $structure->amount);

        $studentFee = StudentFee::create([
            'student_id' => $request->student_id,
            'fee_structure_id' => $structure->id,
            'academic_year_id' => $request->academic_year_id ?? $structure->academic_year_id,
            'amount' => $amount,
            'paid_amount' => 0.00,
            'discount_amount' => 0.00,
            'fine_amount' => 0.00,
            'balance_amount' => $amount,
            'due_date' => $request->due_date,
            'remarks' => $request->remarks,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        if ($structure->items->isNotEmpty()) {
            foreach ($structure->items as $item) {
                StudentFeeItem::create([
                    'student_fee_id' => $studentFee->id,
                    'fee_structure_item_id' => $item->id,
                    'fee_type_id' => $item->fee_type_id,
                    'name' => optional($item->feeType)->name ?? ('Fee Item #' . $item->id),
                    'amount' => $item->amount,
                    'paid_amount' => 0.00,
                    'discount_amount' => 0.00,
                    'fine_amount' => 0.00,
                    'balance_amount' => $item->amount,
                    'due_date' => $item->due_date ?? $request->due_date,
                    'status' => 'pending',
                ]);
            }
        }

        $studentFee->recalculateSummary();

        return response()->json([
            'success' => true,
            'message' => 'Student fee obligation created successfully',
            'data' => $studentFee->load('items')
        ], 201);
    }

    /**
     * @OA\Put(
     *      path="/api/student-fees/{id}",
     *      operationId="updateStudentFee",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Update existing Student Fee",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, $id)
    {
        $studentFee = StudentFee::find($id);
        if (!$studentFee) {
            return response()->json([
                'success' => false,
                'message' => 'Student fee record not found'
            ], 404);
        }

        $payload = $request->only(['due_date', 'remarks', 'status']);
        $payload['updated_by'] = Auth::id();
        $studentFee->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Student fee updated successfully',
            'data' => $studentFee
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/student-fees/{id}",
     *      operationId="deleteStudentFee",
     *      tags={"5. Finance & Fee Collection - StudentFee"},
     *      summary="Delete Student Fee record",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy($id)
    {
        $studentFee = StudentFee::find($id);
        if (!$studentFee) {
            return response()->json([
                'success' => false,
                'message' => 'Student fee record not found'
            ], 404);
        }

        $studentFee->items()->delete();
        $studentFee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student fee deleted successfully'
        ]);
    }
}
