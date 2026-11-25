<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomTypeController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/room-types",
     *      operationId="getRoomTypesList",
     *      tags={"5. Dormitory - Room Type"},
     *      summary="Get list of Room Types",
     *      description="Returns list of room types with SL, Room Type name, Description, and Actions.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by room type name or description", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $query = RoomType::withCount('rooms');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->get();

        $formatted = $items->map(function ($rt, $index) {
            return [
                'id'          => $rt->id,
                'sl'          => $index + 1,
                'name'        => $rt->name,
                'room_type'   => $rt->name,
                'type'        => $rt->name,
                'description' => $rt->description,
                'rooms_count' => $rt->rooms_count,
                'created_at'  => $rt->created_at,
                'updated_at'  => $rt->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $formatted->count(),
            'data'    => $formatted
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/room-types",
     *      operationId="storeRoomType",
     *      tags={"5. Dormitory - Room Type"},
     *      summary="Store new Room Type",
     *      description="Creates a new Room Type with name and description.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Single", description="Room Type name"),
     *              @OA\Property(property="description", type="string", example="Single bed room")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Room Type created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'description']);
        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $item = RoomType::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Room Type created successfully',
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/room-types/{id}",
     *      operationId="getRoomTypeById",
     *      tags={"5. Dormitory - Room Type"},
     *      summary="Get Room Type details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $item = RoomType::with('rooms')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/room-types/{id}",
     *      operationId="updateRoomType",
     *      tags={"5. Dormitory - Room Type"},
     *      summary="Update existing Room Type",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Double"),
     *              @OA\Property(property="description", type="string", example="Double bed room")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = RoomType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:100',
            'description' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $item->update($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Room Type updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/room-types/{id}",
     *      operationId="deleteRoomType",
     *      tags={"5. Dormitory - Room Type"},
     *      summary="Delete Room Type",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = RoomType::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room Type deleted successfully'
        ]);
    }
}
