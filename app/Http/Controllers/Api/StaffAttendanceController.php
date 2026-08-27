<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffAttendanceController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/staff-attendances",
     *      operationId="getStaffAttendanceList",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Get list of StaffAttendance",
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
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="staff_id", type="integer"),
     *                      @OA\Property(property="date", type="string"),
     *                      @OA\Property(property="status", type="string"),
     *                      @OA\Property(property="note", type="string", example="On sick leave"),
     *                      @OA\Property(property="photo", type="string", example="http://localhost:8000/uploads/staff_photos/example.jpg"),
     *                      @OA\Property(property="staff", type="object")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index()
    {
        $data = StaffAttendance::with(['staff'])->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/staff-attendances",
     *      operationId="storeStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Store new StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"staff_id","date","status"},
     *              @OA\Property(property="staff_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-08-09"),
     *              @OA\Property(property="status", type="string", example="present"),
     *              @OA\Property(property="note", type="string", example="On sick leave")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|integer',
            'date' => 'required',
            'status' => 'required',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item = StaffAttendance::create($request->all());
        $item->load('staff');
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/staff-attendances/{id}",
     *      operationId="getStaffAttendanceById",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Get StaffAttendance information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        $item = StaffAttendance::with(['staff'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/staff-attendances/{id}",
     *      operationId="updateStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Update existing StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"staff_id","date","status"},
     *              @OA\Property(property="staff_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-08-09"),
     *              @OA\Property(property="status", type="string", example="present"),
     *              @OA\Property(property="note", type="string", example="Updated note")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $item = StaffAttendance::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|integer',
            'date' => 'required',
            'status' => 'required',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item->update($request->all());
        $item->load('staff');
        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/staff-attendances/{id}",
     *      operationId="deleteStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Delete existing StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=204,description="Successful operation")
     * )
     */
    public function destroy($id)
    {
        $item = StaffAttendance::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}