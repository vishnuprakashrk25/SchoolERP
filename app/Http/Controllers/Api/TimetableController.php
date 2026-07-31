<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimetableController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/timetables",
     *      operationId="getTimetableList",
     *      tags={"3. Academic Infrastructure - Timetable"},
     *      summary="Get list of Timetable",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index()
    {
        $data = Timetable::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/timetables",
     *      operationId="storeTimetable",
     *      tags={"3. Academic Infrastructure - Timetable"},
     *      summary="Store new Timetable",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"class_id","section_id","subject_id","teacher_id","day","start_time","end_time"},
     *              @OA\Property(property="class_id", type="integer", example="1"),
     *              @OA\Property(property="section_id", type="integer", example="1"),
     *              @OA\Property(property="subject_id", type="integer", example="1"),
     *              @OA\Property(property="teacher_id", type="integer", example="1"),
     *              @OA\Property(property="day", type="string", example="day"),
     *              @OA\Property(property="start_time", type="string", example="start_time"),
     *              @OA\Property(property="end_time", type="string", example="end_time")
     *          )
     *      ),
     *      @OA\Response(response=201,description="Successful operation")
     * )
     */
    public function store(Request $request)
    {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'class_id' => 'required|integer',
            'section_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item = Timetable::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/timetables/{id}",
     *      operationId="getTimetableById",
     *      tags={"3. Academic Infrastructure - Timetable"},
     *      summary="Get Timetable information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = Timetable::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/timetables/{id}",
     *      operationId="updateTimetable",
     *      tags={"3. Academic Infrastructure - Timetable"},
     *      summary="Update existing Timetable",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"class_id","section_id","subject_id","teacher_id","day","start_time","end_time"},
     *              @OA\Property(property="class_id", type="integer", example="1"),
     *              @OA\Property(property="section_id", type="integer", example="1"),
     *              @OA\Property(property="subject_id", type="integer", example="1"),
     *              @OA\Property(property="teacher_id", type="integer", example="1"),
     *              @OA\Property(property="day", type="string", example="day"),
     *              @OA\Property(property="start_time", type="string", example="start_time"),
     *              @OA\Property(property="end_time", type="string", example="end_time")
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = Timetable::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'class_id' => 'required|integer',
            'section_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
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
     *      path="/api/timetables/{id}",
     *      operationId="deleteTimetable",
     *      tags={"3. Academic Infrastructure - Timetable"},
     *      summary="Delete existing Timetable",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $item = Timetable::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}