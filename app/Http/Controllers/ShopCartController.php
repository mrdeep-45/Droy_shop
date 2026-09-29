<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShopCartController extends Controller
{
    // Helper to get current session ID or customer ID
    private function getCartIdentifier()
    {
        $customerId = session('customer_id');
        $sessionId = Session::getId();
        return ['customer_id' => $customerId, 'session_id' => $sessionId];
    }

    // ─── Wishlist (Requires Login usually, but we can store in session if guest, 
    // actually schema tbl_shop_wishlist has no session_id, so it strictly requires login)
    public function wishlist()
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return redirect()->route('store.login')->with('error', 'Please login to view your wishlist.');
        }

        $wishlist = DB::table('tbl_shop_wishlist as w')
            ->where('w.customer_id', $customerId)
            ->join('tbl_shop_products as p', 'w.product_id', '=', 'p.product_id')
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            })
            ->select('w.wishlist_id', 'p.product_id', 'p.product_name', 'p.product_slug', 'p.selling_price', 'p.base_price', 'p.stock_qty', 'img.image_path as primary_image')
            ->get();

        return view('store.wishlist', compact('wishlist'));
    }

    public function addToWishlist(Request $request)
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return response()->json(['status' => 'error', 'message' => 'Please login to add items to wishlist', 'redirect' => route('store.login')]);
        }

        $productId = $request->product_id;
        $variantId = $request->variant_id ?? null;

        $exists = DB::table('tbl_shop_wishlist')
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->exists();

        if ($exists) {
            return response()->json(['status' => 'info', 'message' => 'Product is already in your wishlist']);
        }

        DB::table('tbl_shop_wishlist')->insert([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'created_at' => now()
        ]);

        return response()->json(['status' => 'success', 'message' => 'Added to wishlist']);
    }

    public function removeFromWishlist(Request $request)
    {
        $customerId = session('customer_id');
        if (!$customerId) return response()->json(['status' => 'error', 'message' => 'Unauthorized']);

        DB::table('tbl_shop_wishlist')
            ->where('customer_id', $customerId)
            ->where('wishlist_id', $request->wishlist_id)
            ->delete();

        return response()->json(['status' => 'success', 'message' => 'Item removed from wishlist']);
    }


    // ─── Cart (Supports Guest and Logged In) ─────────────────────────
    public function cart()
    {
        $idData = $this->getCartIdentifier();
        
        $query = DB::table('tbl_shop_cart as c')
            ->join('tbl_shop_products as p', 'c.product_id', '=', 'p.product_id')
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            });

        if ($idData['customer_id']) {
            $query->where('c.customer_id', $idData['customer_id']);
        } else {
            $query->where('c.session_id', $idData['session_id'])->whereNull('c.customer_id');
        }

        $cartItems = $query->select('c.cart_id', 'c.quantity', 'c.unit_price as cart_price', 
                                    'p.product_id', 'p.product_name', 'p.product_slug', 'p.stock_qty',
                                    'img.image_path as primary_image')->get();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += ($item->cart_price * $item->quantity);
        }
        
        // Coupon Logic
        $discount = 0;
        $couponCode = session('applied_coupon');
        if($couponCode) {
            $coupon = DB::table('tbl_shop_coupons')->where('coupon_code', $couponCode)->where('status', 0)->first();
            if($coupon) {
                if($coupon->discount_type == 'fixed') {
                    $discount = $coupon->discount_value;
                } else {
                    $discount = ($subtotal * $coupon->discount_value) / 100;
                    if($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                        $discount = $coupon->max_discount_amount;
                    }
                }
            }
        }

        $total = $subtotal - $discount;
        if($total < 0) $total = 0;

        return view('store.cart', compact('cartItems', 'subtotal', 'discount', 'total', 'couponCode'));
    }

    public function addToCart(Request $request)
    {
        $idData = $this->getCartIdentifier();
        $productId = $request->product_id;
        $variantId = $request->variant_id ?? null;
        $qty = (int)$request->quantity > 0 ? (int)$request->quantity : 1;

        $product = DB::table('tbl_shop_products')->where('product_id', $productId)->first();
        if(!$product) return response()->json(['status' => 'error', 'message' => 'Product not found']);

        // Check if already in cart
        $query = DB::table('tbl_shop_cart')
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId);
            
        if ($idData['customer_id']) {
            $query->where('customer_id', $idData['customer_id']);
        } else {
            $query->where('session_id', $idData['session_id'])->whereNull('customer_id');
        }
        
        $existing = $query->first();

        if ($existing) {
            $newQty = $existing->quantity + $qty;
            if($newQty > $product->stock_qty) {
                return response()->json(['status' => 'error', 'message' => 'Cannot add more than available stock']);
            }
            DB::table('tbl_shop_cart')->where('cart_id', $existing->cart_id)->update(['quantity' => $newQty]);
        } else {
            if($qty > $product->stock_qty) {
                return response()->json(['status' => 'error', 'message' => 'Not enough stock']);
            }
            DB::table('tbl_shop_cart')->insert([
                'customer_id' => $idData['customer_id'],
                'session_id' => $idData['session_id'],
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $qty,
                'unit_price' => $product->selling_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Added to cart']);
    }

    public function updateCart(Request $request)
    {
        $idData = $this->getCartIdentifier();
        $cartId = $request->cart_id;
        $qty = (int)$request->quantity;

        if ($qty <= 0) return $this->removeFromCart($request);

        $query = DB::table('tbl_shop_cart')->where('cart_id', $cartId);
        if ($idData['customer_id']) $query->where('customer_id', $idData['customer_id']);
        else $query->where('session_id', $idData['session_id']);
        
        $cartItem = (clone $query)->first();
        if(!$cartItem) return response()->json(['status' => 'error', 'message' => 'Item not found']);
        
        $product = DB::table('tbl_shop_products')->where('product_id', $cartItem->product_id)->first();
        if($qty > $product->stock_qty) {
             return response()->json(['status' => 'error', 'message' => 'Not enough stock available']);
        }

        $query->update(['quantity' => $qty, 'updated_at' => now()]);
        return response()->json(['status' => 'success', 'message' => 'Cart updated']);
    }

    public function removeFromCart(Request $request)
    {
        $idData = $this->getCartIdentifier();
        $query = DB::table('tbl_shop_cart')->where('cart_id', $request->cart_id);
        
        if ($idData['customer_id']) $query->where('customer_id', $idData['customer_id']);
        else $query->where('session_id', $idData['session_id']);
        
        $query->delete();
        return response()->json(['status' => 'success', 'message' => 'Removed from cart']);
    }

    // ─── Coupons (Module 16) ─────────────────────────────────────────
    public function applyCoupon(Request $request)
    {
        $code = trim($request->coupon_code);
        $coupon = DB::table('tbl_shop_coupons')
            ->where('coupon_code', $code)
            ->where('status', 0)
            ->first();

        if (!$coupon) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired coupon code']);
        }

        // Add validation for valid_until, min_order_amount, etc...
        if ($coupon->valid_until && strtotime($coupon->valid_until) < time()) {
             return response()->json(['status' => 'error', 'message' => 'Coupon has expired']);
        }

        session(['applied_coupon' => $code]);
        return response()->json(['status' => 'success', 'message' => 'Coupon applied successfully']);
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('applied_coupon');
        return response()->json(['status' => 'success', 'message' => 'Coupon removed']);
    }
    
    // View helper to get cart count in header
    public static function getCartCount()
    {
        $customerId = session('customer_id');
        $sessionId = Session::getId();
        
        $query = DB::table('tbl_shop_cart');
        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('customer_id');
        }
        return $query->sum('quantity') ?? 0;
    }
}
