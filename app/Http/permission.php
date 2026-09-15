<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Router;


if (!function_exists('checkPermissions')) {
    function checkPermissions($routeName)
    {
        $role_id = session('role_id');
        $login_type = session('login_type');
        $menuData = DB::table('company_submenus')
            ->where('sub_menu_route', $routeName)
            ->where('status', 0)
            ->select('c_menu_id', 'c_sub_menu_id')
            ->first() ??

            DB::table('company_menus')
            ->where('menu_route', $routeName)
            ->where('status', 0)
            ->select('c_menu_id', DB::raw('NULL as c_sub_menu_id'))
            ->first();
        if ($login_type === 'SuperAdmin' || $login_type === 'Company') {
            return [
                'canView' => true,
                'canCreate' => true,
                'canUpdate' => true,
                'canDelete' => true,
                'hasAnyPermission' => true
            ];
        }
        $permissions = [
            'canView' => false,
            'canCreate' => false,
            'canUpdate' => false,
            'canDelete' => false,
            'hasAnyPermission' => false
        ];
        if ($menuData) {
            $permissionCheck = DB::table('role_menu_permissions')
                ->where('role_id', $role_id)
                ->where('menu_id', $menuData->c_menu_id)
                ->when($menuData->c_sub_menu_id, function ($query, $sub_menu_id) {
                    return $query->where('sub_menu_id', $sub_menu_id);
                }, function ($query) {
                    return $query->whereNull('sub_menu_id');
                })
                ->first();
            if ($permissionCheck) {
                $permissions['canView'] = (bool) ($permissionCheck->can_view ?? 0);
                $permissions['canCreate'] = (bool) ($permissionCheck->can_create ?? 0);
                $permissions['canUpdate'] = (bool) ($permissionCheck->can_update ?? 0);
                $permissions['canDelete'] = (bool) ($permissionCheck->can_delete ?? 0);
                if ($permissions['canCreate'] || $permissions['canUpdate'] || $permissions['canDelete']) {
                    $permissions['canView'] = true;
                    $permissions['hasAnyPermission'] = true;
                }
            }
        }
        return $permissions;
    }

    if (!function_exists('get_index_route')) {
        function get_index_route($controller)
        {
            $controllerClass = get_class($controller);
            $router = app(Router::class);
            foreach ($router->getRoutes() as $route) {
                $routeAction = $route->getAction();
                if (isset($routeAction['controller'])) {
                    [$routeController, $routeMethod] = explode('@', $routeAction['controller']);
                    if ($routeMethod === 'index' && $routeController === $controllerClass) {
                        return $route->getName();
                    }
                }
            }
            return null;
        }
    }

    if (!function_exists('check_current_route_permissions')) {
        function check_current_route_permissions($controller)
        {
            $routeName = get_index_route($controller);
            return checkPermissions($routeName);
        }
    }
}


if (!function_exists('get_staff_name')) {
    function get_staff_name($staff_id)
    {
        $staff = DB::table('tbl_staff')->select('staff_name')->where('staff_id', $staff_id)->first();
        return $staff ? $staff->staff_name : "SuperAdmin";
    }
}

if (!function_exists('permissions_check')) {
    function permissions_check($permission, $routeName = null)
    {
        $route = $routeName ?? request()->route()->getName();

        $permissions = checkPermissions($route);
        if (is_array($permission)) {
            $permission = $permission[0] ?? null;
        }

        if ($permission && is_array($permissions) && isset($permissions[$permission])) {
            return $permissions[$permission];
        }

        return false;
    }
}
