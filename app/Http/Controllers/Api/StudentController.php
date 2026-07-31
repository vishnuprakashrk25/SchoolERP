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

class StudentController extends Controller
{

    // Get all students
    /**
     * @OA\Get(
     *      path="/api/students",
     *      operationId="getStudentsList",
     *      tags={"4. Student Enrollment - Student"},
     *      summary="Get list of Students",
     *      description="Returns list of Students",
     *      security={{"sanctum":{}}},
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
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
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
        $students = Student::with([
            'user',
            'class',
            'section',
            'category',
            'parents',
            'studentAcademics.academicYear',
            'emergencyContacts'
        ])->paginate($limit);

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
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              required={"admission_no","first_name","class_id","section_id","academic_year_id"},
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
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
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
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
            'first_name' => 'required',
            'email' => 'nullable|email|unique:users,email|unique:students,email',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'gender' => 'nullable|string',
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
        $user = User::create([
            'name' => $request->first_name,
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

        $photoPath = $request->photo;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/student_photos'), $filename);
            $photoPath = '/uploads/student_photos/' . $filename;
        }

        $student = Student::create([
            'user_id' => $user->id,
            'admission_no' => $request->admission_no,
            'student_code' => $request->student_code,
            'admission_date' => $request->admission_date,
            'roll_no' => $request->roll_no,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
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
            'previous_school_name' => $request->previous_school_name,
            'previous_qualification' => $request->previous_qualification,
            'previous_school_details' => $request->previous_school_details,
            'note' => $request->note,
            'emergency_phone' => $request->emergency_phone,
            'alternate_phone' => $request->alternate_phone,
            'student_group_id' => $request->student_group_id,
            'medical_history' => $request->medical_history,
            'is_disabled' => $request->is_disabled ?? false,
            'disable_reason' => $request->disable_reason,
            'disable_date' => $request->disable_date,
            'route_id' => $request->route_id,
            'vehicle_id' => $request->vehicle_id,
            'dormitory_id' => $request->dormitory_id,
            'room_id' => $request->room_id,
            'photo' => $photoPath
        ]);

        $fatherPhoto = $request->father_photo;
        if ($request->hasFile('father_photo')) {
            $fatherPhoto = $request->file('father_photo')->store('parent_photos', 'public');
        }

        $motherPhoto = $request->mother_photo;
        if ($request->hasFile('mother_photo')) {
            $motherPhoto = $request->file('mother_photo')->store('parent_photos', 'public');
        }

        $guardianPhoto = $request->guardian_photo;
        if ($request->hasFile('guardian_photo')) {
            $guardianPhoto = $request->file('guardian_photo')->store('parent_photos', 'public');
        }

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

        StudentAcademic::create([
            'student_id' => $student->id,
            'academic_year_id' => $request->academic_year_id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id
        ]);

        if ($request->has('emergency_contacts') && is_array($request->emergency_contacts)) {
            $student->emergencyContacts()->createMany($request->emergency_contacts);
        }

        if ($request->has('documents') && is_array($request->documents)) {
            foreach ($request->documents as $doc) {
                if (isset($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filePath = $doc['file']->store('student_documents', 'public');
                    $student->documents()->create([
                        'title' => $doc['title'] ?? 'Document',
                        'file' => $filePath
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => Student::with(['class', 'section', 'parents', 'user', 'group', 'route', 'vehicle', 'dormitory', 'room', 'emergencyContacts'])->find($student->id)
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
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
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
     *              required={"first_name","class_id","section_id","academic_year_id"},
     *              @OA\Property(property="admission_no", type="string", example="ADM123"),
     *              @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *              @OA\Property(property="roll_no", type="string", example="101"),
     *              @OA\Property(property="first_name", type="string", example="John"),
     *              @OA\Property(property="last_name", type="string", example="Doe"),
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
     *                  @OA\Property(property="address", type="string"),
     *                  @OA\Property(property="date_of_birth", type="string"),
     *                  @OA\Property(property="nationality", type="string"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
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
            'first_name' => 'sometimes|required',
            'student_code' => 'nullable|string|unique:students,student_code,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $student->user_id . '|unique:students,email,' . $id,
            'class_id' => 'sometimes|required|exists:classes,id',
            'section_id' => 'sometimes|required|exists:sections,id',
            'academic_year_id' => 'sometimes|required|exists:academic_years,id',
            'gender' => 'nullable|string',
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
            $updateData['photo'] = $request->file('photo')->store('student_photos', 'public');
        } else {
            unset($updateData['photo']); // Do not overwrite with a null or string if it's not a file
        }

        $student->update($updateData);

        if ($student->user) {
            $student->user->update([
                'name' => $request->first_name,
                'email' => $request->email
            ]);
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

        if ($request->hasFile('father_photo')) {
            $parentUpdateData['father_photo'] = $request->file('father_photo')->store('parent_photos', 'public');
        }
        if ($request->hasFile('mother_photo')) {
            $parentUpdateData['mother_photo'] = $request->file('mother_photo')->store('parent_photos', 'public');
        }
        if ($request->hasFile('guardian_photo')) {
            $parentUpdateData['guardian_photo'] = $request->file('guardian_photo')->store('parent_photos', 'public');
        }

        StudentParent::updateOrCreate(
            ['student_id' => $student->id],
            $parentUpdateData
        );

        StudentAcademic::updateOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $request->academic_year_id],
            ['class_id' => $request->class_id, 'section_id' => $request->section_id]
        );

        if ($request->has('emergency_contacts') && is_array($request->emergency_contacts)) {
            $student->emergencyContacts()->delete();
            $student->emergencyContacts()->createMany($request->emergency_contacts);
        }

        if ($request->has('documents') && is_array($request->documents)) {
            foreach ($request->documents as $doc) {
                if (isset($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $filePath = $doc['file']->store('student_documents', 'public');
                    $student->documents()->create([
                        'title' => $doc['title'] ?? 'Document',
                        'file' => $filePath
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Student updated',
            'data' => Student::with(['class', 'section', 'parents', 'user', 'group', 'route', 'vehicle', 'dormitory', 'room', 'emergencyContacts'])->find($student->id)
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
     *                      required={"admission_no","first_name","class_id","section_id","academic_year_id"},
     *                      @OA\Property(property="admission_no", type="string", example="ADM123"),
     *                      @OA\Property(property="admission_date", type="string", format="date", example="2023-01-01"),
     *                      @OA\Property(property="roll_no", type="string", example="101"),
     *                      @OA\Property(property="first_name", type="string", example="John"),
     *                      @OA\Property(property="last_name", type="string", example="Doe"),
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
            'students.*.first_name' => 'required',
            'students.*.email' => 'nullable|email|distinct|unique:users,email|unique:students,email',
            'students.*.class_id' => 'required|exists:classes,id',
            'students.*.section_id' => 'required|exists:sections,id',
            'students.*.academic_year_id' => 'required|exists:academic_years,id',
            'students.*.gender' => 'nullable|string',
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
                $user = User::create([
                    'name' => $studentData['first_name'],
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

                $student = Student::create([
                    'user_id' => $user->id,
                    'admission_no' => $studentData['admission_no'] ?? null,
                    'admission_date' => $studentData['admission_date'] ?? null,
                    'roll_no' => $studentData['roll_no'] ?? null,
                    'first_name' => $studentData['first_name'] ?? null,
                    'last_name' => $studentData['last_name'] ?? null,
                    'email' => $studentData['email'] ?? null,
                    'phone' => $studentData['phone'] ?? null,
                    'class_id' => $studentData['class_id'] ?? null,
                    'section_id' => $studentData['section_id'] ?? null,
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
                    'previous_school_name' => $studentData['previous_school_name'] ?? null,
                    'previous_qualification' => $studentData['previous_qualification'] ?? null,
                    'previous_school_details' => $studentData['previous_school_details'] ?? null,
                    'note' => $studentData['note'] ?? null,
                    'emergency_phone' => $studentData['emergency_phone'] ?? null,
                    'alternate_phone' => $studentData['alternate_phone'] ?? null,
                    'student_group_id' => $studentData['student_group_id'] ?? null,
                    'medical_history' => $studentData['medical_history'] ?? null,
                    'is_disabled' => $studentData['is_disabled'] ?? false,
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

                StudentAcademic::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $studentData['academic_year_id'] ?? null,
                    'class_id' => $studentData['class_id'] ?? null,
                    'section_id' => $studentData['section_id'] ?? null
                ]);

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

}