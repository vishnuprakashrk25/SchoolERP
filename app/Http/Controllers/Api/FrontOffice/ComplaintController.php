<?php

namespace App\Http\Controllers\Api\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/front-office/complaints",
     *      operationId="getComplaintsList",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Get list of complaints",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Complaint::latest()->get()
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/front-office/complaints",
     *      operationId="storeComplaint",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Store new complaint",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(required={"name", "phone", "message"}, type="object")),
     *      @OA\Response(response=201, description="Created"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $complaint = Complaint::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Complaint logged successfully',
            'data' => $complaint
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/front-office/complaints/{id}",
     *      operationId="getComplaintById",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Get complaint details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => Complaint::findOrFail($id)
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/front-office/complaints/{id}",
     *      operationId="updateComplaint",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Update complaint",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Complaint updated successfully',
            'data' => $complaint
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/front-office/complaints/{id}",
     *      operationId="deleteComplaint",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Delete complaint",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        Complaint::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complaint deleted successfully'
        ]);
    }
}
