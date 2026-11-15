<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\FeePaymentItem;
use App\Models\StudentFee;
use App\Models\StudentFeeItem;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/payments",
     *      operationId="getPaymentsList",
     *      tags={"5. Finance & Fee Collection - Payment"},
     *      summary="Get list of Payments",
     *      description="Returns payment transactions with receipts, methods, and status filters",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="student_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="payment_method", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="date_from", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="date_to", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = Payment::with([
            'student:id,admission_no,first_name,last_name,class_id,section_id',
            'student.classes:id,name',
            'student.section:id,name',
            'studentFee:id,fee_structure_id,amount,balance_amount',
            'collector:id,name',
            'items.studentFeeItem.feeType',
        ]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        return $this->respondPaginatedOrAll($query->latest('payment_date')->latest('id'), $request);
    }

    /**
     * @OA\Post(
     *      path="/api/payments/collect",
     *      operationId="collectFeePayment",
     *      tags={"5. Finance & Fee Collection - Payment"},
     *      summary="Collect fee payment and generate receipt",
     *      description="Processes student fee collection with partial/full support, item allocations, multiple payment modes, and auto receipt generation",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id","amount","payment_method"},
     *              @OA\Property(property="student_id", type="integer", example=1),
     *              @OA\Property(property="student_fee_id", type="integer", example=1),
     *              @OA\Property(property="amount", type="number", format="float", example=1000.00),
     *              @OA\Property(property="discount_amount", type="number", format="float", example=0.00),
     *              @OA\Property(property="fine_amount", type="number", format="float", example=0.00),
     *              @OA\Property(property="payment_method", type="string", enum={"cash","upi","bank_transfer","cheque","card","neft_rtgs","online_gateway","other"}, example="cash"),
     *              @OA\Property(property="payment_reference", type="string", example="UPI12345678"),
     *              @OA\Property(property="bank_name", type="string", example="HDFC Bank"),
     *              @OA\Property(property="cheque_date", type="string", format="date", example="2026-05-15"),
     *              @OA\Property(property="payment_date", type="string", format="date", example="2026-05-10"),
     *              @OA\Property(property="note", type="string", example="First term payment"),
     *              @OA\Property(
     *                  property="allocations",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="student_fee_item_id", type="integer", example=1),
     *                      @OA\Property(property="amount", type="number", example=500.00),
     *                      @OA\Property(property="discount_amount", type="number", example=0.00),
     *                      @OA\Property(property="fine_amount", type="number", example=0.00)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Payment collected and receipt generated"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function collect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'student_fee_id' => 'nullable|integer|exists:student_fees,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'fine_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|in:cash,upi,bank_transfer,cheque,card,neft_rtgs,online_gateway,other',
            'payment_reference' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'cheque_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string',
            'allocations' => 'nullable|array',
            'allocations.*.student_fee_item_id' => 'required_with:allocations|integer|exists:student_fee_items,id',
            'allocations.*.amount' => 'required_with:allocations|numeric|min:0',
            'allocations.*.discount_amount' => 'nullable|numeric|min:0',
            'allocations.*.fine_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::findOrFail($request->student_id);

        // Find or validate student_fee record
        $studentFeeId = $request->student_fee_id;
        if (!$studentFeeId) {
            $studentFee = StudentFee::where('student_id', $student->id)
                ->where('balance_amount', '>', 0)
                ->latest()
                ->first();

            if (!$studentFee) {
                // Check if any student_fee exists for student
                $studentFee = StudentFee::where('student_id', $student->id)->latest()->first();
            }

            $studentFeeId = $studentFee ? $studentFee->id : null;
        } else {
            $studentFee = StudentFee::find($studentFeeId);
        }

        return DB::transaction(function () use ($request, $student, $studentFee, $studentFeeId) {
            $amountPaid = (float)$request->amount;
            $discount = (float)$request->input('discount_amount', 0);
            $fine = (float)$request->input('fine_amount', 0);
            $totalEffect = $amountPaid + $discount - $fine;

            $paymentNo = Payment::generatePaymentNo();
            $receiptNo = Payment::generateReceiptNo();

            $payment = Payment::create([
                'payment_no' => $paymentNo,
                'receipt_no' => $receiptNo,
                'student_id' => $student->id,
                'student_fee_id' => $studentFeeId,
                'academic_year_id' => $studentFee ? $studentFee->academic_year_id : $student->academic_year_id,
                'amount' => $amountPaid,
                'payment_date' => $request->payment_date ?? date('Y-m-d'),
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'bank_name' => $request->bank_name,
                'cheque_date' => $request->cheque_date,
                'discount_amount' => $discount,
                'fine_amount' => $fine,
                'total_paid' => $amountPaid,
                'note' => $request->note,
                'collected_by' => Auth::id(),
                'status' => 'success',
            ]);

            // Item-level allocations
            $allocations = $request->input('allocations', []);

            if (!empty($allocations)) {
                foreach ($allocations as $alloc) {
                    $item = StudentFeeItem::findOrFail($alloc['student_fee_item_id']);
                    $itemAmount = (float)$alloc['amount'];
                    $itemDiscount = (float)($alloc['discount_amount'] ?? 0);
                    $itemFine = (float)($alloc['fine_amount'] ?? 0);

                    $payment->items()->create([
                        'student_fee_item_id' => $item->id,
                        'amount' => $itemAmount,
                        'discount_amount' => $itemDiscount,
                        'fine_amount' => $itemFine,
                    ]);

                    $item->paid_amount = (float)$item->paid_amount + $itemAmount;
                    $item->discount_amount = (float)$item->discount_amount + $itemDiscount;
                    $item->fine_amount = (float)$item->fine_amount + $itemFine;
                    $item->recalculateBalances();
                }
            } elseif ($studentFee) {
                // Automatic FIFO allocation against pending items of this student fee
                $remainingToAllocate = $amountPaid;
                $remainingDiscount = $discount;
                $remainingFine = $fine;

                $pendingItems = StudentFeeItem::where('student_fee_id', $studentFee->id)
                    ->where('balance_amount', '>', 0)
                    ->orderBy('due_date')
                    ->orderBy('id')
                    ->get();

                if ($pendingItems->isNotEmpty()) {
                    foreach ($pendingItems as $item) {
                        if ($remainingToAllocate <= 0 && $remainingDiscount <= 0 && $remainingFine <= 0) {
                            break;
                        }

                        $itemBalance = (float)$item->balance_amount;
                        $allocAmount = min($itemBalance, $remainingToAllocate);
                        $allocDiscount = min($itemBalance - $allocAmount, $remainingDiscount);
                        $allocFine = $remainingFine > 0 ? min($remainingFine, (float)$item->fine_amount ?: $remainingFine) : 0;

                        $payment->items()->create([
                            'student_fee_item_id' => $item->id,
                            'amount' => $allocAmount,
                            'discount_amount' => $allocDiscount,
                            'fine_amount' => $allocFine,
                        ]);

                        $item->paid_amount = (float)$item->paid_amount + $allocAmount;
                        $item->discount_amount = (float)$item->discount_amount + $allocDiscount;
                        $item->fine_amount = (float)$item->fine_amount + $allocFine;
                        $item->recalculateBalances();

                        $remainingToAllocate -= $allocAmount;
                        $remainingDiscount -= $allocDiscount;
                        $remainingFine -= $allocFine;
                    }
                }

                // If still leftover after items (or no items), record a single allocation
                if ($remainingToAllocate > 0 || $pendingItems->isEmpty()) {
                    $payment->items()->create([
                        'student_fee_item_id' => null,
                        'amount' => $remainingToAllocate > 0 ? $remainingToAllocate : $amountPaid,
                        'discount_amount' => $remainingDiscount,
                        'fine_amount' => $remainingFine,
                        'note' => 'General fee payment',
                    ]);
                }
            }

            if ($studentFee) {
                $studentFee->recalculateSummary();
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment collected and receipt generated successfully',
                'data' => [
                    'payment' => $payment->load(['items.studentFeeItem', 'student', 'collector']),
                    'receipt_no' => $receiptNo,
                    'payment_no' => $paymentNo,
                ]
            ], 201);
        });
    }

    /**
     * Store alias for resource routes
     */
    public function store(Request $request)
    {
        return $this->collect($request);
    }

    /**
     * @OA\Get(
     *      path="/api/payments/{id}",
     *      operationId="getPaymentById",
     *      tags={"5. Finance & Fee Collection - Payment"},
     *      summary="Get Payment details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $payment = Payment::with([
            'student.classes',
            'student.section',
            'studentFee.feeStructure',
            'collector:id,name',
            'reverser:id,name',
            'items.studentFeeItem.feeType'
        ])->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/payments/{id}/receipt",
     *      operationId="getPaymentReceiptData",
     *      tags={"5. Finance & Fee Collection - Payment"},
     *      summary="Get printable/downloadable receipt information",
     *      description="Returns structured receipt layout with student, itemized fee breakdown, and balances",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function receipt($id)
    {
        $payment = Payment::with([
            'student.classes',
            'student.section',
            'student.parents',
            'studentFee',
            'collector:id,name',
            'items.studentFeeItem.feeType'
        ])->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment transaction not found'
            ], 404);
        }

        $student = $payment->student;
        $totalBalanceRemaining = StudentFee::where('student_id', $payment->student_id)->sum('balance_amount');

        $receiptData = [
            'receipt_no' => $payment->receipt_no,
            'payment_no' => $payment->payment_no,
            'payment_date' => $payment->payment_date ? $payment->payment_date->format('d-m-Y') : null,
            'payment_method' => strtoupper($payment->payment_method),
            'payment_reference' => $payment->payment_reference,
            'bank_name' => $payment->bank_name,
            'status' => $payment->status,
            'collected_by' => optional($payment->collector)->name,
            'student' => [
                'id' => $student->id,
                'admission_no' => $student->admission_no,
                'roll_no' => $student->roll_no,
                'name' => $student->full_name,
                'class' => optional($student->classes)->name,
                'section' => optional($student->section)->name,
                'father_name' => optional($student->parents)->father_name,
                'phone' => $student->phone,
            ],
            'items' => $payment->items->map(function ($item) {
                return [
                    'fee_type' => optional(optional($item->studentFeeItem)->feeType)->name ?? 'General Fee',
                    'amount' => (float)$item->amount,
                    'discount' => (float)$item->discount_amount,
                    'fine' => (float)$item->fine_amount,
                ];
            }),
            'financials' => [
                'subtotal' => (float)$payment->amount,
                'discount' => (float)$payment->discount_amount,
                'fine' => (float)$payment->fine_amount,
                'total_paid' => (float)$payment->amount,
                'outstanding_balance' => (float)$totalBalanceRemaining,
            ],
            'note' => $payment->note,
        ];

        return response()->json([
            'success' => true,
            'data' => $receiptData
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/payments/{id}/reverse",
     *      operationId="reversePaymentTransaction",
     *      tags={"5. Finance & Fee Collection - Payment"},
     *      summary="Reverse/cancel a payment transaction with authorization",
     *      description="Safely rolls back student balances while maintaining transaction history",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"reversal_reason"},
     *              @OA\Property(property="reversal_reason", type="string", example="Entered incorrect amount by cashier")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Payment reversed successfully"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function reverse(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reversal_reason' => 'required|string|min:5|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = Payment::with(['items.studentFeeItem', 'studentFee'])->find($id);
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment transaction not found'
            ], 404);
        }

        if ($payment->status === 'reversed') {
            return response()->json([
                'success' => false,
                'message' => 'This payment transaction has already been reversed'
            ], 422);
        }

        DB::transaction(function () use ($payment, $request) {
            // Reverse item allocations
            foreach ($payment->items as $pItem) {
                if ($pItem->studentFeeItem) {
                    $sItem = $pItem->studentFeeItem;
                    $sItem->paid_amount = max(0, (float)$sItem->paid_amount - (float)$pItem->amount);
                    $sItem->discount_amount = max(0, (float)$sItem->discount_amount - (float)$pItem->discount_amount);
                    $sItem->fine_amount = max(0, (float)$sItem->fine_amount - (float)$pItem->fine_amount);
                    $sItem->recalculateBalances();
                }
            }

            if ($payment->studentFee) {
                $payment->studentFee->recalculateSummary();
            }

            $payment->update([
                'status' => 'reversed',
                'reversal_reason' => $request->reversal_reason,
                'reversed_at' => now(),
                'reversed_by' => Auth::id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment transaction reversed successfully',
            'data' => $payment->fresh()
        ]);
    }

    /**
     * Delete payment - In compliance with financial security rules, prevent permanent hard deletion
     */
    public function destroy(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Permanent deletion of payments is prohibited for accounting audit integrity. Please use POST /api/payments/{id}/reverse instead.'
        ], 403);
    }
}
