<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentMultiClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentMultiClassController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/student-multi-classes",
     *      operationId="getStudentMultiClassList",
     *      tags={"4. Student Enrollment - StudentMultiClass"},
     *      summary="Get list of StudentMultiClass",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index()
    {
        $data = StudentMultiClass::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/student-multi-classes",
     *      operationId="storeStudentMultiClass",
     *      tags={"4. Student Enrollment - StudentMultiClass"},
     *      summary="Store new StudentMultiClass",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id","class_id","section_id"},
     *              @OA\Property(property="student_id", type="integer", example="1"),
     *              @OA\Property(property="class_id", type="integer", example="1"),
     *              @OA\Property(property="section_id", type="integer", example="1")
     *          )
     *      ),
     *      @OA\Response(response=201,description="Successful operation")
     * )
     */
    public function store(Request $request)
    {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'class_id' => 'required|integer',
            'section_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item = StudentMultiClass::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/student-multi-classes/{id}",
     *      operationId="getStudentMultiClassById",
     *      tags={"4. Student Enrollment - StudentMultiClass"},
     *      summary="Get StudentMultiClass information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = StudentMultiClass::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/student-multi-classes/{id}",
     *      operationId="updateStudentMultiClass",
     *      tags={"4. Student Enrollment - StudentMultiClass"},
     *      summary="Update existing StudentMultiClass",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id","class_id","section_id"},
     *              @OA\Property(property="student_id", type="integer", example="1"),
     *              @OA\Property(property="class_id", type="integer", example="1"),
     *              @OA\Property(property="section_id", type="integer", example="1")
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = StudentMultiClass::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'class_id' => 'required|integer',
            'section_id' => 'required|integer',
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
     *      path="/api/student-multi-classes/{id}",
     *      operationId="deleteStudentMultiClass",
     *      tags={"4. Student Enrollment - StudentMultiClass"},
     *      summary="Delete existing StudentMultiClass",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $item = StudentMultiClass::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}