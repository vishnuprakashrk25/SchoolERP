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
     *      path="/api/staff-attendance",
     *      operationId="getStaffAttendanceList",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Get list of StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function index()
    {
        $data = StaffAttendance::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/staff-attendance",
     *      operationId="storeStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Store new StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"staff_id","date","status"},
     *              @OA\Property(property="staff_id", type="integer", example="1"),
     *              @OA\Property(property="date", type="string", example="date"),
     *              @OA\Property(property="status", type="string", example="status")
     *          )
     *      ),
     *      @OA\Response(response=201,description="Successful operation")
     * )
     */
    public function store(Request $request)
    {
                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'staff_id' => 'required|integer',
            'date' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $item = StaffAttendance::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/staff-attendance/{id}",
     *      operationId="getStaffAttendanceById",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Get StaffAttendance information",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = StaffAttendance::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/staff-attendance/{id}",
     *      operationId="updateStaffAttendance",
     *      tags={"2. Staff & HR - StaffAttendance"},
     *      summary="Update existing StaffAttendance",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id",required=true,in="path",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"staff_id","date","status"},
     *              @OA\Property(property="staff_id", type="integer", example="1"),
     *              @OA\Property(property="date", type="string", example="date"),
     *              @OA\Property(property="status", type="string", example="status")
     *          )
     *      ),
     *      @OA\Response(response=200,description="Successful operation")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = StaffAttendance::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'staff_id' => 'required|integer',
            'date' => 'required',
            'status' => 'required',
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
     *      path="/api/staff-attendance/{id}",
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