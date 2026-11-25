<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dormitory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DormitoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/dormitories",
     *      operationId="getDormitoriesList",
     *      tags={"5. Dormitory - Dormitory"},
     *      summary="Get list of Dormitories",
     *      description="Returns list of dormitories with SL, Dormitory Name, Type, Address, Intake, and Actions.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by dormitory name, address, or type", @OA\Schema(type="string")),
     *      @OA\Parameter(name="type", in="query", required=false, description="Filter by type (Boys, Girls, Mixed)", @OA\Schema(type="string")),
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
        $query = Dormitory::withCount('rooms');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $items = $query->latest()->get();

        $formatted = $items->map(function ($d, $index) {
            return [
                'id'             => $d->id,
                'sl'             => $index + 1,
                'dormitory_name' => $d->name,
                'name'           => $d->name,
                'type'           => $d->type,
                'address'        => $d->address,
                'intake'         => (int)$d->intake,
                'description'    => $d->description,
                'rooms_count'    => $d->rooms_count,
                'created_at'     => $d->created_at,
                'updated_at'     => $d->updated_at,
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
     *      path="/api/dormitories",
     *      operationId="storeDormitory",
     *      tags={"5. Dormitory - Dormitory"},
     *      summary="Store new Dormitory",
     *      description="Creates a new Dormitory with name, type (Boys, Girls, Mixed), address, intake, and description.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Test Dormitory"),
     *              @OA\Property(property="type", type="string", example="Boys", description="Boys, Girls, or Mixed"),
     *              @OA\Property(property="address", type="string", example="25/13, Sukrabad Rd, Dhaka"),
     *              @OA\Property(property="intake", type="integer", example=200),
     *              @OA\Property(property="description", type="string", example="Main hostel building")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Dormitory created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'type'        => 'nullable|string|max:50',
            'address'     => 'nullable|string',
            'intake'      => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'type', 'address', 'intake', 'description']);
        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $item = Dormitory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Dormitory created successfully',
            'data'    => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/dormitories/{id}",
     *      operationId="getDormitoryById",
     *      tags={"5. Dormitory - Dormitory"},
     *      summary="Get Dormitory details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $item = Dormitory::with('rooms.roomType')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/dormitories/{id}",
     *      operationId="updateDormitory",
     *      tags={"5. Dormitory - Dormitory"},
     *      summary="Update existing Dormitory",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="type", type="string"),
     *              @OA\Property(property="address", type="string"),
     *              @OA\Property(property="intake", type="integer"),
     *              @OA\Property(property="description", type="string")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = Dormitory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'type'        => 'sometimes|nullable|string|max:50',
            'address'     => 'sometimes|nullable|string',
            'intake'      => 'sometimes|nullable|integer|min:0',
            'description' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $item->update($request->only(['name', 'type', 'address', 'intake', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Dormitory updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/dormitories/{id}",
     *      operationId="deleteDormitory",
     *      tags={"5. Dormitory - Dormitory"},
     *      summary="Delete Dormitory",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = Dormitory::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dormitory deleted successfully'
        ]);
    }
}
