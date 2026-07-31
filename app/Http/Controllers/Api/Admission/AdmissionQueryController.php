<?php

namespace App\Http\Controllers\Api\Admission;

use App\Http\Controllers\Controller;
use App\Models\Admission\AdmissionQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdmissionQueryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/admission-queries",
     *      operationId="getAdmissionQueriesList",
     *      tags={"1. Reception & Admission - Inquiry"},
     *      summary="Get list of admission inquiries",
     *      description="Returns a list of all admission inquiries",
     *      security={{"sanctum":{}}},
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
    public function index()
    {
        $queries = AdmissionQuery::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $queries
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/admission-queries",
     *      operationId="storeAdmissionQuery",
     *      tags={"1. Reception & Admission - Inquiry"},
     *      summary="Store new admission inquiry",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "phone"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="follow_up_date", type="string", format="date"),
     *              @OA\Property(property="source", type="string"),
     *              @OA\Property(property="status", type="string", example="pending")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Query created"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'follow_up_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $query = AdmissionQuery::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Admission inquiry created successfully',
            'data' => $query
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/admission-queries/{id}",
     *      operationId="getAdmissionQueryById",
     *      tags={"1. Reception & Admission - Inquiry"},
     *      summary="Get admission inquiry details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id)
    {
        $query = AdmissionQuery::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $query
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/admission-queries/{id}",
     *      operationId="updateAdmissionQuery",
     *      tags={"1. Reception & Admission - Inquiry"},
     *      summary="Update admission inquiry",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *      @OA\Response(response=200, description="Updated successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, string $id)
    {
        $query = AdmissionQuery::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email',
            'follow_up_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $query->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Admission inquiry updated successfully',
            'data' => $query
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/admission-queries/{id}",
     *      operationId="deleteAdmissionQuery",
     *      tags={"1. Reception & Admission - Inquiry"},
     *      summary="Delete admission inquiry",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy(string $id)
    {
        $query = AdmissionQuery::findOrFail($id);
        $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admission inquiry deleted successfully'
        ]);
    }
}
