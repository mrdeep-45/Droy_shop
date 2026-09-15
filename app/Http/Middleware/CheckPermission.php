<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    public function handle($request, Closure $next, ...$requiredPermissions)
    {
        $loginType = session('login_type');
        $currentRoute = Route::currentRouteName();

        if (in_array($loginType, ['SuperAdmin', 'Super Admin', 'Company'])) {
            return $next($request);
        }

        if (in_array($loginType, ['Staff', 'Manager'])) {

            $indexRoute = $this->getParentIndexRoute($currentRoute);

            $routeToCheck = $indexRoute ?? $currentRoute;

            // Company Documents routes are accessible for viewing by staff (backend queries enforce document-level permissions)
            if (in_array($currentRoute, ['documents.index', 'document.category.show', 'document.category.get', 'document.mngt.list']) ||
                in_array($routeToCheck, ['documents.index', 'document.category.show', 'document.category.get', 'document.mngt.list'])) {
                if (empty($requiredPermissions) || (count($requiredPermissions) === 1 && in_array($requiredPermissions[0], ['view', 'index']))) {
                    return $next($request);
                }
            }

            $permissions = checkPermissions($routeToCheck);

            if (empty($requiredPermissions)) {
                $requiredPermissions = ['view'];
            }

            $map = [
                'view'   => 'canView',
                'create' => 'canCreate',
                'update' => 'canUpdate',
                'edit'   => 'canUpdate',
                'delete' => 'canDelete',
                'remove' => 'canDelete',
            ];

            foreach ($requiredPermissions as $perm) {
                $permKey = $map[$perm] ?? null;

                if (!$permKey || empty($permissions[$permKey])) {
                    session()->flush();
                    session()->regenerateToken();
                    return redirect()->route('login');
                }
            }
        }

        return $next($request);
    }



    private function getParentIndexRoute($currentRoute)
    {
        if (str_contains($currentRoute, '.')) {
            $prefix = explode('.', $currentRoute)[0];
            $indexRoute = $prefix . '.index';

            if (Route::has($indexRoute)) {
                return $indexRoute;
            }
        }

        return null;
    }
}
