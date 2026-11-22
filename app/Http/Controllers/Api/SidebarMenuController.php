<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SidebarMenu;
use App\Models\SidebarSubMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SidebarMenuController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/sidebar-menus",
     *      operationId="getSidebarMenus",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Get hierarchical sidebar menus for frontend navigation",
     *      description="Returns grouped sidebar menus matching frontend array format (sections -> menus -> sub_menus) from normalized relational tables.",
     *      @OA\Parameter(name="only_active", in="query", required=false, description="Filter out inactive menus and submenus (true/false)", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Administration"),
     *                      @OA\Property(
     *                          property="menus",
     *                          type="array",
     *                          @OA\Items(
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="title", type="string", example="Admin Section"),
     *                              @OA\Property(property="icon", type="string", example="/sidebar_icons/admin_section.svg"),
     *                              @OA\Property(property="path", type="string", example="/admin-section"),
     *                              @OA\Property(property="permission_id", type="integer", nullable=true, example=13),
     *                              @OA\Property(property="permission", type="string", nullable=true, example="manage_admission_queries"),
     *                              @OA\Property(property="is_active", type="boolean", example=true),
     *                              @OA\Property(
     *                                  property="sub_menus",
     *                                  type="array",
     *                                  @OA\Items(
     *                                      @OA\Property(property="id", type="integer", example=1),
     *                                      @OA\Property(property="title", type="string", example="Admission Query"),
     *                                      @OA\Property(property="path", type="string", nullable=true, example="/admission-query"),
     *                                      @OA\Property(property="permission_id", type="integer", nullable=true, example=13),
     *                                      @OA\Property(property="permission", type="string", nullable=true, example="manage_admission_queries"),
     *                                      @OA\Property(property="is_active", type="boolean", example=true)
     *                                  )
     *                              )
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $onlyActive = $request->boolean('only_active');
        $cacheKey = $onlyActive ? 'sidebar_menus_active' : 'sidebar_menus_all';

        $result = Cache::remember($cacheKey, 3600, function () use ($onlyActive) {
            $query = SidebarMenu::ordered();

            if ($onlyActive) {
                $query->active();
            }

            $menus = $query->with([
                'subMenus' => function ($q) use ($onlyActive) {
                    $q->ordered();
                    if ($onlyActive) {
                        $q->active();
                    }
                }
            ])->get();

            // Group by section
            $grouped = $menus->groupBy('section');

            // Cache permissions map (name -> id and id -> name) for high-performance lookup
            $permissionsMap = Cache::remember('permissions_name_id_map', 3600, function () {
                return \Spatie\Permission\Models\Permission::pluck('id', 'name')->toArray();
            });
            $permissionsIdMap = Cache::remember('permissions_id_name_map', 3600, function () {
                return \Spatie\Permission\Models\Permission::pluck('name', 'id')->toArray();
            });

            $result = [];
            foreach ($grouped as $sectionTitle => $items) {
                $sectionMenus = [];
                foreach ($items as $item) {
                    $permissionId = $item->permission_id ?? ($item->permission ? ($permissionsMap[$item->permission] ?? null) : null);
                    $permissionName = $item->permission ?? ($permissionId ? ($permissionsIdMap[$permissionId] ?? null) : null);

                    $menuData = [
                        'id'            => $item->id,
                        'title'         => $item->title,
                        'icon'          => $item->icon,
                        'path'          => $item->path,
                        'permission_id' => $permissionId,
                        'permission'    => $permissionName,
                        'is_active'     => (bool) $item->is_active,
                    ];

                    if ($item->subMenus->isNotEmpty()) {
                        $menuData['sub_menus'] = $item->subMenus->map(function ($sub) use ($permissionsMap, $permissionsIdMap) {
                            $subPermissionId = $sub->permission_id ?? ($sub->permission ? ($permissionsMap[$sub->permission] ?? null) : null);
                            $subPermissionName = $sub->permission ?? ($subPermissionId ? ($permissionsIdMap[$subPermissionId] ?? null) : null);

                            return [
                                'id'            => $sub->id,
                                'title'         => $sub->title,
                                'path'          => $sub->path,
                                'permission_id' => $subPermissionId,
                                'permission'    => $subPermissionName,
                                'is_active'     => (bool) $sub->is_active,
                            ];
                        })->values()->all();
                    }

                    $sectionMenus[] = $menuData;
                }

                $result[] = [
                    'title' => $sectionTitle,
                    'menus' => $sectionMenus,
                ];
            }

            return $result;
        });

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/sidebar-menus/items",
     *      operationId="getAllSidebarMenuItems",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Get flat list of all menu items with child submenus for admin management",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function items(Request $request)
    {
        $items = Cache::remember('sidebar_menus_items', 3600, function () {
            return SidebarMenu::ordered()->with('subMenus')->get();
        });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/sidebar-menus",
     *      operationId="storeSidebarMenuItem",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Create a new sidebar menu item",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"section", "title"},
     *              @OA\Property(property="section", type="string", example="Administration"),
     *              @OA\Property(property="title", type="string", example="New Module"),
     *              @OA\Property(property="icon", type="string", example="/sidebar_icons/new.svg"),
     *              @OA\Property(property="path", type="string", example="/new-module"),
     *              @OA\Property(property="permission_id", type="integer", example=21),
     *              @OA\Property(property="permission", type="string", example="manage_general_settings"),
     *              @OA\Property(property="order", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Menu item created successfully")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section'       => 'required|string|max:100',
            'title'         => 'required|string|max:150',
            'icon'          => 'nullable|string|max:255',
            'path'          => 'nullable|string|max:255',
            'permission_id' => 'nullable|integer',
            'permission'    => 'nullable|string|max:100',
            'order'         => 'nullable|integer',
            'is_active'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // Auto-sync permission string & permission_id
        if (!empty($data['permission_id']) && empty($data['permission'])) {
            $perm = \Spatie\Permission\Models\Permission::find($data['permission_id']);
            if ($perm) {
                $data['permission'] = $perm->name;
            }
        } elseif (!empty($data['permission']) && empty($data['permission_id'])) {
            $data['permission_id'] = \Spatie\Permission\Models\Permission::where('name', $data['permission'])->value('id');
        }

        $menu = SidebarMenu::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Sidebar menu created successfully',
            'data' => $menu,
        ], 201);
    }

    /**
     * @OA\Put(
     *      path="/api/sidebar-menus/{id}",
     *      operationId="updateSidebarMenuItem",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Update a sidebar menu item",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="section", type="string", example="Administration"),
     *              @OA\Property(property="title", type="string", example="New Module"),
     *              @OA\Property(property="icon", type="string", example="/sidebar_icons/new.svg"),
     *              @OA\Property(property="path", type="string", example="/new-module"),
     *              @OA\Property(property="permission_id", type="integer", example=21),
     *              @OA\Property(property="permission", type="string", example="manage_general_settings"),
     *              @OA\Property(property="order", type="integer", example=1),
     *              @OA\Property(property="is_active", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Menu item updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $menu = SidebarMenu::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'section'       => 'sometimes|required|string|max:100',
            'title'         => 'sometimes|required|string|max:150',
            'icon'          => 'sometimes|nullable|string|max:255',
            'path'          => 'sometimes|nullable|string|max:255',
            'permission_id' => 'sometimes|nullable|integer',
            'permission'    => 'sometimes|nullable|string|max:100',
            'order'         => 'sometimes|integer',
            'is_active'     => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // Auto-sync permission string & permission_id
        if (!empty($data['permission_id']) && empty($data['permission'])) {
            $perm = \Spatie\Permission\Models\Permission::find($data['permission_id']);
            if ($perm) {
                $data['permission'] = $perm->name;
            }
        } elseif (!empty($data['permission']) && empty($data['permission_id'])) {
            $data['permission_id'] = \Spatie\Permission\Models\Permission::where('name', $data['permission'])->value('id');
        }

        $menu->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Sidebar menu updated successfully',
            'data' => $menu,
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/sidebar-menus/{id}",
     *      operationId="deleteSidebarMenuItem",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Delete a sidebar menu item",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Menu item deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $menu = SidebarMenu::findOrFail($id);
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sidebar menu deleted successfully',
        ]);
    }

    /**
     * @OA\Patch(
     *      path="/api/sidebar-sub-menus/{id}/status",
     *      operationId="updateSubMenuStatusById",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Toggle is_active state of a specific sub-menu by its ID",
     *      description="Direct 1-row relational update for a submenu status (true/false).",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Submenu primary key ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"is_active"},
     *              @OA\Property(property="is_active", type="boolean", example=false)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Submenu status updated successfully"),
     *      @OA\Response(response=404, description="Submenu not found")
     * )
     */
    public function updateSubMenuStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subMenu = SidebarSubMenu::findOrFail($id);
        $subMenu->update([
            'is_active' => $request->boolean('is_active')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub-menu status updated successfully',
            'data' => $subMenu,
        ]);
    }

    /**
     * @OA\Patch(
     *      path="/api/sidebar-menus/{id}/sub-menu-status",
     *      operationId="toggleSubMenuStatusByTitle",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Toggle is_active state of a sub-menu by Title within a parent menu",
     *      description="Finds and toggles a submenu's is_active state within a parent menu.",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Parent Menu ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title", "is_active"},
     *              @OA\Property(property="title", type="string", example="Admission Query"),
     *              @OA\Property(property="is_active", type="boolean", example=false)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Submenu status updated successfully"),
     *      @OA\Response(response=404, description="Menu or submenu not found")
     * )
     */
    public function toggleSubMenuStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $menu = SidebarMenu::findOrFail($id);

        $subMenu = SidebarSubMenu::where('sidebar_menu_id', $menu->id)
            ->where('title', $request->input('title'))
            ->first();

        if (!$subMenu) {
            return response()->json([
                'success' => false,
                'message' => 'Sub-menu with title "' . $request->input('title') . '" not found in this menu.'
            ], 404);
        }

        $subMenu->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub-menu status updated successfully',
            'data' => $subMenu,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/sidebar-sub-menus",
     *      operationId="storeSidebarSubMenuItem",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Create a new sidebar sub-menu item",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"sidebar_menu_id", "title"},
     *              @OA\Property(property="sidebar_menu_id", type="integer", example=1, description="Parent sidebar menu ID"),
     *              @OA\Property(property="title", type="string", example="Admission Query"),
     *              @OA\Property(property="path", type="string", example="/admission-query"),
     *              @OA\Property(property="permission_id", type="integer", example=13),
     *              @OA\Property(property="permission", type="string", example="manage_admission_queries"),
     *              @OA\Property(property="order", type="integer", example=1),
     *              @OA\Property(property="is_active", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Submenu created successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function storeSubMenu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sidebar_menu_id' => 'required|exists:sidebar_menus,id',
            'title'           => 'required|string|max:150',
            'path'            => 'nullable|string|max:255',
            'permission_id'   => 'nullable|integer',
            'permission'      => 'nullable|string|max:100',
            'order'           => 'nullable|integer',
            'is_active'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // Auto-sync permission string & permission_id
        if (!empty($data['permission_id']) && empty($data['permission'])) {
            $perm = \Spatie\Permission\Models\Permission::find($data['permission_id']);
            if ($perm) {
                $data['permission'] = $perm->name;
            }
        } elseif (!empty($data['permission']) && empty($data['permission_id'])) {
            $data['permission_id'] = \Spatie\Permission\Models\Permission::where('name', $data['permission'])->value('id');
        }

        $subMenu = SidebarSubMenu::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Sidebar sub-menu created successfully',
            'data' => $subMenu->load('menu'),
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/sidebar-sub-menus/{id}",
     *      operationId="getSidebarSubMenuItem",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Get details of a specific sidebar sub-menu item",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Submenu ID", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Submenu not found")
     * )
     */
    public function showSubMenu($id)
    {
        $subMenu = SidebarSubMenu::with('menu')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $subMenu,
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/sidebar-sub-menus/{id}",
     *      operationId="updateSidebarSubMenuItem",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Update a sidebar sub-menu item",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Submenu ID", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="sidebar_menu_id", type="integer", example=1, description="Parent sidebar menu ID"),
     *              @OA\Property(property="title", type="string", example="Admission Query"),
     *              @OA\Property(property="path", type="string", example="/admission-query"),
     *              @OA\Property(property="permission_id", type="integer", example=13),
     *              @OA\Property(property="permission", type="string", example="manage_admission_queries"),
     *              @OA\Property(property="order", type="integer", example=1),
     *              @OA\Property(property="is_active", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Submenu updated successfully"),
     *      @OA\Response(response=404, description="Submenu not found"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateSubMenu(Request $request, $id)
    {
        $subMenu = SidebarSubMenu::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'sidebar_menu_id' => 'sometimes|required|exists:sidebar_menus,id',
            'title'           => 'sometimes|required|string|max:150',
            'path'            => 'sometimes|nullable|string|max:255',
            'permission_id'   => 'sometimes|nullable|integer',
            'permission'      => 'sometimes|nullable|string|max:100',
            'order'           => 'sometimes|integer',
            'is_active'       => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // Auto-sync permission string & permission_id
        if (!empty($data['permission_id']) && empty($data['permission'])) {
            $perm = \Spatie\Permission\Models\Permission::find($data['permission_id']);
            if ($perm) {
                $data['permission'] = $perm->name;
            }
        } elseif (!empty($data['permission']) && empty($data['permission_id'])) {
            $data['permission_id'] = \Spatie\Permission\Models\Permission::where('name', $data['permission'])->value('id');
        }

        $subMenu->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Sidebar sub-menu updated successfully',
            'data' => $subMenu->fresh()->load('menu'),
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/sidebar-sub-menus/{id}",
     *      operationId="deleteSidebarSubMenuItem",
     *      tags={"Navigation - Sidebar Menus"},
     *      summary="Delete a sidebar sub-menu item",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, description="Submenu ID", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Submenu deleted successfully"),
     *      @OA\Response(response=404, description="Submenu not found")
     * )
     */
    public function destroySubMenu($id)
    {
        $subMenu = SidebarSubMenu::findOrFail($id);
        $subMenu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sidebar sub-menu deleted successfully',
        ]);
    }
}
