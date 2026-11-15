<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamResult\SaveExamResultRequest;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class ExamResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:super-admin|admin|teacher|staff'])->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     *      path="/api/exam-results",
     *      operationId="getExamResultList",
     *      tags={"6. Examinations & Assessment - ExamResult"},
     *      summary="Get list of ExamResult",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        return $this->respondPaginatedOrAll(ExamResult::latest(), $request);
    }

    /**
     * @OA\Post(
     *      path="/api/exam-results",
     *      operationId="storeExamResult",
     *      tags={"6. Examinations & Assessment - ExamResult"},
     *      summary="Store new ExamResult",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id","exam_id","subject_id","marks"},
     *              @OA\Property(property="student_id", type="integer", example="1"),
     *              @OA\Property(property="exam_id", type="integer", example="1"),
     *              @OA\Property(property="subject_id", type="integer", example="1"),
     *              @OA\Property(property="marks", type="string", example="marks")
     *          )
     *      ),
     *      @OA\Response(response=201,description="Successful operation")
     * )
     */
    public function store(SaveExamResultRequest $request)
    {
        $item = ExamResult::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/exam-results/{id}",
     *      operationId="getExamResultById",
     *      tags={"6. Examinations & Assessment - ExamResult"},
     *      summary="Get ExamResult information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = ExamResult::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/exam-results/{id}",
     *      operationId="updateExamResult",
     *      tags={"6. Examinations & Assessment - ExamResult"},
     *      summary="Update existing ExamResult",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id","exam_id","subject_id","marks"},
     *              @OA\Property(property="student_id", type="integer", example="1"),
     *              @OA\Property(property="exam_id", type="integer", example="1"),
     *              @OA\Property(property="subject_id", type="integer", example="1"),
     *              @OA\Property(property="marks", type="string", example="marks")
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function update(SaveExamResultRequest $request, $id)
    {
        $item = ExamResult::findOrFail($id);
        $item->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/exam-results/{id}",
     *      operationId="deleteExamResult",
     *      tags={"6. Examinations & Assessment - ExamResult"},
     *      summary="Delete existing ExamResult",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $item = ExamResult::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}