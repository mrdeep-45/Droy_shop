<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShopProfileController extends Controller
{
    /**
     * Show profile page for currently logged-in shop admin / vendor / delivery staff.
     */
    public function index()
    {
        $loginType = session('shop_login_type');
        $user = null;

        if (in_array($loginType, ['ShopAdmin', 'ShopSuperAdmin'])) {
            $user = DB::table('shop_admin')->where('shop_admin_id', session('shop_admin_id'))->first();
        } elseif ($loginType === 'Vendor') {
            $user = DB::table('shop_vendors')->where('vendor_id', session('vendor_id'))->first();
            if (!$user) {
                $user = DB::table('tbl_shop_vendors')->where('id', session('vendor_id'))->first();
            }
        } elseif ($loginType === 'DeliveryStaff') {
            $user = DB::table('shop_delivery_staff')->where('delivery_staff_id', session('delivery_staff_id'))->first();
        }

        $roleLabel = match($loginType) {
            'ShopSuperAdmin' => 'Super Admin',
            'ShopAdmin'      => 'Shop Admin',
            'Vendor'         => 'Vendor',
            'DeliveryStaff'  => 'Delivery Staff',
            default          => 'User',
        };

        return view('shop.profile.index', compact('user', 'loginType', 'roleLabel'));
    }

    /**
     * Update Profile Info (name, email, phone, avatar/logo).
     */
    public function updateProfile(Request $request)
    {
        $loginType = session('shop_login_type');

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        if (in_array($loginType, ['ShopAdmin', 'ShopSuperAdmin'])) {
            $adminId = session('shop_admin_id');
            $updateData = [
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'updated_at' => now(),
            ];

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = 'admin_' . $adminId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile'), $filename);
                $updateData['profile_image'] = 'uploads/profile/' . $filename;
            }

            DB::table('shop_admin')->where('shop_admin_id', $adminId)->update($updateData);

            session([
                'shop_admin_name'  => $request->name,
                'shop_admin_email' => $request->email,
            ]);

            return response()->json(['success' => true, 'message' => 'Profile updated successfully!']);

        } elseif ($loginType === 'Vendor') {
            $vendorId = session('vendor_id');
            $updateData = [
                'owner_name'    => $request->name,
                'business_name' => $request->business_name ?? $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'updated_at'    => now(),
            ];

            if (DB::table('shop_vendors')->where('vendor_id', $vendorId)->exists()) {
                DB::table('shop_vendors')->where('vendor_id', $vendorId)->update($updateData);
            }
            if (DB::table('tbl_shop_vendors')->where('id', $vendorId)->exists()) {
                DB::table('tbl_shop_vendors')->where('id', $vendorId)->update([
                    'name'       => $request->name,
                    'shop_name'  => $request->business_name ?? $request->name,
                    'email'      => $request->email,
                    'phone'      => $request->phone,
                    'updated_at' => now(),
                ]);
            }

            session([
                'vendor_name' => $request->name,
                'vendor_biz'  => $request->business_name ?? $request->name,
            ]);

            return response()->json(['success' => true, 'message' => 'Vendor profile updated successfully!']);

        } elseif ($loginType === 'DeliveryStaff') {
            $staffId = session('delivery_staff_id');
            DB::table('shop_delivery_staff')->where('delivery_staff_id', $staffId)->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'updated_at' => now(),
            ]);

            session(['delivery_staff_name' => $request->name]);

            return response()->json(['success' => true, 'message' => 'Delivery staff profile updated!']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid session.'], 403);
    }

    /**
     * Show Change Password modal/view.
     */
    public function showChangePassword()
    {
        return view('shop.profile.change_password');
    }

    /**
     * Update Password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $loginType = session('shop_login_type');

        if (in_array($loginType, ['ShopAdmin', 'ShopSuperAdmin'])) {
            $adminId = session('shop_admin_id');
            $user = DB::table('shop_admin')->where('shop_admin_id', $adminId)->first();

            if (!$user || md5($request->current_password) !== $user->password) {
                return response()->json(['success' => false, 'message' => 'Current password does not match.'], 422);
            }

            DB::table('shop_admin')->where('shop_admin_id', $adminId)->update([
                'password'   => md5($request->new_password),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Password changed successfully!']);

        } elseif ($loginType === 'Vendor') {
            $vendorId = session('vendor_id');
            $user = DB::table('shop_vendors')->where('vendor_id', $vendorId)->first();
            if (!$user) {
                $user = DB::table('tbl_shop_vendors')->where('id', $vendorId)->first();
            }

            if (!$user || (md5($request->current_password) !== $user->password && !password_verify($request->current_password, $user->password))) {
                return response()->json(['success' => false, 'message' => 'Current password does not match.'], 422);
            }

            $hashed = md5($request->new_password);
            DB::table('shop_vendors')->where('vendor_id', $vendorId)->update(['password' => $hashed, 'updated_at' => now()]);
            if (DB::table('tbl_shop_vendors')->where('id', $vendorId)->exists()) {
                DB::table('tbl_shop_vendors')->where('id', $vendorId)->update(['password' => $hashed, 'updated_at' => now()]);
            }

            return response()->json(['success' => true, 'message' => 'Password changed successfully!']);

        } elseif ($loginType === 'DeliveryStaff') {
            $staffId = session('delivery_staff_id');
            $user = DB::table('shop_delivery_staff')->where('delivery_staff_id', $staffId)->first();

            if (!$user || md5($request->current_password) !== $user->password) {
                return response()->json(['success' => false, 'message' => 'Current password does not match.'], 422);
            }

            DB::table('shop_delivery_staff')->where('delivery_staff_id', $staffId)->update([
                'password'   => md5($request->new_password),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Password changed successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
    }
}
