<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicCategoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/academic-categories",
     *      operationId="getAcademicCategoriesList",
     *      tags={"3. Academic Infrastructure - Academic Category"},
     *      summary="Get list of Academic Categories",
     *      description="Returns list of Academic Categories (e.g., Rainbow - 3, Rainbow - 4, Planet, Galaxy HS, etc.)",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=6),
     *                      @OA\Property(property="name", type="string", example="Rainbow - 3"),
     *                      @OA\Property(property="description", type="string", example="Only 3rd Class"),
     *                      @OA\Property(property="created_at", type="string"),
     *                      @OA\Property(property="updated_at", type="string"),
     *                      @OA\Property(property="deleted_at", type="string", nullable=true)
     *                  )
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function index()
    {
        $data = AcademicCategory::orderBy('id', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/academic-categories",
     *      operationId="storeAcademicCategory",
     *      tags={"3. Academic Infrastructure - Academic Category"},
     *      summary="Store new academic category",
     *      description="Creates a new academic category (e.g., Rainbow - 3, Planet, etc.)",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Rainbow - 6"),
     *              @OA\Property(property="description", type="string", example="Only 6th Class")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Academic category created successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = AcademicCategory::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Academic category created successfully',
            'data' => $item
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/academic-categories/{id}",
     *      operationId="getAcademicCategoryById",
     *      tags={"3. Academic Infrastructure - Academic Category"},
     *      summary="Get academic category information",
     *      description="Returns academic category data by ID",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AcademicCategory ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      )
     * )
     */
    public function show($id)
    {
        $item = AcademicCategory::with(['classes'])->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Academic category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/academic-categories/{id}",
     *      operationId="updateAcademicCategory",
     *      tags={"3. Academic Infrastructure - Academic Category"},
     *      summary="Update academic category",
     *      description="Updates an existing academic category",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AcademicCategory ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Rainbow - 3 Updated"),
     *              @OA\Property(property="description", type="string", example="Only 3rd Class")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Academic category updated successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $item = AcademicCategory::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Academic category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
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
            'message' => 'Academic category updated successfully',
            'data' => $item
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/academic-categories/{id}",
     *      operationId="deleteAcademicCategory",
     *      tags={"3. Academic Infrastructure - Academic Category"},
     *      summary="Delete academic category",
     *      description="Soft deletes an academic category",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AcademicCategory ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Academic category deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      )
     * )
     */
    public function destroy($id)
    {
        $item = AcademicCategory::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Academic category not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Academic category deleted successfully'
        ]);
    }
}
