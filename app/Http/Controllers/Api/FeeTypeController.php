<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FeeTypeController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/fee-types",
     *      operationId="getFeeTypeList",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Get list of Fee Types",
     *      description="Returns list of Fee Types with optional group and academic year filters",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="fee_group_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="billing_cycle", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = FeeType::with(['feeGroup', 'academicYear']);

        if ($request->filled('fee_group_id')) {
            $query->where('fee_group_id', $request->fee_group_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('billing_cycle')) {
            $query->where('billing_cycle', $request->billing_cycle);
        }

        $data = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/fee-types",
     *      operationId="storeFeeType",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Store new Fee Type",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="fee_group_id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Monthly Tuition"),
     *              @OA\Property(property="code", type="string", example="TUIT_01"),
     *              @OA\Property(property="amount", type="number", format="float", example=1500.00),
     *              @OA\Property(property="description", type="string", example="Tuition fee per month"),
     *              @OA\Property(property="billing_cycle", type="string", enum={"one_time","monthly","quarterly","half_yearly","annual"}, example="monthly"),
     *              @OA\Property(property="academic_year_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'fee_group_id' => 'nullable|integer|exists:fee_groups,id',
            'code' => 'nullable|string|max:100',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'billing_cycle' => 'nullable|string|in:one_time,monthly,quarterly,half_yearly,annual',
            'academic_year_id' => 'nullable|integer',
            'school_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payload = $request->all();
        $payload['amount'] = $request->input('amount', 0);
        $payload['created_by'] = Auth::id();

        $item = FeeType::create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Fee type created successfully',
            'data' => $item->load('feeGroup')
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/fee-types/{id}",
     *      operationId="getFeeTypeById",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Get Fee Type information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $item = FeeType::with(['feeGroup', 'academicYear'])->find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Fee type not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/fee-types/{id}",
     *      operationId="updateFeeType",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Update existing Fee Type",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="fee_group_id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Updated Tuition"),
     *              @OA\Property(property="code", type="string", example="TUIT_02"),
     *              @OA\Property(property="amount", type="number", format="float", example=1600.00),
     *              @OA\Property(property="billing_cycle", type="string", example="monthly")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = FeeType::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Fee type not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'fee_group_id' => 'nullable|integer|exists:fee_groups,id',
            'code' => 'nullable|string|max:100',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'billing_cycle' => 'nullable|string|in:one_time,monthly,quarterly,half_yearly,annual',
            'academic_year_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payload = $request->all();
        $payload['updated_by'] = Auth::id();
        $item->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Fee type updated successfully',
            'data' => $item->fresh(['feeGroup'])
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/fee-types/{id}",
     *      operationId="deleteFeeType",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Delete Fee Type",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy($id)
    {
        $item = FeeType::find($id);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Fee type not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee type deleted successfully'
        ]);
    }
}