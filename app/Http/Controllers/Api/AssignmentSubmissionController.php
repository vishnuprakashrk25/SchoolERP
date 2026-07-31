<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\Models\AssignmentSubmission;

class AssignmentSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/assignment-submissions",
     *      operationId="getAssignmentSubmissionsList",
     *      tags={"7. E-Learning & Content - AssignmentSubmission"},
     *      summary="Get list of AssignmentSubmissions",
     *      description="Returns list of AssignmentSubmissions",
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
     *                  @OA\Property(property="assignment_id", type="string"),
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="remarks", type="string"),
     *                  @OA\Property(property="submitted_at", type="string"),
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
        $data = AssignmentSubmission::latest()->get();
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
     *      path="/api/assignment-submissions",
     *      operationId="storeAssignmentSubmission",
     *      tags={"7. E-Learning & Content - AssignmentSubmission"},
     *      summary="Store new assignment submission",
     *      description="Returns saved assignment submission data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"assignment_id","student_id"},
     *              @OA\Property(property="assignment_id", type="integer", example="1"),
     *              @OA\Property(property="student_id", type="integer", example="1")
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
     *                  @OA\Property(property="assignment_id", type="string"),
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="remarks", type="string"),
     *                  @OA\Property(property="submitted_at", type="string"),
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
            'assignment_id' => 'required',
            'student_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = AssignmentSubmission::create($request->all());
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
     *      path="/api/assignment-submissions/{id}",
     *      operationId="getAssignmentSubmissionById",
     *      tags={"7. E-Learning & Content - AssignmentSubmission"},
     *      summary="Get assignment submission information",
     *      description="Returns assignment submission data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AssignmentSubmission id",
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
     *                  @OA\Property(property="assignment_id", type="string"),
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="remarks", type="string"),
     *                  @OA\Property(property="submitted_at", type="string"),
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
        $item = AssignmentSubmission::findOrFail($id);
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
     *      path="/api/assignment-submissions/{id}",
     *      operationId="updateAssignmentSubmission",
     *      tags={"7. E-Learning & Content - AssignmentSubmission"},
     *      summary="Update existing assignment submission",
     *      description="Returns updated assignment submission data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AssignmentSubmission id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"assignment_id","student_id"},
     *              @OA\Property(property="assignment_id", type="integer", example="1"),
     *              @OA\Property(property="student_id", type="integer", example="1")
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
     *                  @OA\Property(property="assignment_id", type="string"),
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="file", type="string"),
     *                  @OA\Property(property="remarks", type="string"),
     *                  @OA\Property(property="submitted_at", type="string"),
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
        $item = AssignmentSubmission::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required',
            'student_id' => 'required'
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
     *      path="/api/assignment-submissions/{id}",
     *      operationId="deleteAssignmentSubmission",
     *      tags={"7. E-Learning & Content - AssignmentSubmission"},
     *      summary="Delete existing assignment submission",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="AssignmentSubmission id",
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
        $item = AssignmentSubmission::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
