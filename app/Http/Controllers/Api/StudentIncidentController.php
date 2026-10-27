<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Incident;
use App\Models\StudentIncident;
use App\Models\Classes;
use App\Models\Section;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentIncidentController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/student-incidents",
     *      operationId="getStudentIncidentsList",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="List assigned student incidents (Behaviour Records)",
     *      description="Fetches assigned behaviour incidents. Can filter by student_id, class_id, section_id, or date range.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="student_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="date_from", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="date_to", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="summary", type="object",
     *                  @OA\Property(property="total_points", type="integer", example=15),
     *                  @OA\Property(property="positive_points", type="integer", example=20),
     *                  @OA\Property(property="negative_points", type="integer", example=5),
     *                  @OA\Property(property="total_incidents", type="integer", example=3)
     *              ),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $query = StudentIncident::query()
            ->with([
                'student:id,user_id,admission_no,first_name,last_name,full_name,roll_no,class_id,section_id,photo',
                'student.class:id,name',
                'student.section:id,name',
                'incident:id,title,point,is_negative,description',
                'assignedBy:id,name,full_name,email',
            ]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('class_id') || $request->filled('section_id')) {
            $query->whereHas('student', function ($sq) use ($request) {
                if ($request->filled('class_id')) {
                    $sq->where('class_id', $request->input('class_id'));
                }
                if ($request->filled('section_id')) {
                    $sq->where('section_id', $request->input('section_id'));
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        if ($request->user() && $request->user()->branch_id) {
            $query->where(function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id)
                  ->orWhereNull('branch_id');
            });
        }

        $records = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();

        // Calculate points summary
        $positivePoints = $records->where('is_negative', false)->sum('point');
        $negativePoints = $records->where('is_negative', true)->sum('point');
        $totalPoints    = $positivePoints - $negativePoints;

        return response()->json([
            'success' => true,
            'summary' => [
                'total_points'    => $totalPoints,
                'positive_points' => $positivePoints,
                'negative_points' => $negativePoints,
                'total_incidents' => $records->count(),
            ],
            'data'    => $records
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/students/{student_id}/incidents",
     *      operationId="getStudentAssignedIncidents",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Get assigned incidents for a specific student",
     *      description="Fetches student info, total behaviour points summary, and list of assigned incidents.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="student_id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Student not found")
     * )
     */
    public function getByStudent($studentId)
    {
        $student = Student::with(['class:id,name', 'section:id,name'])->findOrFail($studentId);

        $records = StudentIncident::where('student_id', $studentId)
            ->with([
                'incident:id,title,point,is_negative,description',
                'assignedBy:id,name,full_name,email',
            ])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $positivePoints = $records->where('is_negative', false)->sum('point');
        $negativePoints = $records->where('is_negative', true)->sum('point');
        $totalPoints    = $positivePoints - $negativePoints;

        return response()->json([
            'success' => true,
            'student' => [
                'id'           => $student->id,
                'full_name'    => $student->full_name,
                'first_name'   => $student->first_name,
                'last_name'    => $student->last_name,
                'admission_no' => $student->admission_no,
                'roll_no'      => $student->roll_no,
                'class'        => $student->class ? $student->class->name : null,
                'section'      => $student->section ? $student->section->name : null,
                'photo'        => $student->photo,
            ],
            'summary' => [
                'total_points'    => $totalPoints,
                'positive_points' => $positivePoints,
                'negative_points' => $negativePoints,
                'total_incidents' => $records->count(),
            ],
            'data'    => $records
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/student-incidents",
     *      operationId="assignStudentIncident",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Assign incident to student(s) (Modal ASSIGN action)",
     *      description="Assigns an incident to one or multiple students, recording the remarks and behaviour points.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"incident_id"},
     *              @OA\Property(property="incident_id", type="integer", example=1),
     *              @OA\Property(property="student_id", type="integer", example=5, description="Single student ID"),
     *              @OA\Property(property="student_ids", type="array", @OA\Items(type="integer"), description="Or multiple student IDs for bulk assign"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-10-16"),
     *              @OA\Property(property="description", type="string", example="Custom remark note for this incident")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Incident assigned successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'incident_id'  => 'required|exists:incidents,id',
            'student_id'   => 'required_without:student_ids|nullable|exists:students,id',
            'student_ids'  => 'required_without:student_id|nullable|array',
            'student_ids.*'=> 'exists:students,id',
            'date'         => 'nullable|date',
            'description'  => 'nullable|string',
            'point'        => 'nullable|integer',
            'branch_id'    => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $incident = Incident::findOrFail($request->input('incident_id'));

        $studentIds = [];
        if ($request->filled('student_id')) {
            $studentIds[] = $request->input('student_id');
        } elseif (!empty($request->input('student_ids'))) {
            $studentIds = $request->input('student_ids');
        }

        $assignedBy = $request->user() ? $request->user()->id : null;
        $branchId   = $request->input('branch_id') ?? ($request->user() ? $request->user()->branch_id : $incident->branch_id);
        $date       = $request->filled('date') ? Carbon::parse($request->input('date'))->toDateString() : now()->toDateString();
        $point      = $request->filled('point') ? (int)$request->input('point') : (int)$incident->point;
        $desc       = $request->filled('description') ? $request->input('description') : $incident->description;

        $createdRecords = [];
        foreach ($studentIds as $sId) {
            $record = StudentIncident::create([
                'branch_id'   => $branchId,
                'student_id'  => $sId,
                'incident_id' => $incident->id,
                'title'       => $incident->title,
                'point'       => $point,
                'is_negative' => $incident->is_negative,
                'date'        => $date,
                'description' => $desc,
                'assigned_by' => $assignedBy,
            ]);

            $record->load(['incident:id,title,point,is_negative', 'assignedBy:id,name,full_name,email']);
            $createdRecords[] = $record;
        }

        return response()->json([
            'success' => true,
            'message' => 'Incident assigned successfully',
            'data'    => count($createdRecords) === 1 ? $createdRecords[0] : $createdRecords
        ], 201);
    }

    /**
     * Shortcut to assign directly via /api/students/{student_id}/incidents
     */
    public function assignToStudent(Request $request, $studentId)
    {
        $request->merge(['student_id' => $studentId]);
        return $this->store($request);
    }

    /**
     * @OA\Get(
     *      path="/api/student-incidents/{id}",
     *      operationId="getStudentIncidentById",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Get single assigned incident record",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Record not found")
     * )
     */
    public function show($id)
    {
        $record = StudentIncident::with([
            'student:id,admission_no,first_name,last_name,full_name,roll_no,class_id,section_id',
            'incident',
            'assignedBy:id,name,full_name,email'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $record
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/student-incidents/{id}",
     *      operationId="updateStudentIncident",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Update assigned incident record",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="date", type="string", format="date"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="point", type="integer")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Record updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $record = StudentIncident::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date'        => 'sometimes|nullable|date',
            'description' => 'sometimes|nullable|string',
            'point'       => 'sometimes|nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $record->update($request->only(['date', 'description', 'point']));

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully',
            'data'    => $record
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/student-incidents/{id}",
     *      operationId="deleteStudentIncident",
     *      tags={"Student Behaviour - Incidents"},
     *      summary="Delete assigned incident from student (ACTIONS column delete)",
     *      description="Deletes the assigned incident record from the student behaviour log.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Assigned incident deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $record = StudentIncident::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assigned incident deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/reports/remarks-report",
     *      operationId="getRemarksReport",
     *      tags={"Student Behaviour - Reports"},
     *      summary="Remarks Report Page / Incident Wise Report",
     *      description="Fetches behavior remarks filtered by Class, Section, and Incident (Incident-wise). Matches Screenshot 5.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, description="Filter by class ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, description="Filter by section ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="incident_id", in="query", required=false, description="Filter by incident ID (Incident wise)", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="date_from", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="date_to", in="query", required=false, @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search student name or admission number", @OA\Schema(type="string")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function remarksReport(Request $request)
    {
        $query = StudentIncident::query()
            ->with([
                'student:id,user_id,admission_no,first_name,last_name,full_name,roll_no,class_id,section_id,photo',
                'student.class:id,name',
                'student.section:id,name',
                'incident:id,title,point,is_negative,description',
                'assignedBy:id,name,full_name,email',
            ]);

        if ($request->filled('class_id') || $request->filled('section_id')) {
            $query->whereHas('student', function ($sq) use ($request) {
                if ($request->filled('class_id')) {
                    $sq->where('class_id', $request->input('class_id'));
                }
                if ($request->filled('section_id')) {
                    $sq->where('section_id', $request->input('section_id'));
                }
            });
        }

        if ($request->filled('incident_id')) {
            $query->where('incident_id', $request->input('incident_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('admission_no', 'like', "%{$search}%")
                         ->orWhere('roll_no', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        if ($request->user() && $request->user()->branch_id) {
            $query->where(function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id)
                  ->orWhereNull('branch_id');
            });
        }

        $perPage = $request->input('per_page', 15);
        $paginated = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate($perPage);

        $paginated->getCollection()->transform(function ($item) {
            $student = $item->student;
            $class = $student && $student->class ? $student->class->name : '';
            $section = $student && $student->section ? $student->section->name : '';
            return [
                'id'              => $item->id,
                'student_id'      => $item->student_id,
                'admission_no'    => $student ? $student->admission_no : null,
                'student_name'    => $student ? ($student->full_name ?: trim($student->first_name . ' ' . $student->last_name)) : 'Unknown',
                'photo'           => $student ? $student->photo : null,
                'class_name'      => $class,
                'section_name'    => $section,
                'class_section'   => trim($class . ' ' . $section),
                'incident_id'     => $item->incident_id,
                'incident_title'  => $item->title ?: ($item->incident ? $item->incident->title : 'N/A'),
                'point'           => $item->point,
                'is_negative'     => $item->is_negative,
                'point_display'   => ($item->is_negative ? '-' : '+') . $item->point,
                'date'            => $item->date ? Carbon::parse($item->date)->format('d/m/Y') : null,
                'raw_date'        => $item->date ? Carbon::parse($item->date)->toDateString() : null,
                'description'     => $item->description,
                'assigned_by'     => $item->assignedBy ? ($item->assignedBy->full_name ?: $item->assignedBy->name) : 'Admin',
                'created_at'      => $item->created_at ? $item->created_at->toDateTimeString() : null,
            ];
        });

        // Filter dropdown helper options for convenient UI consumption
        $classes = Classes::select('id', 'name')->orderBy('name')->get();
        $incidents = Incident::select('id', 'title', 'point', 'is_negative')->orderBy('title')->get();

        return response()->json([
            'success'        => true,
            'data'           => $paginated,
            'filter_options' => [
                'classes'   => $classes,
                'incidents' => $incidents,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/reports/student-incidents",
     *      operationId="getStudentIncidentReport",
     *      tags={"Student Behaviour - Reports"},
     *      summary="Student Incident Report (Screenshot 3)",
     *      description="Table listing students with Total Incidents count and Net Points summary, filterable by class and section.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function studentIncidentReport(Request $request)
    {
        $query = Student::query()
            ->with(['class:id,name', 'section:id,name'])
            ->withCount('studentIncidents as total_incidents');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%");
            });
        }

        if (!$request->boolean('all_students')) {
            $query->has('studentIncidents');
        }

        $perPage = $request->input('per_page', 15);
        $paginated = $query->paginate($perPage);

        $paginated->getCollection()->transform(function ($student) {
            $incidents = StudentIncident::where('student_id', $student->id)->get();
            $pos = $incidents->where('is_negative', false)->sum('point');
            $neg = $incidents->where('is_negative', true)->sum('point');
            $totalPoints = $pos - $neg;

            return [
                'student_id'      => $student->id,
                'student_name'    => $student->full_name ?: trim($student->first_name . ' ' . $student->last_name),
                'admission_no'    => $student->admission_no,
                'roll_no'         => $student->roll_no,
                'class_name'      => $student->class ? $student->class->name : '',
                'section_name'    => $student->section ? $student->section->name : '',
                'class_section'   => trim(($student->class ? $student->class->name : '') . ' ' . ($student->section ? $student->section->name : '')),
                'total_incidents' => $student->total_incidents,
                'total_points'    => $totalPoints,
                'positive_points' => $pos,
                'negative_points' => $neg,
                'photo'           => $student->photo,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $paginated
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/reports/student-incidents/{student_id}",
     *      operationId="getStudentIncidentReportDetails",
     *      tags={"Student Behaviour - Reports"},
     *      summary="Incident Details Modal (Screenshot 3 Modal)",
     *      description="Returns incident details modal data for a single student.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="student_id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function studentIncidentReportDetails($studentId)
    {
        $student = Student::with(['class:id,name', 'section:id,name'])->findOrFail($studentId);

        $incidents = StudentIncident::where('student_id', $studentId)
            ->with(['assignedBy:id,name,full_name,email'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $sessionName = AcademicYear::where('is_active', 1)->value('title') ?? '2025 (Jun - Mar)';

        $details = $incidents->map(function ($inc) use ($sessionName) {
            return [
                'id'          => $inc->id,
                'title'       => $inc->title,
                'point'       => $inc->point,
                'is_negative' => $inc->is_negative,
                'point_text'  => ($inc->is_negative ? '-' : '+') . $inc->point,
                'session'     => $sessionName,
                'date'        => $inc->date ? Carbon::parse($inc->date)->format('d/m/Y') : null,
                'description' => $inc->description,
                'assigned_by' => $inc->assignedBy ? ($inc->assignedBy->full_name ?: $inc->assignedBy->name) : 'Admin',
            ];
        });

        $pos = $incidents->where('is_negative', false)->sum('point');
        $neg = $incidents->where('is_negative', true)->sum('point');

        return response()->json([
            'success' => true,
            'student' => [
                'id'            => $student->id,
                'student_name'  => $student->full_name ?: trim($student->first_name . ' ' . $student->last_name),
                'admission_no'  => $student->admission_no,
                'class_section' => trim(($student->class ? $student->class->name : '') . ' ' . ($student->section ? $student->section->name : '')),
                'photo'         => $student->photo,
                'total_points'  => $pos - $neg,
            ],
            'incidents' => $details
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/reports/class-section-rank",
     *      operationId="getClassSectionRankReport",
     *      tags={"Student Behaviour - Reports"},
     *      summary="Class Section Wise Rank Report (Screenshot 4)",
     *      description="Lists ranked classes with breakdown of students by section [e.g. A(20), B(5)...] and Total Points.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function classSectionRankReport(Request $request)
    {
        $classes = Classes::with(['sections:id,class_id,name'])
            ->get();

        $rows = [];
        foreach ($classes as $class) {
            $studentIncidents = StudentIncident::whereHas('student', function ($sq) use ($class) {
                $sq->where('class_id', $class->id);
            })->get();

            $pos = $studentIncidents->where('is_negative', false)->sum('point');
            $neg = $studentIncidents->where('is_negative', true)->sum('point');
            $totalPoints = $pos - $neg;

            $sectionsBreakdown = [];
            $sectionParts = [];
            foreach ($class->sections as $sec) {
                $count = Student::where('class_id', $class->id)->where('section_id', $sec->id)->count();
                $sectionsBreakdown[] = [
                    'section_id'   => $sec->id,
                    'section_name' => $sec->name,
                    'count'        => $count,
                ];
                $sectionParts[] = $sec->name . ' (' . $count . ')';
            }

            $rows[] = [
                'class_id'           => $class->id,
                'class_name'         => $class->name,
                'sections_breakdown' => $sectionsBreakdown,
                'sections_display'   => implode('   ', $sectionParts),
                'total_points'       => $totalPoints,
            ];
        }

        // Sort by total points descending to establish rank
        usort($rows, fn($a, $b) => $b['total_points'] <=> $a['total_points']);

        $rank = 1;
        foreach ($rows as &$row) {
            $row['rank'] = $rank++;
        }
        unset($row);

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $rows = array_values(array_filter($rows, function ($r) use ($search) {
                return str_contains(strtolower($r['class_name']), $search) || str_contains(strtolower($r['sections_display']), $search);
            }));
        }

        return response()->json([
            'success' => true,
            'data'    => $rows
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/reports/class-section-rank/{class_id}",
     *      operationId="getClassSectionRankDetails",
     *      tags={"Student Behaviour - Reports"},
     *      summary="Class Section Wise Rank Report Details (Screenshot 4 Page 2)",
     *      description="Returns detailed student list for a class with Admission No, Student Name, Class & Section, Incident Description, and Point.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *      @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function classSectionRankDetails(Request $request, $classId)
    {
        $class = Classes::findOrFail($classId);

        $query = StudentIncident::whereHas('student', function ($sq) use ($classId) {
            $sq->where('class_id', $classId);
        })->with([
            'student:id,admission_no,first_name,last_name,full_name,class_id,section_id',
            'student.class:id,name',
            'student.section:id,name',
        ])->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 15);
        $paginated = $query->paginate($perPage);

        $paginated->getCollection()->transform(function ($item) {
            $student = $item->student;
            $className = $student && $student->class ? $student->class->name : '';
            $sectionName = $student && $student->section ? $student->section->name : '';
            return [
                'id'           => $item->id,
                'admission_no' => $student ? $student->admission_no : 'N/A',
                'student_name' => $student ? ($student->full_name ?: trim($student->first_name . ' ' . $student->last_name)) : 'Unknown',
                'class_section'=> trim($className . ' ' . $sectionName),
                'description'  => $item->description ?: ($item->title ?: 'N/A'),
                'point'        => $item->point,
                'point_display'=> ($item->is_negative ? '-' : '+') . $item->point,
                'date'         => $item->date ? Carbon::parse($item->date)->format('d/m/Y') : null,
            ];
        });

        return response()->json([
            'success' => true,
            'class'   => [
                'id'   => $class->id,
                'name' => $class->name,
            ],
            'data'    => $paginated
        ]);
    }
}
