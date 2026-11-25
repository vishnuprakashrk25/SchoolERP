<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/vehicles",
     *      operationId="getVehiclesList",
     *      tags={"5. Transport - Vehicle"},
     *      summary="Get list of Vehicles",
     *      description="Returns list of vehicles with Vehicle No, Model No, Year Made, Driver Name, and Driver Phone Number.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by vehicle number, model, or driver name", @OA\Schema(type="string")),
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
        $query = Vehicle::with('driver:id,first_name,last_name,full_name,phone,emergency_mobile,staff_code');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_model', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhereHas('driver', function ($dq) use ($search) {
                      $dq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $vehicles = $query->latest()->get();

        $formatted = $vehicles->map(function ($v) {
            $driverName = $v->driver?->full_name 
                ?? ($v->driver ? trim($v->driver->first_name . ' ' . $v->driver->last_name) : null)
                ?? $v->driver_name;

            $driverPhone = $v->driver?->phone 
                ?? $v->driver?->emergency_mobile 
                ?? $v->driver_contact;

            return [
                'id'                  => $v->id,
                'vehicle_no'          => $v->vehicle_no,
                'vehicle_number'      => $v->vehicle_no,
                'vehicle_model'       => $v->vehicle_model,
                'model_no'            => $v->vehicle_model,
                'year_made'           => $v->year_made,
                'driver_id'           => $v->driver_id,
                'driver_name'         => $driverName,
                'driver_phone_number' => $driverPhone,
                'driver_license'      => $v->driver_license,
                'note'                => $v->note,
                'created_at'          => $v->created_at,
                'updated_at'          => $v->updated_at,
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
     *      path="/api/vehicles",
     *      operationId="storeVehicle",
     *      tags={"5. Transport - Vehicle"},
     *      summary="Store new Vehicle",
     *      description="Creates a new vehicle with Vehicle Number, Vehicle Model, Year Made, Driver, and Note.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"vehicle_no"},
     *              @OA\Property(property="vehicle_no", type="string", example="Test Vehicle 23232"),
     *              @OA\Property(property="vehicle_model", type="string", example="Test Model 4343433"),
     *              @OA\Property(property="year_made", type="string", example="2007"),
     *              @OA\Property(property="driver_id", type="integer", example=10, description="Staff ID with Driver role"),
     *              @OA\Property(property="driver_name", type="string", example="Jhon Martin"),
     *              @OA\Property(property="driver_contact", type="string", example="7037372929"),
     *              @OA\Property(property="note", type="string", example="School bus 1")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Vehicle created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_no'     => 'required|string|max:100',
            'vehicle_model'  => 'nullable|string|max:100',
            'year_made'      => 'nullable|string|max:50',
            'driver_id'      => 'nullable|integer',
            'driver_name'    => 'nullable|string|max:255',
            'driver_license' => 'nullable|string|max:100',
            'driver_contact' => 'nullable|string|max:100',
            'note'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'vehicle_no', 'vehicle_model', 'year_made', 'driver_id',
            'driver_name', 'driver_license', 'driver_contact', 'note'
        ]);

        // Auto-fill driver details if driver_id passed
        if (!empty($data['driver_id'])) {
            $driver = Staff::find($data['driver_id']);
            if ($driver) {
                if (empty($data['driver_name'])) {
                    $data['driver_name'] = $driver->full_name ?? trim($driver->first_name . ' ' . $driver->last_name);
                }
                if (empty($data['driver_contact'])) {
                    $data['driver_contact'] = $driver->phone ?? $driver->emergency_mobile;
                }
            }
        }

        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $vehicle = Vehicle::create($data);
        $vehicle->load('driver:id,first_name,last_name,full_name,phone');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle created successfully',
            'data'    => $vehicle
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/vehicles/{id}",
     *      operationId="getVehicleById",
     *      tags={"5. Transport - Vehicle"},
     *      summary="Get Vehicle details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $vehicle = Vehicle::with(['driver', 'routes'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $vehicle
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/vehicles/{id}",
     *      operationId="updateVehicle",
     *      tags={"5. Transport - Vehicle"},
     *      summary="Update existing Vehicle",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="vehicle_no", type="string"),
     *              @OA\Property(property="vehicle_model", type="string"),
     *              @OA\Property(property="year_made", type="string"),
     *              @OA\Property(property="driver_id", type="integer"),
     *              @OA\Property(property="note", type="string")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'vehicle_no'     => 'sometimes|required|string|max:100',
            'vehicle_model'  => 'sometimes|nullable|string|max:100',
            'year_made'      => 'sometimes|nullable|string|max:50',
            'driver_id'      => 'sometimes|nullable|integer',
            'driver_name'    => 'sometimes|nullable|string|max:255',
            'driver_contact' => 'sometimes|nullable|string|max:100',
            'note'           => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'vehicle_no', 'vehicle_model', 'year_made', 'driver_id',
            'driver_name', 'driver_license', 'driver_contact', 'note'
        ]);

        if (array_key_exists('driver_id', $data) && !empty($data['driver_id'])) {
            $driver = Staff::find($data['driver_id']);
            if ($driver) {
                $data['driver_name'] = $driver->full_name ?? trim($driver->first_name . ' ' . $driver->last_name);
                $data['driver_contact'] = $driver->phone ?? $driver->emergency_mobile;
            }
        }

        $vehicle->update($data);
        $vehicle->load('driver:id,first_name,last_name,full_name,phone');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data'    => $vehicle
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/vehicles/{id}",
     *      operationId="deleteVehicle",
     *      tags={"5. Transport - Vehicle"},
     *      summary="Delete Vehicle",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/vehicles/drivers-dropdown",
     *      operationId="getDriversDropdown",
     *      tags={"5. Transport - Vehicle"},
     *      summary="Get drivers dropdown list",
     *      description="Returns list of staff with driver role or all staff for driver selection.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function driversDropdown()
    {
        $drivers = Staff::where(function ($q) {
            $q->where('role', 'like', '%driver%')
              ->orWhere('designation', 'like', '%driver%');
        })->get(['id', 'first_name', 'last_name', 'full_name', 'phone', 'staff_code']);

        if ($drivers->isEmpty()) {
            $drivers = Staff::latest()->take(30)->get(['id', 'first_name', 'last_name', 'full_name', 'phone', 'staff_code']);
        }

        return response()->json([
            'success' => true,
            'data'    => $drivers
        ]);
    }
}
