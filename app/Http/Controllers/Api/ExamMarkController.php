<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamMark;
use App\Models\ExamSetup;
use App\Models\MarkGrade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamMarkController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/exam-marks",
     *      operationId="getExamMarksList",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Mark Register: Get student marks roster for exam, class, section, and subject",
     *      description="Fetches student mark entries including mark distribution columns (Written, Practical, Viva, etc.), total marks, grade, GPA, and absent status.",
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
     *              @OA\Property(property="exam_setup", type="object"),
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
                'success'    => true,
                'exam_setup' => null,
                'data'       => []
            ]);
        }

        // Fetch Exam Setup if configured
        $examSetup = ExamSetup::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where(function ($q) use ($examId) {
                $q->where('exam_type_id', $examId)
                  ->orWhereNull('exam_type_id');
            })
            ->when(!empty($sectionId), fn($q) => $q->where(fn($sq) => $sq->where('section_id', $sectionId)->orWhereNull('section_id')))
            ->first();

        $defaultDistributions = $examSetup?->mark_distributions ?? [
            ['title' => 'Written', 'mark' => 70],
            ['title' => 'Practical', 'mark' => 20],
            ['title' => 'Viva', 'mark' => 10],
        ];
        $totalMaxMark = $examSetup?->total_mark ?? 100;
        $passMark = $examSetup?->pass_mark ?? 33;

        // Fetch Students
        $studentQuery = Student::where('class_id', $classId);
        if (!empty($sectionId)) {
            $studentQuery->where('section_id', $sectionId);
        }
        $students = $studentQuery->orderBy('roll_no')->orderBy('first_name')->get();

        // Existing Marks
        $existingMarks = ExamMark::where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->when(!empty($sectionId), fn($q) => $q->where('section_id', $sectionId))
            ->get()
            ->keyBy('student_id');

        $roster = $students->map(function ($stu) use ($existingMarks, $examId, $classId, $sectionId, $subjectId, $examSetup, $defaultDistributions) {
            $markRec = $existingMarks->get($stu->id);

            $distributions = $markRec?->marks_distribution ?? [];
            if (empty($distributions)) {
                // Initialize empty distribution marks matching setup
                foreach ($defaultDistributions as $dist) {
                    $distributions[$dist['title']] = 0;
                }
            }

            return [
                'mark_id'            => $markRec?->id,
                'student_id'         => $stu->id,
                'admission_no'       => $stu->admission_no,
                'roll_no'            => $stu->roll_no,
                'full_name'          => $stu->full_name,
                'photo'              => $stu->photo,
                'exam_id'            => (int)$examId,
                'class_id'           => (int)$classId,
                'section_id'         => $sectionId ? (int)$sectionId : null,
                'subject_id'         => (int)$subjectId,
                'exam_setup_id'      => $examSetup?->id,
                'marks_distribution' => $distributions,
                'total_marks'        => $markRec?->total_marks ?? 0,
                'grade'              => $markRec?->grade ?? '',
                'gpa'                => $markRec?->gpa ?? 0.0,
                'is_absent'          => (bool)($markRec?->is_absent ?? false),
                'remarks'            => $markRec?->remarks ?? '',
            ];
        });

        return response()->json([
            'success'            => true,
            'exam_id'            => (int)$examId,
            'class_id'           => (int)$classId,
            'section_id'         => $sectionId ? (int)$sectionId : null,
            'subject_id'         => (int)$subjectId,
            'total_students'     => $roster->count(),
            'exam_setup'         => [
                'id'                 => $examSetup?->id,
                'exam_title'         => $examSetup?->exam_title,
                'total_mark'         => $totalMaxMark,
                'pass_mark'          => $passMark,
                'mark_distributions' => $defaultDistributions,
            ],
            'data'               => $roster
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/exam-marks",
     *      operationId="storeExamMarks",
     *      tags={"6. Examinations & Assessment - Exam"},
     *      summary="Store / bulk update marks in Mark Register",
     *      description="Saves marks for multiple students. Automatically calculates total marks, grade, and GPA based on Mark Grade configuration.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"exam_id","class_id","subject_id","marks"},
     *              @OA\Property(property="exam_id", type="integer", example=1),
     *              @OA\Property(property="class_id", type="integer", example=5),
     *              @OA\Property(property="section_id", type="integer", example=2),
     *              @OA\Property(property="subject_id", type="integer", example=3),
     *              @OA\Property(property="exam_setup_id", type="integer", example=1),
     *              @OA\Property(
     *                  property="marks",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="student_id", type="integer", example=101),
     *                      @OA\Property(property="marks_distribution", type="object", example={"Written": 45, "Practical": 20, "Viva": 22}),
     *                      @OA\Property(property="total_marks", type="number", example=87),
     *                      @OA\Property(property="grade", type="string", example="A+"),
     *                      @OA\Property(property="gpa", type="number", example=5.0),
     *                      @OA\Property(property="is_absent", type="boolean", example=false),
     *                      @OA\Property(property="remarks", type="string", example="Good performance")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="Marks saved successfully")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id'                 => 'required|integer',
            'class_id'                => 'required|integer',
            'section_id'              => 'nullable|integer',
            'subject_id'              => 'required|integer',
            'exam_setup_id'           => 'nullable|integer',
            'marks'                   => 'required|array|min:1',
            'marks.*.student_id'      => 'required|integer',
            'marks.*.total_marks'     => 'nullable|numeric|min:0',
            'marks.*.is_absent'       => 'nullable|boolean',
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
        $examSetupId = $request->input('exam_setup_id');

        // All grades for automatic lookup
        $allGrades = MarkGrade::orderBy('percent_from', 'desc')->get();

        $saved = [];
        foreach ($request->input('marks') as $row) {
            $studentId = $row['student_id'];
            $isAbsent = !empty($row['is_absent']);
            $distribution = $row['marks_distribution'] ?? [];

            // Calculate total marks: from sum of distribution or provided total
            if (!empty($distribution) && is_array($distribution)) {
                $calcTotal = 0;
                foreach ($distribution as $k => $val) {
                    $calcTotal += (float)$val;
                }
                $totalMarks = $calcTotal;
            } else {
                $totalMarks = (float)($row['total_marks'] ?? 0);
            }

            // Grade & GPA calculation
            $grade = $row['grade'] ?? null;
            $gpa = $row['gpa'] ?? null;

            if ($isAbsent) {
                $grade = 'F';
                $gpa = 0.0;
                $totalMarks = 0;
            } elseif (empty($grade) && $allGrades->isNotEmpty()) {
                // Compute percentage assuming out of 100 or setup total
                $percent = $totalMarks;
                foreach ($allGrades as $g) {
                    if ($percent >= $g->percent_from && $percent <= $g->percent_to) {
                        $grade = $g->grade_name;
                        $gpa = $g->gpa;
                        break;
                    }
                }
            }

            $record = ExamMark::updateOrCreate(
                [
                    'exam_id'    => $examId,
                    'class_id'   => $classId,
                    'subject_id' => $subjectId,
                    'student_id' => $studentId,
                ],
                [
                    'section_id'         => $sectionId,
                    'exam_setup_id'      => $examSetupId,
                    'marks_distribution' => $distribution,
                    'total_marks'        => $totalMarks,
                    'grade'              => $grade,
                    'gpa'                => $gpa,
                    'is_absent'          => $isAbsent,
                    'remarks'            => $row['remarks'] ?? null,
                ]
            );

            $saved[] = $record;
        }

        return response()->json([
            'success' => true,
            'message' => count($saved) . ' Mark record(s) saved successfully',
            'count'   => count($saved),
            'data'    => $saved
        ]);
    }
}
