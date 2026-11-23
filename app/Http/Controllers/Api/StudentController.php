<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Services\AccountNotificationService;
use App\Services\StudentPhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\StudentAcademic;
use App\Models\StudentParent;
use App\Models\GeneralSetting;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Staff;

class StudentController extends Controller
{

    // Get all students
    /**
     * @OA\Get(
     *      path="/api/students",
     *      operationId="getStudentsList",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Get list of Students",
     *      description="Returns list of Students with optional filters: academic_year_id, class_id, section_id, search, category_id, academic_category_id, student_group_id, gender, status",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="academic_year_id", in="query", required=false, description="Filter by academic_year_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="class_id", in="query", required=false, description="Filter by class_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, description="Filter by section_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="student_code", in="query", required=false, description="Filter by student_code (e.g. YESMLR647)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="studentcode", in="query", required=false, description="Alias for student_code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="search", in="query", required=false, description="Search by name, roll_no, student_code, admission_no, email, phone, etc.", @OA\Schema(type="string")),
     *      @OA\Parameter(name="category_id", in="query", required=false, description="Filter by student social category_id (e.g. OBC, General, etc.)", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="academic_category_id", in="query", required=false, description="Filter by student academic category_id (e.g. Rainbow - 3, Planet, etc.)", @OA\Schema(type="integer")),
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
     *                  @OA\Property(property="roll_no", type="string", example="101"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="remark_point", type="integer", example=5),
     *                  @OA\Property(property="remark_points", type="integer", example=5),
     *                  @OA\Property(property="behaviour_points", type="integer", example=5),
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
            'academicCategory',
            'parents',
            'studentAcademics.academicYear',
            'emergencyContacts',
            'studentIncidents:id,student_id,point,is_negative'
        ]);

        // Academic Year, Class, Section Filters
        if ($request->filled('academic_year_id') || $request->filled('class_id') || $request->filled('section_id')) {
            $query->where(function ($mainQ) use ($request) {
                // Primary Branch: Match directly on students table
                $mainQ->where(function ($q) use ($request) {
                    if ($request->filled('academic_year_id')) {
                        $ayId = (int)$request->input('academic_year_id');
                        $defaultAyId = (int)(AcademicYear::cachedCurrentId() ?? GeneralSetting::cached()?->academic_year_id ?? 0);
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

        if ($request->filled('academic_category_id')) {
            $query->where('students.academic_category_id', $request->input('academic_category_id'));
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

        if ($request->filled('student_code')) {
            $studentCode = trim($request->input('student_code'));
            $query->where('students.student_code', 'like', "%{$studentCode}%");
        } elseif ($request->filled('studentcode')) {
            $studentCode = trim($request->input('studentcode'));
            $query->where('students.student_code', 'like', "%{$studentCode}%");
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
                  ->orWhereHas('studentAcademics', fn($sq) => $sq->where('roll_no', 'like', $term))
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
     *              required={"admission_no","class_id"},
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", nullable=true, example="John", description="First name (optional; leave null if school only uses full_name)"),
     *              @OA\Property(property="last_name", type="string", nullable=true, example="Doe", description="Last name (optional; leave null if school only uses full_name)"),
     *              @OA\Property(property="full_name", type="string", nullable=true, example="John Doe", description="Full name (can be used standalone without first_name/last_name)"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="class_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="category_id", type="integer", nullable=true, example=3, description="Student Social Category ID (e.g. 2=General, 3=OBC, 4=SC, 5=ST, 12=Other)"),
     *              @OA\Property(property="academic_category_id", type="integer", nullable=true, example=6, description="Student Academic Category ID (e.g. Rainbow - 3, Planet, Galaxy HS). Auto-assigned from class if omitted"),
     *              @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *              @OA\Property(property="gender", type="string", example="male"),
     *              @OA\Property(property="religion", type="string", nullable=true, example="Christianity"),
     *              @OA\Property(property="caste", type="string", nullable=true, example="General"),
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
     *              required={"admission_no"},
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
     *              @OA\Property(property="full_name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="class_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="category_id", type="integer", nullable=true, example=1, description="Student category ID. If omitted, auto-assigned from selected class"),
     *              @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *              @OA\Property(property="gender", type="string", example="male"),
     *              @OA\Property(property="religion", type="string", nullable=true, example="Christianity"),
     *              @OA\Property(property="caste", type="string", nullable=true, example="General"),
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
        $this->sanitizeEmptyInputs($request);

        $validator = Validator::make($request->all(), [
            'admission_no' => 'required|unique:students,admission_no',
            'student_code' => 'nullable|string|unique:students,student_code',
            'full_name' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email|unique:students,email',
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'caste' => 'nullable|string',
            'religion' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'category_id' => 'nullable|exists:student_categories,id',
            'academic_category_id' => 'nullable|exists:academic_categories,id',
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

        return DB::transaction(function () use ($request) {
            // create user login
            $fullName = $request->full_name;
            $firstName = $request->first_name;
            $lastName = $request->last_name;

            $userDisplayName = $fullName ?: ($firstName ? trim($firstName . ' ' . ($lastName ?? '')) : 'Student ' . ($request->admission_no ?? rand(100, 999)));

            $studentPassword = $request->filled('password') ? $request->input('password') : '123456';

            $user = User::create([
                'name' => $userDisplayName,
                'full_name' => $fullName,
                'email' => $request->email,
                'password' => Hash::make($studentPassword)
            ]);

            // Ensure student role exists for current guard and assign safely
            try {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'sanctum']);
                $user->assignRole('student');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not assign student role: " . $e->getMessage());
            }

            // Send welcome password reset / setup email to student
            AccountNotificationService::sendWelcomeResetEmail($user);

        $photoPath = $this->processPhotoUpload($request, 'photo', 'uploads/student_photos');

        $academicYearId = $this->resolveAcademicYearId($request->academic_year_id, $request->class_id);

        // Student academic category (e.g., Rainbow - 3, Planet, Galaxy HS)
        $academicCategoryId = $request->academic_category_id;
        if (empty($academicCategoryId) && !empty($request->class_id)) {
            $classObj = Classes::find($request->class_id);
            if ($classObj && ($classObj->academic_category_id || $classObj->category_id)) {
                $academicCategoryId = $classObj->academic_category_id ?: $classObj->category_id;
            }
        }

        // Student demographic/social category (e.g., General, OBC, SC, ST, Other)
        $categoryId = $request->category_id;

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
            'category_id' => $categoryId,
            'academic_category_id' => $academicCategoryId,
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
                $isNewParent = false;
                if (!$parentUser) {
                    $parentUser = User::create([
                        'name' => $parentName,
                        'email' => $parentEmail,
                        'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                        'is_active' => true,
                    ]);

                    try {
                        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
                        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'sanctum']);
                        $parentUser->assignRole('parent');
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Could not assign parent role: " . $e->getMessage());
                    }
                    $isNewParent = true;
                }
                if ($isNewParent) {
                    AccountNotificationService::sendWelcomeResetEmail($parentUser);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Could not auto-create parent user or send password reset: " . $e->getMessage());
            }
        }

        if ($academicYearId && $request->class_id) {
            StudentAcademic::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'roll_no' => $request->roll_no
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
        });
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
     *                  @OA\Property(property="roll_no", type="string", example="101"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="remark_point", type="integer", example=5),
     *                  @OA\Property(property="remark_points", type="integer", example=5),
     *                  @OA\Property(property="behaviour_points", type="integer", example=5),
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
     *                  ),
     *                  @OA\Property(
     *                      property="siblings",
     *                      type="array",
     *                      description="Siblings sharing the same parents",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=206),
     *                          @OA\Property(property="admission_no", type="string", example="ADM-002"),
     *                          @OA\Property(property="roll_no", type="string", example="14"),
     *                          @OA\Property(property="first_name", type="string", example="Bob"),
     *                          @OA\Property(property="last_name", type="string", example="Sharma"),
     *                          @OA\Property(property="full_name", type="string", example="Bob Sharma"),
     *                          @OA\Property(property="gender", type="string", example="Male"),
     *                          @OA\Property(property="photo", type="string", example="uploads/student_photos/1794060795_6aef31fb88317.png"),
     *                          @OA\Property(property="dob", type="string", example="2012-05-15"),
     *                          @OA\Property(property="class_id", type="integer", example=3),
     *                          @OA\Property(property="class_name", type="string", example="Class 8"),
     *                          @OA\Property(property="section_id", type="integer", example=1),
     *                          @OA\Property(property="section_name", type="string", example="A"),
     *                          @OA\Property(property="academic_year_id", type="integer", example=1),
     *                          @OA\Property(property="academic_year_name", type="string", example="2026-2027"),
     *                          @OA\Property(property="is_disabled", type="boolean", example=false),
     *                          @OA\Property(property="status", type="integer", example=1)
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
     *          description="Student not found"
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
            'academicCategory',
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
            'emergencyContacts',
            'studentIncidents.incident',
            'studentIncidents.assignedBy:id,name,full_name,email'
        ])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $student->append('siblings');

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
     *      summary="Update existing student (JSON / Base64 photo)",
     *      description="Returns updated student data. For binary file/photo uploads, use POST /api/students/{id} (PHP only parses file uploads on POST requests).",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Student id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              example=5
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="admission_no", type="string", example="ADM123"),
     *                  @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *                  @OA\Property(property="roll_no", type="string", example="101"),
     *                  @OA\Property(property="first_name", type="string", nullable=true, example="John", description="First name (optional; leave null if school only uses full_name)"),
     *                  @OA\Property(property="last_name", type="string", nullable=true, example="Doe", description="Last name (optional; leave null if school only uses full_name)"),
     *                  @OA\Property(property="full_name", type="string", nullable=true, example="John Doe", description="Full name (can be used standalone without first_name/last_name)"),
     *                  @OA\Property(property="email", type="string", example="john@example.com"),
     *                  @OA\Property(property="class_id", type="integer", nullable=true, example=1),
     *                  @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *                  @OA\Property(property="academic_year_id", type="integer", example=1),
     *                  @OA\Property(property="category_id", type="integer", nullable=true, example=3, description="Student Social Category ID (e.g. 2=General, 3=OBC, 4=SC, 5=ST, 12=Other)"),
     *                  @OA\Property(property="academic_category_id", type="integer", nullable=true, example=6, description="Student Academic Category ID (e.g. Rainbow - 3, Planet, Galaxy HS)"),
     *                  @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *                  @OA\Property(property="gender", type="string", example="male"),
     *                  @OA\Property(property="religion", type="string", nullable=true, example="Christianity"),
     *                  @OA\Property(property="caste", type="string", nullable=true, example="General"),
     *                  @OA\Property(property="phone", type="string", example="1234567890"),
     *                  @OA\Property(property="blood_group", type="string", example="O+"),
     *                  @OA\Property(property="current_address", type="string", example="123 Main St"),
     *                  @OA\Property(property="permanent_address", type="string", example="456 Other St"),
     *                  @OA\Property(property="father_name", type="string", example="Richard Doe"),
     *                  @OA\Property(property="father_phone", type="string", example="0987654321"),
     *                  @OA\Property(property="father_occupation", type="string", example="Engineer"),
     *                  @OA\Property(property="mother_name", type="string", example="Jane Doe"),
     *                  @OA\Property(property="mother_phone", type="string", example="1122334455"),
     *                  @OA\Property(property="mother_occupation", type="string", example="Teacher"),
     *                  @OA\Property(property="guardian_name", type="string", example="Uncle Bob"),
     *                  @OA\Property(property="guardian_phone", type="string", example="6677889900"),
     *                  @OA\Property(property="guardian_relation", type="string", example="Uncle"),
     *                  @OA\Property(property="guardian_address", type="string", example="123 Guardian St"),
     *                  @OA\Property(property="bank_account_no", type="string", example="123456789"),
     *                  @OA\Property(property="bank_name", type="string", example="Example Bank"),
     *                  @OA\Property(property="ifsc_code", type="string", example="EXAMP001"),
     *                  @OA\Property(property="national_id_no", type="string", example="NIC123456"),
     *                  @OA\Property(property="local_id_no", type="string", example="LOC123456"),
     *                  @OA\Property(property="birth_certificate_no", type="string", example="BC123456"),
     *                  @OA\Property(property="apaar_id", type="string", example="APAAR123456"),
     *                  @OA\Property(property="aadharshila_no", type="string", example="AADHAR123456"),
     *                  @OA\Property(property="pen_no", type="string", example="PEN123456"),
     *                  @OA\Property(property="previous_school_details", type="string", example="Previous School Name"),
     *                  @OA\Property(property="note", type="string", example="Some note"),
     *                  @OA\Property(property="emergency_phone", type="string", example="9988776655"),
     *                  @OA\Property(property="father_email", type="string", example="father@example.com"),
     *                  @OA\Property(property="mother_email", type="string", example="mother@example.com"),
     *                  @OA\Property(property="dormitory_id", type="integer", example=1),
     *                  @OA\Property(property="room_id", type="integer", example=1),
     *                  @OA\Property(property="route_id", type="integer", example=1),
     *                  @OA\Property(property="vehicle_id", type="integer", example=1),
     *                  @OA\Property(property="alternate_phone", type="string", example="5566778899"),
     *                  @OA\Property(property="student_group_id", type="integer", example=1),
     *                  @OA\Property(property="medical_history", type="string", example="None"),
     *                  @OA\Property(property="is_disabled", type="boolean", example=false),
     *                  @OA\Property(property="disable_reason", type="string", example=""),
     *                  @OA\Property(property="disable_date", type="string", format="date", example="2023-01-01"),
     *                  @OA\Property(property="guardian_is", type="string", example="Father"),
     *                  @OA\Property(property="previous_school_name", type="string", example="Old School"),
     *                  @OA\Property(property="previous_qualification", type="string", example="Grade 8")
     *              )
     *          ),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
     *              @OA\Property(property="full_name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="class_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="category_id", type="integer", nullable=true, example=1, description="Student category ID"),
     *              @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *              @OA\Property(property="gender", type="string", example="male"),
     *              @OA\Property(property="religion", type="string", nullable=true, example="Christianity"),
     *              @OA\Property(property="caste", type="string", nullable=true, example="General"),
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
     *              @OA\Property(property="message", type="string", example="Student updated"),
     *              @OA\Property(property="data", type="object")
     *          )
     *       ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     *
     * @OA\Post(
     *      path="/api/students/{id}",
     *      operationId="updateStudentPost",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Update existing student via POST (with multipart/form-data for photo uploads)",
     *      description="Alternative update route supporting multipart/form-data with file uploads",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Student id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer", example=5)
     *      ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="admission_no", type="string", example="ADM123"),
     *                  @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *                  @OA\Property(property="roll_no", type="string", example="101"),
     *                  @OA\Property(property="first_name", type="string", example="John"),
     *                  @OA\Property(property="last_name", type="string", example="Doe"),
     *                  @OA\Property(property="full_name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", example="john@example.com"),
     *                  @OA\Property(property="class_id", type="integer", nullable=true, example=1),
     *                  @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *                  @OA\Property(property="academic_year_id", type="integer", example=1),
     *                  @OA\Property(property="category_id", type="integer", nullable=true, example=1),
     *                  @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *                  @OA\Property(property="gender", type="string", example="male"),
     *                  @OA\Property(property="religion", type="string", nullable=true, example="Christianity"),
     *                  @OA\Property(property="caste", type="string", nullable=true, example="General"),
     *                  @OA\Property(property="phone", type="string", example="1234567890"),
     *                  @OA\Property(property="blood_group", type="string", example="O+"),
     *                  @OA\Property(property="current_address", type="string", example="123 Main St"),
     *                  @OA\Property(property="permanent_address", type="string", example="456 Other St"),
     *                  @OA\Property(property="photo", type="string", format="binary"),
     *                  @OA\Property(property="father_name", type="string", example="Richard Doe"),
     *                  @OA\Property(property="father_phone", type="string", example="0987654321"),
     *                  @OA\Property(property="father_occupation", type="string", example="Engineer"),
     *                  @OA\Property(property="father_photo", type="string", format="binary"),
     *                  @OA\Property(property="mother_name", type="string", example="Jane Doe"),
     *                  @OA\Property(property="mother_phone", type="string", example="1122334455"),
     *                  @OA\Property(property="mother_occupation", type="string", example="Teacher"),
     *                  @OA\Property(property="mother_photo", type="string", format="binary"),
     *                  @OA\Property(property="guardian_name", type="string", example="Uncle Bob"),
     *                  @OA\Property(property="guardian_phone", type="string", example="6677889900"),
     *                  @OA\Property(property="guardian_relation", type="string", example="Uncle"),
     *                  @OA\Property(property="guardian_address", type="string", example="123 Guardian St")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Student updated"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $this->parseMultipartIfPut($request);
        $this->sanitizeEmptyInputs($request);

        $student = Student::find($id);

        if (!$student) {
            $latestStudent = Student::latest('id')->first();
            $suggestId = $latestStudent ? $latestStudent->id : 1;
            return response()->json([
                'success' => false,
                'message' => "Student not found with ID {$id}. Please provide a valid existing student ID (for example, ID {$suggestId})."
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'admission_no' => 'sometimes|required|string|max:255|unique:students,admission_no,' . $id,
            'student_code' => 'nullable|string|unique:students,student_code,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $student->user_id . '|unique:students,email,' . $id,
            'class_id' => 'sometimes|nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'caste' => 'nullable|string',
            'religion' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'category_id' => 'nullable|exists:student_categories,id',
            'academic_category_id' => 'nullable|exists:academic_categories,id',
            'gender' => 'nullable|string',
            'apaar_id' => 'nullable|string',
            'aadharshila_no' => 'nullable|string',
            'pen_no' => 'nullable|string',
            'photo' => 'nullable',
            'father_photo' => 'nullable',
            'mother_photo' => 'nullable',
            'guardian_photo' => 'nullable',
        ], [
            'admission_no.required' => 'The admission number cannot be null or empty.',
            'admission_no.unique'   => 'The admission number has already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->except(['user_id', 'created_at', 'deleted_at']);

        // Convert any empty strings to null for database compatibility
        foreach ($updateData as $key => $val) {
            if ($val === '' || (is_string($val) && trim($val) === '')) {
                $updateData[$key] = null;
            }
        }

        // Never wipe out required non-nullable fields with null during update (e.g. from Swagger "Send empty value")
        if (array_key_exists('admission_no', $updateData) && empty($updateData['admission_no'])) {
            unset($updateData['admission_no']); // Preserve existing admission_no
        }
        if (array_key_exists('status', $updateData) && $updateData['status'] === null) {
            unset($updateData['status']);
        }

        // Process student photo (handles file upload, base64, or existing path)
        $photoPath = $this->processPhotoUpload($request, 'photo', 'uploads/student_photos');
        if ($photoPath !== null) {
            $updateData['photo'] = $photoPath;
        } else {
            unset($updateData['photo']); // Do not overwrite if not provided
        }

        $latestAcademic = $student->studentAcademics()->latest()->first();
        $academicYearId = $this->resolveAcademicYearId(
            $request->academic_year_id ?? $student->academic_year_id ?? $latestAcademic?->academic_year_id,
            $request->class_id ?? $student->class_id
        );
        if ($academicYearId) {
            $updateData['academic_year_id'] = $academicYearId;
        }

        // Auto-assign Student Academic Category from Class if not explicitly provided and class is present/updated
        if ($request->has('academic_category_id')) {
            $updateData['academic_category_id'] = $request->academic_category_id;
        } elseif ($request->filled('class_id') || (empty($student->academic_category_id) && !empty($student->class_id))) {
            $targetClassId = $request->class_id ?? $student->class_id;
            $classObj = Classes::find($targetClassId);
            if ($classObj && ($classObj->academic_category_id || $classObj->category_id)) {
                $updateData['academic_category_id'] = $classObj->academic_category_id ?: $classObj->category_id;
            }
        }

        // Student Social Category is only updated when provided in request (never overwritten by class)
        if ($request->has('category_id')) {
            $updateData['category_id'] = $request->category_id;
        }

        if ($request->has('is_disabled')) {
            $updateData['is_disabled'] = filter_var($request->is_disabled, FILTER_VALIDATE_BOOLEAN);
        }

        try {
            $student->update($updateData);
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1] ?? null;
            $msg = 'A database validation error occurred.';
            $field = 'general';
            if ($errorCode == 1062) {
                $msg = 'A student with this admission number or email already exists.';
                $field = 'admission_no';
            } elseif ($errorCode == 1048) {
                $msg = 'A required field cannot be null.';
            }
            return response()->json([
                'success' => false,
                'message' => $msg,
                'errors' => [
                    $field => [$msg]
                ]
            ], 422);
        }

        $user = $student->user ?? ($student->user_id ? User::find($student->user_id) : null);
        if ($user) {
            $userUpdate = [];
            if ($request->has('full_name')) {
                $userUpdate['full_name'] = $student->full_name;
                $userUpdate['name'] = $student->full_name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
            } elseif ($request->has('first_name') || $request->has('last_name')) {
                $userUpdate['name'] = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
            }

            if ($request->has('email')) {
                $userUpdate['email'] = $student->email;
            }

            if (!empty($userUpdate)) {
                $user->update($userUpdate);
            }
        }

        // Process parent information only if parent fields are provided in request
        $parentFields = [
            'father_name',
            'father_phone',
            'father_occupation',
            'mother_name',
            'mother_phone',
            'mother_occupation',
            'guardian_name',
            'guardian_phone',
            'guardian_occupation',
            'guardian_email',
            'guardian_relation',
            'guardian_address',
            'father_email',
            'mother_email',
            'guardian_is'
        ];

        $parentUpdateData = [];
        foreach ($parentFields as $field) {
            if ($request->has($field)) {
                $val = $request->input($field);
                $parentUpdateData[$field] = ($val === '' || (is_string($val) && trim($val) === '')) ? null : $val;
            }
        }

        $fatherPhoto = $this->processPhotoUpload($request, 'father_photo', 'uploads/parent_photos', 'father');
        if ($fatherPhoto !== null) $parentUpdateData['father_photo'] = $fatherPhoto;

        $motherPhoto = $this->processPhotoUpload($request, 'mother_photo', 'uploads/parent_photos', 'mother');
        if ($motherPhoto !== null) $parentUpdateData['mother_photo'] = $motherPhoto;

        $guardianPhoto = $this->processPhotoUpload($request, 'guardian_photo', 'uploads/parent_photos', 'guardian');
        if ($guardianPhoto !== null) $parentUpdateData['guardian_photo'] = $guardianPhoto;

        if (!empty($parentUpdateData)) {
            StudentParent::updateOrCreate(
                ['student_id' => $student->id],
                $parentUpdateData
            );
        }

        $classId = $request->class_id ?? $latestAcademic?->class_id ?? $student->class_id;
        $sectionId = $request->section_id ?? $latestAcademic?->section_id ?? $student->section_id;

        if ($academicYearId && $classId) {
            $academicData = [
                'class_id' => $classId,
                'section_id' => $sectionId,
            ];
            if ($request->has('roll_no')) {
                $academicData['roll_no'] = $request->roll_no;
            } elseif (!empty($student->roll_no)) {
                $academicData['roll_no'] = $student->roll_no;
            }
            StudentAcademic::updateOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $academicYearId],
                $academicData
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
        if (!auth()->user()?->hasAnyRole(['super-admin', 'admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators can delete student records.'
            ], 403);
        }

        $student = Student::with(['parents', 'user'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $parent = $student->parents;
        $parentEmails = array_unique(array_filter([
            $parent?->father_email ? trim($parent->father_email) : null,
            $parent?->mother_email ? trim($parent->mother_email) : null,
            $parent?->guardian_email ? trim($parent->guardian_email) : null,
        ]));
        $parentPhones = array_unique(array_filter([
            $parent?->father_phone ? trim($parent->father_phone) : null,
            $parent?->mother_phone ? trim($parent->mother_phone) : null,
            $parent?->guardian_phone ? trim($parent->guardian_phone) : null,
        ]));

        $hasOtherStudents = false;
        if (!empty($parentEmails) || !empty($parentPhones)) {
            $hasOtherStudents = Student::where('id', '!=', $student->id)
                ->whereHas('parents', function ($q) use ($parentEmails, $parentPhones) {
                    $q->where(function ($subQ) use ($parentEmails, $parentPhones) {
                        if (!empty($parentEmails)) {
                            $subQ->whereIn('father_email', $parentEmails)
                                 ->orWhereIn('mother_email', $parentEmails)
                                 ->orWhereIn('guardian_email', $parentEmails);
                        }
                        if (!empty($parentPhones)) {
                            $subQ->orWhereIn('father_phone', $parentPhones)
                                 ->orWhereIn('mother_phone', $parentPhones)
                                 ->orWhereIn('guardian_phone', $parentPhones);
                        }
                    });
                })->exists();
        }

        $student->delete();

        $message = $hasOtherStudents
            ? 'Student deleted. Parent account kept because they have other enrolled student(s) in this school.'
            : 'Student and associated parent record deleted.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'parent_kept' => $hasOtherStudents
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
     *                      required={"admission_no","full_name","class_id"},
     *                      @OA\Property(property="admission_no", type="string", example="ADM123"),
     *                      @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *                      @OA\Property(property="roll_no", type="string", example="101"),
     *                      @OA\Property(property="first_name", type="string", example="John"),
     *                      @OA\Property(property="last_name", type="string", example="Doe"),
     *                      @OA\Property(property="full_name", type="string", example="John Doe"),
     *                      @OA\Property(property="email", type="string", example="john@example.com"),
     *                      @OA\Property(property="class_id", type="integer", example=1),
     *                      @OA\Property(property="section_id", type="integer", nullable=true, example=1),
     *                      @OA\Property(property="academic_year_id", type="integer", example=1),
     *                      @OA\Property(property="dob", type="string", format="date", example="2010-01-01"),
     *                      @OA\Property(property="gender", type="string", example="male"),
     *                      @OA\Property(property="religion", type="string", nullable=true, example="Christianity"),
     *                      @OA\Property(property="caste", type="string", nullable=true, example="General"),
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
            'students.*.section_id' => 'nullable|exists:sections,id',
            'students.*.caste' => 'nullable|string',
            'students.*.religion' => 'nullable|string',
            'students.*.blood_group' => 'nullable|string',
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

                try {
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'sanctum']);
                    $user->assignRole('student');
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not assign student role (bulk): " . $e->getMessage());
                }

                // Send welcome password reset / setup email to student (bulk)
                AccountNotificationService::sendWelcomeResetEmail($user);

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
                        $isNewParent = false;
                        if (!$parentUser) {
                            $parentUser = User::create([
                                'name' => $parentName,
                                'email' => $parentEmail,
                                'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                                'is_active' => true,
                            ]);

                            try {
                                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
                                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'sanctum']);
                                $parentUser->assignRole('parent');
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning("Could not assign parent role (bulk): " . $e->getMessage());
                            }
                            $isNewParent = true;
                        }
                        if ($isNewParent) {
                            AccountNotificationService::sendWelcomeResetEmail($parentUser);
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning("Could not auto-create parent user or send password reset (bulk): " . $e->getMessage());
                    }
                }

                if ($academicYearId && $classId && $sectionId) {
                    StudentAcademic::create([
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYearId,
                        'class_id' => $classId,
                        'section_id' => $sectionId,
                        'roll_no' => $studentData['roll_no'] ?? null
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
        return GeneralSetting::cached()?->academic_year_id
            ?? AcademicYear::cachedCurrentId()
            ?? AcademicYear::first()?->id;
    }

    /**
     * Parses raw multipart/form-data on PUT/PATCH requests where PHP SAPI does not populate $_POST/$_FILES.
     */
    private function parseMultipartIfPut(Request $request): void
    {
        $contentType = $request->header('Content-Type', '');
        if (!str_contains($contentType, 'multipart/form-data')) {
            return;
        }

        // If PHP already populated inputs (e.g. on POST), nothing to do
        if (!empty($request->all()) || !empty($request->allFiles())) {
            return;
        }

        $rawInput = file_get_contents('php://input');
        if (empty($rawInput)) {
            return;
        }

        preg_match('/boundary=(.*)$/', $contentType, $matches);
        if (empty($matches[1])) {
            return;
        }

        $boundary = trim($matches[1], '"');
        $parts = array_slice(explode('--' . $boundary, $rawInput), 1);

        $parsedData = [];
        $parsedFiles = [];

        foreach ($parts as $part) {
            if ($part === "--\r\n" || $part === "--" || trim($part) === '') {
                break;
            }

            $sections = explode("\r\n\r\n", $part, 2);
            if (count($sections) < 2) {
                continue;
            }

            $rawHeaders = $sections[0];
            $body = substr($sections[1], 0, -2); // remove trailing \r\n

            if (preg_match('/name="([^"]+)"(?:;\s*filename="([^"]+)")?/', $rawHeaders, $matches)) {
                $name = $matches[1];
                $filename = $matches[2] ?? null;

                if ($filename !== null && $filename !== '') {
                    $fileType = 'application/octet-stream';
                    if (preg_match('/Content-Type:\s*([^\r\n]+)/i', $rawHeaders, $typeMatches)) {
                        $fileType = trim($typeMatches[1]);
                    }

                    $tmpPath = tempnam(sys_get_temp_dir(), 'php_put_');
                    file_put_contents($tmpPath, $body);

                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tmpPath,
                        $filename,
                        $fileType,
                        null,
                        true
                    );

                    $parsedFiles[$name] = $uploadedFile;
                } else {
                    $parsedData[$name] = ($body === '') ? null : $body;
                }
            }
        }

        if (!empty($parsedData)) {
            $request->merge($parsedData);
        }
        if (!empty($parsedFiles)) {
            $request->files->add($parsedFiles);
        }
    }

    /**
     * Converts empty string inputs to null for foreign keys, dates, and nullable strings.
     */
    private function sanitizeEmptyInputs(Request $request): void
    {
        $input = $request->all();
        $cleaned = [];

        foreach ($input as $key => $value) {
            if ($value === '' || (is_string($value) && trim($value) === '')) {
                $cleaned[$key] = null;
            }
        }

        if (!empty($cleaned)) {
            $request->merge($cleaned);
        }
    }

    /**
     * Helper method to process photo uploads from file, base64 string, or relative path.
     */
    private function processPhotoUpload($request, $key, $targetDir, $prefix = '')
    {
        return StudentPhotoService::upload($request, $key, $targetDir, $prefix);
    }

    /**
     * @OA\Get(
     *      path="/api/students/additional-contact/siblings",
     *      operationId="getSiblingsDropdownList",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="List siblings by class and section for Additional Contact modal",
     *      description="Fetches a list of students in a given class and section for selecting a sibling.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="class_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="section_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getSiblings(Request $request)
    {
        $query = Student::query()
            ->select('students.id', 'students.first_name', 'students.last_name', 'students.full_name', 'students.admission_no', 'students.roll_no', 'students.class_id', 'students.section_id', 'students.photo')
            ->with(['class:id,name', 'section:id,name', 'parents:id,student_id,father_name,mother_name,guardian_name']);

        if ($request->filled('class_id')) {
            $query->where('students.class_id', $request->input('class_id'));
        }

        if ($request->filled('section_id')) {
            $query->where('students.section_id', $request->input('section_id'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('students.full_name', 'like', "%{$search}%")
                  ->orWhere('students.first_name', 'like', "%{$search}%")
                  ->orWhere('students.last_name', 'like', "%{$search}%")
                  ->orWhere('students.admission_no', 'like', "%{$search}%");
            });
        }

        $siblings = $query->orderBy('students.full_name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data'    => $siblings
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/students/additional-contact/sibling/{id}",
     *      operationId="getSiblingContactDetails",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Get parent and guardian details of a selected sibling",
     *      description="Retrieves father, mother, and guardian info from a sibling student to autofill the Add Student form.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Student not found")
     * )
     */
    public function getSiblingContactInfo($id)
    {
        $student = Student::with(['parents', 'class:id,name', 'section:id,name'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Sibling student not found'
            ], 404);
        }

        $parents = $student->parents;

        return response()->json([
            'success' => true,
            'data'    => [
                'source'              => 'sibling',
                'sibling_id'          => $student->id,
                'sibling_name'        => $student->full_name,
                'admission_no'        => $student->admission_no,
                'class_id'            => $student->class_id,
                'class_name'          => $student->class?->name,
                'section_id'          => $student->section_id,
                'section_name'        => $student->section?->name,

                // Father Details
                'father_name'         => $parents?->father_name,
                'father_phone'        => $parents?->father_phone,
                'father_email'        => $parents?->father_email,
                'father_occupation'   => $parents?->father_occupation,
                'father_photo'        => $parents?->father_photo,

                // Mother Details
                'mother_name'         => $parents?->mother_name,
                'mother_phone'        => $parents?->mother_phone,
                'mother_email'        => $parents?->mother_email,
                'mother_occupation'   => $parents?->mother_occupation,
                'mother_photo'        => $parents?->mother_photo,

                // Guardian Details
                'guardian_is'         => $parents?->guardian_is ?? 'father',
                'guardian_name'       => $parents?->guardian_name ?? $parents?->father_name ?? $parents?->mother_name,
                'guardian_relation'   => $parents?->guardian_relation ?? ($parents?->guardian_is === 'mother' ? 'Mother' : 'Father'),
                'guardian_phone'      => $parents?->guardian_phone ?? $parents?->father_phone ?? $parents?->mother_phone,
                'guardian_email'      => $parents?->guardian_email ?? $parents?->father_email ?? $parents?->mother_email,
                'guardian_occupation' => $parents?->guardian_occupation ?? $parents?->father_occupation ?? $parents?->mother_occupation,
                'guardian_address'    => $parents?->guardian_address ?? $student->current_address,
                'guardian_photo'      => $parents?->guardian_photo,

                // Addresses
                'current_address'     => $student->current_address,
                'permanent_address'   => $student->permanent_address,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/students/check-email",
     *      operationId="checkStudentDuplicateEmail",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Detect duplicate email across students, parents, staff, and users",
     *      description="Checks if an email is already registered anywhere in the system and returns full entity context (e.g. parent with existing children for sibling auto-linking, or existing student login).",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="email", in="query", required=true, description="Email address to check", @OA\Schema(type="string")),
     *      @OA\Parameter(name="exclude_student_id", in="query", required=false, description="Student ID to exclude (for update forms)", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="exclude_user_id", in="query", required=false, description="User ID to exclude (for update forms)", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Duplicate check response",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="is_duplicate", type="boolean", example=true),
     *              @OA\Property(property="exists", type="boolean", example=true),
     *              @OA\Property(property="email", type="string", example="parent@example.com"),
     *              @OA\Property(property="message", type="string", example="This email is already registered to Parent: Jahangeer"),
     *              @OA\Property(property="types", type="array", @OA\Items(type="string")),
     *              @OA\Property(property="details", type="object")
     *          )
     *      )
     * )
     */
    public function checkEmail(Request $request)
    {
        $email = trim($request->input('email') ?? $request->query('email') ?? '');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success'      => false,
                'is_duplicate' => false,
                'exists'       => false,
                'message'      => 'Please provide a valid email address to check.'
            ], 422);
        }

        $excludeStudentId = $request->input('exclude_student_id') ?? $request->query('exclude_student_id');
        $excludeUserId = $request->input('exclude_user_id') ?? $request->query('exclude_user_id');

        $types = [];
        $details = [];
        $messages = [];

        // 1. Check Student Table
        $studentQuery = Student::where('email', $email)->with(['class:id,name', 'section:id,name']);
        if ($excludeStudentId) {
            $studentQuery->where('id', '!=', $excludeStudentId);
        }
        $existingStudent = $studentQuery->first();
        if ($existingStudent) {
            $types[] = 'student';
            $className = $existingStudent->class?->name ?? '';
            $sectionName = $existingStudent->section?->name ?? '';
            $classSec = trim($className . ($sectionName ? " - {$sectionName}" : ''));
            $details['student'] = [
                'id'           => $existingStudent->id,
                'full_name'    => $existingStudent->full_name,
                'admission_no' => $existingStudent->admission_no,
                'student_code' => $existingStudent->student_code,
                'class'        => $classSec ?: 'N/A',
                'photo'        => $existingStudent->photo ? url($existingStudent->photo) : null,
            ];
            $messages[] = "Student: {$existingStudent->full_name} (Adm: {$existingStudent->admission_no})";
        }

        // 2. Check Parent Table
        $parentQuery = StudentParent::where(function ($q) use ($email) {
            $q->where('father_email', $email)
              ->orWhere('mother_email', $email)
              ->orWhere('guardian_email', $email);
        })->with(['student.class:id,name', 'student.section:id,name']);

        if ($excludeStudentId) {
            $parentQuery->where('student_id', '!=', $excludeStudentId);
        }
        $existingParents = $parentQuery->get();

        if ($existingParents->isNotEmpty()) {
            $types[] = 'parent';
            $matchedAs = [];
            $children = [];
            $parentName = '';

            foreach ($existingParents as $p) {
                if (strtolower($p->father_email ?? '') === strtolower($email)) {
                    $matchedAs[] = 'father';
                    if (!$parentName) $parentName = $p->father_name;
                }
                if (strtolower($p->mother_email ?? '') === strtolower($email)) {
                    $matchedAs[] = 'mother';
                    if (!$parentName) $parentName = $p->mother_name;
                }
                if (strtolower($p->guardian_email ?? '') === strtolower($email)) {
                    $matchedAs[] = 'guardian';
                    if (!$parentName) $parentName = $p->guardian_name;
                }

                if ($p->student) {
                    $cName = $p->student->class?->name ?? '';
                    $sName = $p->student->section?->name ?? '';
                    $classSec = trim($cName . ($sName ? " - {$sName}" : ''));
                    $children[] = [
                        'id'           => $p->student->id,
                        'full_name'    => $p->student->full_name,
                        'admission_no' => $p->student->admission_no,
                        'student_code' => $p->student->student_code,
                        'class'        => $classSec ?: 'N/A',
                    ];
                }
            }

            $firstParent = $existingParents->first();
            $details['parent'] = [
                'id'                 => $firstParent->id,
                'parent_name'        => $parentName ?: 'Parent',
                'matched_roles'      => array_values(array_unique($matchedAs)),
                'father_name'        => $firstParent->father_name,
                'father_phone'       => $firstParent->father_phone,
                'mother_name'        => $firstParent->mother_name,
                'mother_phone'       => $firstParent->mother_phone,
                'guardian_name'      => $firstParent->guardian_name,
                'guardian_phone'     => $firstParent->guardian_phone,
                'existing_children'  => $children,
                'children_count'     => count($children),
            ];
            $childNames = implode(', ', array_column($children, 'full_name'));
            $messages[] = "Parent: " . ($parentName ?: 'Parent') . (count($children) > 0 ? " (Parent of: {$childNames})" : '');
        }

        // 3. Check Staff Table
        $existingStaff = Staff::where('email', $email)->first();
        if ($existingStaff) {
            $types[] = 'staff';
            $details['staff'] = [
                'id'          => $existingStaff->id,
                'full_name'   => $existingStaff->full_name,
                'staff_code'  => $existingStaff->staff_code,
                'role'        => $existingStaff->role,
                'designation' => $existingStaff->designation,
            ];
            $messages[] = "Staff Member: {$existingStaff->full_name} ({$existingStaff->role})";
        }

        // 4. Check User Auth Table
        $userQuery = User::where('email', $email);
        if ($excludeUserId) {
            $userQuery->where('id', '!=', $excludeUserId);
        } elseif ($existingStudent && $existingStudent->user_id) {
            $userQuery->where('id', '!=', $existingStudent->user_id);
        }
        $existingUser = $userQuery->first();
        if ($existingUser) {
            $types[] = 'user';
            $details['user'] = [
                'id'        => $existingUser->id,
                'name'      => $existingUser->name,
                'roles'     => $existingUser->getRoleNames(),
                'is_active' => (bool)$existingUser->is_active,
            ];
            if (!in_array('student', $types) && !in_array('staff', $types)) {
                $messages[] = "User Account: {$existingUser->name} (" . implode(', ', $existingUser->getRoleNames()->toArray()) . ")";
            }
        }

        $isDuplicate = !empty($types);
        $message = $isDuplicate
            ? "Email is already in use by " . implode(' and ', $messages) . "."
            : "Email is available.";

        return response()->json([
            'success'      => true,
            'is_duplicate' => $isDuplicate,
            'exists'       => $isDuplicate,
            'email'        => $email,
            'message'      => $message,
            'types'        => array_values(array_unique($types)),
            'details'      => $isDuplicate ? $details : null,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/students/{id}/siblings",
     *      operationId="getStudentSiblingsList",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Get siblings of a specific student",
     *      description="Returns array of sibling students sharing the same parents",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Student id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="count", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=206),
     *                      @OA\Property(property="admission_no", type="string", example="ADM-002"),
     *                      @OA\Property(property="roll_no", type="string", example="14"),
     *                      @OA\Property(property="first_name", type="string", example="Bob"),
     *                      @OA\Property(property="last_name", type="string", example="Sharma"),
     *                      @OA\Property(property="full_name", type="string", example="Bob Sharma"),
     *                      @OA\Property(property="gender", type="string", example="Male"),
     *                      @OA\Property(property="photo", type="string", example="uploads/student_photos/1794060795_6aef31fb88317.png"),
     *                      @OA\Property(property="dob", type="string", example="2012-05-15"),
     *                      @OA\Property(property="class_id", type="integer", example=3),
     *                      @OA\Property(property="class_name", type="string", example="Class 8"),
     *                      @OA\Property(property="section_id", type="integer", example=1),
     *                      @OA\Property(property="section_name", type="string", example="A"),
     *                      @OA\Property(property="academic_year_id", type="integer", example=1),
     *                      @OA\Property(property="academic_year_name", type="string", example="2026-2027"),
     *                      @OA\Property(property="is_disabled", type="boolean", example=false),
     *                      @OA\Property(property="status", type="integer", example=1)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=404, description="Student not found")
     * )
     */
    public function studentSiblings($id)
    {
        $student = Student::with('parents')->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $siblings = $student->siblings ?? collect();

        return response()->json([
            'success' => true,
            'count'   => count($siblings),
            'data'    => $siblings
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/students/additional-contact/staff",
     *      operationId="getStaffDropdownList",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="List all staff members for Additional Contact dropdown",
     *      description="Fetches a list of staff members with name, role, phone, and email for selecting staff as contact.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getStaffDropdown(Request $request)
    {
        $query = Staff::query()
            ->select('id', 'first_name', 'last_name', 'full_name', 'staff_code', 'role', 'designation', 'phone', 'email', 'photo', 'current_address');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staffList = $query->orderBy('full_name', 'asc')->get()->map(function ($staff) {
            $staff->staff_no = $staff->staff_code;
            return $staff;
        });

        return response()->json([
            'success' => true,
            'data'    => $staffList
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/students/additional-contact/staff/{id}",
     *      operationId="getStaffContactDetails",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Get contact details of a selected staff member",
     *      description="Retrieves a staff member's info formatted as parent/guardian to autofill the Add Student form.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Staff not found")
     * )
     */
    public function getStaffContactInfo($id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found'
            ], 404);
        }

        $fullName = $staff->full_name ?? trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));

        return response()->json([
            'success' => true,
            'data'    => [
                'source'              => 'staff',
                'staff_id'            => $staff->id,
                'staff_no'            => $staff->staff_no ?? $staff->staff_code,
                'name'                => $fullName,
                'full_name'           => $fullName,
                'phone'               => $staff->phone,
                'email'               => $staff->email,
                'designation'         => $staff->designation,
                'role'                => $staff->role,
                'gender'              => $staff->gender,
                'photo'               => $staff->photo,

                // Formatted as Guardian fields for instant form population
                'guardian_is'         => 'other',
                'guardian_name'       => $fullName,
                'guardian_relation'   => 'Staff / School Member',
                'guardian_phone'      => $staff->phone,
                'guardian_email'      => $staff->email,
                'guardian_occupation' => $staff->designation ?? $staff->role ?? 'Staff',
                'guardian_address'    => $staff->current_address,
                'guardian_photo'      => $staff->photo,

                'current_address'     => $staff->current_address,
                'permanent_address'   => $staff->permanent_address,
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/students/additional-contact",
     *      operationId="resolveAdditionalContact",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Resolve and return Additional Contact details for form auto-fill",
     *      description="Unified endpoint called when clicking 'Save Information' in the Add Additional Contact modal.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"type"},
     *              @OA\Property(property="type", type="string", enum={"sibling", "staff"}, example="sibling"),
     *              @OA\Property(property="sibling_id", type="integer", example=1),
     *              @OA\Property(property="staff_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function resolveAdditionalContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'       => 'required|in:sibling,staff',
            'sibling_id' => 'required_if:type,sibling|nullable|integer|exists:students,id',
            'staff_id'   => 'required_if:type,staff|nullable|integer|exists:staff,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->input('type') === 'sibling') {
            return $this->getSiblingContactInfo($request->input('sibling_id'));
        }

        return $this->getStaffContactInfo($request->input('staff_id'));
    }

}