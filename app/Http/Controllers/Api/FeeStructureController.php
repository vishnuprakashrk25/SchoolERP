<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\FeeInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FeeStructureController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/fee-structures",
     *      operationId="getFeeStructuresList",
     *      tags={"5. Finance & Fee Collection - FeeStructure"},
     *      summary="Get list of Fee Structures",
     *      description="Returns list of Fee Structures with items and installments",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="category_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = FeeStructure::with([
            'classes',
            'section',
            'academicYear',
            'category',
            'academicCategory',
            'feeType',
            'items.feeType.feeGroup',
            'installments'
        ]);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $data = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/fee-structures",
     *      operationId="storeFeeStructure",
     *      tags={"5. Finance & Fee Collection - FeeStructure"},
     *      summary="Store new Fee Structure",
     *      description="Creates a fee structure with component fee items and optional installment schedule",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","class_id"},
     *              @OA\Property(property="name", type="string", example="Class 5 General Fee 2026-27"),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", example=null),
     *              @OA\Property(property="category_id", type="integer", example=null),
     *              @OA\Property(property="description", type="string", example="Annual fee plan"),
     *              @OA\Property(
     *                  property="items",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="fee_type_id", type="integer", example=1),
     *                      @OA\Property(property="amount", type="number", example=1000.00),
     *                      @OA\Property(property="due_date", type="string", format="date", example="2026-05-10")
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="installments",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="name", type="string", example="Term 1"),
     *                      @OA\Property(property="due_date", type="string", format="date", example="2026-06-10"),
     *                      @OA\Property(property="amount", type="number", example=500.00),
     *                      @OA\Property(property="late_fee_type", type="string", example="fixed"),
     *                      @OA\Property(property="late_fee_amount", type="number", example=50.00),
     *                      @OA\Property(property="grace_period_days", type="integer", example=5)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'academic_year_id' => 'nullable|integer',
            'class_id' => 'required|integer',
            'section_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'academic_category_id' => 'nullable|integer',
            'amount' => 'nullable|numeric|min:0',
            'fee_type_id' => 'nullable|integer',
            'items' => 'nullable|array',
            'items.*.fee_type_id' => 'required_with:items|integer|exists:fee_types,id',
            'items.*.amount' => 'required_with:items|numeric|min:0',
            'items.*.due_date' => 'nullable|date',
            'installments' => 'nullable|array',
            'installments.*.name' => 'required_with:installments|string|max:255',
            'installments.*.due_date' => 'required_with:installments|date',
            'installments.*.amount' => 'required_with:installments|numeric|min:0',
            'installments.*.late_fee_type' => 'nullable|string|in:fixed,percentage,daily',
            'installments.*.late_fee_amount' => 'nullable|numeric|min:0',
            'installments.*.grace_period_days' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $payload = $request->except(['items', 'installments']);
            $items = $request->input('items', []);
            $installments = $request->input('installments', []);

            // Auto calculate total amount
            $totalAmount = 0;
            if (!empty($items)) {
                $totalAmount = collect($items)->sum('amount');
            } elseif ($request->filled('amount')) {
                $totalAmount = (float)$request->amount;
            }

            $payload['total_amount'] = $totalAmount;
            $payload['amount'] = $totalAmount;
            $payload['created_by'] = Auth::id();

            if (empty($payload['name'])) {
                $payload['name'] = 'Class ' . $payload['class_id'] . ' Fee Structure';
            }

            $structure = FeeStructure::create($payload);

            // Create items
            if (!empty($items)) {
                foreach ($items as $itemData) {
                    $structure->items()->create([
                        'fee_type_id' => $itemData['fee_type_id'],
                        'amount' => $itemData['amount'],
                        'due_date' => $itemData['due_date'] ?? null,
                    ]);
                }
            } elseif ($request->filled('fee_type_id') && $request->filled('amount')) {
                // Support legacy single fee_type item
                $structure->items()->create([
                    'fee_type_id' => $request->fee_type_id,
                    'amount' => $request->amount,
                ]);
            }

            // Create installments
            if (!empty($installments)) {
                foreach ($installments as $instData) {
                    $structure->installments()->create([
                        'name' => $instData['name'],
                        'due_date' => $instData['due_date'],
                        'amount' => $instData['amount'],
                        'late_fee_type' => $instData['late_fee_type'] ?? 'fixed',
                        'late_fee_amount' => $instData['late_fee_amount'] ?? 0,
                        'grace_period_days' => $instData['grace_period_days'] ?? 0,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Fee structure created successfully',
                'data' => $structure->load(['items.feeType', 'installments', 'classes', 'section'])
            ], 201);
        });
    }

    /**
     * @OA\Get(
     *      path="/api/fee-structures/{id}",
     *      operationId="getFeeStructureById",
     *      tags={"5. Finance & Fee Collection - FeeStructure"},
     *      summary="Get Fee Structure details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $structure = FeeStructure::with([
            'classes',
            'section',
            'academicYear',
            'category',
            'academicCategory',
            'feeType',
            'items.feeType.feeGroup',
            'installments'
        ])->find($id);

        if (!$structure) {
            return response()->json([
                'success' => false,
                'message' => 'Fee structure not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $structure
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/fee-structures/{id}",
     *      operationId="updateFeeStructure",
     *      tags={"5. Finance & Fee Collection - FeeStructure"},
     *      summary="Update Fee Structure",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(Request $request, $id)
    {
        $structure = FeeStructure::find($id);
        if (!$structure) {
            return response()->json([
                'success' => false,
                'message' => 'Fee structure not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'academic_year_id' => 'nullable|integer',
            'class_id' => 'sometimes|required|integer',
            'section_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'academic_category_id' => 'nullable|integer',
            'items' => 'nullable|array',
            'items.*.fee_type_id' => 'required_with:items|integer|exists:fee_types,id',
            'items.*.amount' => 'required_with:items|numeric|min:0',
            'items.*.due_date' => 'nullable|date',
            'installments' => 'nullable|array',
            'installments.*.name' => 'required_with:installments|string|max:255',
            'installments.*.due_date' => 'required_with:installments|date',
            'installments.*.amount' => 'required_with:installments|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request, $structure) {
            $payload = $request->except(['items', 'installments']);
            $payload['updated_by'] = Auth::id();

            if ($request->has('items')) {
                $items = $request->input('items', []);
                $totalAmount = collect($items)->sum('amount');
                $payload['total_amount'] = $totalAmount;
                $payload['amount'] = $totalAmount;

                // Sync items
                $structure->items()->delete();
                foreach ($items as $itemData) {
                    $structure->items()->create([
                        'fee_type_id' => $itemData['fee_type_id'],
                        'amount' => $itemData['amount'],
                        'due_date' => $itemData['due_date'] ?? null,
                    ]);
                }
            }

            if ($request->has('installments')) {
                $installments = $request->input('installments', []);
                $structure->installments()->delete();
                foreach ($installments as $instData) {
                    $structure->installments()->create([
                        'name' => $instData['name'],
                        'due_date' => $instData['due_date'],
                        'amount' => $instData['amount'],
                        'late_fee_type' => $instData['late_fee_type'] ?? 'fixed',
                        'late_fee_amount' => $instData['late_fee_amount'] ?? 0,
                        'grace_period_days' => $instData['grace_period_days'] ?? 0,
                    ]);
                }
            }

            $structure->update($payload);

            return response()->json([
                'success' => true,
                'message' => 'Fee structure updated successfully',
                'data' => $structure->fresh(['items.feeType', 'installments', 'classes', 'section'])
            ]);
        });
    }

    /**
     * @OA\Delete(
     *      path="/api/fee-structures/{id}",
     *      operationId="deleteFeeStructure",
     *      tags={"5. Finance & Fee Collection - FeeStructure"},
     *      summary="Delete Fee Structure",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy($id)
    {
        $structure = FeeStructure::find($id);
        if (!$structure) {
            return response()->json([
                'success' => false,
                'message' => 'Fee structure not found'
            ], 404);
        }

        $structure->items()->delete();
        $structure->installments()->delete();
        $structure->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee structure deleted successfully'
        ]);
    }
}
