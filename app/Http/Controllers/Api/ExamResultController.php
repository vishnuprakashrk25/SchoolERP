<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamResultController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exam-results",
     *      operationId="getExamResultList",
     *      tags={"6. Examinations & Assessment - ExamResult"},
     *      summary="Get list of ExamResult",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index()
    {
        $data = ExamResult::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
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
    public function store(Request $request)
    {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'exam_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'marks' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
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
    public function update(Request $request, $id)
    {
        $item = ExamResult::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'exam_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'marks' => 'required',
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