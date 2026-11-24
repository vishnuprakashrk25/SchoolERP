<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarkGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarkGradeController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/mark-grades",
     *      operationId="getMarkGradesList",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Get list of Mark Grades (Grade List)",
     *      description="Returns list of grading scales with Grade Name, GPA, Percent (From To), and GPA (From To).",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by grade name or description", @OA\Schema(type="string")),
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
        $query = MarkGrade::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('grade_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $grades = $query->orderBy('percent_from', 'desc')->get();

        $formatted = $grades->map(function ($g, $index) {
            return [
                'id'              => $g->id,
                'sl'              => $index + 1,
                'grade'           => $g->grade_name,
                'grade_name'      => $g->grade_name,
                'gpa'             => (float)$g->gpa,
                'percent_from'    => (float)$g->percent_from,
                'percent_to'      => (float)$g->percent_to,
                'percent_range'   => "{$g->percent_from}-{$g->percent_to}%",
                'gpa_from'        => (float)$g->gpa_from,
                'gpa_to'          => (float)$g->gpa_to,
                'gpa_range'       => "{$g->gpa_from}-{$g->gpa_to}",
                'description'     => $g->description,
                'created_at'      => $g->created_at,
                'updated_at'      => $g->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $formatted->count(),
            'data'    => $formatted
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/mark-grades",
     *      operationId="storeMarkGrade",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Store new Mark Grade",
     *      description="Creates a new Grade system entry.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"grade_name","gpa","percent_from","percent_to"},
     *              @OA\Property(property="grade_name", type="string", example="A+"),
     *              @OA\Property(property="gpa", type="number", example=5.0),
     *              @OA\Property(property="percent_from", type="number", example=80),
     *              @OA\Property(property="percent_to", type="number", example=100),
     *              @OA\Property(property="gpa_from", type="number", example=5.0),
     *              @OA\Property(property="gpa_to", type="number", example=5.99),
     *              @OA\Property(property="description", type="string", example="Outstanding performance")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Grade created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grade_name'   => 'required|string|max:50',
            'gpa'          => 'required|numeric|min:0',
            'percent_from' => 'required|numeric|min:0',
            'percent_to'   => 'required|numeric|min:0',
            'gpa_from'     => 'nullable|numeric|min:0',
            'gpa_to'       => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'grade_name', 'gpa', 'percent_from', 'percent_to',
            'gpa_from', 'gpa_to', 'description'
        ]);

        if (!isset($data['gpa_from'])) {
            $data['gpa_from'] = $data['gpa'];
        }
        if (!isset($data['gpa_to'])) {
            $data['gpa_to'] = $data['gpa'] + 0.99;
        }

        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $grade = MarkGrade::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Grade created successfully',
            'data'    => $grade
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/mark-grades/{id}",
     *      operationId="getMarkGradeById",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Get Mark Grade details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $item = MarkGrade::findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/mark-grades/{id}",
     *      operationId="updateMarkGrade",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Update existing Mark Grade",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="grade_name", type="string"),
     *              @OA\Property(property="gpa", type="number"),
     *              @OA\Property(property="percent_from", type="number"),
     *              @OA\Property(property="percent_to", type="number"),
     *              @OA\Property(property="gpa_from", type="number"),
     *              @OA\Property(property="gpa_to", type="number"),
     *              @OA\Property(property="description", type="string")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = MarkGrade::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'grade_name'   => 'sometimes|required|string|max:50',
            'gpa'          => 'sometimes|required|numeric|min:0',
            'percent_from' => 'sometimes|required|numeric|min:0',
            'percent_to'   => 'sometimes|required|numeric|min:0',
            'gpa_from'     => 'sometimes|nullable|numeric|min:0',
            'gpa_to'       => 'sometimes|nullable|numeric|min:0',
            'description'  => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $item->update($request->only([
            'grade_name', 'gpa', 'percent_from', 'percent_to',
            'gpa_from', 'gpa_to', 'description'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Grade updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/mark-grades/{id}",
     *      operationId="deleteMarkGrade",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Delete Mark Grade",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = MarkGrade::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grade deleted successfully'
        ]);
    }
}
