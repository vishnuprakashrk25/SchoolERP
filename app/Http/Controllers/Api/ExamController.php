<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exams",
     *      operationId="getExamList",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Get list of Exam",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index()
    {
        $data = Exam::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/exams",
     *      operationId="storeExam",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Store new Exam",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","academic_year_id"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="academic_year_id", type="integer", example="1")
     *          )
     *      ),
     *      @OA\Response(response=201,description="Successful operation")
     * )
     */
    public function store(Request $request)
    {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'academic_year_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item = Exam::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/exams/{id}",
     *      operationId="getExamById",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Get Exam information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = Exam::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/exams/{id}",
     *      operationId="updateExam",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Update existing Exam",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","academic_year_id"},
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="academic_year_id", type="integer", example="1")
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = Exam::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'academic_year_id' => 'required|integer',
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
     *      path="/api/exams/{id}",
     *      operationId="deleteExam",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Delete existing Exam",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $item = Exam::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}