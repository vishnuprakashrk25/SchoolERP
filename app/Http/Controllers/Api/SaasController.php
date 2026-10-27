<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class SaasController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/saas/dashboard",
     *      operationId="getSaasDashboard",
     *      tags={"SaaS Management"},
     *      summary="Get SaaS Platform Overview Dashboard",
     *      description="Returns platform-level statistics across all tenant schools for SaaS Super Admin.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="total_schools", type="integer", example=5),
     *                  @OA\Property(property="active_schools", type="integer", example=4),
     *                  @OA\Property(property="inactive_schools", type="integer", example=1),
     *                  @OA\Property(property="total_students", type="integer", example=350),
     *                  @OA\Property(property="total_staff", type="integer", example=45),
     *                  @OA\Property(property="total_users", type="integer", example=420)
     *              )
     *          )
     *      )
     * )
     */
    public function dashboard()
    {
        $totalSchools    = Branch::count();
        $activeSchools   = Branch::where('status', 1)->count();
        $inactiveSchools = Branch::where('status', 0)->count();
        $totalStudents   = Student::count();
        $totalStaff      = Staff::count();
        $totalUsers      = User::count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_schools'    => $totalSchools,
                'active_schools'   => $activeSchools,
                'inactive_schools' => $inactiveSchools,
                'total_students'   => $totalStudents,
                'total_staff'      => $totalStaff,
                'total_users'      => $totalUsers,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/super-admins",
     *      operationId="getSuperAdminsList",
     *      tags={"Super Admin Management"},
     *      summary="List all Super Admins",
     *      description="Fetches all platform-level Super Admins (branch_id = null).",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getSuperAdmins()
    {
        $superAdmins = User::role('super-admin')
            ->whereNull('branch_id')
            ->select('id', 'name', 'full_name', 'email', 'is_active', 'created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $superAdmins
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/super-admin",
     *      operationId="createSuperAdmin",
     *      tags={"Super Admin Management"},
     *      summary="Create a new Super Admin",
     *      description="Creates a platform-level Super Admin account with full privileges.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password"},
     *              @OA\Property(property="name", type="string", example="Global Super Admin"),
     *              @OA\Property(property="email", type="string", example="superadmin2@erp.com"),
     *              @OA\Property(property="password", type="string", example="secret123")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Super Admin created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createSuperAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Ensure super-admin role exists
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'sanctum']);

        $user = User::create([
            'name'      => $request->input('name'),
            'full_name' => $request->input('name'),
            'email'     => $request->input('email'),
            'password'  => Hash::make($request->input('password')),
            'branch_id' => null, // Global SaaS platform level
            'is_active' => true,
        ]);

        $user->assignRole('super-admin');

        return response()->json([
            'success' => true,
            'message' => 'SaaS Super Admin created successfully',
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => 'super-admin',
                'branch_id' => null,
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/saas/schools",
     *      operationId="getSaasSchools",
     *      tags={"SaaS Management"},
     *      summary="List all tenant schools / branches with admin details",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getSchools(Request $request)
    {
        $branches = Branch::latest()->get()->map(function ($branch) {
            $admin = User::where('branch_id', $branch->id)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'principal']);
                })
                ->select('id', 'name', 'full_name', 'email', 'phone', 'is_active')
                ->first();

            return [
                'id'            => $branch->id,
                'name'          => $branch->name,
                'code'          => $branch->code,
                'email'         => $branch->email,
                'phone'         => $branch->phone,
                'address'       => $branch->address,
                'status'        => (bool) $branch->status,
                'students_count'=> Student::where('branch_id', $branch->id)->count(),
                'staff_count'   => Staff::where('branch_id', $branch->id)->count(),
                'admin'         => $admin,
                'created_at'    => $branch->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $branches
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/saas/schools",
     *      operationId="createSaasSchool",
     *      tags={"SaaS Management"},
     *      summary="Create a new School (Tenant) + School Admin (SaaS Onboarding)",
     *      description="Creates a new school/branch and automatically sets up the School Admin user account.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "code", "admin_name", "admin_email", "admin_password"},
     *              @OA\Property(property="name", type="string", example="Green Valley International School"),
     *              @OA\Property(property="code", type="string", example="GVIS"),
     *              @OA\Property(property="email", type="string", example="info@greenvalley.edu"),
     *              @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *              @OA\Property(property="address", type="string", example="123 Academic Road, City"),
     *              @OA\Property(property="admin_name", type="string", example="Principal Sharma"),
     *              @OA\Property(property="admin_email", type="string", example="admin@greenvalley.edu"),
     *              @OA\Property(property="admin_password", type="string", example="password123")
     *          )
     *      ),
     *      @OA\Response(response=201, description="School and Admin created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function createSchool(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:50|unique:branches,code',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Create the School / Branch
            $branch = Branch::create([
                'name'    => $request->input('name'),
                'code'    => strtoupper(trim($request->input('code'))),
                'email'   => $request->input('email'),
                'phone'   => $request->input('phone'),
                'address' => $request->input('address'),
                'status'  => 1,
            ]);

            // 2. Ensure admin role exists
            Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);

            // 3. Create the School Admin
            $adminUser = User::create([
                'name'      => $request->input('admin_name'),
                'full_name' => $request->input('admin_name'),
                'email'     => $request->input('admin_email'),
                'password'  => Hash::make($request->input('admin_password')),
                'branch_id' => $branch->id,
                'is_active' => true,
            ]);

            $adminUser->assignRole('admin');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'School and Admin created successfully',
                'data'    => [
                    'school' => $branch,
                    'admin'  => [
                        'id'        => $adminUser->id,
                        'name'      => $adminUser->name,
                        'email'     => $adminUser->email,
                        'role'      => 'admin',
                        'branch_id' => $branch->id,
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create school: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/saas/schools/{id}/status",
     *      operationId="updateSchoolStatus",
     *      tags={"SaaS Management"},
     *      summary="Toggle or update school active status",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"status"},
     *              @OA\Property(property="status", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Status updated successfully")
     * )
     */
    public function updateSchoolStatus(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        $status = $request->boolean('status');

        $branch->update(['status' => $status ? 1 : 0]);

        // Also toggle users belonging to this school
        User::where('branch_id', $branch->id)->update(['is_active' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'School status updated successfully',
            'data'    => $branch
        ]);
    }
}
