<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:super-admin|admin']);
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *      path="/api/users",
     *      operationId="getUsersList",
     *      tags={"1. Core System - User"},
     *      summary="Get list of Users",
     *      description="Returns list of Users",
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
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="branch_id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *                  @OA\Property(property="photo", type="string", example="http://localhost:8000/uploads/user_photos/example.jpg"),
     *                  @OA\Property(property="password", type="string"),
     *                  @OA\Property(property="is_active", type="boolean"),
     *                  @OA\Property(property="remember_token", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *                  @OA\Property(property="updated_at", type="string"),
     *                  @OA\Property(property="deleted_at", type="string")
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
        return $this->respondPaginatedOrAll(User::with('roles')->latest(), $request);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/api/users",
     *      operationId="storeUser",
     *      tags={"1. Core System - User"},
     *      summary="Store new user with role(s), phone, and photo",
     *      description="Returns saved user data with assigned roles, phone, and photo",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="Sarah Connor"),
     *              @OA\Property(property="full_name", type="string", example="Sarah Connor"),
     *              @OA\Property(property="email", type="string", example="sarah@school.com"),
     *              @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *              @OA\Property(property="photo", type="string", example="/uploads/user_photos/photo.jpg", description="Photo file, URL, or relative path"),
     *              @OA\Property(property="password", type="string", example="password123"),
     *              @OA\Property(
     *                  property="roles",
     *                  type="array",
     *                  description="List of roles or comma-separated string e.g. ['teacher', 'parent']",
     *                  @OA\Items(type="string", example="teacher")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Success"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'full_name' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'photo' => 'nullable',
            'password' => 'required|string|min:6',
            'roles' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();
        $fullName = $input['full_name'] ?? $input['name'] ?? 'User';
        $input['name'] = $fullName;
        $input['full_name'] = $fullName;
        $input['password'] = \Illuminate\Support\Facades\Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/user_photos'), $filename);
            $input['photo'] = '/uploads/user_photos/' . $filename;
        }

        $item = User::create($input);

        if ($request->has('roles')) {
            $roles = $request->roles;
            if (is_string($roles)) {
                $roles = array_map('trim', explode(',', $roles));
            }
            if (in_array('super-admin', $roles) && !$request->user()?->hasRole('super-admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Super Admin can assign the super-admin role.'
                ], 403);
            }
            $item->syncRoles($roles);
        } elseif ($request->has('role')) {
            if ($request->role === 'super-admin' && !$request->user()?->hasRole('super-admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Super Admin can assign the super-admin role.'
                ], 403);
            }
            $item->assignRole($request->role);
        }

        // Sync profile record (Staff/Teacher or Student) with phone and photo
        $assignedRoles = $item->getRoleNames()->toArray();
        $this->syncProfileRecord($item, $assignedRoles, $input['phone'] ?? null, $input['photo'] ?? null);

        return response()->json([
            'success' => true,
            'data' => $item->load('roles')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *      path="/api/users/{id}",
     *      operationId="getUserById",
     *      tags={"1. Core System - User"},
     *      summary="Get User details by ID",
     *      description="Returns user details with assigned roles",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="branch_id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="full_name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *                  @OA\Property(property="photo", type="string", example="http://localhost:8000/uploads/user_photos/example.jpg"),
     *                  @OA\Property(property="is_active", type="boolean")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id)
    {
        $item = User::with('roles')->findOrFail($id);
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
     *      path="/api/users/{id}",
     *      operationId="updateUser",
     *      tags={"1. Core System - User"},
     *      summary="Update existing user and roles",
     *      description="Returns updated user data and roles",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Sarah Connor"),
     *              @OA\Property(property="full_name", type="string", example="Sarah Connor"),
     *              @OA\Property(property="email", type="string", example="sarah@school.com"),
     *              @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *              @OA\Property(property="photo", type="string", example="/uploads/user_photos/photo.jpg", description="Photo file, URL, or relative path"),
     *              @OA\Property(property="password", type="string", example="newpassword"),
     *              @OA\Property(
     *                  property="roles",
     *                  type="array",
     *                  description="List of roles or comma-separated string e.g. ['teacher', 'parent']",
     *                  @OA\Items(type="string", example="parent")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $item = User::findOrFail($id);

        // Disallow non-super-admin from updating a super-admin user
        if ($item->hasRole('super-admin') && !$request->user()?->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can modify a Super Admin user.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'full_name' => 'nullable|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'photo' => 'nullable',
            'password' => 'nullable|string|min:6',
            'roles' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();
        if (isset($input['full_name']) || isset($input['name'])) {
            $fullName = $input['full_name'] ?? $input['name'];
            $input['name'] = $fullName;
            $input['full_name'] = $fullName;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/user_photos'), $filename);
            $input['photo'] = '/uploads/user_photos/' . $filename;
        }

        if (!empty($input['password'])) {
            $input['password'] = \Illuminate\Support\Facades\Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $item->update($input);

        if ($request->has('roles')) {
            $roles = $request->roles;
            if (is_string($roles)) {
                $roles = array_map('trim', explode(',', $roles));
            }
            if (in_array('super-admin', $roles) && !$request->user()?->hasRole('super-admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Super Admin can assign the super-admin role.'
                ], 403);
            }
            $item->syncRoles($roles);
        } elseif ($request->has('role')) {
            if ($request->role === 'super-admin' && !$request->user()?->hasRole('super-admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Super Admin can assign the super-admin role.'
                ], 403);
            }
            $item->syncRoles([$request->role]);
        }

        // Sync profile record (Staff/Teacher or Student) with phone and photo
        $assignedRoles = $item->getRoleNames()->toArray();
        $this->syncProfileRecord($item, $assignedRoles, $input['phone'] ?? $item->phone, $input['photo'] ?? $item->photo);

        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item->load('roles')
        ]);
    }

    /**
     * Automatically synchronize phone and photo to linked Staff or Student profiles.
     */
    private function syncProfileRecord(User $user, array $roles, ?string $phone = null, ?string $photo = null)
    {
        $normalizedRoles = array_map('strtolower', $roles);

        // 1. Staff / Teacher role synchronization
        $isTeacherOrStaff = in_array('teacher', $normalizedRoles) || in_array('staff', $normalizedRoles);
        if ($isTeacherOrStaff) {
            $staff = \App\Models\Staff::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            $staffData = [
                'user_id'   => $user->id,
                'email'     => $user->email,
                'full_name' => $user->full_name ?? $user->name,
                'role'      => in_array('teacher', $normalizedRoles) ? 'Teacher' : 'Staff',
            ];
            if (!empty($phone)) {
                $staffData['phone'] = $phone;
            }
            if (!empty($photo)) {
                $staffData['photo'] = $photo;
            }

            if ($staff) {
                $staff->update($staffData);
            } else {
                \App\Models\Staff::create($staffData);
            }
        }

        // 2. Student role synchronization
        if (in_array('student', $normalizedRoles)) {
            $student = \App\Models\Student::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if ($student) {
                $studentData = [
                    'user_id' => $user->id,
                ];
                if (!empty($phone)) {
                    $studentData['phone'] = $phone;
                    $studentData['mobile_no'] = $phone;
                }
                if (!empty($photo)) {
                    $studentData['photo'] = $photo;
                }
                $student->update($studentData);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *      path="/api/users/{id}",
     *      operationId="deleteUser",
     *      tags={"1. Core System - User"},
     *      summary="Delete existing user",
     *      description="Deletes a record and returns no content",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
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
    public function destroy(Request $request, $id)
    {
        $item = User::findOrFail($id);

        if ($item->hasRole('super-admin') && !$request->user()?->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can delete a Super Admin user.'
            ], 403);
        }

        if ($request->user()?->id === $item->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account.'
            ], 422);
        }

        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}
