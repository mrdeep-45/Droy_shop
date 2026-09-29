<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShopCheckoutController extends Controller
{
    public function index()
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return redirect()->route('store.login')->with('error', 'Please login to checkout.');
        }

        $cartItems = DB::table('tbl_shop_cart as c')
            ->where('c.customer_id', $customerId)
            ->join('tbl_shop_products as p', 'c.product_id', '=', 'p.product_id')
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            })
            ->select('c.*', 'p.product_name', 'p.product_slug', 'img.image_path as primary_image')
            ->get();

        if (count($cartItems) == 0) {
            return redirect()->route('store.cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += ($item->unit_price * $item->quantity);
        }

        // Coupon Logic
        $couponDiscount = 0;
        $couponCode = session('applied_coupon');
        $couponId = null;
        if($couponCode) {
            $coupon = DB::table('tbl_shop_coupons')->where('coupon_code', $couponCode)->where('status', 0)->first();
            if($coupon) {
                $couponId = $coupon->coupon_id;
                if($coupon->discount_type == 'fixed') {
                    $couponDiscount = $coupon->discount_value;
                } else {
                    $couponDiscount = ($subtotal * $coupon->discount_value) / 100;
                    if($coupon->max_discount_amount && $couponDiscount > $coupon->max_discount_amount) {
                        $couponDiscount = $coupon->max_discount_amount;
                    }
                }
            }
        }

        $shippingCharge = 0; // Flat shipping or calculated
        if ($subtotal < 999) $shippingCharge = 50;

        $total = ($subtotal - $couponDiscount) + $shippingCharge;
        if($total < 0) $total = 0;

        $addresses = DB::table('tbl_shop_addresses')->where('customer_id', $customerId)->get();

        return view('store.checkout', compact('cartItems', 'subtotal', 'couponDiscount', 'couponCode', 'shippingCharge', 'total', 'addresses'));
    }

    public function process(Request $request)
    {
        $customerId = session('customer_id');
        if (!$customerId) return response()->json(['status' => 'error', 'message' => 'Unauthorized']);

        // Validate
        $request->validate([
            'address_id' => 'required|exists:tbl_shop_addresses,address_id',
            'payment_method' => 'required|in:cod,razorpay'
        ]);

        $cartItems = DB::table('tbl_shop_cart as c')
            ->where('c.customer_id', $customerId)
            ->join('tbl_shop_products as p', 'c.product_id', '=', 'p.product_id')
            ->select('c.*', 'p.product_name', 'p.product_code', 'p.base_price')
            ->get();

        if (count($cartItems) == 0) {
            return response()->json(['status' => 'error', 'message' => 'Your cart is empty.']);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsData = [];
            foreach ($cartItems as $item) {
                $subtotal += ($item->unit_price * $item->quantity);
                $itemsData[] = [
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product_name,
                    'product_code' => $item->product_code,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'base_price' => $item->base_price,
                    'total_price' => $item->unit_price * $item->quantity,
                    'created_at' => now()
                ];
            }

            $couponDiscount = 0;
            $couponCode = session('applied_coupon');
            $couponId = null;
            if($couponCode) {
                $coupon = DB::table('tbl_shop_coupons')->where('coupon_code', $couponCode)->where('status', 0)->first();
                if($coupon) {
                    $couponId = $coupon->coupon_id;
                    if($coupon->discount_type == 'fixed') {
                        $couponDiscount = $coupon->discount_value;
                    } else {
                        $couponDiscount = ($subtotal * $coupon->discount_value) / 100;
                        if($coupon->max_discount_amount && $couponDiscount > $coupon->max_discount_amount) {
                            $couponDiscount = $coupon->max_discount_amount;
                        }
                    }
                }
            }

            $shippingCharge = 0;
            if ($subtotal < 999) $shippingCharge = 50;

            $total = ($subtotal - $couponDiscount) + $shippingCharge;
            if($total < 0) $total = 0;

            $address = DB::table('tbl_shop_addresses')->where('address_id', $request->address_id)->first();
            $orderNum = 'ORD-' . strtoupper(uniqid());

            // 1. Insert Order
            $orderId = DB::table('tbl_shop_orders')->insertGetId([
                'order_number' => $orderNum,
                'customer_id' => $customerId,
                'address_id' => $request->address_id,
                'shipping_address' => json_encode($address),
                'coupon_id' => $couponId,
                'coupon_code' => $couponCode,
                'subtotal' => $subtotal,
                'coupon_discount' => $couponDiscount,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'created_at' => now(),
            ]);

            // 2. Insert Items and decrease stock
            foreach ($itemsData as &$idata) {
                $idata['order_id'] = $orderId;
                
                // Inventory tracking (Decrease stock)
                $product = DB::table('tbl_shop_products')->where('product_id', $idata['product_id'])->first();
                $newStock = $product->stock_qty - $idata['quantity'];
                
                DB::table('tbl_shop_products')->where('product_id', $idata['product_id'])->update(['stock_qty' => $newStock]);
                
                DB::table('tbl_shop_inventory')->insert([
                    'product_id' => $idata['product_id'],
                    'transaction_type' => 'sale',
                    'quantity' => -$idata['quantity'],
                    'before_stock' => $product->stock_qty,
                    'after_stock' => $newStock,
                    'reference_type' => 'Order',
                    'reference_id' => $orderId,
                    'reference_number' => $orderNum,
                    'created_at' => now()
                ]);
            }
            DB::table('tbl_shop_order_items')->insert($itemsData);

            // 3. Insert Tracking
            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $orderId,
                'status' => 'pending',
                'description' => 'Order placed successfully',
                'created_by_type' => 'system',
                'created_at' => now()
            ]);

            // 4. Log Coupon Usage
            if ($couponId) {
                DB::table('tbl_shop_coupon_usage')->insert([
                    'coupon_id' => $couponId,
                    'customer_id' => $customerId,
                    'order_id' => $orderId,
                    'discount_applied' => $couponDiscount,
                    'used_at' => now()
                ]);
                DB::table('tbl_shop_coupons')->where('coupon_id', $couponId)->increment('used_count');
            }

            // 5. Clear Cart & Session Coupon
            DB::table('tbl_shop_cart')->where('customer_id', $customerId)->delete();
            session()->forget('applied_coupon');

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Order placed successfully!', 'order_id' => $orderId]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Checkout failed: ' . $e->getMessage()]);
        }
    }

    public function success($order_id)
    {
        $customerId = session('customer_id');
        $order = DB::table('tbl_shop_orders')
            ->where('order_id', $order_id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$order) abort(404);

        return view('store.checkout_success', compact('order'));
    }
}
