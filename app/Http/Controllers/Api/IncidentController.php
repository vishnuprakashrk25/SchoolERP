<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/incidents",
     *      operationId="getIncidentsList",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Get list of master incidents (remarks templates)",
     *      description="Returns all incident definitions used to assign behaviour points/remarks to students.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="is_negative", in="query", required=false, @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="all", in="query", required=false, description="Set to 1 to fetch unpaginated list for dropdowns", @OA\Schema(type="integer")),
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
        $query = Incident::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_negative') && $request->input('is_negative') !== null) {
            $isNegative = filter_var($request->input('is_negative'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_negative', $isNegative);
        }

        if ($request->user() && $request->user()->branch_id) {
            $query->where(function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id)
                  ->orWhereNull('branch_id');
            });
        }

        $query->orderBy('created_at', 'desc');

        if ($request->boolean('all') || $request->input('per_page') == -1) {
            $incidents = $query->get();
        } else {
            $perPage = $request->input('per_page', 15);
            $incidents = $query->paginate($perPage);
        }

        return response()->json([
            'success' => true,
            'data'    => $incidents
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/incidents",
     *      operationId="storeIncident",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Create a new incident (Add Incident modal)",
     *      description="Creates a new behaviour incident master record.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "point"},
     *              @OA\Property(property="title", type="string", example="Good Discipline"),
     *              @OA\Property(property="point", type="integer", example=10),
     *              @OA\Property(property="is_negative", type="boolean", example=false),
     *              @OA\Property(property="description", type="string", example="Exemplary attitude and conduct in school")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Incident created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Incident created successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'point'       => 'required|integer|min:0',
            'is_negative' => 'nullable|boolean',
            'description' => 'nullable|string',
            'branch_id'   => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $branchId = $request->input('branch_id') ?? ($request->user() ? $request->user()->branch_id : null);

        $incident = Incident::create([
            'branch_id'   => $branchId,
            'title'       => $request->input('title'),
            'point'       => (int) $request->input('point'),
            'is_negative' => $request->boolean('is_negative'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Incident created successfully',
            'data'    => $incident
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/incidents/{id}",
     *      operationId="getIncidentById",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Get incident details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Incident not found")
     * )
     */
    public function show($id)
    {
        $incident = Incident::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $incident
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/incidents/{id}",
     *      operationId="updateIncident",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Update an incident",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string"),
     *              @OA\Property(property="point", type="integer"),
     *              @OA\Property(property="is_negative", type="boolean"),
     *              @OA\Property(property="description", type="string")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Incident updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $incident = Incident::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|required|string|max:255',
            'point'       => 'sometimes|required|integer|min:0',
            'is_negative' => 'nullable|boolean',
            'description' => 'nullable|string',
            'branch_id'   => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['title', 'point', 'description', 'branch_id']);
        if ($request->has('is_negative')) {
            $data['is_negative'] = $request->boolean('is_negative');
        }

        $incident->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Incident updated successfully',
            'data'    => $incident
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/incidents/{id}",
     *      operationId="deleteIncident",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Delete an incident",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Incident deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);
        $incident->delete();

        return response()->json([
            'success' => true,
            'message' => 'Incident deleted successfully'
        ]);
    }
}
