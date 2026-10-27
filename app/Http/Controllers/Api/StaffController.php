<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\User;
use App\Services\AccountNotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/staff",
     *      operationId="getStaffsList",
     *      tags={"2. Staff & HR - Staff"},
     *      summary="Get list of Staffs",
     *      description="Returns list of Staffs",
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
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="staff_no", type="string"),
     *                  @OA\Property(property="role", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date"),
     *                  @OA\Property(property="marital_status", type="string"),
     *                  @OA\Property(property="emergency_mobile", type="string"),
     *                  @OA\Property(property="driving_license", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="is_expert_staff", type="boolean"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="experience", type="string"),
     *                  @OA\Property(property="facebook_url", type="string"),
     *                  @OA\Property(property="twitter_url", type="string"),
     *                  @OA\Property(property="linkedin_url", type="string"),
     *                  @OA\Property(property="instagram_url", type="string"),
     *                  @OA\Property(property="resume", type="string"),
     *                  @OA\Property(property="joining_letter", type="string"),
     *                  @OA\Property(property="other_document", type="string"),
     *                  @OA\Property(property="bank_account_name", type="string"),
     *                  @OA\Property(property="bank_account_no", type="string"),
     *                  @OA\Property(property="bank_name", type="string"),
     *                  @OA\Property(property="bank_branch_name", type="string"),
     *                  @OA\Property(property="epf_no", type="string"),
     *                  @OA\Property(property="basic_salary", type="number", format="float"),
     *                  @OA\Property(property="contract_type", type="string"),
     *                  @OA\Property(property="location", type="string"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time"),
     *                  @OA\Property(property="deleted_at", type="string", format="date-time")
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
        $data = Staff::latest()->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/api/staff",
     *      operationId="storeStaff",
     *      tags={"2. Staff & HR - Staff"},
     *      summary="Store new staff",
     *      description="Returns saved staff data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"role","email"},
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="category", type="string", example="Teaching"),
     *                  @OA\Property(property="staff_code", type="string", example="STF001"),
     *                  @OA\Property(property="staff_no", type="string", example="STF001"),
     *                  @OA\Property(property="role", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date"),
     *                  @OA\Property(property="marital_status", type="string"),
     *                  @OA\Property(property="emergency_mobile", type="string"),
     *                  @OA\Property(property="driving_license", type="string"),
     *                  @OA\Property(property="photo", type="string", format="binary"),
     *                  @OA\Property(property="is_expert_staff", type="boolean"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="experience", type="string"),
     *                  @OA\Property(property="facebook_url", type="string"),
     *                  @OA\Property(property="twitter_url", type="string"),
     *                  @OA\Property(property="linkedin_url", type="string"),
     *                  @OA\Property(property="instagram_url", type="string"),
     *                  @OA\Property(property="resume", type="string", format="binary"),
     *                  @OA\Property(property="joining_letter", type="string", format="binary"),
     *                  @OA\Property(property="other_document", type="string", format="binary"),
     *                  @OA\Property(property="bank_account_name", type="string"),
     *                  @OA\Property(property="bank_account_no", type="string"),
     *                  @OA\Property(property="bank_name", type="string"),
     *                  @OA\Property(property="bank_branch_name", type="string"),
     *                  @OA\Property(property="epf_no", type="string"),
     *                  @OA\Property(property="basic_salary", type="number", format="float"),
     *                  @OA\Property(property="contract_type", type="string"),
     *                  @OA\Property(property="location", type="string")
     *              )
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
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="staff_no", type="string"),
     *                  @OA\Property(property="role", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date"),
     *                  @OA\Property(property="marital_status", type="string"),
     *                  @OA\Property(property="emergency_mobile", type="string"),
     *                  @OA\Property(property="driving_license", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="is_expert_staff", type="boolean"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="experience", type="string"),
     *                  @OA\Property(property="facebook_url", type="string"),
     *                  @OA\Property(property="twitter_url", type="string"),
     *                  @OA\Property(property="linkedin_url", type="string"),
     *                  @OA\Property(property="instagram_url", type="string"),
     *                  @OA\Property(property="resume", type="string"),
     *                  @OA\Property(property="joining_letter", type="string"),
     *                  @OA\Property(property="other_document", type="string"),
     *                  @OA\Property(property="bank_account_name", type="string"),
     *                  @OA\Property(property="bank_account_no", type="string"),
     *                  @OA\Property(property="bank_name", type="string"),
     *                  @OA\Property(property="bank_branch_name", type="string"),
     *                  @OA\Property(property="epf_no", type="string"),
     *                  @OA\Property(property="basic_salary", type="number", format="float"),
     *                  @OA\Property(property="contract_type", type="string"),
     *                  @OA\Property(property="location", type="string"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time"),
     *                  @OA\Property(property="deleted_at", type="string", format="date-time")
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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'staff_code' => 'nullable|string',
            'staff_no' => 'nullable|string',
            'category' => 'nullable|string',
            'role' => 'required',
            'full_name' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'designation' => 'nullable|string',
            'last_name' => 'nullable|string',
            'father_name' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'dob' => 'nullable|date',
            'date_of_joining' => 'nullable|date',
            'marital_status' => 'nullable|string',
            'emergency_mobile' => 'nullable|string',
            'driving_license' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'is_expert_staff' => 'nullable|boolean',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'experience' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'resume' => 'nullable|file|max:5120',
            'joining_letter' => 'nullable|file|max:5120',
            'other_document' => 'nullable|file|max:5120',
            'bank_account_name' => 'nullable|string',
            'bank_account_no' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_branch_name' => 'nullable|string',
            'epf_no' => 'nullable|string',
            'basic_salary' => 'nullable|numeric',
            'contract_type' => 'nullable|string',
            'location' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['staff_code'] = $request->staff_code ?? $request->staff_no;
        
        // Handle direct public file uploads
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_photos'), $filename);
            $data['photo'] = '/uploads/staff_photos/' . $filename;
        }
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $filename = time() . '_resume_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_documents'), $filename);
            $data['resume'] = '/uploads/staff_documents/' . $filename;
        }
        if ($request->hasFile('joining_letter')) {
            $file = $request->file('joining_letter');
            $filename = time() . '_joining_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_documents'), $filename);
            $data['joining_letter'] = '/uploads/staff_documents/' . $filename;
        }
        if ($request->hasFile('other_document')) {
            $file = $request->file('other_document');
            $filename = time() . '_other_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_documents'), $filename);
            $data['other_document'] = '/uploads/staff_documents/' . $filename;
        }

        $fullName = $data['full_name'] ?? $data['name'] ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $data['full_name'] = $fullName;
        unset($data['name']);

        return DB::transaction(function () use ($data, $fullName) {
            $user = User::create([
                'name' => $fullName,
                'full_name' => $fullName,
                'email' => $data['email'],
                'password' => Hash::make('123456'),
                'is_active' => true,
            ]);

            // Assign staff role (teacher/staff/admin) if it exists
            try {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => $data['role'], 'guard_name' => 'web']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => $data['role'], 'guard_name' => 'sanctum']);
                $user->assignRole($data['role']);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not assign staff role: " . $e->getMessage());
            }

            // Check if staff member is ALSO a parent of a student in the school
            $isParent = \App\Models\StudentParent::where('father_email', $data['email'])
                ->orWhere('mother_email', $data['email'])
                ->orWhere('guardian_email', $data['email'])
                ->exists();

            if ($isParent) {
                try {
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'sanctum']);
                    if (!$user->hasRole('parent')) {
                        $user->assignRole('parent');
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not assign parent role: " . $e->getMessage());
                }
            }

            $data['user_id'] = $user->id;
            $item = Staff::create($data);

            // Send welcome password reset / setup email
            AccountNotificationService::sendWelcomeResetEmail($user);

            return response()->json([
                'success' => true,
                'data' => $item->load('user')
            ]);
        });
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *      path="/api/staff/{id}",
     *      operationId="getStaffById",
     *      tags={"2. Staff & HR - Staff"},
     *      summary="Get staff information",
     *      description="Returns staff data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Staff id",
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
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="staff_no", type="string"),
     *                  @OA\Property(property="role", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date"),
     *                  @OA\Property(property="marital_status", type="string"),
     *                  @OA\Property(property="emergency_mobile", type="string"),
     *                  @OA\Property(property="driving_license", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="is_expert_staff", type="boolean"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="experience", type="string"),
     *                  @OA\Property(property="facebook_url", type="string"),
     *                  @OA\Property(property="twitter_url", type="string"),
     *                  @OA\Property(property="linkedin_url", type="string"),
     *                  @OA\Property(property="instagram_url", type="string"),
     *                  @OA\Property(property="resume", type="string"),
     *                  @OA\Property(property="joining_letter", type="string"),
     *                  @OA\Property(property="other_document", type="string"),
     *                  @OA\Property(property="bank_account_name", type="string"),
     *                  @OA\Property(property="bank_account_no", type="string"),
     *                  @OA\Property(property="bank_name", type="string"),
     *                  @OA\Property(property="bank_branch_name", type="string"),
     *                  @OA\Property(property="epf_no", type="string"),
     *                  @OA\Property(property="basic_salary", type="number", format="float"),
     *                  @OA\Property(property="contract_type", type="string"),
     *                  @OA\Property(property="location", type="string"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time"),
     *                  @OA\Property(property="deleted_at", type="string", format="date-time")
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
        $item = Staff::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *      path="/api/staff/{id}",
     *      operationId="updateStaff",
     *      tags={"2. Staff & HR - Staff"},
     *      summary="Update existing staff",
     *      description="Returns updated staff data",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Staff id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="staff_no", type="string"),
     *                  @OA\Property(property="role", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date"),
     *                  @OA\Property(property="marital_status", type="string"),
     *                  @OA\Property(property="emergency_mobile", type="string"),
     *                  @OA\Property(property="driving_license", type="string"),
     *                  @OA\Property(property="photo", type="string", format="binary"),
     *                  @OA\Property(property="is_expert_staff", type="boolean"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="experience", type="string"),
     *                  @OA\Property(property="facebook_url", type="string"),
     *                  @OA\Property(property="twitter_url", type="string"),
     *                  @OA\Property(property="linkedin_url", type="string"),
     *                  @OA\Property(property="instagram_url", type="string"),
     *                  @OA\Property(property="resume", type="string", format="binary"),
     *                  @OA\Property(property="joining_letter", type="string", format="binary"),
     *                  @OA\Property(property="other_document", type="string", format="binary"),
     *                  @OA\Property(property="bank_account_name", type="string"),
     *                  @OA\Property(property="bank_account_no", type="string"),
     *                  @OA\Property(property="bank_name", type="string"),
     *                  @OA\Property(property="bank_branch_name", type="string"),
     *                  @OA\Property(property="epf_no", type="string"),
     *                  @OA\Property(property="basic_salary", type="number", format="float"),
     *                  @OA\Property(property="contract_type", type="string"),
     *                  @OA\Property(property="location", type="string")
     *              )
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
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="staff_no", type="string"),
     *                  @OA\Property(property="role", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="father_name", type="string"),
     *                  @OA\Property(property="mother_name", type="string"),
     *                  @OA\Property(property="gender", type="string"),
     *                  @OA\Property(property="dob", type="string", format="date"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date"),
     *                  @OA\Property(property="marital_status", type="string"),
     *                  @OA\Property(property="emergency_mobile", type="string"),
     *                  @OA\Property(property="driving_license", type="string"),
     *                  @OA\Property(property="photo", type="string"),
     *                  @OA\Property(property="is_expert_staff", type="boolean"),
     *                  @OA\Property(property="current_address", type="string"),
     *                  @OA\Property(property="permanent_address", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="experience", type="string"),
     *                  @OA\Property(property="facebook_url", type="string"),
     *                  @OA\Property(property="twitter_url", type="string"),
     *                  @OA\Property(property="linkedin_url", type="string"),
     *                  @OA\Property(property="instagram_url", type="string"),
     *                  @OA\Property(property="resume", type="string"),
     *                  @OA\Property(property="joining_letter", type="string"),
     *                  @OA\Property(property="other_document", type="string"),
     *                  @OA\Property(property="bank_account_name", type="string"),
     *                  @OA\Property(property="bank_account_no", type="string"),
     *                  @OA\Property(property="bank_name", type="string"),
     *                  @OA\Property(property="bank_branch_name", type="string"),
     *                  @OA\Property(property="epf_no", type="string"),
     *                  @OA\Property(property="basic_salary", type="number", format="float"),
     *                  @OA\Property(property="contract_type", type="string"),
     *                  @OA\Property(property="location", type="string"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time"),
     *                  @OA\Property(property="deleted_at", type="string", format="date-time")
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
        $item = Staff::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'staff_code' => 'nullable|string',
            'staff_no' => 'nullable|string',
            'category' => 'nullable|string',
            'role' => 'sometimes|required',
            'first_name' => 'sometimes|required',
            'email' => 'sometimes|required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        if ($request->has('staff_code') || $request->has('staff_no')) {
            $data['staff_code'] = $request->staff_code ?? $request->staff_no;
        }
        
        // Handle direct public file uploads
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_photos'), $filename);
            $data['photo'] = '/uploads/staff_photos/' . $filename;
        }
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $filename = time() . '_resume_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_documents'), $filename);
            $data['resume'] = '/uploads/staff_documents/' . $filename;
        }
        if ($request->hasFile('joining_letter')) {
            $file = $request->file('joining_letter');
            $filename = time() . '_joining_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_documents'), $filename);
            $data['joining_letter'] = '/uploads/staff_documents/' . $filename;
        }
        if ($request->hasFile('other_document')) {
            $file = $request->file('other_document');
            $filename = time() . '_other_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/staff_documents'), $filename);
            $data['other_document'] = '/uploads/staff_documents/' . $filename;
        }

        if ($request->has('first_name') || $request->has('last_name') || $request->has('full_name') || $request->has('name')) {
            $fullName = $data['full_name'] ?? $data['name'] ?? trim(($data['first_name'] ?? $item->first_name) . ' ' . ($data['last_name'] ?? $item->last_name));
            $data['full_name'] = $fullName;
            unset($data['name']);
        }

        $item->update($data);

        if ($item->user) {
            $fullName = $item->full_name;
            $userUpdate = [
                'name' => $fullName,
                'full_name' => $fullName
            ];
            if ($request->has('email')) {
                $userUpdate['email'] = $request->email;
            }
            $item->user->update($userUpdate);
        }
        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *      path="/api/staff/{id}",
     *      operationId="deleteStaff",
     *      tags={"2. Staff & HR - Staff"},
     *      summary="Delete existing staff",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Staff id",
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
        if (!auth()->user()?->hasAnyRole(['super-admin', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators can delete staff records.'
            ], 403);
        }

        $item = Staff::findOrFail($id);
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
    // Bulk create staff
    /**
     * @OA\Post(
     *      path="/api/staff/bulk",
     *      operationId="bulkStoreStaff",
     *      tags={"2. Staff & HR - Staff"},
     *      summary="Bulk store new staff",
     *      description="Returns saved staff data",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"staffs"},
     *              @OA\Property(
     *                  property="staffs",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      required={"email","role"},
     *                      @OA\Property(property="email", type="string"),
     *                      @OA\Property(property="phone", type="string"),
     *                      @OA\Property(property="designation", type="string"),
     *                      @OA\Property(property="category", type="string", example="Teaching"),
     *                      @OA\Property(property="staff_code", type="string", example="STF001"),
     *                      @OA\Property(property="staff_no", type="string", example="STF001"),
     *                      @OA\Property(property="role", type="string"),
     *                      @OA\Property(property="first_name", type="string"),
     *                      @OA\Property(property="last_name", type="string"),
     *                      @OA\Property(property="full_name", type="string"),
     *                      @OA\Property(property="father_name", type="string"),
     *                      @OA\Property(property="mother_name", type="string"),
     *                      @OA\Property(property="gender", type="string"),
     *                      @OA\Property(property="dob", type="string", format="date"),
     *                      @OA\Property(property="date_of_joining", type="string", format="date"),
     *                      @OA\Property(property="marital_status", type="string"),
     *                      @OA\Property(property="emergency_mobile", type="string"),
     *                      @OA\Property(property="driving_license", type="string"),
     *                      @OA\Property(property="photo", type="string"),
     *                      @OA\Property(property="is_expert_staff", type="boolean"),
     *                      @OA\Property(property="current_address", type="string"),
     *                      @OA\Property(property="permanent_address", type="string"),
     *                      @OA\Property(property="qualifications", type="string"),
     *                      @OA\Property(property="experience", type="string"),
     *                      @OA\Property(property="facebook_url", type="string"),
     *                      @OA\Property(property="twitter_url", type="string"),
     *                      @OA\Property(property="linkedin_url", type="string"),
     *                      @OA\Property(property="instagram_url", type="string"),
     *                      @OA\Property(property="resume", type="string"),
     *                      @OA\Property(property="joining_letter", type="string"),
     *                      @OA\Property(property="other_document", type="string"),
     *                      @OA\Property(property="bank_account_name", type="string"),
     *                      @OA\Property(property="bank_account_no", type="string"),
     *                      @OA\Property(property="bank_name", type="string"),
     *                      @OA\Property(property="bank_branch_name", type="string"),
     *                      @OA\Property(property="epf_no", type="string"),
     *                      @OA\Property(property="basic_salary", type="number", format="float"),
     *                      @OA\Property(property="contract_type", type="string"),
     *                      @OA\Property(property="location", type="string")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Staffs created successfully"),
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
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function bulkStore(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'staffs' => 'required|array',
            'staffs.*.staff_code' => 'nullable|string',
            'staffs.*.staff_no' => 'nullable|string',
            'staffs.*.category' => 'nullable|string',
            'staffs.*.role' => 'required',
            'staffs.*.full_name' => 'nullable|string',
            'staffs.*.first_name' => 'nullable|string',
            'staffs.*.last_name' => 'nullable|string',
            'staffs.*.email' => 'required|email|distinct|unique:staff,email|unique:users,email',
            'staffs.*.phone' => 'nullable|string',
            'staffs.*.designation' => 'nullable|string',
            'staffs.*.father_name' => 'nullable|string',
            'staffs.*.mother_name' => 'nullable|string',
            'staffs.*.gender' => 'nullable|string',
            'staffs.*.dob' => 'nullable|date',
            'staffs.*.date_of_joining' => 'nullable|date',
            'staffs.*.marital_status' => 'nullable|string',
            'staffs.*.emergency_mobile' => 'nullable|string',
            'staffs.*.driving_license' => 'nullable|string',
            'staffs.*.photo' => 'nullable|string',
            'staffs.*.is_expert_staff' => 'nullable|boolean',
            'staffs.*.current_address' => 'nullable|string',
            'staffs.*.permanent_address' => 'nullable|string',
            'staffs.*.qualifications' => 'nullable|string',
            'staffs.*.experience' => 'nullable|string',
            'staffs.*.facebook_url' => 'nullable|url',
            'staffs.*.twitter_url' => 'nullable|url',
            'staffs.*.linkedin_url' => 'nullable|url',
            'staffs.*.instagram_url' => 'nullable|url',
            'staffs.*.resume' => 'nullable|string',
            'staffs.*.joining_letter' => 'nullable|string',
            'staffs.*.other_document' => 'nullable|string',
            'staffs.*.bank_account_name' => 'nullable|string',
            'staffs.*.bank_account_no' => 'nullable|string',
            'staffs.*.bank_name' => 'nullable|string',
            'staffs.*.bank_branch_name' => 'nullable|string',
            'staffs.*.epf_no' => 'nullable|string',
            'staffs.*.basic_salary' => 'nullable|numeric',
            'staffs.*.contract_type' => 'nullable|string',
            'staffs.*.location' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $createdStaffs = [];

            foreach ($request->staffs as $staffData) {
                $staffCode = $staffData['staff_code'] ?? $staffData['staff_no'] ?? null;
                if ($staffCode) {
                    $staffData['staff_code'] = $staffCode;
                }
                $fullName = $staffData['full_name'] ?? $staffData['name'] ?? trim(($staffData['first_name'] ?? '') . ' ' . ($staffData['last_name'] ?? ''));
                $staffData['full_name'] = $fullName;
                unset($staffData['name']);

                $user = User::create([
                    'name' => $fullName,
                    'full_name' => $fullName,
                    'email' => $staffData['email'],
                    'password' => Hash::make('123456'),
                    'is_active' => true,
                ]);

                try {
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => $staffData['role'], 'guard_name' => 'web']);
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => $staffData['role'], 'guard_name' => 'sanctum']);
                    $user->assignRole($staffData['role']);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not assign staff role in bulk: " . $e->getMessage());
                }

                $staffData['user_id'] = $user->id;
                $staff = Staff::create($staffData);
                $createdStaffs[] = $staff->id;

                // Send welcome password reset / setup email
                AccountNotificationService::sendWelcomeResetEmail($user);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully',
                'data' => Staff::with('user')->whereIn('id', $createdStaffs)->get()
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
