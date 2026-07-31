<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\Models\Assignment;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/assignments",
     *      operationId="getAssignmentsList",
     *      tags={"7. E-Learning & Content - Assignment"},
     *      summary="Get list of Assignments",
     *      description="Returns list of Assignments",
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
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="assignment_date", type="string"),
     *                  @OA\Property(property="submission_date", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="created_by", type="string"),
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
        $data = Assignment::latest()->get();
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
     *      path="/api/assignments",
     *      operationId="storeAssignment",
     *      tags={"7. E-Learning & Content - Assignment"},
     *      summary="Store new assignment",
     *      description="Returns saved assignment data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"class_id","section_id","subject_id","title","assignment_date","submission_date"},
     *              @OA\Property(property="class_id", type="integer", example="1"),
     *              @OA\Property(property="section_id", type="integer", example="1"),
     *              @OA\Property(property="subject_id", type="integer", example="1"),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="assignment_date", type="string", example="assignment_date"),
     *              @OA\Property(property="submission_date", type="string", example="submission_date")
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
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="assignment_date", type="string"),
     *                  @OA\Property(property="submission_date", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="created_by", type="string"),
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
            'class_id' => 'required',
            'section_id' => 'required',
            'subject_id' => 'required',
            'title' => 'required',
            'assignment_date' => 'required',
            'submission_date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = Assignment::create($request->all());
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
     *      path="/api/assignments/{id}",
     *      operationId="getAssignmentById",
     *      tags={"7. E-Learning & Content - Assignment"},
     *      summary="Get assignment information",
     *      description="Returns assignment data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Assignment id",
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
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="assignment_date", type="string"),
     *                  @OA\Property(property="submission_date", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="created_by", type="string"),
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
        $item = Assignment::findOrFail($id);
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
     *      path="/api/assignments/{id}",
     *      operationId="updateAssignment",
     *      tags={"7. E-Learning & Content - Assignment"},
     *      summary="Update existing assignment",
     *      description="Returns updated assignment data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Assignment id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"class_id","section_id","subject_id","title","assignment_date","submission_date"},
     *              @OA\Property(property="class_id", type="integer", example="1"),
     *              @OA\Property(property="section_id", type="integer", example="1"),
     *              @OA\Property(property="subject_id", type="integer", example="1"),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="assignment_date", type="string", example="assignment_date"),
     *              @OA\Property(property="submission_date", type="string", example="submission_date")
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
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="string"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="assignment_date", type="string"),
     *                  @OA\Property(property="submission_date", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="created_by", type="string"),
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
        $item = Assignment::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'class_id' => 'required',
            'section_id' => 'required',
            'subject_id' => 'required',
            'title' => 'required',
            'assignment_date' => 'required',
            'submission_date' => 'required'
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
     *      path="/api/assignments/{id}",
     *      operationId="deleteAssignment",
     *      tags={"7. E-Learning & Content - Assignment"},
     *      summary="Delete existing assignment",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Assignment id",
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
        $item = Assignment::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
