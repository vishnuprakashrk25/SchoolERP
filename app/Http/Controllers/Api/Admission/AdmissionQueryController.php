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
        $queries = AdmissionQuery::with('class')->latest()->get();

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
     *              @OA\Property(property="name", type="string", example="Mohammed Rafi"),
     *              @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *              @OA\Property(property="email", type="string", format="email", example="rafi@example.com"),
     *              @OA\Property(property="address", type="string", example="123 Maloora Road, Malappuram"),
     *              @OA\Property(property="description", type="string", example="Interested in admission for Class 5"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-11-16", description="Date From"),
     *              @OA\Property(property="next_follow_up_date", type="string", format="date", example="2026-11-23", description="Next Follow Up Date"),
     *              @OA\Property(property="assigned", type="string", example="Admin Staff"),
     *              @OA\Property(property="reference", type="string", example="School Website"),
     *              @OA\Property(property="source", type="string", example="Direct Walk-in"),
     *              @OA\Property(property="class_id", type="integer", example=1, description="Class ID"),
     *              @OA\Property(property="no_of_child", type="integer", example=1, description="Number of child"),
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
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'message' => 'nullable|string',
            'date' => 'nullable|date',
            'date_from' => 'nullable|date',
            'next_follow_up_date' => 'nullable|date',
            'follow_up_date' => 'nullable|date',
            'assigned' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'class_id' => 'nullable|integer',
            'no_of_child' => 'nullable|integer',
            'number_of_child' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        // Support field aliases from UI
        if (!empty($data['date_from']) && empty($data['date'])) {
            $data['date'] = $data['date_from'];
        }
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }
        if (!empty($data['description']) && empty($data['message'])) {
            $data['message'] = $data['description'];
        } elseif (!empty($data['message']) && empty($data['description'])) {
            $data['description'] = $data['message'];
        }
        if (!empty($data['next_follow_up_date']) && empty($data['follow_up_date'])) {
            $data['follow_up_date'] = $data['next_follow_up_date'];
        } elseif (!empty($data['follow_up_date']) && empty($data['next_follow_up_date'])) {
            $data['next_follow_up_date'] = $data['follow_up_date'];
        }
        if (isset($data['number_of_child']) && !isset($data['no_of_child'])) {
            $data['no_of_child'] = $data['number_of_child'];
        }
        if (isset($data['class']) && !isset($data['class_id']) && is_numeric($data['class'])) {
            $data['class_id'] = $data['class'];
        }

        $query = AdmissionQuery::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Admission inquiry created successfully',
            'data' => $query->load('class')
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
        $query = AdmissionQuery::with('class')->findOrFail($id);

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
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Mohammed Rafi"),
     *              @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *              @OA\Property(property="email", type="string", format="email", example="rafi@example.com"),
     *              @OA\Property(property="address", type="string", example="123 Maloora Road, Malappuram"),
     *              @OA\Property(property="description", type="string", example="Interested in admission for Class 5"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-11-16", description="Date From"),
     *              @OA\Property(property="next_follow_up_date", type="string", format="date", example="2026-11-23", description="Next Follow Up Date"),
     *              @OA\Property(property="assigned", type="string", example="Admin Staff"),
     *              @OA\Property(property="reference", type="string", example="School Website"),
     *              @OA\Property(property="source", type="string", example="Direct Walk-in"),
     *              @OA\Property(property="class_id", type="integer", example=1, description="Class ID"),
     *              @OA\Property(property="no_of_child", type="integer", example=1, description="Number of child"),
     *              @OA\Property(property="status", type="string", example="contacted")
     *          )
     *      ),
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
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'message' => 'nullable|string',
            'date' => 'nullable|date',
            'date_from' => 'nullable|date',
            'next_follow_up_date' => 'nullable|date',
            'follow_up_date' => 'nullable|date',
            'assigned' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'class_id' => 'nullable|integer',
            'no_of_child' => 'nullable|integer',
            'number_of_child' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (!empty($data['date_from']) && empty($data['date'])) {
            $data['date'] = $data['date_from'];
        }
        if (!empty($data['description']) && empty($data['message'])) {
            $data['message'] = $data['description'];
        } elseif (!empty($data['message']) && empty($data['description'])) {
            $data['description'] = $data['message'];
        }
        if (!empty($data['next_follow_up_date']) && empty($data['follow_up_date'])) {
            $data['follow_up_date'] = $data['next_follow_up_date'];
        } elseif (!empty($data['follow_up_date']) && empty($data['next_follow_up_date'])) {
            $data['next_follow_up_date'] = $data['follow_up_date'];
        }
        if (isset($data['number_of_child']) && !isset($data['no_of_child'])) {
            $data['no_of_child'] = $data['number_of_child'];
        }
        if (isset($data['class']) && !isset($data['class_id']) && is_numeric($data['class'])) {
            $data['class_id'] = $data['class'];
        }

        $query->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Admission inquiry updated successfully',
            'data' => $query->load('class')
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
