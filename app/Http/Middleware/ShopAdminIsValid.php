<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware: ShopAdminIsValid
 * Guards all shop admin panel routes.
 * Allows Super Admin and Admin login types.
 */
class ShopAdminIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $adminId   = $request->session()->get('shop_admin_id');
        $loginType = $request->session()->get('shop_login_type');

        if (!$adminId || !in_array($loginType, ['ShopAdmin', 'ShopSuperAdmin'])) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('shop.login');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}
