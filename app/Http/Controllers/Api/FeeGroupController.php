<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeeGroup;
use Illuminate\Support\Facades\Validator;

class FeeGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/fee-groups",
     *      operationId="getFeeGroupsList",
     *      tags={"5. Finance & Fee Collection - FeeGroup"},
     *      summary="Get list of FeeGroups",
     *      description="Returns list of FeeGroups",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="school_id", type="integer"),
     *                  @OA\Property(property="academic_id", type="integer"),
     *                  @OA\Property(property="created_by", type="integer"),
     *                  @OA\Property(property="updated_by", type="integer"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *                  )
     *              )
     *          )
     *       ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     */
    public function index()
    {
        $data = FeeGroup::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
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
     *              @OA\Property(property="description", type="string", example="Monthly tuition fees"),
     *              @OA\Property(property="school_id", type="integer", example=1),
     *              @OA\Property(property="academic_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(property="data", type="object")
     *          )
     *       ),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'school_id' => 'nullable|integer',
            'academic_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();
        $input['created_by'] = $request->user()->id ?? null;

        $item = FeeGroup::create($input);
        
        return response()->json([
            'success' => true,
            'message' => 'Fee Group created successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = FeeGroup::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *      path="/api/fee-groups/{id}",
     *      operationId="updateFeeGroup",
     *      tags={"5. Finance & Fee Collection - FeeGroup"},
     *      summary="Update existing fee group",
     *      description="Returns updated fee group data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="school_id", type="integer"),
     *              @OA\Property(property="academic_id", type="integer")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Updated"),
     *              @OA\Property(property="data", type="object")
     *          )
     *       ),
     *      @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = FeeGroup::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'school_id' => 'sometimes|nullable|integer',
            'academic_id' => 'sometimes|nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();
        $input['updated_by'] = $request->user()->id ?? null;

        $item->update($input);
        
        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = FeeGroup::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
