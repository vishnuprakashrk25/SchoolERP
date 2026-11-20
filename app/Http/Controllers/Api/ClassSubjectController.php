<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ClassSubject;
use App\Models\Staff;

class ClassSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *      path="/api/class-subjects",
     *      operationId="getClassSubjectsList",
     *      tags={"3. Academic Infrastructure - ClassSubject"},
     *      summary="Get list of ClassSubjects",
     *      description="Returns list of ClassSubjects. Can be filtered by class_id, section_id, teacher_id, academic_year_id. Teachers automatically see only their assigned subjects.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="subject_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="teacher_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request)
    {
        $query = ClassSubject::with(['teacher.user', 'class', 'section', 'subject', 'academicYear']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        } else {
            $user = $request->user() ?? auth('sanctum')->user();
            if ($user && ($user->hasRole('teacher') || $user->hasRole('Teacher')) && !$user->hasAnyRole(['super-admin', 'admin', 'Super Admin', 'Admin'])) {
                $teacherId = $user->staff?->id ?? Staff::where('user_id', $user->id)->value('id');
                if ($teacherId) {
                    $query->where('teacher_id', $teacherId);
                }
            }
        }

        $data = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *      path="/api/class-subjects",
     *      operationId="storeClassSubject",
     *      tags={"3. Academic Infrastructure - ClassSubject"},
     *      summary="Store new class subject",
     *      description="Assign a teacher to a subject for a class and optional section",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"class_id", "subject_id", "teacher_id"},
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", nullable=true, example=1, description="Optional section ID (leave null for all sections of class)"),
     *              @OA\Property(property="subject_id", type="integer", example=1),
     *              @OA\Property(property="teacher_id", type="integer", example=1, description="Staff/Teacher ID"),
     *              @OA\Property(property="academic_year_id", type="integer", nullable=true, example=1, description="Optional academic year ID")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Class subject created successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item = ClassSubject::create([
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'academic_year_id' => $request->academic_year_id,
        ]);
        $item->load(['class', 'section', 'subject', 'teacher.user', 'academicYear']);

        return response()->json([
            'success' => true,
            'message' => 'Class subject created successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *      path="/api/class-subjects/{id}",
     *      operationId="getClassSubjectById",
     *      tags={"3. Academic Infrastructure - ClassSubject"},
     *      summary="Get class subject information",
     *      description="Returns class subject data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function show($id)
    {
        $item = ClassSubject::with(['class', 'section', 'subject', 'teacher.user', 'academicYear'])->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Class subject not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *      path="/api/class-subjects/{id}",
     *      operationId="updateClassSubject",
     *      tags={"3. Academic Infrastructure - ClassSubject"},
     *      summary="Update existing class subject",
     *      description="Returns updated class subject data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="subject_id", type="integer", example=1),
     *              @OA\Property(property="teacher_id", type="integer", example=1),
     *              @OA\Property(property="academic_year_id", type="integer", nullable=true, example=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Class subject updated successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $item = ClassSubject::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Class subject not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'sometimes|required|exists:classes,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'teacher_id' => 'sometimes|required|exists:staff,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $item->update($request->only([
            'class_id',
            'section_id',
            'subject_id',
            'teacher_id',
            'academic_year_id',
        ]));
        $item->load(['class', 'section', 'subject', 'teacher.user', 'academicYear']);

        return response()->json([
            'success' => true,
            'message' => 'Class subject updated successfully',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *      path="/api/class-subjects/{id}",
     *      operationId="deleteClassSubject",
     *      tags={"3. Academic Infrastructure - ClassSubject"},
     *      summary="Delete existing class subject",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Class subject removed successfully")
     *          )
     *      ),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function destroy($id)
    {
        $item = ClassSubject::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Class subject not found'
            ], 404);
        }

        try {
            $item->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains(strtolower($e->getMessage()), 'deleted_at')) {
                $item->forceDelete();
            } else {
                throw $e;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Class subject removed successfully'
        ]);
    }
}
