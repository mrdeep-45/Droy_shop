<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ShopRolePermissionController extends Controller
{
    /**
     * Show Role & Permission Management Screen
     */
    public function index()
    {
        $page_title = 'Roles & Permissions';
        $page_name  = 'Role Permission';

        // Fetch all shop roles
        $roles = DB::table('shop_roles')
            ->where('status', 0)
            ->orderBy('shop_role_id', 'asc')
            ->get();

        // Fetch menus & submenus
        $menus = DB::table('shop_menus')
            ->where('status', 0)
            ->where('menu_type', 'admin')
            ->orderBy('menu_order', 'asc')
            ->get()
            ->map(function ($menu) {
                if ($menu->is_submenu) {
                    $menu->submenus = DB::table('shop_submenus')
                        ->where('shop_menu_id', $menu->shop_menu_id)
                        ->where('status', 0)
                        ->orderBy('submenu_order', 'asc')
                        ->get();
                } else {
                    $menu->submenus = collect([]);
                }
                return $menu;
            });

        return view('shop.setting.role_permission', compact('page_title', 'page_name', 'roles', 'menus'));
    }

    /**
     * Create New Role
     */
    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:shop_roles,role_name',
            'role_type' => 'required|in:admin,vendor,delivery,customer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $roleId = DB::table('shop_roles')->insertGetId([
                'role_name'  => trim($request->role_name),
                'role_type'  => $request->role_type,
                'status'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'role'    => [
                    'shop_role_id' => $roleId,
                    'role_name'    => $request->role_name,
                    'role_type'    => $request->role_type,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Permissions for a selected Role
     */
    public function getPermissions($roleId)
    {
        $permissions = DB::table('shop_role_permissions')
            ->where('shop_role_id', $roleId)
            ->get()
            ->map(function ($perm) {
                return [
                    'menu_id'    => $perm->shop_menu_id,
                    'submenu_id' => $perm->shop_submenu_id,
                    'can_view'   => (bool) $perm->can_view,
                    'can_create' => (bool) $perm->can_create,
                    'can_update' => (bool) $perm->can_update,
                    'can_delete' => (bool) $perm->can_delete,
                ];
            });

        return response()->json([
            'success'     => true,
            'permissions' => $permissions
        ]);
    }

    /**
     * Save/Update Permissions for a Role
     */
    public function savePermissions(Request $request)
    {
        $request->validate([
            'role_id'     => 'required|integer|exists:shop_roles,shop_role_id',
            'permissions' => 'nullable|array'
        ]);

        $roleId      = $request->role_id;
        $permissions = $request->permissions ?? [];

        DB::beginTransaction();
        try {
            // Delete existing permissions for this role
            DB::table('shop_role_permissions')->where('shop_role_id', $roleId)->delete();

            $inserts = [];
            foreach ($permissions as $p) {
                $canView   = !empty($p['can_view']) ? 1 : 0;
                $canCreate = !empty($p['can_create']) ? 1 : 0;
                $canUpdate = !empty($p['can_update']) ? 1 : 0;
                $canDelete = !empty($p['can_delete']) ? 1 : 0;

                // Auto-enable view if any action is enabled
                if ($canCreate || $canUpdate || $canDelete) {
                    $canView = 1;
                }

                if ($canView || $canCreate || $canUpdate || $canDelete) {
                    $inserts[] = [
                        'shop_role_id'    => $roleId,
                        'shop_menu_id'    => $p['menu_id'],
                        'shop_submenu_id' => !empty($p['submenu_id']) ? $p['submenu_id'] : null,
                        'can_view'        => $canView,
                        'can_create'      => $canCreate,
                        'can_update'      => $canUpdate,
                        'can_delete'      => $canDelete,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            if (!empty($inserts)) {
                DB::table('shop_role_permissions')->insert($inserts);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role permissions saved successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a Role
     */
    public function deleteRole(Request $request)
    {
        $roleId = $request->role_id;

        // Prevent deleting core Super Admin or Shop Admin
        $role = DB::table('shop_roles')->where('shop_role_id', $roleId)->first();
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found.'], 404);
        }

        if (in_array(strtolower($role->role_name), ['super admin', 'shop admin', 'vendor', 'delivery'])) {
            return response()->json(['success' => false, 'message' => 'Default system roles cannot be deleted.'], 422);
        }

        try {
            DB::table('shop_role_permissions')->where('shop_role_id', $roleId)->delete();
            DB::table('shop_roles')->where('shop_role_id', $roleId)->delete();

            return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
