<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassRoomController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/class-rooms",
     *      operationId="getClassRoomsList",
     *      tags={"3. Academic Infrastructure - Class Room"},
     *      summary="Get list of class rooms",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => ClassRoom::all()
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/class-rooms",
     *      operationId="storeClassRoom",
     *      tags={"3. Academic Infrastructure - Class Room"},
     *      summary="Store new class room",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(required={"name"}, type="object")),
     *      @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $room = ClassRoom::create($request->all());

        return response()->json(['success' => true, 'data' => $room], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/class-rooms/{id}",
     *      operationId="getClassRoomById",
     *      tags={"3. Academic Infrastructure - Class Room"},
     *      summary="Get class room details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => ClassRoom::findOrFail($id)
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/class-rooms/{id}",
     *      operationId="updateClassRoom",
     *      tags={"3. Academic Infrastructure - Class Room"},
     *      summary="Update class room",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $room = ClassRoom::findOrFail($id);
        $room->update($request->all());

        return response()->json(['success' => true, 'data' => $room]);
    }

    /**
     * @OA\Delete(
     *      path="/api/class-rooms/{id}",
     *      operationId="deleteClassRoom",
     *      tags={"3. Academic Infrastructure - Class Room"},
     *      summary="Delete class room",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        ClassRoom::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
