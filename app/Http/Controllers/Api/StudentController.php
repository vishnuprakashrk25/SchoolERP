<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\StudentAcademic;
use App\Models\StudentParent;
use App\Models\GeneralSetting;
use App\Models\AcademicYear;
use App\Models\Classes;

class StudentController extends Controller
{

    // Get all students
    /**
     * @OA\Get(
     *      path="/api/students",
     *      operationId="getStudentsList",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Get list of Students",
     *      description="Returns list of Students with optional filters: academic_year_id, class_id, section_id, search, category_id, student_group_id, gender, status",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, description="Filter by academic_year_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="class_id", in="query", required=false, description="Filter by class_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, description="Filter by section_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by name, roll_no, student_code, admission_no, email, phone, etc.", @OA\Schema(type="string")),
     *      @OA\Parameter(name="category_id", in="query", required=false, description="Filter by category_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="student_group_id", in="query", required=false, description="Filter by student_group_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="gender", in="query", required=false, description="Filter by gender", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status", in="query", required=false, description="Filter by status", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     *      @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property(property="current_page", type="integer"),
     *                  @OA\Property(property="last_page", type="integer"),
     *                  @OA\Property(property="per_page", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="admission_no", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="dob", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="category_id", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="status", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="apaar_id", type="string"),
     *                  @OA\Property(property="aadharshila_no", type="string"),
     *                  @OA\Property(property="pen_no", type="string"),
     *                  @OA\Property(property="emergency_phone", type="string"),
     *                  @OA\Property(property="alternate_phone", type="string"),
     *                  @OA\Property(property="student_group_id", type="integer"),
     *                  @OA\Property(property="medical_history", type="string"),
     *                  @OA\Property(property="is_disabled", type="boolean"),
     *                  @OA\Property(property="disable_reason", type="string"),
     *                  @OA\Property(property="disable_date", type="string"),
     *                  @OA\Property(property="route_id", type="integer"),
     *                  @OA\Property(property="vehicle_id", type="integer"),
     *                  @OA\Property(property="dormitory_id", type="integer"),
     *                  @OA\Property(property="room_id", type="integer"),
     *                  @OA\Property(property="previous_school_name", type="string"),
     *                  @OA\Property(property="previous_qualification", type="string"),
     *                  @OA\Property(
     *                      property="emergencyContacts",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="relation", type="string"),
     *                          @OA\Property(property="phone", type="string")
     *                      )
     *                  )
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
    public function index(Request $request)
    {
        $limit = $request->input('limit', 15);
        $query = Student::with([
            'user',
            'class',
            'section',
            'academicYear',
            'category',
            'parents',
            'studentAcademics.academicYear',
            'emergencyContacts'
        ]);

        // Academic Year, Class, Section Filters
        if ($request->filled('academic_year_id') || $request->filled('class_id') || $request->filled('section_id')) {
            $query->where(function ($mainQ) use ($request) {
                // Primary Branch: Match directly on students table
                $mainQ->where(function ($q) use ($request) {
                    if ($request->filled('academic_year_id')) {
                        $ayId = (int)$request->input('academic_year_id');
                        $defaultAyId = (int)(AcademicYear::where('is_current', true)->value('id') ?? GeneralSetting::first()?->academic_year_id ?? 0);
                        if ($ayId > 0 && $ayId === $defaultAyId) {
                            $q->where(function($ayQ) use ($ayId) {
                                $ayQ->where('students.academic_year_id', $ayId)
                                    ->orWhereNull('students.academic_year_id');
                            });
                        } else {
                            $q->where('students.academic_year_id', $ayId);
                        }
                    }
                    if ($request->filled('class_id')) {
                        $q->where('students.class_id', $request->input('class_id'));
                    }
                    if ($request->filled('section_id')) {
                        $q->where('students.section_id', $request->input('section_id'));
                    }
                });

                // Secondary Branch: Match via student_academics table
                $mainQ->orWhereHas('studentAcademics', function ($sq) use ($request) {
                    if ($request->filled('academic_year_id')) {
                        $sq->where('academic_year_id', $request->input('academic_year_id'));
                    }
                    if ($request->filled('class_id')) {
                        $sq->where('class_id', $request->input('class_id'));
                    }
                    if ($request->filled('section_id')) {
                        $sq->where('section_id', $request->input('section_id'));
                    }
                });
            });
        }

        // Additional Specific Filters
        if ($request->filled('category_id')) {
            $query->where('students.category_id', $request->input('category_id'));
        }

        if ($request->filled('student_group_id')) {
            $query->where('students.student_group_id', $request->input('student_group_id'));
        }

        if ($request->filled('gender')) {
            $query->where('students.gender', $request->input('gender'));
        }

        if ($request->filled('status')) {
            $query->where('students.status', $request->boolean('status'));
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $term = "%{$search}%";
            $words = array_filter(explode(' ', $search));

            $query->where(function ($q) use ($search, $term, $words) {
                $q->where('students.full_name', 'like', $term)
                  ->orWhere('students.first_name', 'like', $term)
                  ->orWhere('students.last_name', 'like', $term)
                  ->orWhere('students.admission_no', 'like', $term)
                  ->orWhere('students.student_code', 'like', $term)
                  ->orWhere('students.roll_no', 'like', $term)
                  ->orWhere('students.email', 'like', $term)
                  ->orWhere('students.phone', 'like', $term)
                  ->orWhere('students.apaar_id', 'like', $term)
                  ->orWhere('students.aadharshila_no', 'like', $term)
                  ->orWhere('students.pen_no', 'like', $term)
                  ->orWhere(DB::raw("CONCAT(COALESCE(students.first_name, ''), ' ', COALESCE(students.last_name, ''))"), 'like', $term);

                if (count($words) > 1) {
                    $q->orWhere(function ($subQ) use ($words) {
                        foreach ($words as $w) {
                            $wTerm = "%{$w}%";
                            $subQ->where(function ($wQ) use ($wTerm) {
                                $wQ->where('students.first_name', 'like', $wTerm)
                                   ->orWhere('students.last_name', 'like', $wTerm)
                                   ->orWhere('students.full_name', 'like', $wTerm);
                            });
                        }
                    });
                }
            });
        }

        if ($limit == -1 || strtolower((string)$limit) === 'all') {
            $items = $query->get();
            return response()->json([
                'success' => true,
                'data' => $items,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => count($items),
                    'total' => count($items),
                ]
            ]);
        }

        $students = $query->paginate((int)$limit);

        return response()->json([
            'success' => true,
            'data' => $students->items(),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ]
        ]);
    }

    // Create student
    /**
     * @OA\Post(
     *      path="/api/students",
     *      operationId="storeStudent",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Store new student",
     *      description="Returns saved student data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *              required={"admission_no","class_id","section_id"},
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
     *              @OA\Property(property="full_name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *              @OA\Property(property="gender", type="string", example="male"),
     *              @OA\Property(property="religion", type="string", example="Christianity"),
     *              @OA\Property(property="caste", type="string", example="General"),
     *              @OA\Property(property="phone", type="string", example="1234567890"),
     *              @OA\Property(property="blood_group", type="string", example="O+"),
     *              @OA\Property(property="height", type="string", example="150cm"),
     *              @OA\Property(property="weight", type="string", example="45kg"),
     *              @OA\Property(property="current_address", type="string", example="123 Main St"),
     *              @OA\Property(property="permanent_address", type="string", example="456 Other St"),
     *              @OA\Property(property="father_name", type="string", example="Richard Doe"),
     *              @OA\Property(property="father_phone", type="string", example="0987654321"),
     *              @OA\Property(property="father_occupation", type="string", example="Engineer"),
     *              @OA\Property(property="mother_name", type="string", example="Jane Doe"),
     *              @OA\Property(property="mother_phone", type="string", example="1122334455"),
     *              @OA\Property(property="mother_occupation", type="string", example="Teacher"),
     *              @OA\Property(property="guardian_name", type="string", example="Uncle Bob"),
     *              @OA\Property(property="guardian_phone", type="string", example="6677889900"),
     *              @OA\Property(property="guardian_relation", type="string", example="Uncle"),
     *              @OA\Property(property="bank_account_no", type="string", example="123456789"),
     *              @OA\Property(property="bank_name", type="string", example="Example Bank"),
     *              @OA\Property(property="ifsc_code", type="string", example="EXAMP001"),
     *              @OA\Property(property="national_id_no", type="string", example="NIC123456"),
     *              @OA\Property(property="local_id_no", type="string", example="LOC123456"),
     *              @OA\Property(property="birth_certificate_no", type="string", example="BC123456"),
     *              @OA\Property(property="apaar_id", type="string", example="APAAR123456"),
     *              @OA\Property(property="aadharshila_no", type="string", example="AADHAR123456"),
     *              @OA\Property(property="pen_no", type="string", example="PEN123456"),
     *              @OA\Property(property="emergency_phone", type="string", example="9988776655"),
     *              @OA\Property(property="father_email", type="string", example="father@example.com"),
     *              @OA\Property(property="mother_email", type="string", example="mother@example.com"),
     *              @OA\Property(property="dormitory_id", type="integer", example=1),
     *              @OA\Property(property="room_id", type="integer", example=1),
     *              @OA\Property(property="route_id", type="integer", example=1),
     *              @OA\Property(property="vehicle_id", type="integer", example=1),
     *              @OA\Property(property="alternate_phone", type="string", example="5566778899"),
     *              @OA\Property(property="student_group_id", type="integer", example=1),
     *              @OA\Property(property="medical_history", type="string", example="None"),
     *              @OA\Property(property="is_disabled", type="boolean", example=false),
     *              @OA\Property(property="disable_reason", type="string", example=""),
     *              @OA\Property(property="disable_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="guardian_is", type="string", example="Father"),
     *              @OA\Property(property="previous_school_name", type="string", example="Old School"),
     *              @OA\Property(property="previous_qualification", type="string", example="Grade 8"),
     *              @OA\Property(
     *                  property="emergency_contacts",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="name", type="string", example="Bob"),
     *                      @OA\Property(property="relation", type="string", example="Uncle"),
     *                      @OA\Property(property="phone", type="string", example="1234")
     *                  )
     *              )
     *              )
     *          ),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              required={"admission_no","class_id","section_id"},
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
     *              @OA\Property(property="full_name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *              @OA\Property(property="gender", type="string", example="male"),
     *              @OA\Property(property="religion", type="string", example="Christianity"),
     *              @OA\Property(property="caste", type="string", example="General"),
     *              @OA\Property(property="phone", type="string", example="1234567890"),
     *              @OA\Property(property="blood_group", type="string", example="O+"),
     *              @OA\Property(property="height", type="string", example="150cm"),
     *              @OA\Property(property="weight", type="string", example="45kg"),
     *              @OA\Property(property="current_address", type="string", example="123 Main St"),
     *              @OA\Property(property="permanent_address", type="string", example="456 Other St"),
     *              @OA\Property(property="photo", type="string", format="binary"),
     *              @OA\Property(property="father_name", type="string", example="Richard Doe"),
     *              @OA\Property(property="father_phone", type="string", example="0987654321"),
     *              @OA\Property(property="father_occupation", type="string", example="Engineer"),
     *              @OA\Property(property="father_photo", type="string", format="binary"),
     *              @OA\Property(property="mother_name", type="string", example="Jane Doe"),
     *              @OA\Property(property="mother_phone", type="string", example="1122334455"),
     *              @OA\Property(property="mother_occupation", type="string", example="Teacher"),
     *              @OA\Property(property="mother_photo", type="string", format="binary"),
     *              @OA\Property(property="guardian_name", type="string", example="Uncle Bob"),
     *              @OA\Property(property="guardian_phone", type="string", example="6677889900"),
     *              @OA\Property(property="guardian_relation", type="string", example="Uncle"),
     *              @OA\Property(property="bank_account_no", type="string", example="123456789"),
     *              @OA\Property(property="bank_name", type="string", example="Example Bank"),
     *              @OA\Property(property="ifsc_code", type="string", example="EXAMP001"),
     *              @OA\Property(property="national_id_no", type="string", example="NIC123456"),
     *              @OA\Property(property="local_id_no", type="string", example="LOC123456"),
     *              @OA\Property(property="birth_certificate_no", type="string", example="BC123456"),
     *              @OA\Property(property="apaar_id", type="string", example="APAAR123456"),
     *              @OA\Property(property="aadharshila_no", type="string", example="AADHAR123456"),
     *              @OA\Property(property="pen_no", type="string", example="PEN123456"),
     *              @OA\Property(property="emergency_phone", type="string", example="9988776655"),
     *              @OA\Property(property="father_email", type="string", example="father@example.com"),
     *              @OA\Property(property="mother_email", type="string", example="mother@example.com"),
     *              @OA\Property(property="dormitory_id", type="integer", example=1),
     *              @OA\Property(property="room_id", type="integer", example=1),
     *              @OA\Property(property="route_id", type="integer", example=1),
     *              @OA\Property(property="vehicle_id", type="integer", example=1),
     *              @OA\Property(property="alternate_phone", type="string", example="5566778899"),
     *              @OA\Property(property="student_group_id", type="integer", example=1),
     *              @OA\Property(property="medical_history", type="string", example="None"),
     *              @OA\Property(property="is_disabled", type="boolean", example=false),
     *              @OA\Property(property="disable_reason", type="string", example=""),
     *              @OA\Property(property="disable_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="guardian_is", type="string", example="Father"),
     *              @OA\Property(property="previous_school_name", type="string", example="Old School"),
     *              @OA\Property(property="previous_qualification", type="string", example="Grade 8")
     *          )
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
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="admission_no", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="dob", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="category_id", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="status", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="apaar_id", type="string"),
     *                  @OA\Property(property="aadharshila_no", type="string"),
     *                  @OA\Property(property="pen_no", type="string"),
     *                  @OA\Property(property="emergency_phone", type="string"),
     *                  @OA\Property(property="alternate_phone", type="string"),
     *                  @OA\Property(property="student_group_id", type="integer"),
     *                  @OA\Property(property="medical_history", type="string"),
     *                  @OA\Property(property="is_disabled", type="boolean"),
     *                  @OA\Property(property="disable_reason", type="string"),
     *                  @OA\Property(property="disable_date", type="string"),
     *                  @OA\Property(property="route_id", type="integer"),
     *                  @OA\Property(property="vehicle_id", type="integer"),
     *                  @OA\Property(property="dormitory_id", type="integer"),
     *                  @OA\Property(property="room_id", type="integer"),
     *                  @OA\Property(property="previous_school_name", type="string"),
     *                  @OA\Property(property="previous_qualification", type="string"),
     *                  @OA\Property(
     *                      property="emergencyContacts",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="relation", type="string"),
     *                          @OA\Property(property="phone", type="string")
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="documents",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="title", type="string"),
     *                          @OA\Property(property="file", type="string")
     *                      )
     *                  )
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
            'admission_no' => 'required|unique:students,admission_no',
            'student_code' => 'nullable|string|unique:students,student_code',
            'full_name' => 'nullable|string',
            'first_name' => 'required_without:full_name|nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email|unique:students,email',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'gender' => 'nullable|string',
            'apaar_id' => 'nullable|string',
            'aadharshila_no' => 'nullable|string',
            'pen_no' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'father_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'mother_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'guardian_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // create user login
        $fullName = $request->full_name ?? trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? ''));
        if (empty($fullName)) {
            $fullName = 'Student ' . ($request->admission_no ?? rand(100, 999));
        }

        $firstName = $request->first_name;
        $lastName = $request->last_name;
        if (empty($firstName) && !empty($fullName)) {
            $parts = explode(' ', trim($fullName), 2);
            $firstName = $parts[0] ?? $fullName;
            if (empty($lastName) && isset($parts[1])) {
                $lastName = $parts[1];
            }
        }
        if (empty($firstName)) {
            $firstName = $fullName;
        }

        $user = User::create([
            'name' => $fullName,
            'full_name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make('123456')
        ]);

        $user->assignRole('student');

        // Send password reset email to student
        if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Could not send password reset email to student: " . $e->getMessage());
            }
        }

        $photoPath = $this->processPhotoUpload($request, 'photo', 'uploads/student_photos');

        $academicYearId = $this->resolveAcademicYearId($request->academic_year_id, $request->class_id);

        $student = Student::create([
            'user_id' => $user->id,
            'admission_no' => $request->admission_no,
            'student_code' => $request->student_code,
            'admission_date' => $request->admission_date,
            'roll_no' => $request->roll_no,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $fullName,
            'email' => $request->email,
            'phone' => $request->phone,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'academic_year_id' => $academicYearId,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'caste' => $request->caste,
            'blood_group' => $request->blood_group,
            'height' => $request->height,
            'weight' => $request->weight,
            'as_on_date' => $request->as_on_date,
            'address' => $request->address,
            'nationality' => $request->nationality,
            'current_address' => $request->current_address,
            'permanent_address' => $request->permanent_address,
            'bank_account_no' => $request->bank_account_no,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'national_id_no' => $request->national_id_no,
            'local_id_no' => $request->local_id_no,
            'birth_certificate_no' => $request->birth_certificate_no,
            'apaar_id' => $request->apaar_id,
            'aadharshila_no' => $request->aadharshila_no,
            'pen_no' => $request->pen_no,
            'previous_school_name' => $request->previous_school_name,
            'previous_qualification' => $request->previous_qualification,
            'previous_school_details' => $request->previous_school_details,
            'note' => $request->note,
            'emergency_phone' => $request->emergency_phone,
            'alternate_phone' => $request->alternate_phone,
            'student_group_id' => !empty($request->student_group_id) ? $request->student_group_id : null,
            'medical_history' => $request->medical_history,
            'is_disabled' => filter_var($request->is_disabled ?? false, FILTER_VALIDATE_BOOLEAN),
            'disable_reason' => $request->disable_reason,
            'disable_date' => !empty($request->disable_date) ? $request->disable_date : null,
            'route_id' => !empty($request->route_id) ? $request->route_id : null,
            'vehicle_id' => !empty($request->vehicle_id) ? $request->vehicle_id : null,
            'dormitory_id' => !empty($request->dormitory_id) ? $request->dormitory_id : null,
            'room_id' => !empty($request->room_id) ? $request->room_id : null,
            'photo' => $photoPath
        ]);

        $fatherPhoto = $this->processPhotoUpload($request, 'father_photo', 'uploads/parent_photos', 'father');
        $motherPhoto = $this->processPhotoUpload($request, 'mother_photo', 'uploads/parent_photos', 'mother');
        $guardianPhoto = $this->processPhotoUpload($request, 'guardian_photo', 'uploads/parent_photos', 'guardian');

        StudentParent::create([
            'student_id' => $student->id,
            'father_name' => $request->father_name,
            'father_phone' => $request->father_phone,
            'father_occupation' => $request->father_occupation,
            'mother_name' => $request->mother_name,
            'mother_phone' => $request->mother_phone,
            'mother_occupation' => $request->mother_occupation,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_occupation' => $request->guardian_occupation,
            'guardian_email' => $request->guardian_email,
            'guardian_relation' => $request->guardian_relation,
            'guardian_address' => $request->guardian_address,
            'father_email' => $request->father_email,
            'mother_email' => $request->mother_email,
            'guardian_is' => $request->guardian_is,
            'father_photo' => $fatherPhoto,
            'mother_photo' => $motherPhoto,
            'guardian_photo' => $guardianPhoto
        ]);

        // Auto-create parent/guardian user login and send reset email
        $parentEmail = null;
        $parentName = null;
        $guardianIs = strtolower($request->guardian_is ?? '');
        if ($guardianIs === 'father') {
            $parentEmail = $request->father_email;
            $parentName = $request->father_name ?? 'Parent';
        } elseif ($guardianIs === 'mother') {
            $parentEmail = $request->mother_email;
            $parentName = $request->mother_name ?? 'Parent';
        } else {
            $parentEmail = $request->guardian_email;
            $parentName = $request->guardian_name ?? 'Guardian';
        }

        if ($parentEmail && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                $parentUser = User::where('email', $parentEmail)->first();
                if (!$parentUser) {
                    $parentUser = User::create([
                        'name' => $parentName,
                        'email' => $parentEmail,
                        'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                        'is_active' => true,
                    ]);

                    if (\Illuminate\Support\Facades\DB::table('roles')->where('name', 'parent')->exists()) {
                        $parentUser->assignRole('parent');
                    }
                }
                \Illuminate\Support\Facades\Password::sendResetLink(['email' => $parentEmail]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Could not auto-create parent user or send password reset: " . $e->getMessage());
            }
        }

        if ($academicYearId && $request->class_id && $request->section_id) {
            StudentAcademic::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id
            ]);
        }

        $emergencyContacts = $request->emergency_contacts;
        if (is_string($emergencyContacts)) {
            $emergencyContacts = json_decode($emergencyContacts, true);
        }
        if (!empty($emergencyContacts) && is_array($emergencyContacts)) {
            $student->emergencyContacts()->createMany($emergencyContacts);
        }

        $documents = $request->documents;
        if (is_string($documents)) {
            $documents = json_decode($documents, true);
        }
        if (!empty($documents) && is_array($documents)) {
            foreach ($documents as $doc) {
                if (isset($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filePath = $doc['file']->store('student_documents', 'public');
                    $student->documents()->create([
                        'title' => $doc['title'] ?? 'Document',
                        'file' => $filePath
                    ]);
                } elseif (isset($doc['title']) || isset($doc['file'])) {
                    $student->documents()->create([
                        'title' => $doc['title'] ?? 'Document',
                        'file' => is_string($doc['file'] ?? null) ? $doc['file'] : null
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => Student::with(['class', 'section', 'academicYear', 'parents', 'user', 'group', 'route', 'vehicle', 'dormitory', 'room', 'emergencyContacts'])->find($student->id)
        ]);
    }

    // Show student
    /**
     * @OA\Get(
     *      path="/api/students/{id}",
     *      operationId="getStudentById",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Get student information",
     *      description="Returns student data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Student id",
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
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="admission_no", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="dob", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="category_id", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="status", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="apaar_id", type="string"),
     *                  @OA\Property(property="aadharshila_no", type="string"),
     *                  @OA\Property(property="pen_no", type="string"),
     *                  @OA\Property(property="emergency_phone", type="string"),
     *                  @OA\Property(property="alternate_phone", type="string"),
     *                  @OA\Property(property="student_group_id", type="integer"),
     *                  @OA\Property(property="medical_history", type="string"),
     *                  @OA\Property(property="is_disabled", type="boolean"),
     *                  @OA\Property(property="disable_reason", type="string"),
     *                  @OA\Property(property="disable_date", type="string"),
     *                  @OA\Property(property="route_id", type="integer"),
     *                  @OA\Property(property="vehicle_id", type="integer"),
     *                  @OA\Property(property="dormitory_id", type="integer"),
     *                  @OA\Property(property="room_id", type="integer"),
     *                  @OA\Property(property="previous_school_name", type="string"),
     *                  @OA\Property(property="previous_qualification", type="string"),
     *                  @OA\Property(
     *                      property="emergencyContacts",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="relation", type="string"),
     *                          @OA\Property(property="phone", type="string")
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="documents",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="title", type="string"),
     *                          @OA\Property(property="file", type="string")
     *                      )
     *                  )
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

        $student = Student::with([
            'user',
            'class',
            'section',
            'academicYear',
            'category',
            'parents',
            'studentAcademics.academicYear',
            'documents',
            'fees',
            'attendances',
            'group',
            'route',
            'vehicle',
            'dormitory',
            'room',
            'emergencyContacts'
        ])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    // Update student
    /**
     * @OA\Put(
     *      path="/api/students/{id}",
     *      operationId="updateStudent",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Update existing student",
     *      description="Returns updated student data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Student id",
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
     *              required={"full_name"},
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
     *              @OA\Property(property="full_name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="class_id", type="integer", example=1),
     *              @OA\Property(property="section_id", type="integer", example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *              @OA\Property(property="gender", type="string", example="male"),
     *              @OA\Property(property="religion", type="string", example="Christianity"),
     *              @OA\Property(property="caste", type="string", example="General"),
     *              @OA\Property(property="phone", type="string", example="1234567890"),
     *              @OA\Property(property="blood_group", type="string", example="O+"),
     *              @OA\Property(property="current_address", type="string", example="123 Main St"),
     *              @OA\Property(property="permanent_address", type="string", example="456 Other St"),
     *              @OA\Property(property="photo", type="string", format="binary"),
     *              @OA\Property(property="father_name", type="string", example="Richard Doe"),
     *              @OA\Property(property="father_phone", type="string", example="0987654321"),
     *              @OA\Property(property="father_occupation", type="string", example="Engineer"),
     *              @OA\Property(property="father_photo", type="string", format="binary"),
     *              @OA\Property(property="mother_name", type="string", example="Jane Doe"),
     *              @OA\Property(property="mother_phone", type="string", example="1122334455"),
     *              @OA\Property(property="mother_occupation", type="string", example="Teacher"),
     *              @OA\Property(property="mother_photo", type="string", format="binary"),
     *              @OA\Property(property="guardian_name", type="string", example="Uncle Bob"),
     *              @OA\Property(property="guardian_phone", type="string", example="6677889900"),
     *              @OA\Property(property="guardian_relation", type="string", example="Uncle"),
     *              @OA\Property(property="guardian_address", type="string", example="123 Guardian St"),
     *              @OA\Property(property="bank_account_no", type="string", example="123456789"),
     *              @OA\Property(property="bank_name", type="string", example="Example Bank"),
     *              @OA\Property(property="ifsc_code", type="string", example="EXAMP001"),
     *              @OA\Property(property="national_id_no", type="string", example="NIC123456"),
     *              @OA\Property(property="local_id_no", type="string", example="LOC123456"),
     *              @OA\Property(property="birth_certificate_no", type="string", example="BC123456"),
     *              @OA\Property(property="apaar_id", type="string", example="APAAR123456"),
     *              @OA\Property(property="aadharshila_no", type="string", example="AADHAR123456"),
     *              @OA\Property(property="pen_no", type="string", example="PEN123456"),
     *              @OA\Property(property="previous_school_details", type="string", example="Previous School Name"),
     *              @OA\Property(property="note", type="string", example="Some note"),
     *              @OA\Property(property="emergency_phone", type="string", example="9988776655"),
     *              @OA\Property(property="father_email", type="string", example="father@example.com"),
     *              @OA\Property(property="mother_email", type="string", example="mother@example.com"),
     *              @OA\Property(property="dormitory_id", type="integer", example=1),
     *              @OA\Property(property="room_id", type="integer", example=1),
     *              @OA\Property(property="route_id", type="integer", example=1),
     *              @OA\Property(property="vehicle_id", type="integer", example=1),
     *              @OA\Property(property="alternate_phone", type="string", example="5566778899"),
     *              @OA\Property(property="student_group_id", type="integer", example=1),
     *              @OA\Property(property="medical_history", type="string", example="None"),
     *              @OA\Property(property="is_disabled", type="boolean", example=false),
     *              @OA\Property(property="disable_reason", type="string", example=""),
     *              @OA\Property(property="disable_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="guardian_is", type="string", example="Father"),
     *              @OA\Property(property="previous_school_name", type="string", example="Old School"),
     *              @OA\Property(property="previous_qualification", type="string", example="Grade 8"),
     *              @OA\Property(
     *                  property="emergency_contacts",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="name", type="string", example="Bob"),
     *                      @OA\Property(property="relation", type="string", example="Uncle"),
     *                      @OA\Property(property="phone", type="string", example="1234")
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="documents",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Birth Certificate"),
     *                      @OA\Property(property="file", type="string", format="binary")
     *                  )
     *              )
     *          )
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
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="admission_no", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="dob", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="category_id", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="status", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string"),
     *                  @OA\Property(property="class_id", type="integer"),
     *                  @OA\Property(property="section_id", type="integer"),
     *                  @OA\Property(property="academic_year_id", type="integer"),
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="apaar_id", type="string"),
     *                  @OA\Property(property="aadharshila_no", type="string"),
     *                  @OA\Property(property="pen_no", type="string"),
     *                  @OA\Property(property="emergency_phone", type="string"),
     *                  @OA\Property(property="alternate_phone", type="string"),
     *                  @OA\Property(property="student_group_id", type="integer"),
     *                  @OA\Property(property="medical_history", type="string"),
     *                  @OA\Property(property="is_disabled", type="boolean"),
     *                  @OA\Property(property="disable_reason", type="string"),
     *                  @OA\Property(property="disable_date", type="string"),
     *                  @OA\Property(property="route_id", type="integer"),
     *                  @OA\Property(property="vehicle_id", type="integer"),
     *                  @OA\Property(property="dormitory_id", type="integer"),
     *                  @OA\Property(property="room_id", type="integer"),
     *                  @OA\Property(property="previous_school_name", type="string"),
     *                  @OA\Property(property="previous_qualification", type="string"),
     *                  @OA\Property(
     *                      property="emergencyContacts",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="name", type="string"),
     *                          @OA\Property(property="relation", type="string"),
     *                          @OA\Property(property="phone", type="string")
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="documents",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="title", type="string"),
     *                          @OA\Property(property="file", type="string")
     *                      )
     *                  )
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

        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|required',
            'student_code' => 'nullable|string|unique:students,student_code,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $student->user_id . '|unique:students,email,' . $id,
            'class_id' => 'sometimes|required|exists:classes,id',
            'section_id' => 'sometimes|required|exists:sections,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'gender' => 'nullable|string',
            'apaar_id' => 'nullable|string',
            'aadharshila_no' => 'nullable|string',
            'pen_no' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'father_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'mother_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'guardian_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->all();
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dir = public_path('uploads/student_photos');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $file->move($dir, $filename);
            $updateData['photo'] = 'uploads/student_photos/' . $filename;
        } else {
            unset($updateData['photo']); // Do not overwrite with a null or string if it's not a file
        }

        $latestAcademic = $student->studentAcademics()->latest()->first();
        $academicYearId = $this->resolveAcademicYearId(
            $request->academic_year_id ?? $student->academic_year_id ?? $latestAcademic?->academic_year_id,
            $request->class_id ?? $student->class_id
        );
        if ($academicYearId) {
            $updateData['academic_year_id'] = $academicYearId;
        }

        if ($request->has('first_name') || $request->has('last_name') || $request->has('full_name')) {
            $updateData['full_name'] = $request->full_name ?? trim(($request->first_name ?? $student->first_name) . ' ' . ($request->last_name ?? $student->last_name));
        }

        if ($request->has('is_disabled')) {
            $updateData['is_disabled'] = filter_var($request->is_disabled, FILTER_VALIDATE_BOOLEAN);
        }

        $student->update($updateData);

        if ($student->user) {
            $fullName = $student->full_name;
            $userUpdate = [
                'name' => $fullName,
                'full_name' => $fullName
            ];
            if ($request->has('email')) {
                $userUpdate['email'] = $request->email;
            }
            $student->user->update($userUpdate);
        }

        $parentUpdateData = [
            'father_name' => $request->father_name,
            'father_phone' => $request->father_phone,
            'father_occupation' => $request->father_occupation,
            'mother_name' => $request->mother_name,
            'mother_phone' => $request->mother_phone,
            'mother_occupation' => $request->mother_occupation,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_occupation' => $request->guardian_occupation,
            'guardian_email' => $request->guardian_email,
            'guardian_relation' => $request->guardian_relation,
            'guardian_address' => $request->guardian_address,
            'father_email' => $request->father_email,
            'mother_email' => $request->mother_email,
            'guardian_is' => $request->guardian_is
        ];

        $photoPath = $this->processPhotoUpload($request, 'photo', 'uploads/student_photos');
        if ($photoPath !== null) {
            $updateData['photo'] = $photoPath;
        } else {
            unset($updateData['photo']);
        }

        $fatherPhoto = $this->processPhotoUpload($request, 'father_photo', 'uploads/parent_photos', 'father');
        if ($fatherPhoto !== null) $parentUpdateData['father_photo'] = $fatherPhoto;

        $motherPhoto = $this->processPhotoUpload($request, 'mother_photo', 'uploads/parent_photos', 'mother');
        if ($motherPhoto !== null) $parentUpdateData['mother_photo'] = $motherPhoto;

        $guardianPhoto = $this->processPhotoUpload($request, 'guardian_photo', 'uploads/parent_photos', 'guardian');
        if ($guardianPhoto !== null) $parentUpdateData['guardian_photo'] = $guardianPhoto;

        StudentParent::updateOrCreate(
            ['student_id' => $student->id],
            $parentUpdateData
        );

        $classId = $request->class_id ?? $latestAcademic?->class_id ?? $student->class_id;
        $sectionId = $request->section_id ?? $latestAcademic?->section_id ?? $student->section_id;

        if ($academicYearId && $classId && $sectionId) {
            StudentAcademic::updateOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $academicYearId],
                ['class_id' => $classId, 'section_id' => $sectionId]
            );
        }

        $emergencyContacts = $request->emergency_contacts;
        if (is_string($emergencyContacts)) {
            $emergencyContacts = json_decode($emergencyContacts, true);
        }
        if (!empty($emergencyContacts) && is_array($emergencyContacts)) {
            $student->emergencyContacts()->delete();
            $student->emergencyContacts()->createMany($emergencyContacts);
        }

        $documents = $request->documents;
        if (is_string($documents)) {
            $documents = json_decode($documents, true);
        }
        if (!empty($documents) && is_array($documents)) {
            foreach ($documents as $doc) {
                if (isset($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filePath = $doc['file']->store('student_documents', 'public');
                    $student->documents()->create([
                        'title' => $doc['title'] ?? 'Document',
                        'file' => $filePath
                    ]);
                } elseif (isset($doc['title']) || isset($doc['file'])) {
                    $student->documents()->create([
                        'title' => $doc['title'] ?? 'Document',
                        'file' => is_string($doc['file'] ?? null) ? $doc['file'] : null
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Student updated',
            'data' => Student::with(['class', 'section', 'academicYear', 'parents', 'user', 'group', 'route', 'vehicle', 'dormitory', 'room', 'emergencyContacts'])->find($student->id)
        ]);
    }

    // Soft delete
    /**
     * @OA\Delete(
     *      path="/api/students/{id}",
     *      operationId="deleteStudent",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Delete existing student",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Student id",
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

        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted'
        ]);
    }

    // Bulk create students
    /**
     * @OA\Post(
     *      path="/api/students/bulk",
     *      operationId="bulkStoreStudent",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Bulk store new students",
     *      description="Returns saved students data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"students"},
     *              @OA\Property(
     *                  property="students",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      required={"admission_no","full_name","class_id","section_id"},
     *                      @OA\Property(property="admission_no", type="string", example="ADM123"),
     *                      @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *                      @OA\Property(property="roll_no", type="string", example="101"),
     *                      @OA\Property(property="first_name", type="string", example="John"),
     *                      @OA\Property(property="last_name", type="string", example="Doe"),
     *                      @OA\Property(property="full_name", type="string", example="John Doe"),
     *                      @OA\Property(property="email", type="string", example="john@example.com"),
     *                      @OA\Property(property="class_id", type="integer", example=1),
     *                      @OA\Property(property="section_id", type="integer", example=1),
     *                      @OA\Property(property="academic_year_id", type="integer", example=1),
     *                      @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *                      @OA\Property(property="gender", type="string", example="male"),
     *                      @OA\Property(property="religion", type="string", example="Christianity"),
     *                      @OA\Property(property="caste", type="string", example="General"),
     *                      @OA\Property(property="phone", type="string", example="1234567890"),
     *                      @OA\Property(property="blood_group", type="string", example="O+"),
     *                      @OA\Property(property="height", type="string", example="150cm"),
     *                      @OA\Property(property="weight", type="string", example="45kg"),
     *                      @OA\Property(property="current_address", type="string", example="123 Main St"),
     *                      @OA\Property(property="permanent_address", type="string", example="456 Other St"),
     *                      @OA\Property(property="father_name", type="string", example="Richard Doe"),
     *                      @OA\Property(property="father_phone", type="string", example="0987654321"),
     *                      @OA\Property(property="father_occupation", type="string", example="Engineer"),
     *                      @OA\Property(property="mother_name", type="string", example="Jane Doe"),
     *                      @OA\Property(property="mother_phone", type="string", example="1122334455"),
     *                      @OA\Property(property="mother_occupation", type="string", example="Teacher"),
     *                      @OA\Property(property="guardian_name", type="string", example="Uncle Bob"),
     *                      @OA\Property(property="guardian_phone", type="string", example="6677889900"),
     *                      @OA\Property(property="guardian_relation", type="string", example="Uncle"),
     *                      @OA\Property(property="bank_account_no", type="string", example="123456789"),
     *                      @OA\Property(property="bank_name", type="string", example="Example Bank"),
     *                      @OA\Property(property="ifsc_code", type="string", example="EXAMP001"),
     *                      @OA\Property(property="national_id_no", type="string", example="NIC123456"),
     *                      @OA\Property(property="local_id_no", type="string", example="LOC123456"),
     *                      @OA\Property(property="birth_certificate_no", type="string", example="BC123456"),
     *                      @OA\Property(property="apaar_id", type="string", example="APAAR123456"),
     *                      @OA\Property(property="aadharshila_no", type="string", example="AADHAR123456"),
     *                      @OA\Property(property="pen_no", type="string", example="PEN123456"),
     *                      @OA\Property(property="emergency_phone", type="string", example="9988776655"),
     *                      @OA\Property(property="father_email", type="string", example="father@example.com"),
     *                      @OA\Property(property="mother_email", type="string", example="mother@example.com"),
     *                      @OA\Property(property="dormitory_id", type="integer", example=1),
     *                      @OA\Property(property="room_id", type="integer", example=1),
     *                      @OA\Property(property="route_id", type="integer", example=1),
     *                      @OA\Property(property="vehicle_id", type="integer", example=1),
     *                      @OA\Property(property="alternate_phone", type="string", example="5566778899"),
     *                      @OA\Property(property="student_group_id", type="integer", example=1),
     *                      @OA\Property(property="medical_history", type="string", example="None"),
     *                      @OA\Property(property="is_disabled", type="boolean", example=false),
     *                      @OA\Property(property="disable_reason", type="string", example=""),
     *                      @OA\Property(property="disable_date", type="string", format="date", example="2023-01-01"),
     *                      @OA\Property(property="guardian_is", type="string", example="Father"),
     *                      @OA\Property(property="previous_school_name", type="string", example="Old School"),
     *                      @OA\Property(property="previous_qualification", type="string", example="Grade 8"),
     *                      @OA\Property(
     *                          property="emergency_contacts",
     *                          type="array",
     *                          @OA\Items(
     *                              @OA\Property(property="name", type="string", example="Bob"),
     *                              @OA\Property(property="relation", type="string", example="Uncle"),
     *                              @OA\Property(property="phone", type="string", example="1234")
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Students created successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object")
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
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
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'students' => 'required|array',
            'students.*.admission_no' => 'required|distinct|unique:students,admission_no',
            'students.*.full_name' => 'required|string',
            'students.*.first_name' => 'nullable|string',
            'students.*.last_name' => 'nullable|string',
            'students.*.email' => 'nullable|email|distinct|unique:users,email|unique:students,email',
            'students.*.class_id' => 'required|exists:classes,id',
            'students.*.section_id' => 'required|exists:sections,id',
            'students.*.academic_year_id' => 'nullable|exists:academic_years,id',
            'students.*.gender' => 'nullable|string',
            'students.*.apaar_id' => 'nullable|string',
            'students.*.aadharshila_no' => 'nullable|string',
            'students.*.pen_no' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $createdStudents = [];

            foreach ($request->students as $studentData) {
                // create user login
                $fullName = $studentData['full_name'] ?? trim(($studentData['first_name'] ?? '') . ' ' . ($studentData['last_name'] ?? ''));
                if (empty($fullName)) {
                    $fullName = 'Student ' . ($studentData['admission_no'] ?? rand(100, 999));
                }

                $firstName = $studentData['first_name'] ?? null;
                $lastName = $studentData['last_name'] ?? null;
                if (empty($firstName) && !empty($fullName)) {
                    $parts = explode(' ', trim($fullName), 2);
                    $firstName = $parts[0] ?? $fullName;
                    if (empty($lastName) && isset($parts[1])) {
                        $lastName = $parts[1];
                    }
                }
                if (empty($firstName)) {
                    $firstName = $fullName;
                }

                $user = User::create([
                    'name' => $fullName,
                    'full_name' => $fullName,
                    'email' => $studentData['email'] ?? null,
                    'password' => Hash::make('123456')
                ]);

                $user->assignRole('student');

                // Send password reset email to student (bulk)
                if ($user->email && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning("Could not send password reset email to student (bulk): " . $e->getMessage());
                    }
                }

                $academicYearId = $this->resolveAcademicYearId(
                    $studentData['academic_year_id'] ?? null,
                    $studentData['class_id'] ?? null
                );
                $classId = $studentData['class_id'] ?? null;
                $sectionId = $studentData['section_id'] ?? null;

                $studentPhoto = $this->processPhotoUpload($studentData, 'photo', 'uploads/student_photos');

                $student = Student::create([
                    'user_id' => $user->id,
                    'admission_no' => $studentData['admission_no'] ?? null,
                    'student_code' => $studentData['student_code'] ?? null,
                    'admission_date' => $studentData['admission_date'] ?? null,
                    'roll_no' => $studentData['roll_no'] ?? null,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $fullName,
                    'email' => $studentData['email'] ?? null,
                    'phone' => $studentData['phone'] ?? null,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'academic_year_id' => $academicYearId,
                    'photo' => $studentPhoto,
                    'dob' => $studentData['dob'] ?? null,
                    'gender' => $studentData['gender'] ?? null,
                    'religion' => $studentData['religion'] ?? null,
                    'caste' => $studentData['caste'] ?? null,
                    'blood_group' => $studentData['blood_group'] ?? null,
                    'height' => $studentData['height'] ?? null,
                    'weight' => $studentData['weight'] ?? null,
                    'as_on_date' => $studentData['as_on_date'] ?? null,
                    'address' => $studentData['address'] ?? null,
                    'nationality' => $studentData['nationality'] ?? null,
                    'current_address' => $studentData['current_address'] ?? null,
                    'permanent_address' => $studentData['permanent_address'] ?? null,
                    'bank_account_no' => $studentData['bank_account_no'] ?? null,
                    'bank_name' => $studentData['bank_name'] ?? null,
                    'ifsc_code' => $studentData['ifsc_code'] ?? null,
                    'national_id_no' => $studentData['national_id_no'] ?? null,
                    'local_id_no' => $studentData['local_id_no'] ?? null,
                    'birth_certificate_no' => $studentData['birth_certificate_no'] ?? null,
                    'apaar_id' => $studentData['apaar_id'] ?? null,
                    'aadharshila_no' => $studentData['aadharshila_no'] ?? null,
                    'pen_no' => $studentData['pen_no'] ?? null,
                    'previous_school_name' => $studentData['previous_school_name'] ?? null,
                    'previous_qualification' => $studentData['previous_qualification'] ?? null,
                    'previous_school_details' => $studentData['previous_school_details'] ?? null,
                    'note' => $studentData['note'] ?? null,
                    'emergency_phone' => $studentData['emergency_phone'] ?? null,
                    'alternate_phone' => $studentData['alternate_phone'] ?? null,
                    'student_group_id' => $studentData['student_group_id'] ?? null,
                    'medical_history' => $studentData['medical_history'] ?? null,
                    'is_disabled' => filter_var($studentData['is_disabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'disable_reason' => $studentData['disable_reason'] ?? null,
                    'disable_date' => $studentData['disable_date'] ?? null,
                    'route_id' => $studentData['route_id'] ?? null,
                    'vehicle_id' => $studentData['vehicle_id'] ?? null,
                    'dormitory_id' => $studentData['dormitory_id'] ?? null,
                    'room_id' => $studentData['room_id'] ?? null,
                ]);

                StudentParent::create([
                    'student_id' => $student->id,
                    'father_name' => $studentData['father_name'] ?? null,
                    'father_phone' => $studentData['father_phone'] ?? null,
                    'father_occupation' => $studentData['father_occupation'] ?? null,
                    'mother_name' => $studentData['mother_name'] ?? null,
                    'mother_phone' => $studentData['mother_phone'] ?? null,
                    'mother_occupation' => $studentData['mother_occupation'] ?? null,
                    'guardian_name' => $studentData['guardian_name'] ?? null,
                    'guardian_phone' => $studentData['guardian_phone'] ?? null,
                    'guardian_occupation' => $studentData['guardian_occupation'] ?? null,
                    'guardian_email' => $studentData['guardian_email'] ?? null,
                    'guardian_relation' => $studentData['guardian_relation'] ?? null,
                    'guardian_address' => $studentData['guardian_address'] ?? null,
                    'father_email' => $studentData['father_email'] ?? null,
                    'mother_email' => $studentData['mother_email'] ?? null,
                    'guardian_is' => $studentData['guardian_is'] ?? null,
                ]);

                // Auto-create parent/guardian user login and send reset email
                $parentEmail = null;
                $parentName = null;
                $guardianIs = strtolower($studentData['guardian_is'] ?? '');
                if ($guardianIs === 'father') {
                    $parentEmail = $studentData['father_email'] ?? null;
                    $parentName = $studentData['father_name'] ?? 'Parent';
                } elseif ($guardianIs === 'mother') {
                    $parentEmail = $studentData['mother_email'] ?? null;
                    $parentName = $studentData['mother_name'] ?? 'Parent';
                } else {
                    $parentEmail = $studentData['guardian_email'] ?? null;
                    $parentName = $studentData['guardian_name'] ?? 'Guardian';
                }

                if ($parentEmail && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $parentUser = User::where('email', $parentEmail)->first();
                        if (!$parentUser) {
                            $parentUser = User::create([
                                'name' => $parentName,
                                'email' => $parentEmail,
                                'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                                'is_active' => true,
                            ]);

                            if (\Illuminate\Support\Facades\DB::table('roles')->where('name', 'parent')->exists()) {
                                $parentUser->assignRole('parent');
                            }
                        }
                        \Illuminate\Support\Facades\Password::sendResetLink(['email' => $parentEmail]);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning("Could not auto-create parent user or send password reset (bulk): " . $e->getMessage());
                    }
                }

                if ($academicYearId && $classId && $sectionId) {
                    StudentAcademic::create([
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYearId,
                        'class_id' => $classId,
                        'section_id' => $sectionId
                    ]);
                }

                if (isset($studentData['emergency_contacts']) && is_array($studentData['emergency_contacts'])) {
                    $student->emergencyContacts()->createMany($studentData['emergency_contacts']);
                }

                $createdStudents[] = $student->id;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Students created successfully',
                'data' => Student::with([
                    'user',
                    'class',
                    'section',
                    'academicYear',
                    'category',
                    'parents',
                    'studentAcademics.academicYear',
                    'emergencyContacts'
                ])->whereIn('id', $createdStudents)->get()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve academic year ID with fallback order:
     * 1. Provided explicit academic_year_id
     * 2. Academic year from the specified class
     * 3. Current academic year setting or active academic year record
     */
    private function resolveAcademicYearId($academicYearId = null, $classId = null)
    {
        if ($academicYearId) {
            return $academicYearId;
        }
        if ($classId) {
            $classAcademicYear = Classes::find($classId)?->academic_year_id;
            if ($classAcademicYear) {
                return $classAcademicYear;
            }
        }
        return GeneralSetting::first()?->academic_year_id 
            ?? AcademicYear::where('is_current', true)->value('id') 
            ?? AcademicYear::first()?->id;
    }

    /**
     * Helper method to process photo uploads from file, base64 string, or relative path.
     */
    private function processPhotoUpload($request, $key, $targetDir, $prefix = '')
    {
        $dir = public_path($targetDir);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($request instanceof Request && $request->hasFile($key)) {
            $file = $request->file($key);
            $filename = time() . '_' . ($prefix ? $prefix . '_' : '') . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            return rtrim($targetDir, '/') . '/' . $filename;
        }

        $value = $request instanceof Request ? $request->input($key) : (is_array($request) ? ($request[$key] ?? null) : null);

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $filename = time() . '_' . ($prefix ? $prefix . '_' : '') . uniqid() . '.' . $value->getClientOriginalExtension();
            $value->move($dir, $filename);
            return rtrim($targetDir, '/') . '/' . $filename;
        }

        if (is_string($value) && str_starts_with($value, 'data:image/')) {
            preg_match('/data:image\/(.*?);base64,(.*)/', $value, $matches);
            if (!empty($matches[2])) {
                $extension = $matches[1] ?? 'png';
                if ($extension === 'jpeg') $extension = 'jpg';
                $filename = time() . '_' . ($prefix ? $prefix . '_' : '') . uniqid() . '.' . $extension;
                file_put_contents($dir . '/' . $filename, base64_decode($matches[2]));
                return rtrim($targetDir, '/') . '/' . $filename;
            }
        }

        if (is_string($value) && !empty($value) && strlen($value) <= 255) {
            return $value;
        }

        return null;
    }

}