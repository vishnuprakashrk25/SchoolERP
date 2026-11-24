<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamAttendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamAttendanceController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exam-attendances",
     *      operationId="getExamAttendanceList",
     *      tags={"6. Examinations & Assessment - Exam Attendance"},
     *      summary="Get Exam Attendance list / student roster",
     *      description="Fetches student attendance for an exam, class, section, and subject. If attendance hasn't been taken yet, returns student roster with default status 'P'.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="exam_id", in="query", required=true, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="class_id", in="query", required=true, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="subject_id", in="query", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="total_students", type="integer", example=25),
     *              @OA\Property(property="present_count", type="integer", example=23),
     *              @OA\Property(property="absent_count", type="integer", example=2),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $examId = $request->input('exam_id') ?? $request->input('exam_type_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        $subjectId = $request->input('subject_id');

        if (empty($examId) || empty($classId) || empty($subjectId)) {
            return response()->json([
                'success'        => true,
                'total_students' => 0,
                'present_count'  => 0,
                'absent_count'   => 0,
                'data'           => []
            ]);
        }

        // Fetch students for this class & section
        $studentQuery = Student::where('class_id', $classId);
        if (!empty($sectionId)) {
            $studentQuery->where('section_id', $sectionId);
        }
        $students = $studentQuery->orderBy('roll_no')->orderBy('first_name')->get();

        // Existing attendances
        $existing = ExamAttendance::where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->when(!empty($sectionId), fn($q) => $q->where('section_id', $sectionId))
            ->get()
            ->keyBy('student_id');

        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;

        $roster = $students->map(function ($stu) use ($existing, $examId, $classId, $sectionId, $subjectId, &$presentCount, &$absentCount, &$lateCount) {
            $rec = $existing->get($stu->id);
            $status = $rec ? $rec->status : 'P';
            $note = $rec ? $rec->note : null;

            if ($status === 'P') $presentCount++;
            elseif ($status === 'A') $absentCount++;
            elseif ($status === 'L') $lateCount++;

            return [
                'attendance_id' => $rec?->id,
                'student_id'    => $stu->id,
                'admission_no'  => $stu->admission_no,
                'roll_no'       => $stu->roll_no,
                'full_name'     => $stu->full_name,
                'photo'         => $stu->photo,
                'exam_id'       => (int)$examId,
                'class_id'      => (int)$classId,
                'section_id'    => $sectionId ? (int)$sectionId : null,
                'subject_id'    => (int)$subjectId,
                'status'        => $status, // 'P', 'A', 'L'
                'note'          => $note,
            ];
        });

        return response()->json([
            'success'        => true,
            'exam_id'        => (int)$examId,
            'class_id'       => (int)$classId,
            'section_id'     => $sectionId ? (int)$sectionId : null,
            'subject_id'     => (int)$subjectId,
            'total_students' => $roster->count(),
            'present_count'  => $presentCount,
            'absent_count'   => $absentCount,
            'late_count'     => $lateCount,
            'data'           => $roster
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/exam-attendances",
     *      operationId="storeExamAttendance",
     *      tags={"6. Examinations & Assessment - Exam Attendance"},
     *      summary="Store / bulk update Exam Attendance",
     *      description="Saves exam attendance records for multiple students.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"exam_id","class_id","subject_id","attendances"},
     *              @OA\Property(property="exam_id", type="integer", example=1),
     *              @OA\Property(property="class_id", type="integer", example=5),
     *              @OA\Property(property="section_id", type="integer", example=2),
     *              @OA\Property(property="subject_id", type="integer", example=3),
     *              @OA\Property(property="date", type="string", format="date", example="2026-11-20"),
     *              @OA\Property(
     *                  property="attendances",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="student_id", type="integer", example=101),
     *                      @OA\Property(property="status", type="string", example="P", description="P: Present, A: Absent, L: Late"),
     *                      @OA\Property(property="note", type="string", example="")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="Attendance saved successfully")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id'                 => 'required|integer',
            'class_id'                => 'required|integer',
            'section_id'              => 'nullable|integer',
            'subject_id'              => 'required|integer',
            'date'                    => 'nullable|date',
            'attendances'             => 'required|array|min:1',
            'attendances.*.student_id'=> 'required|integer',
            'attendances.*.status'    => 'required|string|in:P,A,L,p,a,l',
            'attendances.*.note'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $examId = $request->input('exam_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        $subjectId = $request->input('subject_id');
        $date = $request->input('date') ?? now()->toDateString();

        $saved = [];
        foreach ($request->input('attendances') as $row) {
            $studentId = $row['student_id'];
            $status = strtoupper($row['status']);
            $note = $row['note'] ?? null;

            $record = ExamAttendance::updateOrCreate(
                [
                    'exam_id'    => $examId,
                    'class_id'   => $classId,
                    'subject_id' => $subjectId,
                    'student_id' => $studentId,
                ],
                [
                    'section_id' => $sectionId,
                    'status'     => $status,
                    'note'       => $note,
                    'date'       => $date,
                ]
            );

            $saved[] = $record;
        }

        return response()->json([
            'success' => true,
            'message' => count($saved) . ' Exam Attendance record(s) saved successfully',
            'count'   => count($saved),
            'data'    => $saved
        ]);
    }
}
