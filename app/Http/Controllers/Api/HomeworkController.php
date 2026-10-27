<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeworkController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/homeworks",
     *      operationId="getHomeworkList",
     *      tags={"Student - Home Work"},
     *      summary="List all homework assignments",
     *      description="Returns paginated list of homework assignments with filters for Class, Section, Subject, and Dates.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="subject_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="homework_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="submission_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *      @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 15);
        $query = Homework::with([
            'class:id,name',
            'section:id,name',
            'subject:id,name',
            'createdBy:id,name,email',
            'evaluatedBy:id,name,email',
        ])->withCount([
            'submissions',
            'submissions as evaluated_count' => function ($q) {
                $q->whereIn('status', ['evaluated', 'completed']);
            }
        ]);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        if ($request->filled('homework_date')) {
            $query->whereDate('homework_date', $request->input('homework_date'));
        }

        if ($request->filled('submission_date')) {
            $query->whereDate('submission_date', $request->input('submission_date'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('class', fn ($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('section', fn ($secQ) => $secQ->where('name', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('homework_date', 'desc')->orderBy('id', 'desc');

        if ($limit == -1 || strtolower((string)$limit) === 'all') {
            $items = $query->get()->map(fn ($hw) => $this->formatHomeworkItem($hw));
            return response()->json([
                'success' => true,
                'data'    => $items,
                'meta'    => [
                    'total' => count($items)
                ]
            ]);
        }

        $paginated = $query->paginate((int)$limit);
        $items = collect($paginated->items())->map(fn ($hw) => $this->formatHomeworkItem($hw));

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/homeworks",
     *      operationId="storeHomework",
     *      tags={"Student - Home Work"},
     *      summary="Create a new homework assignment",
     *      description="Creates a homework assignment for a specific Class, Section, and Subject. Supports attachment file up to 100MB.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"class_id", "section_id", "subject_id", "homework_date", "submission_date", "marks", "description"},
     *                  @OA\Property(property="class_id", type="integer", example=1),
     *                  @OA\Property(property="section_id", type="integer", example=1),
     *                  @OA\Property(property="subject_id", type="integer", example=1),
     *                  @OA\Property(property="homework_date", type="string", format="date", example="2025-11-03"),
     *                  @OA\Property(property="submission_date", type="string", format="date", example="2025-11-12"),
     *                  @OA\Property(property="marks", type="number", example=40),
     *                  @OA\Property(property="description", type="string", example="Complete exercise 4.2 in workbook"),
     *                  @OA\Property(property="file", type="string", format="binary", description="Max 100MB file attachment")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Homework created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id'        => 'required|exists:classes,id',
            'section_id'      => 'required|exists:sections,id',
            'subject_id'      => 'required|exists:subjects,id',
            'homework_date'   => 'required|date',
            'submission_date' => 'required|date|after_or_equal:homework_date',
            'marks'           => 'required|numeric|min:0',
            'description'     => 'required|string',
            'file'            => 'nullable|file|max:102400', // 100MB limit
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/homework'), $filename);
            $filePath = 'uploads/homework/' . $filename;
        }

        // Auto resolve branch and academic year
        $classObj = Classes::find($request->class_id);
        $branchId = $classObj?->branch_id ?? auth()->user()?->branch_id;
        $academicYearId = $classObj?->academic_year_id ?? (AcademicYear::where('is_current', true)->value('id') ?? GeneralSetting::first()?->academic_year_id);

        $homework = Homework::create([
            'branch_id'        => $branchId,
            'academic_year_id' => $academicYearId,
            'class_id'         => $request->class_id,
            'section_id'       => $request->section_id,
            'subject_id'       => $request->subject_id,
            'created_by'       => auth()->id(),
            'homework_date'    => $request->homework_date,
            'submission_date'  => $request->submission_date,
            'marks'            => $request->marks,
            'description'      => $request->description,
            'file'             => $filePath,
            'status'           => 'active',
        ]);

        // Auto-initialize pending submissions for all active students in this class & section
        $students = Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', 1)
            ->get();

        foreach ($students as $student) {
            HomeworkSubmission::firstOrCreate([
                'homework_id' => $homework->id,
                'student_id'  => $student->id,
            ], [
                'status' => 'pending',
            ]);
        }

        $homework->load(['class:id,name', 'section:id,name', 'subject:id,name', 'createdBy:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Homework created successfully',
            'data'    => $this->formatHomeworkItem($homework)
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/homeworks/{id}",
     *      operationId="getHomeworkById",
     *      tags={"Student - Home Work"},
     *      summary="Get homework details by ID",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Homework not found")
     * )
     */
    public function show($id)
    {
        $homework = Homework::with([
            'class:id,name',
            'section:id,name',
            'subject:id,name',
            'createdBy:id,name,email',
            'evaluatedBy:id,name,email',
            'submissions.student:id,full_name,admission_no,roll_no'
        ])->find($id);

        if (!$homework) {
            return response()->json(['success' => false, 'message' => 'Homework not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatHomeworkItem($homework, true)
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/homeworks/{id}",
     *      operationId="updateHomework",
     *      tags={"Student - Home Work"},
     *      summary="Update homework details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="subject_id", type="integer"),
     *                  @OA\Property(property="homework_date", type="string", format="date"),
     *                  @OA\Property(property="submission_date", type="string", format="date"),
     *                  @OA\Property(property="marks", type="number"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="file", type="string", format="binary")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="Homework updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $homework = Homework::find($id);
        if (!$homework) {
            return response()->json(['success' => false, 'message' => 'Homework not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'class_id'        => 'nullable|exists:classes,id',
            'section_id'      => 'nullable|exists:sections,id',
            'subject_id'      => 'nullable|exists:subjects,id',
            'homework_date'   => 'nullable|date',
            'submission_date' => 'nullable|date',
            'marks'           => 'nullable|numeric|min:0',
            'description'     => 'nullable|string',
            'file'            => 'nullable|file|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['class_id', 'section_id', 'subject_id', 'homework_date', 'submission_date', 'marks', 'description', 'status']);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/homework'), $filename);
            $data['file'] = 'uploads/homework/' . $filename;
        }

        $homework->update($data);
        $homework->load(['class:id,name', 'section:id,name', 'subject:id,name', 'createdBy:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Homework updated successfully',
            'data'    => $this->formatHomeworkItem($homework)
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/homeworks/{id}",
     *      operationId="deleteHomework",
     *      tags={"Student - Home Work"},
     *      summary="Delete a homework assignment",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Homework deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $homework = Homework::find($id);
        if (!$homework) {
            return response()->json(['success' => false, 'message' => 'Homework not found'], 404);
        }

        HomeworkSubmission::where('homework_id', $homework->id)->delete();
        $homework->delete();

        return response()->json([
            'success' => true,
            'message' => 'Homework deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/homework-report",
     *      operationId="getHomeworkReport",
     *      tags={"Student - Home Work"},
     *      summary="Get Homework Report List (Student-wise)",
     *      description="Fetches student-wise homework submissions with status and marks for the Homework Report screen.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="subject_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="homework_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *      @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function report(Request $request)
    {
        $limit = $request->input('limit', 15);

        $query = HomeworkSubmission::with([
            'homework' => function ($hq) {
                $hq->with(['class:id,name', 'section:id,name', 'subject:id,name', 'createdBy:id,name,email', 'evaluatedBy:id,name,email']);
            },
            'student:id,full_name,first_name,last_name,admission_no,roll_no,class_id,section_id',
            'evaluatedBy:id,name,email'
        ]);

        if ($request->filled('class_id')) {
            $classId = $request->input('class_id');
            $query->whereHas('homework', fn ($hq) => $hq->where('class_id', $classId));
        }

        if ($request->filled('section_id')) {
            $sectionId = $request->input('section_id');
            $query->whereHas('homework', fn ($hq) => $hq->where('section_id', $sectionId));
        }

        if ($request->filled('subject_id')) {
            $subjectId = $request->input('subject_id');
            $query->whereHas('homework', fn ($hq) => $hq->where('subject_id', $subjectId));
        }

        if ($request->filled('homework_date')) {
            $hwDate = $request->input('homework_date');
            $query->whereHas('homework', fn ($hq) => $hq->whereDate('homework_date', $hwDate));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('full_name', 'like', "%{$search}%")
                   ->orWhere('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name', 'like', "%{$search}%")
                   ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        if ($limit == -1 || strtolower((string)$limit) === 'all') {
            $items = $query->get()->map(fn ($sub) => $this->formatReportItem($sub));
            return response()->json([
                'success' => true,
                'data'    => $items,
                'meta'    => ['total' => count($items)]
            ]);
        }

        $paginated = $query->paginate((int)$limit);
        $items = collect($paginated->items())->map(fn ($sub) => $this->formatReportItem($sub));

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/homework-report/evaluation/{submission_id}",
     *      operationId="getEvaluationSummaryModal",
     *      tags={"Student - Home Work"},
     *      summary="Get Evaluation Summary modal details",
     *      description="Fetches full details for the Evaluation Summary modal (Screenshot 2, screen 3).",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="submission_id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Submission not found")
     * )
     */
    public function evaluationSummary($submissionId)
    {
        $submission = HomeworkSubmission::with([
            'homework' => function ($hq) {
                $hq->with(['class:id,name', 'section:id,name', 'subject:id,name', 'academicYear:id,name', 'createdBy:id,name,email', 'evaluatedBy:id,name,email']);
            },
            'student:id,full_name,admission_no,roll_no,class_id,section_id',
            'evaluatedBy:id,name,email'
        ])->find($submissionId);

        if (!$submission || !$submission->homework) {
            return response()->json(['success' => false, 'message' => 'Evaluation record not found'], 404);
        }

        $hw = $submission->homework;
        $totalMarks = (float)($hw->marks ?? 100);
        $obtainedMarks = $submission->marks !== null ? (float)$submission->marks : null;
        $percentage = ($obtainedMarks !== null && $totalMarks > 0) ? round(($obtainedMarks / $totalMarks) * 100, 1) : null;
        $isPass = $percentage !== null ? ($percentage >= 40) : null;

        $createdByName = $hw->createdBy?->name ?? 'Admin';
        $evaluatedByName = $submission->evaluatedBy?->name ?? ($hw->evaluatedBy?->name ?? 'Pending Evaluation');

        $evaluationDate = $submission->evaluation_date ? \Carbon\Carbon::parse($submission->evaluation_date)->format('d/m/Y') : ($hw->evaluation_date ? \Carbon\Carbon::parse($hw->evaluation_date)->format('d/m/Y') : null);

        $className = $hw->class?->name ?? 'Class 1';
        $sectionName = $hw->section?->name ?? 'A';
        $academicYearName = $hw->academicYear?->name ?? '2024-2025';

        return response()->json([
            'success' => true,
            'data'    => [
                // Modal Header
                'header' => [
                    'subject'          => $hw->subject?->name ?? 'Physics',
                    'class_section_ay' => "{$className} {$sectionName} ({$academicYearName})",
                    'marks_badge'      => $obtainedMarks !== null ? "{$obtainedMarks}/{$totalMarks}" : "Not Evaluated",
                    'status'           => ucfirst($submission->status ?? 'Completed'),
                ],

                // Homework Details Section
                'homework_details' => [
                    'homework_date'   => $hw->homework_date ? \Carbon\Carbon::parse($hw->homework_date)->format('d/m/Y') : null,
                    'submission_date' => $hw->submission_date ? \Carbon\Carbon::parse($hw->submission_date)->format('d/m/Y') : null,
                    'evaluation_date' => $evaluationDate ?? '-',
                ],

                // Provided Details Section
                'provided_details' => [
                    'created_by'   => $createdByName,
                    'evaluated_by' => $evaluatedByName,
                ],

                // Student Details Section
                'student_details' => [
                    'student_name'    => $submission->student?->full_name ?? 'Student',
                    'admission_no'    => $submission->student?->admission_no ?? '-',
                    'class_section'   => "{$className} {$sectionName}",
                    'subject'         => $hw->subject?->name ?? 'Physics',
                ],

                // Summary Section
                'summary' => [
                    'marks'              => $totalMarks,
                    'obtained_marks'     => $obtainedMarks,
                    'marks_summary_text' => $obtainedMarks !== null ? "{$obtainedMarks} ({$percentage}%) " . ($isPass ? 'Pass' : 'Fail') : 'Pending',
                    'student_homework'   => $submission->submission_file ? asset($submission->submission_file) : ($hw->file ? asset($hw->file) : null),
                    'description'        => $submission->description ?? ($submission->student_notes ?? 'Well crafted'),
                ]
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/homeworks/{id}/evaluate",
     *      operationId="evaluateStudentHomework",
     *      tags={"Student - Home Work"},
     *      summary="Evaluate a student's homework submission",
     *      description="Allows a teacher to grade and provide feedback for a student's homework.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Homework ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_id", "marks"},
     *              @OA\Property(property="student_id", type="integer", example=5),
     *              @OA\Property(property="marks", type="number", example=20),
     *              @OA\Property(property="description", type="string", example="Well crafted"),
     *              @OA\Property(property="status", type="string", enum={"completed", "evaluated"}, example="completed")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Evaluation saved successfully")
     * )
     */
    public function evaluate(Request $request, $id)
    {
        $homework = Homework::find($id);
        if (!$homework) {
            return response()->json(['success' => false, 'message' => 'Homework not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'student_id'  => 'required|exists:students,id',
            'marks'       => 'required|numeric|min:0|max:' . $homework->marks,
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:completed,evaluated,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $submission = HomeworkSubmission::firstOrNew([
            'homework_id' => $homework->id,
            'student_id'  => $request->student_id,
        ]);

        $submission->marks = $request->marks;
        $submission->description = $request->description ?? 'Well crafted';
        $submission->status = $request->status ?? 'completed';
        $submission->evaluated_by = auth()->id();
        $submission->evaluation_date = now()->format('Y-m-d');
        if (empty($submission->submission_date)) {
            $submission->submission_date = now();
        }
        $submission->save();

        // Update homework overall evaluation date
        $homework->update([
            'evaluation_date' => now()->format('Y-m-d'),
            'evaluated_by'    => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Homework evaluated successfully',
            'data'    => $submission
        ]);
    }

    /**
     * Format a single homework record for list view
     */
    private function formatHomeworkItem($hw, $detailed = false)
    {
        $className = $hw->class?->name ?? '';
        $sectionName = $hw->section?->name ?? '';
        $classSection = trim("{$className} {$sectionName}");

        $data = [
            'id'               => $hw->id,
            'class_id'         => $hw->class_id,
            'class_name'       => $className,
            'section_id'       => $hw->section_id,
            'section_name'     => $sectionName,
            'class_section'    => $classSection,
            'subject_id'       => $hw->subject_id,
            'subject_name'     => $hw->subject?->name ?? '',
            'marks'            => (float)$hw->marks,
            'homework_date'    => $hw->homework_date ? \Carbon\Carbon::parse($hw->homework_date)->format('d/m/Y') : null,
            'submission_date'  => $hw->submission_date ? \Carbon\Carbon::parse($hw->submission_date)->format('d/m/Y') : null,
            'evaluation_date'  => $hw->evaluation_date ? \Carbon\Carbon::parse($hw->evaluation_date)->format('d/m/Y') : null,
            'created_by'       => $hw->createdBy?->name ?? 'Admin',
            'evaluated_by'     => $hw->evaluatedBy?->name ?? '-',
            'description'      => $hw->description,
            'file'             => $hw->file ? asset($hw->file) : null,
            'status'           => $hw->status ?? 'active',
            'submissions_count'=> $hw->submissions_count ?? 0,
            'evaluated_count'  => $hw->evaluated_count ?? 0,
            'created_at'       => $hw->created_at?->toIso8601String(),
        ];

        if ($detailed && $hw->relationLoaded('submissions')) {
            $data['submissions'] = $hw->submissions->map(function ($sub) use ($hw) {
                return [
                    'id'              => $sub->id,
                    'student_id'      => $sub->student_id,
                    'student_name'    => $sub->student?->full_name ?? 'Student',
                    'admission_no'    => $sub->student?->admission_no ?? '-',
                    'roll_no'         => $sub->student?->roll_no ?? '-',
                    'marks'           => $sub->marks !== null ? (float)$sub->marks : null,
                    'total_marks'     => (float)$hw->marks,
                    'status'          => ucfirst($sub->status ?? 'Pending'),
                    'submission_date' => $sub->submission_date ? \Carbon\Carbon::parse($sub->submission_date)->format('d/m/Y') : null,
                    'evaluation_date' => $sub->evaluation_date ? \Carbon\Carbon::parse($sub->evaluation_date)->format('d/m/Y') : null,
                    'description'     => $sub->description,
                    'submission_file' => $sub->submission_file ? asset($sub->submission_file) : null,
                ];
            });
        }

        return $data;
    }

    /**
     * Format a single report row
     */
    private function formatReportItem($sub)
    {
        $hw = $sub->homework;
        $totalMarks = (float)($hw?->marks ?? 100);
        $obtainedMarks = $sub->marks !== null ? (float)$sub->marks : null;
        $className = $hw?->class?->name ?? '';
        $sectionName = $hw?->section?->name ?? '';

        return [
            'id'              => $sub->id,
            'homework_id'     => $sub->homework_id,
            'student_id'      => $sub->student_id,
            'student_name'    => $sub->student?->full_name ?? 'Student',
            'admission_no'    => $sub->student?->admission_no ?? '-',
            'roll_no'         => $sub->student?->roll_no ?? '-',
            'class_section'   => trim("{$className} {$sectionName}"),
            'class_id'        => $hw?->class_id,
            'section_id'      => $hw?->section_id,
            'subject_name'    => $hw?->subject?->name ?? 'Physics',
            'marks_display'   => $obtainedMarks !== null ? "{$obtainedMarks}/{$totalMarks}" : "-/{$totalMarks}",
            'obtained_marks'  => $obtainedMarks,
            'total_marks'     => $totalMarks,
            'homework_date'   => $hw?->homework_date ? \Carbon\Carbon::parse($hw->homework_date)->format('d/m/Y') : null,
            'submission_date' => $hw?->submission_date ? \Carbon\Carbon::parse($hw->submission_date)->format('d/m/Y') : null,
            'evaluation_date' => $sub->evaluation_date ? \Carbon\Carbon::parse($sub->evaluation_date)->format('d/m/Y') : ($hw?->evaluation_date ? \Carbon\Carbon::parse($hw->evaluation_date)->format('d/m/Y') : null),
            'status'          => ucfirst($sub->status ?? 'Pending'), // Completed, Pending, Evaluated
            'has_evaluation'  => $obtainedMarks !== null,
        ];
    }
}
