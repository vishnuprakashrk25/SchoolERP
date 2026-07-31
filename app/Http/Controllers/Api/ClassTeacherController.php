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
     *              required={"class_id", "section_id", "teacher_ids"},
     *              @OA\Property(property="class_id", type="integer"),
     *              @OA\Property(property="section_id", type="integer"),
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
            'section_id' => 'required|exists:sections,id',
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
     * @OA\Delete(
     *      path="/api/class-teachers/{id}",
     *      operationId="removeTeacherAssignment",
     *      tags={"3. Academic Infrastructure - Class Teacher"},
     *      summary="Remove a teacher assignment from a class",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Assignment removed")
     * )
     */
    public function destroy($id)
    {
        $assignment = ClassTeacher::findOrFail($id);
        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignment removed successfully'
        ]);
    }
}
