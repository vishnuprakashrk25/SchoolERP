<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentAttendance;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    /**
     * Display a listing of student attendance with filters.
     *
     * @OA\Get(
     *      path="/api/student-attendances",
     *      operationId="getStudentAttendancesList",
     *      tags={"4. Student Enrollment - StudentAttendance"},
     *      summary="Get list of StudentAttendances with optional class, section, date, student_id, student_code, search filters",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, description="Filter by class_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, description="Filter by section_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, description="Filter by academic_year_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="student_id", in="query", required=false, description="Filter by student_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="student_code", in="query", required=false, description="Filter by student_code or admission_no", @OA\Schema(type="string")),
     *      @OA\Parameter(name="date", in="query", required=false, description="Filter by attendance date (YYYY-MM-DD)", @OA\Schema(type="string", format="date")),
     *      @OA\Parameter(name="status", in="query", required=false, description="Filter by status (Present, Late, Absent, Half Day, Leave)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by student name, roll_no, student_code, or admission_no", @OA\Schema(type="string")),
     *      @OA\Parameter(name="only_uploaded", in="query", required=false, description="Set to true to return only uploaded attendance records", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="student_id", type="integer"),
     *                      @OA\Property(property="class_id", type="integer"),
     *                      @OA\Property(property="section_id", type="integer"),
     *                      @OA\Property(property="academic_year_id", type="integer"),
     *                      @OA\Property(property="roll_no", type="string", example="102"),
     *                      @OA\Property(property="photo", type="string", example="http://localhost:8000/uploads/student_photos/example.jpg"),
     *                      @OA\Property(property="student_name", type="string", example="John Smith"),
     *                      @OA\Property(property="student_code", type="string", example="GS211"),
     *                      @OA\Property(property="admission_no", type="string", example="GS211"),
     *                      @OA\Property(property="date", type="string", format="date", example="2026-08-09"),
     *                      @OA\Property(property="status", type="string", example="Present"),
     *                      @OA\Property(property="note", type="string", example="Doctor appointment"),
     *                      @OA\Property(property="student", type="object"),
     *                      @OA\Property(property="class", type="object"),
     *                      @OA\Property(property="section", type="object"),
     *                      @OA\Property(property="academic_year", type="object")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    private function filterAttendanceData(array $data)
    {
        static $columns = null;
        if ($columns === null) {
            try {
                $columns = DB::getSchemaBuilder()->getColumnListing('student_attendances');
            } catch (\Throwable $e) {
                $columns = ['student_id', 'class_id', 'section_id', 'academic_year_id', 'date', 'status', 'note'];
            }
        }
        return array_intersect_key($data, array_flip($columns));
    }

    public function index(Request $request)
    {
        // When querying for roster/date (and not filtering by explicit status, student_id/code or only_uploaded), return student roster with attendance status (defaulting to 'Present')
        if (!$request->filled('status') && !$request->boolean('only_uploaded') && !$request->filled('student_id') && !$request->filled('student_code')) {
            $classId = $request->class_id;
            $sectionId = $request->section_id;
            $date = $request->date ?? date('Y-m-d');

            $studentsQuery = Student::query();
            if (!empty($classId)) {
                $studentsQuery->where('class_id', $classId);
            }
            if (!empty($sectionId)) {
                $studentsQuery->where('section_id', $sectionId);
            }
            if ($request->filled('academic_year_id')) {
                $studentsQuery->where('academic_year_id', $request->academic_year_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $studentsQuery->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_no', 'like', "%{$search}%")
                      ->orWhere('roll_no', 'like', "%{$search}%")
                      ->orWhere('student_code', 'like', "%{$search}%");
                });
            }

            $students = $studentsQuery->get();

            // Fetch existing attendance records for the given date
            $attendances = StudentAttendance::whereDate('date', $date)
                ->where(function ($q) use ($classId, $sectionId) {
                    if (!empty($classId) && DB::getSchemaBuilder()->hasColumn('student_attendances', 'class_id')) {
                        $q->where('class_id', $classId);
                        if (!empty($sectionId) && DB::getSchemaBuilder()->hasColumn('student_attendances', 'section_id')) {
                            $q->where('section_id', $sectionId);
                        }
                    }
                })
                ->get()
                ->keyBy('student_id');

            $result = $students->map(function ($student) use ($attendances, $date, $classId, $sectionId) {
                $att = $attendances->get($student->id);
                return [
                    'id' => $att?->id,
                    'student_id' => $student->id,
                    'class_id' => $att?->class_id ?? $student->class_id ?? $classId,
                    'section_id' => $att?->section_id ?? $student->section_id ?? $sectionId,
                    'academic_year_id' => $att?->academic_year_id ?? $student->academic_year_id,
                    'roll_no' => $student->roll_no,
                    'photo' => $student->photo,
                    'student_name' => $student->full_name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'student_code' => $student->student_code ?? $student->admission_no,
                    'admission_no' => $student->admission_no,
                    'date' => $att?->date ?? $date,
                    'status' => $att?->status ?? 'Present',
                    'note' => $att?->note ?? '',
                    'student' => $student,
                    'class' => $student->class,
                    'section' => $student->section,
                    'academic_year' => $student->academicYear
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        }

        $query = StudentAttendance::with(['student', 'class', 'section', 'academicYear']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('student_code')) {
            $code = $request->student_code;
            $query->whereHas('student', function ($q) use ($code) {
                $q->where('student_code', $code)
                  ->orWhere('admission_no', $code);
            });
        }

        if ($request->filled('class_id')) {
            $classId = $request->class_id;
            $query->where(function ($q) use ($classId) {
                if (DB::getSchemaBuilder()->hasColumn('student_attendances', 'class_id')) {
                    $q->where('class_id', $classId)
                      ->orWhereHas('student', fn($sq) => $sq->where('class_id', $classId));
                } else {
                    $q->whereHas('student', fn($sq) => $sq->where('class_id', $classId));
                }
            });
        }

        if ($request->filled('section_id')) {
            $sectionId = $request->section_id;
            $query->where(function ($q) use ($sectionId) {
                if (DB::getSchemaBuilder()->hasColumn('student_attendances', 'section_id')) {
                    $q->where('section_id', $sectionId)
                      ->orWhereHas('student', fn($sq) => $sq->where('section_id', $sectionId));
                } else {
                    $q->whereHas('student', fn($sq) => $sq->where('section_id', $sectionId));
                }
            });
        }

        if ($request->filled('academic_year_id')) {
            $yearId = $request->academic_year_id;
            if (DB::getSchemaBuilder()->hasColumn('student_attendances', 'academic_year_id')) {
                $query->where('academic_year_id', $yearId);
            } else {
                $query->whereHas('student', fn($sq) => $sq->where('academic_year_id', $yearId));
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%")
                  ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        $data = $query->latest('date')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store new student attendance (single or bulk array).
     *
     * @OA\Post(
     *      path="/api/student-attendances",
     *      operationId="storeStudentAttendance",
     *      tags={"4. Student Enrollment - StudentAttendance"},
     *      summary="Store student attendance (single record or array)",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"student_code","date","status"},
     *              @OA\Property(property="student_code", type="string", example="GS211"),
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-08-09"),
     *              @OA\Property(property="status", type="string", example="Present"),
     *              @OA\Property(property="note", type="string", example="Optional note")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Attendance saved successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $bulkItems = null;

        // Support bulk submission via {"attendances": [...]} or raw JSON array [...]
        if ($request->has('attendances') && is_array($request->attendances)) {
            $bulkItems = $request->attendances;
        } else {
            $rawJson = $request->json()->all();
            if (is_array($rawJson) && isset($rawJson[0]) && is_array($rawJson[0])) {
                $bulkItems = $rawJson;
            }
        }

        if ($bulkItems !== null) {
            $saved = [];
            $notFoundCodes = [];
            DB::beginTransaction();
            try {
                foreach ($bulkItems as $index => $item) {
                    $studentCode = isset($item['student_code']) ? trim($item['student_code']) : null;
                    $studentId = $item['student_id'] ?? null;

                    $student = null;
                    if (!empty($studentCode)) {
                        $student = Student::where('student_code', $studentCode)
                            ->orWhere('admission_no', $studentCode)
                            ->orWhere('roll_no', $studentCode)
                            ->first();
                        if (!$student && is_numeric($studentCode)) {
                            $student = Student::find($studentCode);
                        }
                    }
                    if (!$student && !empty($studentId)) {
                        $student = Student::find($studentId);
                    }

                    if (!$student) {
                        $notFoundCodes[] = $studentCode ?? ("ID " . ($studentId ?? 'N/A'));
                        continue;
                    }

                    $attributes = [
                        'student_id' => $student->id,
                        'date' => $item['date'] ?? $request->date ?? date('Y-m-d')
                    ];

                    $values = [
                        'class_id' => $item['class_id'] ?? $request->class_id ?? $student->class_id,
                        'section_id' => $item['section_id'] ?? $request->section_id ?? $student->section_id,
                        'academic_year_id' => $item['academic_year_id'] ?? $request->academic_year_id ?? $student->academic_year_id,
                        'status' => $item['status'] ?? 'Present',
                        'note' => $item['note'] ?? null
                    ];

                    $record = StudentAttendance::updateOrCreate(
                        $this->filterAttendanceData($attributes),
                        $this->filterAttendanceData($values)
                    );
                    $saved[] = $record;
                }

                if (empty($saved) && !empty($notFoundCodes)) {
                    DB::rollBack();
                    $uniqueNotFound = array_unique($notFoundCodes);
                    return response()->json([
                        'success' => false,
                        'message' => 'Student(s) not found for code(s): ' . implode(', ', $uniqueNotFound) . '. Please provide valid student_code or admission_no.'
                    ], 422);
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Bulk attendance saved successfully' . (!empty($notFoundCodes) ? ' (Some students were not found: ' . implode(', ', array_unique($notFoundCodes)) . ')' : ''),
                    'data' => StudentAttendance::with(['student', 'class', 'section', 'academicYear'])
                        ->whereIn('id', collect($saved)->pluck('id'))
                        ->get()
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save bulk attendance: ' . $e->getMessage()
                ], 500);
            }
        }

        // Single submission: lookup student by student_code or student_id
        $student = null;
        if ($request->filled('student_code')) {
            $studentCode = trim($request->student_code);
            $student = Student::where('student_code', $studentCode)
                ->orWhere('admission_no', $studentCode)
                ->orWhere('roll_no', $studentCode)
                ->first();
            if (!$student && is_numeric($studentCode)) {
                $student = Student::find($studentCode);
            }
        }
        if (!$student && $request->filled('student_id')) {
            $student = Student::find($request->student_id);
        }

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found. Please provide a valid student_code, admission_no, or student_id.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'status' => 'required|string',
            'note' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $attributes = [
            'student_id' => $student->id,
            'date' => $request->date
        ];

        $values = [
            'class_id' => $request->class_id ?? $student->class_id,
            'section_id' => $request->section_id ?? $student->section_id,
            'academic_year_id' => $request->academic_year_id ?? $student->academic_year_id,
            'status' => $request->status,
            'note' => $request->note
        ];

        $item = StudentAttendance::updateOrCreate(
            $this->filterAttendanceData($attributes),
            $this->filterAttendanceData($values)
        );

        $item->load(['student', 'class', 'section', 'academicYear']);

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Get attendance record by ID.
     *
     * @OA\Get(
     *      path="/api/student-attendances/{id}",
     *      operationId="getStudentAttendanceById",
     *      tags={"4. Student Enrollment - StudentAttendance"},
     *      summary="Get student attendance detail",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
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
    public function show($id)
    {
        $item = StudentAttendance::with(['student', 'class', 'section', 'academicYear'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update existing attendance record.
     *
     * @OA\Put(
     *      path="/api/student-attendances/{id}",
     *      operationId="updateStudentAttendance",
     *      tags={"4. Student Enrollment - StudentAttendance"},
     *      summary="Update existing student attendance",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="student_code", type="string", example="GS211"),
     *              @OA\Property(property="status", type="string", example="Present"),
     *              @OA\Property(property="note", type="string", example="Updated note"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-08-09")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Attendance updated successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $item = StudentAttendance::findOrFail($id);

        if ($request->filled('student_code')) {
            $studentObj = Student::where('student_code', $request->student_code)
                ->orWhere('admission_no', $request->student_code)
                ->first();
            if ($studentObj) {
                $request->merge(['student_id' => $studentObj->id]);
            }
        }

        $validator = Validator::make($request->all(), [
            'student_code' => 'nullable|string',
            'student_id' => 'nullable|exists:students,id',
            'date' => 'sometimes|required|date',
            'status' => 'sometimes|required|string',
            'note' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['student_id', 'class_id', 'section_id', 'academic_year_id', 'date', 'status', 'note']);
        $item->update($this->filterAttendanceData($updateData));
        $item->load(['student', 'class', 'section', 'academicYear']);

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'data' => $item
        ]);
    }

    /**
     * Remove attendance record.
     *
     * @OA\Delete(
     *      path="/api/student-attendances/{id}",
     *      operationId="deleteStudentAttendance",
     *      tags={"4. Student Enrollment - StudentAttendance"},
     *      summary="Delete attendance record",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted successfully")
     * )
     */
    /**
     * Bulk import attendance records via file or JSON.
     *
     * @OA\Post(
     *      path="/api/student-attendances/import",
     *      operationId="importStudentAttendance",
     *      tags={"4. Student Enrollment - StudentAttendance"},
     *      summary="Bulk import student attendance records",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="attendances",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="student_code", type="string", example="ADM101"),
     *                      @OA\Property(property="date", type="string", format="date", example="2025-05-05"),
     *                      @OA\Property(property="status", type="string", example="Present"),
     *                      @OA\Property(property="note", type="string", example="Imported note")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful import",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Attendance imported successfully")
     *          )
     *      )
     * )
     */
    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data);
            $imported = 0;

            DB::beginTransaction();
            try {
                foreach ($data as $row) {
                    if (count($row) < 3) continue;
                    $rowCombined = array_combine($header, $row);
                    $studentCode = $rowCombined['student_code'] ?? $rowCombined['admission_no'] ?? $row[0];
                    $date = $rowCombined['date'] ?? $row[1];
                    $status = $rowCombined['status'] ?? $row[2];
                    $note = $rowCombined['note'] ?? $row[3] ?? null;

                    $student = Student::where('student_code', $studentCode)
                        ->orWhere('admission_no', $studentCode)
                        ->first();
                    if (!$student) continue;

                    StudentAttendance::updateOrCreate(
                        $this->filterAttendanceData(['student_id' => $student->id, 'date' => $date]),
                        $this->filterAttendanceData([
                            'class_id' => $student->class_id,
                            'section_id' => $student->section_id,
                            'academic_year_id' => $student->academic_year_id,
                            'status' => $status,
                            'note' => $note
                        ])
                    );
                    $imported++;
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Successfully imported {$imported} attendance records"
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Import failed: ' . $e->getMessage()
                ], 500);
            }
        }

        return $this->store($request);
    }
}

