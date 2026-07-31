<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\Models\DownloadContent;

class DownloadContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/download-contents",
     *      operationId="getDownloadContentsList",
     *      tags={"7. E-Learning & Content - DownloadContent"},
     *      summary="Get list of DownloadContents",
     *      description="Returns list of DownloadContents",
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
     *                  @OA\Property(property="type_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
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
        $data = DownloadContent::latest()->get();
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
     *      path="/api/download-contents",
     *      operationId="storeDownloadContent",
     *      tags={"7. E-Learning & Content - DownloadContent"},
     *      summary="Store new download content",
     *      description="Returns saved download content data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"type_id","title","uploaded_by"},
     *              @OA\Property(property="type_id", type="integer", example="1"),
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
     *                  @OA\Property(property="type_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
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
            'type_id' => 'required',
            'title' => 'required',
            'uploaded_by' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = DownloadContent::create($request->all());
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
     *      path="/api/download-contents/{id}",
     *      operationId="getDownloadContentById",
     *      tags={"7. E-Learning & Content - DownloadContent"},
     *      summary="Get download content information",
     *      description="Returns download content data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="DownloadContent id",
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
     *                  @OA\Property(property="type_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
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
        $item = DownloadContent::findOrFail($id);
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
     *      path="/api/download-contents/{id}",
     *      operationId="updateDownloadContent",
     *      tags={"7. E-Learning & Content - DownloadContent"},
     *      summary="Update existing download content",
     *      description="Returns updated download content data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="DownloadContent id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"type_id","title","uploaded_by"},
     *              @OA\Property(property="type_id", type="integer", example="1"),
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
     *                  @OA\Property(property="type_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
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
        $item = DownloadContent::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'type_id' => 'required',
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
     *      path="/api/download-contents/{id}",
     *      operationId="deleteDownloadContent",
     *      tags={"7. E-Learning & Content - DownloadContent"},
     *      summary="Delete existing download content",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="DownloadContent id",
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
        $item = DownloadContent::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
