<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware: DeliveryStaffIsValid
 * Guards all delivery staff routes.
 */
class DeliveryStaffIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $staffId   = $request->session()->get('delivery_staff_id');
        $loginType = $request->session()->get('shop_login_type');

        if (!$staffId || $loginType !== 'DeliveryStaff') {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Delivery staff authentication required.'], 401);
            }
            return redirect()->route('delivery.login');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}
