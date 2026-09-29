<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopCouponController extends Controller
{
    public function index()
    {
        $coupons = DB::table('tbl_shop_coupons')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('shop.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|unique:tbl_shop_coupons,coupon_code',
            'coupon_name' => 'required',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        DB::table('tbl_shop_coupons')->insert([
            'coupon_code' => strtoupper($request->coupon_code),
            'coupon_name' => $request->coupon_name,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'per_user_limit' => $request->per_user_limit ?? 1,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'status' => $request->status ?? 0,
            'created_at' => now(),
            'created_by' => session('id')
        ]);

        return redirect()->back()->with('success', 'Coupon created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'coupon_code' => 'required|unique:tbl_shop_coupons,coupon_code,'.$id.',coupon_id',
            'coupon_name' => 'required',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        DB::table('tbl_shop_coupons')->where('coupon_id', $id)->update([
            'coupon_code' => strtoupper($request->coupon_code),
            'coupon_name' => $request->coupon_name,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'per_user_limit' => $request->per_user_limit ?? 1,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'status' => $request->status ?? 0,
            'updated_at' => now(),
            'updated_by' => session('id')
        ]);

        return redirect()->back()->with('success', 'Coupon updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('tbl_shop_coupons')->where('coupon_id', $id)->delete();
        return redirect()->back()->with('success', 'Coupon deleted successfully.');
    }
}
