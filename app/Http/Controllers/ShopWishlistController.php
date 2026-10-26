<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopWishlistController extends Controller
{
    /**
     * Show customer wishlist (storefront account area).
     */
    public function index()
    {
        $customerId = session('customer_id');
        $items = DB::table('tbl_shop_wishlists as w')
            ->join('tbl_shop_products as p', 'p.id', '=', 'w.product_id')
            ->leftJoin('tbl_shop_product_images as pi', function ($j) {
                $j->on('pi.product_id', '=', 'p.id')->where('pi.is_primary', 1);
            })
            ->where('w.customer_id', $customerId)
            ->select('w.id', 'p.id as product_id', 'p.name', 'p.slug', 'p.price',
                     'p.sale_price', 'pi.image_path')
            ->orderByDesc('w.created_at')
            ->get();

        return view('store.wishlist', compact('items'));
    }

    /**
     * Toggle wishlist (add/remove) — AJAX.
     */
    public function toggle(Request $request)
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Please login to use wishlist.', 'login_required' => true]);
        }

        $productId = $request->product_id;
        $exists = DB::table('tbl_shop_wishlists')
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            DB::table('tbl_shop_wishlists')
                ->where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->delete();
            $added = false;
            $message = 'Removed from wishlist.';
        } else {
            DB::table('tbl_shop_wishlists')->insert([
                'customer_id' => $customerId,
                'product_id'  => $productId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $added = true;
            $message = 'Added to wishlist!';
        }

        $count = DB::table('tbl_shop_wishlists')->where('customer_id', $customerId)->count();

        return response()->json(['success' => true, 'added' => $added, 'message' => $message, 'count' => $count]);
    }
}
