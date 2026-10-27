<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AccountNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/teachers",
     *      operationId="getTeachersList",
     *      tags={"0. Authentication - Teacher"},
     *      summary="Get list of Teachers",
     *      description="Returns list of teachers with user information (from unified staff module)",
     *      security={{"sanctum":{}}},
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
     *                      @OA\Property(property="user_id", type="integer"),
     *                      @OA\Property(property="staff_no", type="string"),
     *                      @OA\Property(property="role", type="string"),
     *                      @OA\Property(property="first_name", type="string"),
     *                      @OA\Property(property="last_name", type="string"),
     *                      @OA\Property(property="email", type="string"),
     *                      @OA\Property(property="designation", type="string"),
     *                      @OA\Property(property="qualifications", type="string"),
     *                      @OA\Property(property="date_of_joining", type="string", format="date"),
     *                      @OA\Property(property="phone", type="string"),
     *                      @OA\Property(property="gender", type="string"),
     *                      @OA\Property(property="dob", type="string", format="date"),
     *                      @OA\Property(property="marital_status", type="string"),
     *                      @OA\Property(property="photo", type="string"),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time"),
     *                      @OA\Property(property="deleted_at", type="string", format="date-time")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index()
    {
        $teachers = Teacher::with('user')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/teachers",
     *      operationId="storeTeacher",
     *      tags={"0. Authentication - Teacher"},
     *      summary="Store new teacher",
     *      description="Creates a new user and teacher record",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password", "designation"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="designation", type="string"),
     *              @OA\Property(property="qualification", type="string"),
     *              @OA\Property(property="joining_date", type="string", format="date")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Teacher created",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Teacher created successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="staff_no", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date")
     *              )
     *          )
     *       ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'designation' => 'required|string',
            'branch_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'branch_id' => $request->branch_id,
                'is_active' => true,
            ]);

            // Assign Teacher role safely across guards
            try {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'sanctum']);
                $user->assignRole('teacher');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Could not assign teacher role: " . $e->getMessage());
            }

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'staff_no' => 'TCH-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'role' => 'Teacher',
                'first_name' => $request->name,
                'email' => $request->email,
                'designation' => $request->designation,
                'qualifications' => $request->qualification,
                'date_of_joining' => $request->joining_date,
            ]);

            // Send welcome password reset / setup email
            AccountNotificationService::sendWelcomeResetEmail($user);

            return response()->json([
                'success' => true,
                'message' => 'Teacher created successfully',
                'data' => $teacher->load('user')
            ], 201);
        });
    }

    /**
     * @OA\Get(
     *      path="/api/teachers/{id}",
     *      operationId="getTeacherById",
     *      tags={"0. Authentication - Teacher"},
     *      summary="Get teacher details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="staff_no", type="string"),
     *                  @OA\Property(property="role", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="designation", type="string"),
     *                  @OA\Property(property="qualifications", type="string"),
     *                  @OA\Property(property="date_of_joining", type="string", format="date")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/teachers/{id}",
     *      operationId="updateTeacher",
     *      tags={"0. Authentication - Teacher"},
     *      summary="Update teacher",
     *      description="Updates teacher profile and associated user details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="full_name", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="designation", type="string"),
     *              @OA\Property(property="qualification", type="string"),
     *              @OA\Property(property="joining_date", type="string", format="date")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'full_name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . ($user->id ?? 0),
            'password' => 'nullable|string|min:6',
            'designation' => 'nullable|string',
            'qualification' => 'nullable|string',
            'joining_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $teacher, $user) {
            $fullName = $request->full_name ?? $request->name;

            if ($user) {
                $userUpdate = [];
                if ($fullName) {
                    $userUpdate['name'] = $fullName;
                    $userUpdate['full_name'] = $fullName;
                }
                if ($request->has('email')) {
                    $userUpdate['email'] = $request->email;
                }
                if (!empty($userUpdate)) {
                    $user->update($userUpdate);
                }
            }

            if ($user && $request->has('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $teacherData = [];
            if ($request->has('designation')) $teacherData['designation'] = $request->designation;
            if ($request->has('qualification')) $teacherData['qualifications'] = $request->qualification;
            if ($request->has('joining_date')) $teacherData['date_of_joining'] = $request->joining_date;
            if ($fullName) {
                $teacherData['name'] = $fullName;
                $teacherData['full_name'] = $fullName;
                $teacherData['first_name'] = $fullName;
            }
            if ($request->has('email')) $teacherData['email'] = $request->email;

            if (!empty($teacherData)) {
                $teacher->update($teacherData);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully',
            'data' => $teacher->load('user')
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/teachers/{id}",
     *      operationId="deleteTeacher",
     *      tags={"0. Authentication - Teacher"},
     *      summary="Delete teacher",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        DB::transaction(function () use ($teacher) {
            if ($teacher->user) {
                $teacher->user()->delete();
            }
            $teacher->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully'
        ]);
    }
}
