<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamSetupController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exam-setups",
     *      operationId="getExamSetupsList",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Get list of Exam Setups",
     *      description="Returns list of Exam Setups with filters for class_id, section_id, exam_type_id, and subject_id.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="exam_type_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="subject_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
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
        $query = ExamSetup::with([
            'examType:id,name,is_group',
            'class:id,name',
            'section:id,name',
            'subject:id,name,subject_code',
            'teacher:id,first_name,last_name,full_name,staff_code'
        ]);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }
        if ($request->filled('exam_type_id')) {
            $query->where('exam_type_id', $request->input('exam_type_id'));
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('exam_title', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('class', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
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
     *      path="/api/exam-setups",
     *      operationId="storeExamSetup",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Store new Exam Setup",
     *      description="Creates Exam Setup with mark distributions and pass mark criteria.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"exam_title","class_id","subject_id","total_mark"},
     *              @OA\Property(property="exam_title", type="string", example="First Term Physics"),
     *              @OA\Property(property="exam_type_id", type="integer", example=1),
     *              @OA\Property(property="class_id", type="integer", example=5),
     *              @OA\Property(property="section_id", type="integer", example=2),
     *              @OA\Property(property="subject_id", type="integer", example=3),
     *              @OA\Property(property="teacher_id", type="integer", example=10),
     *              @OA\Property(property="pass_mark", type="number", example=33),
     *              @OA\Property(property="total_mark", type="number", example=100),
     *              @OA\Property(
     *                  property="mark_distributions",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Written"),
     *                      @OA\Property(property="mark", type="number", example=50)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Exam Setup created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_title'           => 'required|string|max:255',
            'exam_type_id'         => 'nullable|integer',
            'class_id'             => 'required|integer',
            'section_id'           => 'nullable|integer',
            'subject_id'           => 'required|integer',
            'teacher_id'           => 'nullable|integer',
            'pass_mark'            => 'nullable|numeric|min:0',
            'total_mark'           => 'required|numeric|min:1',
            'mark_distributions'   => 'nullable|array',
            'mark_distributions.*.title' => 'required|string',
            'mark_distributions.*.mark'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'exam_title',
            'exam_type_id',
            'class_id',
            'section_id',
            'subject_id',
            'teacher_id',
            'pass_mark',
            'total_mark',
            'mark_distributions',
        ]);

        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $item = ExamSetup::create($data);
        $item->load(['examType', 'class', 'section', 'subject', 'teacher']);

        return response()->json([
            'success' => true,
            'message' => 'Exam Setup created successfully',
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/exam-setups/{id}",
     *      operationId="getExamSetupById",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Get Exam Setup details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $item = ExamSetup::with(['examType', 'class', 'section', 'subject', 'teacher'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/exam-setups/{id}",
     *      operationId="updateExamSetup",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Update existing Exam Setup",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="exam_title", type="string"),
     *              @OA\Property(property="pass_mark", type="number"),
     *              @OA\Property(property="total_mark", type="number"),
     *              @OA\Property(property="mark_distributions", type="array", @OA\Items(type="object"))
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = ExamSetup::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'exam_title'           => 'sometimes|required|string|max:255',
            'exam_type_id'         => 'sometimes|nullable|integer',
            'class_id'             => 'sometimes|required|integer',
            'section_id'           => 'sometimes|nullable|integer',
            'subject_id'           => 'sometimes|required|integer',
            'teacher_id'           => 'sometimes|nullable|integer',
            'pass_mark'            => 'sometimes|nullable|numeric|min:0',
            'total_mark'           => 'sometimes|required|numeric|min:1',
            'mark_distributions'   => 'sometimes|nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $item->update($request->only([
            'exam_title',
            'exam_type_id',
            'class_id',
            'section_id',
            'subject_id',
            'teacher_id',
            'pass_mark',
            'total_mark',
            'mark_distributions',
        ]));

        $item->load(['examType', 'class', 'section', 'subject', 'teacher']);

        return response()->json([
            'success' => true,
            'message' => 'Exam Setup updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/exam-setups/{id}",
     *      operationId="deleteExamSetup",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Delete Exam Setup",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = ExamSetup::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam Setup deleted successfully'
        ]);
    }
}
