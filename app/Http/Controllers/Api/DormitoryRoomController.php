<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DormitoryRoom;
use App\Models\Dormitory;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DormitoryRoomController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/dormitory-rooms",
     *      operationId="getDormitoryRoomsList",
     *      tags={"5. Dormitory - Dormitory Room"},
     *      summary="Get list of Dormitory Rooms",
     *      description="Returns list of dormitory rooms with SL, Dormitory, Room Number, Room Type, No. of Bed, Cost Per Bed, and Actions.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="dormitory_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="room_type_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by room number or description", @OA\Schema(type="string")),
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
        $query = DormitoryRoom::with([
            'dormitory:id,name,type',
            'roomType:id,name'
        ]);

        if ($request->filled('dormitory_id')) {
            $query->where('dormitory_id', $request->input('dormitory_id'));
        }
        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->input('room_type_id'));
        }
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('dormitory', fn($dq) => $dq->where('name', 'like', "%{$search}%"));
            });
        }

        $items = $query->latest()->get();

        $formatted = $items->map(function ($r, $index) {
            $roomNum = $r->room_number ?? $r->name;
            return [
                'id'                   => $r->id,
                'sl'                   => $index + 1,
                'dormitory_id'         => $r->dormitory_id,
                'dormitory_name'       => $r->dormitory?->name,
                'dormitory'            => $r->dormitory?->name,
                'room_number'          => $roomNum,
                'name'                 => $roomNum,
                'room_type_id'         => $r->room_type_id,
                'room_type'            => $r->roomType?->name,
                'type'                 => $r->roomType?->name,
                'number_of_bed'        => (int)$r->number_of_bed,
                'no_of_bed'            => (int)$r->number_of_bed,
                'cost_per_bed'         => (float)$r->cost_per_bed,
                'cost_per_bed_formatted'=> '₹ ' . number_format((float)$r->cost_per_bed, 0),
                'description'          => $r->description,
                'created_at'           => $r->created_at,
                'updated_at'           => $r->updated_at,
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
     *      path="/api/dormitory-rooms",
     *      operationId="storeDormitoryRoom",
     *      tags={"5. Dormitory - Dormitory Room"},
     *      summary="Store new Dormitory Room",
     *      description="Creates a new Dormitory Room with Dormitory, Room Number, Type, Number of Bed, Cost Per Bed, and Description.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"dormitory_id","room_number"},
     *              @OA\Property(property="dormitory_id", type="integer", example=1),
     *              @OA\Property(property="room_number", type="string", example="12"),
     *              @OA\Property(property="room_type_id", type="integer", example=1),
     *              @OA\Property(property="number_of_bed", type="integer", example=21),
     *              @OA\Property(property="cost_per_bed", type="number", example=200),
     *              @OA\Property(property="description", type="string", example="First floor room")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Dormitory Room created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dormitory_id'  => 'required|integer|exists:dormitories,id',
            'room_number'   => 'required_without:name|string|max:100',
            'name'          => 'nullable|string|max:100',
            'room_type_id'  => 'nullable|integer',
            'number_of_bed' => 'nullable|integer|min:1',
            'cost_per_bed'  => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $roomNumber = $request->input('room_number') ?? $request->input('name');

        $data = [
            'dormitory_id'  => $request->input('dormitory_id'),
            'room_number'   => $roomNumber,
            'name'          => $roomNumber,
            'room_type_id'  => $request->input('room_type_id'),
            'number_of_bed' => $request->input('number_of_bed', 1),
            'cost_per_bed'  => $request->input('cost_per_bed', 0),
            'description'   => $request->input('description'),
        ];

        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $item = DormitoryRoom::create($data);
        $item->load(['dormitory', 'roomType']);

        return response()->json([
            'success' => true,
            'message' => 'Dormitory Room created successfully',
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/dormitory-rooms/{id}",
     *      operationId="getDormitoryRoomById",
     *      tags={"5. Dormitory - Dormitory Room"},
     *      summary="Get Dormitory Room details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $item = DormitoryRoom::with(['dormitory', 'roomType'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/dormitory-rooms/{id}",
     *      operationId="updateDormitoryRoom",
     *      tags={"5. Dormitory - Dormitory Room"},
     *      summary="Update existing Dormitory Room",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="dormitory_id", type="integer"),
     *              @OA\Property(property="room_number", type="string"),
     *              @OA\Property(property="room_type_id", type="integer"),
     *              @OA\Property(property="number_of_bed", type="integer"),
     *              @OA\Property(property="cost_per_bed", type="number"),
     *              @OA\Property(property="description", type="string")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = DormitoryRoom::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'dormitory_id'  => 'sometimes|required|integer|exists:dormitories,id',
            'room_number'   => 'sometimes|nullable|string|max:100',
            'name'          => 'sometimes|nullable|string|max:100',
            'room_type_id'  => 'sometimes|nullable|integer',
            'number_of_bed' => 'sometimes|nullable|integer|min:1',
            'cost_per_bed'  => 'sometimes|nullable|numeric|min:0',
            'description'   => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'dormitory_id', 'room_type_id', 'number_of_bed', 'cost_per_bed', 'description'
        ]);

        if ($request->filled('room_number')) {
            $data['room_number'] = $request->input('room_number');
            $data['name'] = $request->input('room_number');
        } elseif ($request->filled('name')) {
            $data['room_number'] = $request->input('name');
            $data['name'] = $request->input('name');
        }

        $item->update($data);
        $item->load(['dormitory', 'roomType']);

        return response()->json([
            'success' => true,
            'message' => 'Dormitory Room updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/dormitory-rooms/{id}",
     *      operationId="deleteDormitoryRoom",
     *      tags={"5. Dormitory - Dormitory Room"},
     *      summary="Delete Dormitory Room",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = DormitoryRoom::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dormitory Room deleted successfully'
        ]);
    }
}
