<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopStoreController extends Controller
{
    public function home()
    {
        $categories = DB::table('mst_shop_categories')
            ->where('status', 0)
            ->whereNull('parent_id')
            ->orderBy('category_name')
            ->get();

        $featuredProducts = DB::table('tbl_shop_products as p')
            ->where('p.status', 0)
            ->where('p.is_active', 1)
            ->where('p.is_featured', 1)
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            })
            ->select('p.*', 'img.image_path as primary_image')
            ->limit(8)
            ->get();

        $newArrivals = DB::table('tbl_shop_products as p')
            ->where('p.status', 0)
            ->where('p.is_active', 1)
            ->where('p.is_new_arrival', 1)
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            })
            ->select('p.*', 'img.image_path as primary_image')
            ->orderBy('p.created_at', 'desc')
            ->limit(8)
            ->get();

        return view('store.home', compact('categories', 'featuredProducts', 'newArrivals'));
    }

    public function shop(Request $request)
    {
        $query = DB::table('tbl_shop_products as p')
            ->where('p.status', 0)
            ->where('p.is_active', 1)
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            })
            ->select('p.*', 'img.image_path as primary_image');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('p.product_name', 'like', "%{$search}%");
        }

        if ($request->has('category') && $request->category != '') {
            $cat = DB::table('mst_shop_categories')->where('category_slug', $request->category)->first();
            if ($cat) {
                $query->where('p.category_id', $cat->category_id);
            }
        }

        $products = $query->paginate(12);
        
        $categories = DB::table('mst_shop_categories')->where('status', 0)->orderBy('category_name')->get();

        return view('store.shop', compact('products', 'categories'));
    }

    public function category($slug)
    {
        $category = DB::table('mst_shop_categories')->where('category_slug', $slug)->first();
        if (!$category) abort(404);

        $products = DB::table('tbl_shop_products as p')
            ->where('p.status', 0)
            ->where('p.is_active', 1)
            ->where('p.category_id', $category->category_id)
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            })
            ->select('p.*', 'img.image_path as primary_image')
            ->paginate(12);

        $categories = DB::table('mst_shop_categories')->where('status', 0)->orderBy('category_name')->get();

        return view('store.shop', compact('products', 'categories', 'category'));
    }

    public function product($slug)
    {
        $product = DB::table('tbl_shop_products as p')
            ->where('p.product_slug', $slug)
            ->where('p.status', 0)
            ->where('p.is_active', 1)
            ->first();

        if (!$product) abort(404);

        // Record View
        DB::table('tbl_shop_products')->where('product_id', $product->product_id)->increment('view_count');

        $images = DB::table('tbl_shop_product_images')
            ->where('product_id', $product->product_id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();

        $variants = [];
        if ($product->has_variants == 1) {
            $variants = DB::table('tbl_shop_product_variants')
                ->where('product_id', $product->product_id)
                ->where('status', 0)
                ->get();
        }

        $relatedProducts = DB::table('tbl_shop_products as p')
            ->where('p.category_id', $product->category_id)
            ->where('p.product_id', '!=', $product->product_id)
            ->where('p.status', 0)
            ->where('p.is_active', 1)
            ->leftJoin('tbl_shop_product_images as img', function($join) {
                $join->on('p.product_id', '=', 'img.product_id')->where('img.is_primary', 1);
            })
            ->select('p.*', 'img.image_path as primary_image')
            ->limit(4)
            ->get();

        return view('store.product', compact('product', 'images', 'variants', 'relatedProducts'));
    }
}
