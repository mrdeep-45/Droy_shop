<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * ShopAdminLoginController
 * Handles authentication for all shop user types:
 *   - Shop Super Admin & Admin  → /shop/login
 *   - Vendor                    → /vendor/login
 *   - Delivery Staff            → /delivery/login
 *   - Customer                  → /store/login + /store/register
 */
class ShopAdminLoginController extends Controller
{
    // ─── SHOW LOGIN PAGES ────────────────────────────────────────────

    public function showAdminLogin()
    {
        if (session('shop_admin_id')) {
            return redirect()->route('shop.dashboard');
        }
        return view('shop.auth.admin_login');
    }

    public function showCustomerLogin()
    {
        if (session('customer_id')) {
            return redirect()->route('store.home');
        }
        return view('shop.auth.customer_login');
    }

    public function showVendorLogin()
    {
        if (session('vendor_id')) {
            return redirect()->route('vendor.dashboard');
        }
        return view('shop.auth.vendor_login');
    }

    public function showDeliveryLogin()
    {
        if (session('delivery_staff_id')) {
            return redirect()->route('delivery.dashboard');
        }
        return view('shop.auth.delivery_login');
    }

    public function showCustomerRegister()
    {
        return view('shop.auth.customer_register');
    }

    public function showVendorRegister()
    {
        return view('shop.auth.vendor_register');
    }

    // ─── PROCESS LOGINS ──────────────────────────────────────────────

    /**
     * Process Shop Admin / Super Admin login via AJAX.
     */
    public function processAdminLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $admin = DB::table('shop_admin')
            ->where('email', $request->email)
            ->where('status', 0)
            ->first();

        if (!$admin || md5($request->password) !== $admin->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ]);
        }

        Session::regenerate();
        session([
            'shop_admin_id'    => $admin->shop_admin_id,
            'shop_login_type'  => $admin->admin_type === 'super_admin' ? 'ShopSuperAdmin' : 'ShopAdmin',
            'shop_admin_name'  => $admin->name,
            'shop_admin_email' => $admin->email,
            'shop_admin_type'  => $admin->admin_type,
        ]);

        logShopActivity('login', 'ShopAdmin', $admin->shop_admin_id, $admin->name);

        return response()->json([
            'success'      => true,
            'message'      => 'Login successful! Redirecting...',
            'redirect_url' => route('shop.dashboard'),
        ]);
    }

    /**
     * Process Customer login via AJAX.
     */
    public function processCustomerLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $customer = DB::table('shop_customers')
            ->where('email', $request->email)
            ->where('status', 0)
            ->first();

        if (!$customer || md5($request->password) !== $customer->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ]);
        }

        Session::regenerate();
        $this->createCustomerSession($customer);

        return response()->json([
            'success'      => true,
            'message'      => 'Welcome back, ' . $customer->name . '!',
            'redirect_url' => route('store.home'),
        ]);
    }

    /**
     * Process Vendor login via AJAX.
     */
    public function processVendorLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $vendor = DB::table('shop_vendors')
            ->where('email', $request->email)
            ->where('status', 0)
            ->first();

        if (!$vendor || md5($request->password) !== $vendor->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ]);
        }

        if ($vendor->is_approved != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your vendor account is pending approval. We will notify you once approved.',
            ]);
        }

        Session::regenerate();
        session([
            'vendor_id'       => $vendor->vendor_id,
            'shop_login_type' => 'Vendor',
            'vendor_name'     => $vendor->owner_name,
            'vendor_biz'      => $vendor->business_name,
            'vendor_email'    => $vendor->email,
        ]);

        logShopActivity('login', 'ShopVendor', $vendor->vendor_id, $vendor->business_name);

        return response()->json([
            'success'      => true,
            'message'      => 'Welcome back, ' . $vendor->business_name . '!',
            'redirect_url' => route('vendor.dashboard'),
        ]);
    }

    /**
     * Process Delivery Staff login via AJAX.
     */
    public function processDeliveryLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $staff = DB::table('shop_delivery_staff')
            ->where('email', $request->email)
            ->where('status', 0)
            ->first();

        if (!$staff || md5($request->password) !== $staff->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ]);
        }

        Session::regenerate();
        session([
            'delivery_staff_id'   => $staff->delivery_staff_id,
            'shop_login_type'     => 'DeliveryStaff',
            'delivery_staff_name' => $staff->name,
            'delivery_staff_email'=> $staff->email,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Login successful!',
            'redirect_url' => route('delivery.dashboard'),
        ]);
    }

    // ─── CUSTOMER REGISTRATION ───────────────────────────────────────

    public function processCustomerRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:shop_customers,email',
            'phone'    => 'required|string|max:15',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $customerId = DB::table('shop_customers')->insertGetId([
                'customer_code' => generateCustomerCode(),
                'name'          => trim($request->name),
                'email'         => strtolower(trim($request->email)),
                'phone'         => $request->phone,
                'password'      => md5($request->password),
                'status'        => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $customer = DB::table('shop_customers')->find($customerId);

            DB::commit();
            Session::regenerate();
            $this->createCustomerSession($customer);

            return response()->json([
                'success'      => true,
                'message'      => 'Registration successful! Welcome to HexaShop!',
                'redirect_url' => route('store.home'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    // ─── VENDOR REGISTRATION ─────────────────────────────────────────

    public function processVendorRegister(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:200',
            'owner_name'    => 'required|string|max:100',
            'email'         => 'required|email|unique:shop_vendors,email',
            'phone'         => 'required|string|max:15',
            'password'      => 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            DB::table('shop_vendors')->insert([
                'vendor_code'   => generateVendorCode(),
                'business_name' => trim($request->business_name),
                'owner_name'    => trim($request->owner_name),
                'email'         => strtolower(trim($request->email)),
                'phone'         => $request->phone,
                'password'      => md5($request->password),
                'gst_number'    => $request->gst_number,
                'address'       => $request->address,
                'is_approved'   => 0, // Pending approval
                'status'        => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration submitted! Our team will review and approve your vendor account within 24-48 hours.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    // ─── LOGOUT ──────────────────────────────────────────────────────

    public function shopLogout(Request $request)
    {
        $loginType = session('shop_login_type');

        $redirectRoute = match ($loginType) {
            'ShopAdmin', 'ShopSuperAdmin' => 'shop.login',
            'Customer'                    => 'store.login',
            'Vendor'                      => 'vendor.login',
            'DeliveryStaff'               => 'delivery.login',
            default                       => 'shop.login',
        };

        logShopActivity('logout');

        $request->session()->flush();
        $request->session()->regenerateToken();

        return response()->json([
            'success'      => true,
            'redirect_url' => route($redirectRoute),
        ]);
    }

    // ─── PRIVATE SESSION CREATORS ────────────────────────────────────

    private function createCustomerSession(object $customer): void
    {
        session([
            'customer_id'       => $customer->customer_id,
            'shop_login_type'   => 'Customer',
            'customer_name'     => $customer->name,
            'customer_email'    => $customer->email,
            'customer_phone'    => $customer->phone,
        ]);
    }
}
