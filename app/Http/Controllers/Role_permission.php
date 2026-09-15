<?php

namespace App\Http\Controllers;

use App\Models\RoleMenuPermissionModel;
use App\Models\RoleModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\MenutypeModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class Role_permission extends Controller
{
    public function index()
    {
        $menu_type = MenutypeModel::all();
        $page_title = 'Role Permission';
        $page_name = 'Role Permission';

        $menus = DB::table('company_menus')
            ->where('status', 0)
            ->orderBy('menu_order', 'asc')
            ->get()
            ->map(function ($menu) {
                if ($menu->is_submenu) {
                    $menu->submenus = DB::table('company_submenus')
                        ->where('c_menu_id', $menu->c_menu_id)
                        ->where('status', 0)
                        ->orderBy('submenu_order', 'asc')
                        ->get();
                } else {
                    $menu->submenus = collect([]);
                }
                return $menu;
            });

        $role = RoleModel::where('status', '0')->get();
        return view('company.setting.role_permission', compact('page_title', 'page_name', 'menu_type', 'menus', 'role'));
    }

    public function create_role(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:tbl_role,role_name',
            'role_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $roleId = DB::table('tbl_role')->insertGetId([
                'role_name' => $request->role_name,
                'type' => $request->role_type,
                'company_id' => get_company_id(),
                'created_at' => now(),
                'created_by' => getCreatedBy(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'data' => [
                    'role_id' => $roleId,
                    'role_name' => $request->role_name
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }
    public function save_permissions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|exists:tbl_role,role_id',
            'permissions_menu' => 'sometimes|array',
            'permissions' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $roleId = $request->role_id;

            RoleMenuPermissionModel::where('role_id', $roleId)->delete();

            // Insert permissions_menu if provided
            if ($request->has('permissions_menu')) {
                foreach ($request->permissions_menu as $menuId => $permissions) {
                    RoleMenuPermissionModel::create([
                        'role_id' => $roleId,
                        'menu_id' => $menuId,
                        'sub_menu_id' => null,
                        'can_view' => !empty($permissions['view']) ? 1 : 0,
                        'can_create' => !empty($permissions['create']) ? 1 : 0,
                        'can_update' => !empty($permissions['update']) ? 1 : 0,
                        'can_delete' => !empty($permissions['delete']) ? 1 : 0,
                        'status' => 0,
                        'created_at' => now(),
                        'company_id' => get_company_id(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Insert permissions for submenus if provided
            if ($request->has('permissions')) {
                foreach ($request->permissions as $submenuId => $permissions) {
                    $submenu = DB::table('company_submenus')
                        ->select('c_menu_id')
                        ->where('c_sub_menu_id', $submenuId)
                        ->first();

                    if ($submenu) {
                        RoleMenuPermissionModel::create([
                            'role_id' => $roleId,
                            'menu_id' => $submenu->c_menu_id,
                            'sub_menu_id' => $submenuId,
                            'can_view' => !empty($permissions['view']) ? 1 : 0,
                            'can_create' => !empty($permissions['create']) ? 1 : 0,
                            'can_update' => !empty($permissions['update']) ? 1 : 0,
                            'can_delete' => !empty($permissions['delete']) ? 1 : 0,
                            'status' => 0,
                            'company_id' => get_company_id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permissions saved successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save permissions: ' . $e->getMessage()
            ], 500);
        }
    }
    public function list(Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');
        
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        
        $orderBy = 'role_menu_permissions.role_id';
        if ($orderColumnIndex == 1) {
            $orderBy = 'tbl_role.role_name';
        }

        // Query Roles that have permissions
        $rolesQuery = DB::table('role_menu_permissions')
            ->join('tbl_role', 'tbl_role.role_id', '=', 'role_menu_permissions.role_id');

        if (!empty($search)) {
            $rolesQuery->leftJoin('company_menus', 'company_menus.c_menu_id', '=', 'role_menu_permissions.menu_id')
                       ->leftJoin('company_submenus', 'company_submenus.c_sub_menu_id', '=', 'role_menu_permissions.sub_menu_id')
                       ->where(function($q) use ($search) {
                           $q->where('tbl_role.role_name', 'like', "%{$search}%")
                             ->orWhere('company_menus.menu_title', 'like', "%{$search}%")
                             ->orWhere('company_submenus.sub_menu_title', 'like', "%{$search}%");
                       });
        }

        $totalRoles = DB::table('role_menu_permissions')->distinct()->count('role_id');
        $filteredRoles = $rolesQuery->distinct()->count('role_menu_permissions.role_id');

        $roleIds = $rolesQuery->select('role_menu_permissions.role_id', 'tbl_role.role_name')
            ->distinct()
            ->orderBy($orderBy, $orderDir)
            ->offset($start)
            ->limit($length > 0 ? $length : 1000)
            ->pluck('role_id');

        $permissions = RoleMenuPermissionModel::query()
            ->with(['menu', 'submenu', 'role'])
            ->join('tbl_role', 'tbl_role.role_id', '=', 'role_menu_permissions.role_id')
            ->whereIn('role_menu_permissions.role_id', $roleIds)
            ->orderBy($orderBy, $orderDir)
            ->orderBy('role_menu_permissions.menu_id')
            ->orderBy('role_menu_permissions.sub_menu_id')
            ->select('role_menu_permissions.*')
            ->get()
            ->groupBy(['role_id', 'menu_id']);

        $processedData = [];
        $index = intval($start) + 1;

        foreach ($permissions as $roleId => $menuGroups) {
            foreach ($menuGroups as $menuId => $items) {
                $firstItem = $items->first();

                // Format Submenu Name
                $submenuNameHtml = $items->map(function ($permission) {
                    return ($permission->submenu && $permission->submenu->sub_menu_title !== 'N/A') ? $permission->submenu->sub_menu_title : '';
                })->filter()->implode('<br>');

                // Format Permissions
                $permissionsHtml = $items->map(function ($submenu) {
                    $badges = [];
                    if ($submenu->can_view)
                        $badges[] = '<span class="badge rounded-pill bg-primary-transparent">View</span>';
                    if ($submenu->can_create)
                        $badges[] = '<span class="badge rounded-pill bg-success-transparent">Create</span>';
                    if ($submenu->can_update)
                        $badges[] = '<span class="badge rounded-pill bg-warning-transparent">Update</span>';
                    if ($submenu->can_delete)
                        $badges[] = '<span class="badge rounded-pill bg-danger-transparent">Delete</span>';

                    return implode(' ', $badges) ?: '<span class="text-muted">No permissions</span>';
                })->implode('<br>');
                
                $submenuData = $items->map(function ($permission) {
                    return [
                        'submenu_id' => $permission->sub_menu_id,
                        'submenu_name' => $permission->submenu ? $permission->submenu->sub_menu_title : 'N/A',
                        'can_view' => $permission->can_view,
                        'can_create' => $permission->can_create,
                        'can_update' => $permission->can_update,
                        'can_delete' => $permission->can_delete,
                        'id' => $permission->id,
                    ];
                })->toArray();

                $processedRow = [
                    'DT_RowIndex' => $index,
                    'role_id' => $roleId,
                    'role_name' => $firstItem->role ? $firstItem->role->role_name : 'N/A',
                    'role_type' => $firstItem->role ? $firstItem->role->type : 'N/A',
                    'menu_id' => $menuId,
                    'menu_name' => $firstItem->menu ? $firstItem->menu->menu_title : 'N/A',
                    'submenu_name' => $submenuNameHtml,
                    'permissions' => $permissionsHtml,
                    'action' => '',
                    'submenu_data' => $submenuData
                ];

                $processedData[] = $processedRow;
            }
            $index++; // Increment index ONLY for the next role
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRoles,
            'recordsFiltered' => $filteredRoles,
            'data' => $processedData
        ]);
    }
    public function get_permissions($roleId)
    {
        $permissions = RoleMenuPermissionModel::where('role_id', $roleId)->get();

        $formattedPermissions = [
            'permissions_menu' => [],
            'permissions' => []
        ];

        foreach ($permissions as $permission) {
            if ($permission->sub_menu_id === null) {
                $formattedPermissions['permissions_menu'][$permission->menu_id] = [
                    'view' => $permission->can_view,
                    'create' => $permission->can_create,
                    'update' => $permission->can_update,
                    'delete' => $permission->can_delete
                ];
            } else {
                $formattedPermissions['permissions'][$permission->sub_menu_id] = [
                    'view' => $permission->can_view,
                    'create' => $permission->can_create,
                    'update' => $permission->can_update,
                    'delete' => $permission->can_delete
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $formattedPermissions
        ]);
    }
    public function delete(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:tbl_role,role_id'
        ]);

        // Delete all permissions for this role
        $deleted = RoleMenuPermissionModel::where('role_id', $request->role_id)->delete();

        if ($deleted === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No permissions found for this role'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'All permissions for this role deleted successfully'
        ]);
    }

    public function getRolesByType(Request $request)
    {
        $type = $request->role_type;
        $roles = RoleModel::where('type', $type)->where('status', 0)->get();
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    }
}
