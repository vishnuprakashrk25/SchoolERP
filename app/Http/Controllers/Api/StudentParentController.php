<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\Models\StudentParent;

class StudentParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/student-parents",
     *      operationId="getStudentParentsList",
     *      tags={"4. Student Enrollment - StudentParent"},
     *      summary="Get list of StudentParents",
     *      description="Returns list of StudentParents",
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
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="father_phone", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="mother_phone", type="string"),
     *                  @OA\Property(property="guardian_name", type="string"),
     *                  @OA\Property(property="guardian_phone", type="string"),
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
        $data = StudentParent::latest()->get();
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
     *      path="/api/student-parents",
     *      operationId="storeStudentParent",
     *      tags={"4. Student Enrollment - StudentParent"},
     *      summary="Store new student parent",
     *      description="Returns saved student parent data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id"},
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
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="father_phone", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="mother_phone", type="string"),
     *                  @OA\Property(property="guardian_name", type="string"),
     *                  @OA\Property(property="guardian_phone", type="string"),
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
            'student_id' => 'required',
            'father_name' => 'nullable|string',
            'father_phone' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'mother_phone' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = StudentParent::create($request->all());
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
     *      path="/api/student-parents/{id}",
     *      operationId="getStudentParentById",
     *      tags={"4. Student Enrollment - StudentParent"},
     *      summary="Get student parent information",
     *      description="Returns student parent data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudentParent id",
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
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="father_phone", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="mother_phone", type="string"),
     *                  @OA\Property(property="guardian_name", type="string"),
     *                  @OA\Property(property="guardian_phone", type="string"),
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
        $item = StudentParent::findOrFail($id);
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
     *      path="/api/student-parents/{id}",
     *      operationId="updateStudentParent",
     *      tags={"4. Student Enrollment - StudentParent"},
     *      summary="Update existing student parent",
     *      description="Returns updated student parent data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudentParent id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id"},
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
     *                  @OA\Property(property="student_id", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="father_phone", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="mother_phone", type="string"),
     *                  @OA\Property(property="guardian_name", type="string"),
     *                  @OA\Property(property="guardian_phone", type="string"),
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
        $item = StudentParent::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'father_name' => 'nullable|string',
            'father_phone' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'mother_phone' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string'
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
     *      path="/api/student-parents/{id}",
     *      operationId="deleteStudentParent",
     *      tags={"4. Student Enrollment - StudentParent"},
     *      summary="Delete existing student parent",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudentParent id",
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
        $item = StudentParent::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/parent/children",
     *      operationId="getParentChildrenList",
     *      tags={"4. Student Enrollment - StudentParent"},
     *      summary="Get all children of logged in parent",
     *      description="Returns array of students linked to the authenticated parent's email address.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="count", type="integer", example=2),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function myChildren(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $email = $user->email;

        $children = \App\Models\Student::whereHas('parents', function ($q) use ($email) {
            $q->where('father_email', $email)
              ->orWhere('mother_email', $email)
              ->orWhere('guardian_email', $email);
        })->with([
            'class',
            'section',
            'category',
            'parents',
            'studentAcademics.academicYear',
            'emergencyContacts'
        ])->get();

        return response()->json([
            'success' => true,
            'count' => $children->count(),
            'data' => $children
        ]);
    }
}
