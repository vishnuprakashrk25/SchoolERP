<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeTypeController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/fee-types",
     *      operationId="getFeeTypeList",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Get list of FeeType",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index()
    {
        $data = FeeType::latest()->get();
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
     *      summary="Store new FeeType",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","amount"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="amount", type="string", example="amount")
     *          )
     *      ),
     *      @OA\Response(response=201,description="Successful operation")
     * )
     */
    public function store(Request $request)
    {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item = FeeType::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/fee-types/{id}",
     *      operationId="getFeeTypeById",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Get FeeType information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = FeeType::findOrFail($id);
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
     *      summary="Update existing FeeType",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","amount"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="amount", type="string", example="amount")
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = FeeType::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/fee-types/{id}",
     *      operationId="deleteFeeType",
     *      tags={"5. Finance & Fee Collection - FeeType"},
     *      summary="Delete existing FeeType",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $item = FeeType::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}