<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\Models\StudyMaterial;

class StudyMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/study-materials",
     *      operationId="getStudyMaterialsList",
     *      tags={"7. E-Learning & Content - StudyMaterial"},
     *      summary="Get list of StudyMaterials",
     *      description="Returns list of StudyMaterials",
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
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="uploaded_by", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
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
     *     )
     */
        public function index()
    {
        $data = StudyMaterial::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/api/study-materials",
     *      operationId="storeStudyMaterial",
     *      tags={"7. E-Learning & Content - StudyMaterial"},
     *      summary="Store new study material",
     *      description="Returns saved study material data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","uploaded_by"},
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="uploaded_by", type="string", example="uploaded_by")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="uploaded_by", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
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
        public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'uploaded_by' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = StudyMaterial::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *      path="/api/study-materials/{id}",
     *      operationId="getStudyMaterialById",
     *      tags={"7. E-Learning & Content - StudyMaterial"},
     *      summary="Get study material information",
     *      description="Returns study material data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudyMaterial id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="uploaded_by", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
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
        public function show($id)
    {
        $item = StudyMaterial::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *      path="/api/study-materials/{id}",
     *      operationId="updateStudyMaterial",
     *      tags={"7. E-Learning & Content - StudyMaterial"},
     *      summary="Update existing study material",
     *      description="Returns updated study material data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudyMaterial id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","uploaded_by"},
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="uploaded_by", type="string", example="uploaded_by")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="uploaded_by", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
        public function update(Request $request, $id)
    {
        $item = StudyMaterial::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'uploaded_by' => 'required'
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
            'message' => 'Updated',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *      path="/api/study-materials/{id}",
     *      operationId="deleteStudyMaterial",
     *      tags={"7. E-Learning & Content - StudyMaterial"},
     *      summary="Delete existing study material",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudyMaterial id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
        public function destroy($id)
    {
        $item = StudyMaterial::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
