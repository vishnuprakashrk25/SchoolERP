<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/routes",
     *      operationId="getRoutesList",
     *      tags={"5. Transport - Route"},
     *      summary="Get list of Routes",
     *      description="Returns list of routes with SL, Route Title, Fare, and Actions.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by route title", @OA\Schema(type="string")),
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
        $query = Route::withCount('vehicles');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('title', 'like', "%{$search}%");
        }

        $routes = $query->latest()->get();

        $formatted = $routes->map(function ($r, $index) {
            return [
                'id'             => $r->id,
                'sl'             => $index + 1,
                'title'          => $r->title,
                'route_title'    => $r->title,
                'fare'           => (float)$r->fare,
                'fare_formatted' => '₹ ' . number_format((float)$r->fare, 0),
                'vehicles_count' => $r->vehicles_count,
                'created_at'     => $r->created_at,
                'updated_at'     => $r->updated_at,
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
     *      path="/api/routes",
     *      operationId="storeRoute",
     *      tags={"5. Transport - Route"},
     *      summary="Store new Route",
     *      description="Creates a new route with Route Title and Fare.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","fare"},
     *              @OA\Property(property="title", type="string", example="Test Route"),
     *              @OA\Property(property="fare", type="number", example=200)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Route created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'fare'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['title', 'fare']);
        if ($request->user() && $request->user()->branch_id) {
            $data['branch_id'] = $request->user()->branch_id;
        }

        $route = Route::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Route created successfully',
            'data'    => $route
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/routes/{id}",
     *      operationId="getRouteById",
     *      tags={"5. Transport - Route"},
     *      summary="Get Route details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $route = Route::with('vehicles')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data'    => $route
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/routes/{id}",
     *      operationId="updateRoute",
     *      tags={"5. Transport - Route"},
     *      summary="Update existing Route",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string", example="Updated Route"),
     *              @OA\Property(property="fare", type="number", example=250)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $route = Route::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'fare'  => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $route->update($request->only(['title', 'fare']));

        return response()->json([
            'success' => true,
            'message' => 'Route updated successfully',
            'data'    => $route
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/routes/{id}",
     *      operationId="deleteRoute",
     *      tags={"5. Transport - Route"},
     *      summary="Delete Route",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $route = Route::findOrFail($id);
        $route->delete();

        return response()->json([
            'success' => true,
            'message' => 'Route deleted successfully'
        ]);
    }
}
