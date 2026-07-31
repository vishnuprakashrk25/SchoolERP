<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\StudentDocument;

class StudentDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/student-documents",
     *      operationId="getStudentDocumentsList",
     *      tags={"4. Student Enrollment - StudentDocument"},
     *      summary="Get list of StudentDocuments",
     *      description="Returns list of StudentDocuments",
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
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="file", type="string"),
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
        $data = StudentDocument::latest()->get();
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
     *      path="/api/student-documents",
     *      operationId="storeStudentDocument",
     *      tags={"4. Student Enrollment - StudentDocument"},
     *      summary="Store new student document",
     *      description="Returns saved student document data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"student_id","title","file"},
     *                  @OA\Property(property="student_id", type="integer", example="1"),
     *                  @OA\Property(property="title", type="string", example="title"),
     *                  @OA\Property(property="file", type="string", format="binary")
     *              )
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
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="file", type="string"),
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
            'title' => 'required',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('file');
        
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('student_documents', 'public');
            $data['file'] = $path;
        }

        $item = StudentDocument::create($data);
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
     *      path="/api/student-documents/{id}",
     *      operationId="getStudentDocumentById",
     *      tags={"4. Student Enrollment - StudentDocument"},
     *      summary="Get student document information",
     *      description="Returns student document data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudentDocument id",
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
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="file", type="string"),
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
        $item = StudentDocument::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/api/student-documents/{id}",
     *      operationId="updateStudentDocument",
     *      tags={"4. Student Enrollment - StudentDocument"},
     *      summary="Update existing student document (POST with _method=PUT for file uploads)",
     *      description="Returns updated student document data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudentDocument id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"student_id","title","_method"},
     *                  @OA\Property(property="_method", type="string", example="PUT", description="Required for updating via POST in Laravel multipart requests"),
     *                  @OA\Property(property="student_id", type="integer", example="1"),
     *                  @OA\Property(property="title", type="string", example="title"),
     *                  @OA\Property(property="file", type="string", format="binary")
     *              )
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
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="file", type="string"),
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
        $item = StudentDocument::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'title' => 'required',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['file', '_method']);
        
        if ($request->hasFile('file')) {
            // Delete old file if necessary (optional)
            // if ($item->file && \Storage::disk('public')->exists($item->file)) {
            //     \Storage::disk('public')->delete($item->file);
            // }
            $path = $request->file('file')->store('student_documents', 'public');
            $data['file'] = $path;
        }

        $item->update($data);
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
     *      path="/api/student-documents/{id}",
     *      operationId="deleteStudentDocument",
     *      tags={"4. Student Enrollment - StudentDocument"},
     *      summary="Delete existing student document",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="StudentDocument id",
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
        $item = StudentDocument::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
