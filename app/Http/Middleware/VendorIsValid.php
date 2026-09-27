<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware: VendorIsValid
 * Guards all vendor dashboard routes.
 */
class VendorIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $vendorId  = $request->session()->get('vendor_id');
        $loginType = $request->session()->get('shop_login_type');

        if (!$vendorId || $loginType !== 'Vendor') {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Vendor authentication required.'], 401);
            }
            return redirect()->route('vendor.login');
        }

        // Check vendor is approved and active
        $vendor = \Illuminate\Support\Facades\DB::table('shop_vendors')
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$vendor || $vendor->status != 0 || $vendor->is_approved != 1) {
            $request->session()->flush();
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Vendor account not active or approved.'], 403);
            }
            return redirect()->route('vendor.login')
                ->with('error', 'Your vendor account is pending approval or has been deactivated.');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}
