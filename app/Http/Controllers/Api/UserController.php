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
     *                  @OA\Property(property="email", type="string"),
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
    public function index()
    {
        $data = User::with('roles')->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/api/users",
     *      operationId="storeUser",
     *      tags={"1. Core System - User"},
     *      summary="Store new user with role(s)",
     *      description="Returns saved user data with assigned roles",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="Sarah Connor"),
     *              @OA\Property(property="email", type="string", example="sarah@school.com"),
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

        return response()->json([
            'success' => true,
            'data' => $item->load('roles')
        ]);
    }

    /**
     * Display the specified resource.
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

        return response()->json([
            'success' => true,
            'message' => 'Updated',
            'data' => $item->load('roles')
        ]);
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
