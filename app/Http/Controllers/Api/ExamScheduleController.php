<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;

class ExamScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/exam-schedules",
     *      operationId="getExamSchedulesList",
     *      tags={"6. Examinations & Assessment - ExamSchedule"},
     *      summary="Get list of ExamSchedules",
     *      description="Returns list of ExamSchedules",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="exam_id", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="exam_date", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *                  )
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
        public function index()
    {
        $data = ExamSchedule::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/api/exam-schedules",
     *      operationId="storeExamSchedule",
     *      tags={"6. Examinations & Assessment - ExamSchedule"},
     *      summary="Store new exam schedule",
     *      description="Returns saved exam schedule data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(type="object")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="exam_id", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="exam_date", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
        public function store(Request $request)
    {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'exam_id' => 'required|integer',
            'class_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'exam_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item = ExamSchedule::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *      path="/api/exam-schedules/{id}",
     *      operationId="getExamScheduleById",
     *      tags={"6. Examinations & Assessment - ExamSchedule"},
     *      summary="Get exam schedule information",
     *      description="Returns exam schedule data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="ExamSchedule id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="exam_id", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="exam_date", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
        public function show($id)
    {
        $item = ExamSchedule::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *      path="/api/exam-schedules/{id}",
     *      operationId="updateExamSchedule",
     *      tags={"6. Examinations & Assessment - ExamSchedule"},
     *      summary="Update existing exam schedule",
     *      description="Returns updated exam schedule data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="ExamSchedule id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(type="object")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="exam_id", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="exam_date", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
        public function update(Request $request, $id)
    {
        $item = ExamSchedule::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'exam_id' => 'required|integer',
            'class_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'exam_date' => 'required',
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
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *      path="/api/exam-schedules/{id}",
     *      operationId="deleteExamSchedule",
     *      tags={"6. Examinations & Assessment - ExamSchedule"},
     *      summary="Delete existing exam schedule",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="ExamSchedule id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
        public function destroy($id)
    {
        $item = ExamSchedule::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
