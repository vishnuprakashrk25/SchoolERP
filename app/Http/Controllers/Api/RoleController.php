<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Controller constructor.
     * Enforces authentication and authorization for Super Admin / Admin.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware(function ($request, $next) {
            if (app()->runningInConsole()) {
                return $next($request);
            }

            $user = $request->user();
            if ($user) {
                $isAuthorized = $user->hasRole('super-admin')
                    || $user->hasRole('admin')
                    || $user->hasRole('Super Admin')
                    || $user->hasRole('Admin')
                    || $user->can('manage_roles_permissions');

                if (!$isAuthorized) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. Only Super Admin or Admin can manage roles and assign permissions.',
                    ], 403);
                }
            }

            return $next($request);
        });
    }

    /**
     * Resolve any role dynamically by ID or Name (case-insensitive).
     * If role does not exist yet, creates it dynamically.
     */
    protected function resolveRole(string|int $id): Role
    {
        if (is_numeric($id)) {
            return Role::findOrFail($id);
        }

        $name = trim($id);

        $role = Role::where('name', $name)
            ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if (!$role && strtolower($name) === 'parents') {
            $role = Role::where('name', 'parent')
                ->orWhereRaw('LOWER(name) = ?', ['parent'])
                ->first();
        }

        if (!$role) {
            $role = Role::firstOrCreate([
                'name'       => strtolower($name),
                'guard_name' => 'web',
            ]);
        }

        return $role;
    }

    /**
     * Dynamically count users assigned to a role without relying on morphedByMany.
     */
    protected function getRoleUsersCount($roleId): int
    {
        return DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->count();
    }

    /**
     * Dynamically extract a functional module / category from any permission name.
     * Examples:
     * - 'view_students' -> 'Students'
     * - 'submit_student_attendance' -> 'Student Attendance'
     * - 'manage_fee_structures' -> 'Fee Structures'
     */
    protected function extractModuleName(string $name): string
    {
        $parts = explode('_', strtolower(trim($name)));
        if (count($parts) > 1) {
            $commonVerbs = [
                'view', 'create', 'add', 'edit', 'update',
                'delete', 'destroy', 'manage', 'mark', 'submit',
                'enter', 'upload', 'download', 'collect'
            ];
            $entityParts = array_values(array_filter($parts, fn($p) => !in_array($p, $commonVerbs)));
            if (!empty($entityParts)) {
                return ucwords(implode(' ', $entityParts));
            }
        }
        return ucwords(str_replace(['_', '-'], ' ', $name));
    }

    /**
     * @OA\Get(
     *      path="/api/roles",
     *      operationId="getRolesList",
     *      tags={"1. Core System - Role"},
     *      summary="Get list of all Roles dynamically",
     *      description="Returns all roles from the database with user counts and assigned permissions",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="total", type="integer", example=5),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->orderBy('name', 'asc')->get();

        $roleIds = $roles->pluck('id')->toArray();
        $userCounts = DB::table('model_has_roles')
            ->whereIn('role_id', $roleIds)
            ->select('role_id', DB::raw('count(*) as count'))
            ->groupBy('role_id')
            ->pluck('count', 'role_id');

        $roles->transform(function ($role) use ($userCounts) {
            $role->users_count = (int) ($userCounts[$role->id] ?? 0);
            $role->display_name = ucwords(str_replace(['-', '_'], ' ', $role->name));
            return $role;
        });

        return response()->json([
            'success' => true,
            'total'   => $roles->count(),
            'data'    => $roles,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/roles/{id}",
     *      operationId="getRoleById",
     *      tags={"1. Core System - Role"},
     *      summary="Get details of a specific Role dynamically",
     *      description="Returns role details including assigned permissions by role ID or name",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Role ID or Name (e.g. 'teacher', 'parent', 'principal', '2')", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Role not found")
     * )
     */
    public function show($id)
    {
        $role = $this->resolveRole($id)->load('permissions');
        $role->users_count = $this->getRoleUsersCount($role->id);
        $role->display_name = ucwords(str_replace(['-', '_'], ' ', $role->name));

        return response()->json([
            'success' => true,
            'data'    => $role,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/roles",
     *      operationId="createRole",
     *      tags={"1. Core System - Role"},
     *      summary="Create a new Role dynamically",
     *      description="Creates a new role dynamically and optionally assigns permissions to it",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Accountant"),
     *              @OA\Property(property="guard_name", type="string", example="web"),
     *              @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"view_fees", "collect_fees"})
     *          )
     *      ),
     *      @OA\Response(response=201, description="Role created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:100|unique:roles,name',
            'guard_name'    => 'nullable|string|max:50',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $guardName = $request->guard_name ?: 'web';
        $role = Role::create([
            'name'       => strtolower(trim($request->name)),
            'guard_name' => $guardName,
        ]);

        if ($request->filled('permissions')) {
            foreach ($request->permissions as $permName) {
                $clean = trim($permName);
                if (!empty($clean)) {
                    Permission::firstOrCreate(['name' => $clean, 'guard_name' => $guardName]);
                }
            }
            $role->syncPermissions($request->permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role->users_count = 0;
        $role->display_name = ucwords(str_replace(['-', '_'], ' ', $role->name));

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data'    => $role->load('permissions'),
        ], 201);
    }

    /**
     * @OA\Put(
     *      path="/api/roles/{id}",
     *      operationId="updateRole",
     *      tags={"1. Core System - Role"},
     *      summary="Update existing Role dynamically",
     *      description="Updates role name and optionally syncs permissions",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Senior Teacher"),
     *              @OA\Property(property="permissions", type="array", @OA\Items(type="string"))
     *          )
     *      ),
     *      @OA\Response(response=200, description="Role updated successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $role = $this->resolveRole($id);

        $validator = Validator::make($request->all(), [
            'name'          => 'sometimes|string|max:100|unique:roles,name,' . $role->id,
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->filled('name')) {
            if (in_array(strtolower($role->name), ['admin', 'super-admin', 'super admin', 'superadmin']) && strtolower($request->name) !== strtolower($role->name)) {
                return response()->json([
                    'success' => false,
                    'message' => "The core administrator role name cannot be modified.",
                ], 403);
            }
            $role->name = strtolower(trim($request->name));
            $role->save();
        }

        if ($request->has('permissions')) {
            foreach ($request->permissions as $permName) {
                $clean = trim($permName);
                if (!empty($clean)) {
                    Permission::firstOrCreate(['name' => $clean, 'guard_name' => $role->guard_name ?: 'web']);
                }
            }
            $role->syncPermissions($request->permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role->users_count = $this->getRoleUsersCount($role->id);
        $role->display_name = ucwords(str_replace(['-', '_'], ' ', $role->name));

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data'    => $role->load('permissions'),
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/roles/{id}",
     *      operationId="deleteRole",
     *      tags={"1. Core System - Role"},
     *      summary="Delete a Role dynamically",
     *      description="Deletes a role dynamically if no users are currently assigned to it",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Role deleted successfully"),
     *      @OA\Response(response=422, description="Cannot delete role with assigned users"),
     *      @OA\Response(response=403, description="Cannot delete system admin")
     * )
     */
    public function destroy($id)
    {
        $role = $this->resolveRole($id);

        if (in_array(strtolower($role->name), ['admin', 'super-admin', 'super admin', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => "The core administrator role '{$role->name}' is protected and cannot be deleted.",
            ], 403);
        }

        $usersCount = $this->getRoleUsersCount($role->id);
        if ($usersCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete role '{$role->name}' because it is currently assigned to {$usersCount} user(s). Please unassign users first.",
            ], 422);
        }

        $role->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' deleted successfully",
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/roles/{id}/permissions",
     *      operationId="getRolePermissions",
     *      tags={"1. Core System - Role"},
     *      summary="Super Admin: Get permissions for any Role dynamically",
     *      description="Returns currently assigned permissions and all available system permissions dynamically grouped by module for ANY role (e.g. 'teacher', 'parent', 'principal', 'admin', 'super-admin', or ID)",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Role ID or Name (e.g. 'teacher', 'parent', 'principal', 'admin', '2')", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function getRolePermissions($id)
    {
        $role = $this->resolveRole($id);
        $assignedPerms = $role->permissions()->pluck('name')->toArray();

        $allPermissions = Permission::orderBy('name', 'asc')->get();

        $permissionsList = $allPermissions->map(function ($perm) use ($assignedPerms) {
            return [
                'id'          => $perm->id,
                'name'        => $perm->name,
                'guard_name'  => $perm->guard_name,
                'is_assigned' => in_array($perm->name, $assignedPerms),
                'module'      => $this->extractModuleName($perm->name),
            ];
        });

        $groupedByModule = $permissionsList->groupBy('module');

        return response()->json([
            'success' => true,
            'data'    => [
                'role'                 => [
                    'id'           => $role->id,
                    'name'         => $role->name,
                    'display_name' => ucwords(str_replace(['-', '_'], ' ', $role->name)),
                    'guard_name'   => $role->guard_name,
                    'users_count'  => $this->getRoleUsersCount($role->id),
                ],
                'assigned_permissions' => $assignedPerms,
                'total_permissions'    => $allPermissions->count(),
                'modules'              => $groupedByModule,
                'permissions'          => $permissionsList,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/roles/{id}/permissions",
     *      operationId="syncRolePermissions",
     *      tags={"1. Core System - Role"},
     *      summary="Super Admin: Assign / Sync permissions for any Role dynamically",
     *      description="Dynamically assigns or syncs permissions for ANY role (e.g. Teacher, Parents, Principal, Admin) or optionally for a specific individual user belonging to this role",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Role ID or Name (e.g. 'teacher', 'parent', 'principal', 'admin')", @OA\Schema(type="string")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"permissions"},
     *              @OA\Property(property="permissions", type="array", @OA\Items(type="string")),
     *              @OA\Property(property="user_id", type="integer", nullable=true, description="Optional specific user ID to assign custom direct permissions")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Permissions assigned successfully"),
     *      @OA\Response(response=422, description="Validation error"),
     *      @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function syncRolePermissions(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'permissions'   => 'required|array',
            'permissions.*' => 'string',
            'user_id'       => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $role = $this->resolveRole($id);

        foreach ($request->permissions as $permName) {
            $clean = trim($permName);
            if (!empty($clean)) {
                Permission::firstOrCreate(['name' => $clean, 'guard_name' => 'web']);
            }
        }

        if ($request->filled('user_id')) {
            $user = User::findOrFail($request->user_id);

            if (!$user->hasRole($role->name)) {
                $user->assignRole($role->name);
            }

            $user->syncPermissions($request->permissions);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            return response()->json([
                'success' => true,
                'message' => "Permissions successfully assigned to user '{$user->name}' for role '{$role->name}'",
                'data'    => [
                    'user'        => $user->only(['id', 'name', 'email']),
                    'role'        => $role->name,
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
            ]);
        }

        $role->syncPermissions($request->permissions);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Permissions successfully assigned to role '{$role->name}'",
            'data'    => [
                'role'        => $role->name,
                'users_count' => $this->getRoleUsersCount($role->id),
                'permissions' => $role->fresh()->permissions->pluck('name'),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/permissions",
     *      operationId="getPermissionsList",
     *      tags={"1. Core System - Role"},
     *      summary="Get list of all system Permissions dynamically",
     *      description="Returns all permissions from database dynamically grouped by module",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getPermissions(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $allPermissions = $query->orderBy('name', 'asc')->get();

        $permissionsList = $allPermissions->map(function ($perm) {
            return [
                'id'         => $perm->id,
                'name'       => $perm->name,
                'guard_name' => $perm->guard_name,
                'module'     => $this->extractModuleName($perm->name),
            ];
        });

        $modules = $permissionsList->groupBy('module');

        return response()->json([
            'success'     => true,
            'total'       => $allPermissions->count(),
            'modules'     => $modules,
            'permissions' => $permissionsList,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/permissions",
     *      operationId="createPermission",
     *      tags={"1. Core System - Role"},
     *      summary="Create a new Permission dynamically",
     *      description="Dynamically adds a new permission to the database",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="export_reports"),
     *              @OA\Property(property="guard_name", type="string", example="web")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Permission created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function storePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:100|unique:permissions,name',
            'guard_name' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $permission = Permission::create([
            'name'       => strtolower(trim($request->name)),
            'guard_name' => $request->guard_name ?: 'web',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Permission '{$permission->name}' created successfully",
            'data'    => [
                'id'         => $permission->id,
                'name'       => $permission->name,
                'guard_name' => $permission->guard_name,
                'module'     => $this->extractModuleName($permission->name),
            ],
        ], 201);
    }

    /**
     * @OA\Delete(
     *      path="/api/permissions/{id}",
     *      operationId="deletePermission",
     *      tags={"1. Core System - Role"},
     *      summary="Delete a Permission dynamically",
     *      description="Dynamically removes a permission from the database",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Permission deleted successfully")
     * )
     */
    public function destroyPermission($id)
    {
        $permission = is_numeric($id)
            ? Permission::findOrFail($id)
            : Permission::where('name', $id)->firstOrFail();

        $name = $permission->name;
        $permission->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Permission '{$name}' deleted successfully",
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/users/{id}/permissions",
     *      operationId="getUserPermissions",
     *      tags={"1. Core System - Role"},
     *      summary="Get a User's roles and permissions dynamically",
     *      description="Returns all roles, direct custom permissions, and inherited permissions for a user",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function getUserPermissions($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'user'               => $user->only(['id', 'name', 'email']),
                'roles'              => $user->getRoleNames(),
                'direct_permissions' => $user->getDirectPermissions()->pluck('name'),
                'all_permissions'    => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/users/{id}/roles",
     *      operationId="assignRoleToUser",
     *      tags={"1. Core System - Role"},
     *      summary="Assign Roles to a User dynamically",
     *      description="Assigns or synchronizes roles for a specific user",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"roles"},
     *              @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"teacher", "parent"})
     *          )
     *      ),
     *      @OA\Response(response=200, description="Roles assigned successfully")
     * )
     */
    public function assignRoleToUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->syncRoles($request->roles);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Roles assigned successfully to user '{$user->name}'",
            'data'    => [
                'user'  => $user->only(['id', 'name', 'email']),
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/users/{id}/permissions",
     *      operationId="assignPermissionToUser",
     *      tags={"1. Core System - Role"},
     *      summary="Assign Direct Permissions to a User dynamically",
     *      description="Assigns direct custom permissions to a specific user",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"permissions"},
     *              @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"view_reports"})
     *          )
     *      ),
     *      @OA\Response(response=200, description="Direct permissions assigned successfully")
     * )
     */
    public function assignPermissionToUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'permissions'   => 'required|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        foreach ($request->permissions as $permName) {
            $clean = trim($permName);
            if (!empty($clean)) {
                Permission::firstOrCreate(['name' => $clean, 'guard_name' => 'web']);
            }
        }

        $user->syncPermissions($request->permissions);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Permissions assigned successfully to user '{$user->name}'",
            'data'    => [
                'user'        => $user->only(['id', 'name', 'email']),
                'roles'       => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    /**
     * Backwards-compatible aliases delegating to dynamic methods.
     */
    public function getTeacherPermissions(Request $request)
    {
        return $this->getRolePermissions('teacher');
    }

    public function assignTeacherPermissions(Request $request)
    {
        return $this->syncRolePermissions($request, 'teacher');
    }

    public function getParentPermissions(Request $request)
    {
        return $this->getRolePermissions('parent');
    }

    public function assignParentPermissions(Request $request)
    {
        return $this->syncRolePermissions($request, 'parent');
    }

    public function getPrincipalPermissions(Request $request)
    {
        return $this->getRolePermissions('principal');
    }

    public function assignPrincipalPermissions(Request $request)
    {
        return $this->syncRolePermissions($request, 'principal');
    }

    public function syncPermissions(Request $request, $id)
    {
        return $this->syncRolePermissions($request, $id);
    }
}
