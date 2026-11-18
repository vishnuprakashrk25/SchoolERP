<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\ClassTeacher;
use App\Models\Section;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClassTeacherController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/class-teachers",
     *      operationId="getClassTeachersList",
     *      tags={"3. Academic Infrastructure - Class Teacher"},
     *      summary="Get list of class teacher assignments",
     *      description="Returns a list of classes and sections with their assigned teachers",
     *      security={{"sanctum":{}}},
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
    public function index()
    {
        // Fetch classes with sections and the teachers assigned to those specific sections
        $assignments = Classes::with(['sections.classTeachers.teacher.user'])->get();

        // Transform data to match the "Class List" design if needed, 
        // but typically the frontend handles the mapping.
        
        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/class-teachers",
     *      operationId="assignClassTeacher",
     *      tags={"3. Academic Infrastructure - Class Teacher"},
     *      summary="Assign teachers to a class and section",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"class_id", "teacher_ids"},
     *              @OA\Property(property="class_id", type="integer"),
     *              @OA\Property(property="section_id", type="integer", nullable=true),
     *              @OA\Property(property="teacher_ids", type="array", @OA\Items(type="integer"))
     *          )
     *      ),
     *      @OA\Response(response=201, description="Teachers assigned"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'teacher_ids' => 'required|array',
            'teacher_ids.*' => 'exists:staff,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            // Optional: Clear existing assignments for this class/section if re-assigning
            // ClassTeacher::where('class_id', $request->class_id)
            //             ->where('section_id', $request->section_id)
            //             ->delete();

            foreach ($request->teacher_ids as $teacherId) {
                ClassTeacher::firstOrCreate([
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                    'teacher_id' => $teacherId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Teachers assigned to class successfully'
            ], 201);
        });
    }

    /**
     * @OA\Get(
     *      path="/api/class-teachers/{id}",
     *      operationId="getClassTeacherById",
     *      tags={"3. Academic Infrastructure - Class Teacher"},
     *      summary="Get a specific class teacher assignment",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Class Teacher assignment ID",
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
     *          description="Assignment not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Teacher assignment not found")
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        $assignment = ClassTeacher::with(['class', 'section', 'teacher.user'])->find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher assignment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $assignment
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/class-teachers/{id}",
     *      operationId="updateClassTeacher",
     *      tags={"3. Academic Infrastructure - Class Teacher"},
     *      summary="Update/Edit a class teacher assignment",
     *      description="Update an existing teacher assignment, or reassign teachers for a class and section",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Class Teacher assignment ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="teacher_id", type="integer", example=2, description="Single teacher ID to reassign this record to"),
     *              @OA\Property(property="teacher_ids", type="array", @OA\Items(type="integer"), description="Optional array of teacher IDs to sync all teachers for this class and section")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Assignment updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Class teacher assignment updated successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Assignment not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Teacher assignment not found")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $assignment = ClassTeacher::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher assignment not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'sometimes|required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'teacher_id' => 'sometimes|required|exists:staff,id',
            'teacher_ids' => 'sometimes|array',
            'teacher_ids.*' => 'exists:staff,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $classId = $request->input('class_id', $assignment->class_id);
        $sectionId = $request->has('section_id') ? $request->input('section_id') : $assignment->section_id;

        // If teacher_ids array is provided, sync all teachers for this class and section
        if ($request->has('teacher_ids') && is_array($request->input('teacher_ids'))) {
            $teacherIds = $request->input('teacher_ids');

            DB::transaction(function () use ($classId, $sectionId, $teacherIds) {
                ClassTeacher::where('class_id', $classId)
                    ->where('section_id', $sectionId)
                    ->whereNotIn('teacher_id', $teacherIds)
                    ->delete();

                foreach ($teacherIds as $tId) {
                    ClassTeacher::firstOrCreate([
                        'class_id' => $classId,
                        'section_id' => $sectionId,
                        'teacher_id' => $tId,
                    ]);
                }
            });

            $updated = ClassTeacher::with(['class', 'section', 'teacher.user'])
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Class teachers updated successfully',
                'data' => $updated
            ]);
        }

        // Single assignment update
        $data = [
            'class_id' => $classId,
            'section_id' => $sectionId,
        ];
        if ($request->has('teacher_id')) {
            $data['teacher_id'] = $request->input('teacher_id');
        }

        $assignment->update($data);
        $assignment->load(['class', 'section', 'teacher.user']);

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignment updated successfully',
            'data' => $assignment
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/class-teachers/{id}",
     *      operationId="removeTeacherAssignment",
     *      tags={"3. Academic Infrastructure - Class Teacher"},
     *      summary="Remove a teacher assignment from a class",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Assignment removed",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Teacher assignment removed successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Assignment not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Teacher assignment not found")
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        $assignment = ClassTeacher::find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher assignment not found'
            ], 404);
        }

        try {
            $assignment->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Fallback if soft delete column 'deleted_at' doesn't exist on live table
            if (str_contains(strtolower($e->getMessage()), 'deleted_at')) {
                $assignment->forceDelete();
            } else {
                throw $e;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignment removed successfully'
        ]);
    }
}
