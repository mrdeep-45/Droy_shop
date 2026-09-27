<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware: CustomerIsValid
 * Guards all customer storefront authenticated routes.
 */
class CustomerIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $customerId = $request->session()->get('customer_id');
        $loginType  = $request->session()->get('shop_login_type');

        if (!$customerId || $loginType !== 'Customer') {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Please login to continue.'], 401);
            }
            return redirect()->route('store.login')
                ->with('error', 'Please login to access this page.');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}
