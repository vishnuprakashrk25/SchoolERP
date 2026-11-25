<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignVehicle;
use App\Models\Route;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignVehicleController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/assign-vehicles",
     *      operationId="getAssignVehiclesList",
     *      tags={"5. Transport - Assign Vehicle"},
     *      summary="Get list of Assigned Vehicles",
     *      description="Returns list of Route to Vehicle assignments.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="route_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="vehicle_id", in="query", required=false, @OA\Schema(type="integer")),
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
        $query = AssignVehicle::with([
            'route:id,title,fare',
            'vehicle:id,vehicle_no,vehicle_model,driver_name,driver_contact'
        ]);

        if ($request->filled('route_id')) {
            $query->where('route_id', $request->input('route_id'));
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->input('vehicle_id'));
        }

        $items = $query->latest()->get();

        $formatted = $items->map(function ($av, $index) {
            return [
                'id'            => $av->id,
                'sl'            => $index + 1,
                'route_id'      => $av->route_id,
                'route_title'   => $av->route?->title,
                'route'         => $av->route?->title,
                'fare'          => $av->route?->fare,
                'vehicle_id'    => $av->vehicle_id,
                'vehicle_no'    => $av->vehicle?->vehicle_no,
                'vehicle_model' => $av->vehicle?->vehicle_model,
                'vehicle'       => ($av->vehicle?->vehicle_no ?? '') . ($av->vehicle?->vehicle_model ? ' - ' . $av->vehicle->vehicle_model : ''),
                'driver_name'   => $av->vehicle?->driver_name,
                'driver_contact'=> $av->vehicle?->driver_contact,
                'created_at'    => $av->created_at,
                'updated_at'    => $av->updated_at,
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
     *      path="/api/assign-vehicles",
     *      operationId="storeAssignVehicle",
     *      tags={"5. Transport - Assign Vehicle"},
     *      summary="Assign Vehicle to Route",
     *      description="Assigns one or more vehicles to a route.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"route_id","vehicle_id"},
     *              @OA\Property(property="route_id", type="integer", example=1),
     *              @OA\Property(property="vehicle_id", type="integer", example=2, description="Vehicle ID or array of Vehicle IDs")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Assigned successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_id'   => 'required|integer|exists:routes,id',
            'vehicle_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $routeId = $request->input('route_id');
        $vehicleInput = $request->input('vehicle_id');
        $vehicleIds = is_array($vehicleInput) ? $vehicleInput : [$vehicleInput];

        $saved = [];
        foreach ($vehicleIds as $vId) {
            $item = AssignVehicle::firstOrCreate([
                'route_id'   => $routeId,
                'vehicle_id' => $vId,
            ], [
                'branch_id'  => $request->user()?->branch_id,
            ]);
            $item->load(['route', 'vehicle']);
            $saved[] = $item;
        }

        return response()->json([
            'success' => true,
            'message' => 'Vehicle assigned to route successfully',
            'data'    => count($saved) === 1 ? $saved[0] : $saved
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/assign-vehicles/{id}",
     *      operationId="getAssignVehicleById",
     *      tags={"5. Transport - Assign Vehicle"},
     *      summary="Get Assignment details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        $item = AssignVehicle::with(['route', 'vehicle'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/assign-vehicles/{id}",
     *      operationId="updateAssignVehicle",
     *      tags={"5. Transport - Assign Vehicle"},
     *      summary="Update Vehicle Assignment",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="route_id", type="integer"),
     *              @OA\Property(property="vehicle_id", type="integer")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = AssignVehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'route_id'   => 'sometimes|required|integer|exists:routes,id',
            'vehicle_id' => 'sometimes|required|integer|exists:vehicles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $item->update($request->only(['route_id', 'vehicle_id']));
        $item->load(['route', 'vehicle']);

        return response()->json([
            'success' => true,
            'message' => 'Assignment updated successfully',
            'data'    => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/assign-vehicles/{id}",
     *      operationId="deleteAssignVehicle",
     *      tags={"5. Transport - Assign Vehicle"},
     *      summary="Delete Vehicle Assignment",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $item = AssignVehicle::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle assignment deleted successfully'
        ]);
    }
}
