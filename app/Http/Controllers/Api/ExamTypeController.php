<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamTypeController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exam-types",
     *      operationId="getExamTypesList",
     *      tags={"6. Examinations & Assessment - Exam Type"},
     *      summary="Get list of Exam Types",
     *      description="Returns list of Exam Types with optional search and status filter.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by exam name", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status", in="query", required=false, description="Filter by status", @OA\Schema(type="boolean")),
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
    public function index(Request $request)
    {
        $query = ExamType::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->boolean('status'));
        }

        if ($request->filled('is_group')) {
            $query->where('is_group', $request->boolean('is_group'));
        }

        $items = $query->latest()->get();

        return response()->json([
            'success' => true,
            'count'   => $items->count(),
            'data'    => $items
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/exam-types",
     *      operationId="storeExamType",
     *      tags={"6. Examinations & Assessment - Exam Type"},
     *      summary="Store new Exam Type",
     *      description="Creates a new Exam Type with Group/Term Exam Status toggle.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="First Term"),
     *              @OA\Property(property="is_group", type="boolean", example=false, description="Group / Term Exam Status toggle"),
     *              @OA\Property(property="status", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Exam Type created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'is_group' => 'sometimes|boolean',
            'status'   => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'is_group', 'status']);
        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $item = ExamType::create($data);

        // Also keep legacy exams table in sync
        Exam::firstOrCreate(
            ['name' => $item->name],
            ['is_group' => $item->is_group, 'status' => $item->status]
        );

        return response()->json([
            'success' => true,
            'message' => 'Exam Type created successfully',
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/exam-types/{id}",
     *      operationId="getExamTypeById",
     *      tags={"6. Examinations & Assessment - Exam Type"},
     *      summary="Get Exam Type details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $item = ExamType::findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/exam-types/{id}",
     *      operationId="updateExamType",
     *      tags={"6. Examinations & Assessment - Exam Type"},
     *      summary="Update existing Exam Type",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="First Term"),
     *              @OA\Property(property="is_group", type="boolean", example=true),
     *              @OA\Property(property="status", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = ExamType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'is_group' => 'sometimes|boolean',
            'status'   => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $item->update($request->only(['name', 'is_group', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Exam Type updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/exam-types/{id}",
     *      operationId="deleteExamType",
     *      tags={"6. Examinations & Assessment - Exam Type"},
     *      summary="Delete Exam Type",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = ExamType::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam Type deleted successfully'
        ]);
    }
}
