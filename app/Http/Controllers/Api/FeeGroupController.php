<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeeGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FeeGroupController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/fee-groups",
     *      operationId="getFeeGroupsList",
     *      tags={"5. Finance & Fee Collection - FeeGroup"},
     *      summary="Get list of Fee Groups",
     *      description="Returns list of Fee Groups with optional filters",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="branch_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="is_active", in="query", required=false, @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $query = FeeGroup::with(['feeTypes', 'academicYear', 'branch']);

        if ($request->filled('academic_year_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id)
                  ->orWhere('academic_id', $request->academic_year_id);
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $data = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/fee-groups",
     *      operationId="storeFeeGroup",
     *      tags={"5. Finance & Fee Collection - FeeGroup"},
     *      summary="Store new fee group",
     *      description="Returns saved fee group data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Tuition Fees"),
     *              @OA\Property(property="description", type="string", example="Academic tuition fees"),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="branch_id", type="integer", example=1),
     *              @OA\Property(property="is_active", type="boolean", example=true)
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
            'description' => 'nullable|string',
            'academic_year_id' => 'nullable|integer',
            'academic_id' => 'nullable|integer',
            'branch_id' => 'nullable|integer',
            'school_id' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payload = $request->all();
        if (empty($payload['academic_year_id']) && !empty($payload['academic_id'])) {
            $payload['academic_year_id'] = $payload['academic_id'];
        } elseif (!empty($payload['academic_year_id']) && empty($payload['academic_id'])) {
            $payload['academic_id'] = $payload['academic_year_id'];
        }
        $payload['created_by'] = Auth::id();

        $feeGroup = FeeGroup::create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Fee group created successfully',
            'data' => $feeGroup->load('feeTypes')
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/fee-groups/{id}",
     *      operationId="getFeeGroupById",
     *      tags={"5. Finance & Fee Collection - FeeGroup"},
     *      summary="Get FeeGroup information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        $feeGroup = FeeGroup::with(['feeTypes', 'academicYear', 'branch'])->find($id);
        if (!$feeGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Fee group not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feeGroup
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/fee-groups/{id}",
     *      operationId="updateFeeGroup",
     *      tags={"5. Finance & Fee Collection - FeeGroup"},
     *      summary="Update existing FeeGroup",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Tuition Fees"),
     *              @OA\Property(property="description", type="string", example="Updated description"),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="is_active", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(Request $request, $id)
    {
        $feeGroup = FeeGroup::find($id);
        if (!$feeGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Fee group not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'academic_year_id' => 'nullable|integer',
            'branch_id' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payload = $request->all();
        $payload['updated_by'] = Auth::id();
        $feeGroup->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Fee group updated successfully',
            'data' => $feeGroup->fresh(['feeTypes'])
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/fee-groups/{id}",
     *      operationId="deleteFeeGroup",
     *      tags={"5. Finance & Fee Collection - FeeGroup"},
     *      summary="Delete FeeGroup",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy($id)
    {
        $feeGroup = FeeGroup::find($id);
        if (!$feeGroup) {
            return response()->json([
                'success' => false,
                'message' => 'Fee group not found'
            ], 404);
        }

        $feeGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee group deleted successfully'
        ]);
    }
}
