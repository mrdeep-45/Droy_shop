<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShopProductController extends Controller
{
    public function index()
    {
        $page_title = 'Product Management';
        $page_name  = 'Products';
        
        $brands     = DB::table('mst_shop_brands')->where('status', 0)->orderBy('brand_name')->get();
        $categories = DB::table('mst_shop_categories')->where('status', 0)->orderBy('category_name')->get();
        $units      = DB::table('mst_shop_units')->where('status', 0)->orderBy('unit_name')->get();
        $variants   = DB::table('mst_shop_variants')->where('status', 0)->orderBy('variant_name')->get();
        
        return view('shop.product.index', compact('page_title', 'page_name', 'brands', 'categories', 'units', 'variants'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('tbl_shop_products as p')
            ->leftJoin('mst_shop_brands as b', 'p.brand_id', '=', 'b.brand_id')
            ->leftJoin('mst_shop_categories as c', 'p.category_id', '=', 'c.category_id')
            ->leftJoin('tbl_shop_product_images as pi', function($join) {
                $join->on('p.product_id', '=', 'pi.product_id')
                     ->where('pi.is_primary', '=', 1);
            })
            ->where('p.status', 0)
            ->select([
                'p.product_id', 'p.product_name', 'p.product_code', 'p.sku', 'p.selling_price', 'p.stock_qty', 'p.is_active', 'p.is_featured',
                'b.brand_name', 'c.category_name', 'pi.image_path'
            ]);

        if (session('shop_login_type') === 'Vendor') {
            $query->where('p.vendor_id', session('vendor_id'));
        }

        $totalData = (clone $query)->count();
        
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('p.product_name', 'like', "%{$search}%")
                  ->orWhere('p.product_code', 'like', "%{$search}%")
                  ->orWhere('p.sku', 'like', "%{$search}%")
                  ->orWhere('b.brand_name', 'like', "%{$search}%")
                  ->orWhere('c.category_name', 'like', "%{$search}%");
            });
        }
        $totalFiltered = (clone $query)->count();

        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy('p.product_id', $orderDir);

        $start  = $request->input('start', 0);
        $length = $request->input('length', 25);
        $products = $query->offset($start)->limit($length)->get();

        $data = [];
        foreach ($products as $i => $p) {
            $imgSrc = getShopImageUrl($p->image_path, 'https://placehold.co/100x100/e2e8f0/475569?text=Product');
            $productCell = '<div class="d-flex align-items-center gap-3">
                                <img src="' . $imgSrc . '" alt="IMG" class="rounded shadow-sm" style="width:40px;height:40px;object-fit:cover;">
                                <div>
                                    <h6 class="mb-0 fw-semibold" style="font-size:0.85rem;">' . e($p->product_name) . '</h6>
                                    <small class="text-muted">' . e($p->product_code) . ' | SKU: ' . e($p->sku ?? 'N/A') . '</small>
                                </div>
                            </div>';
            
            $catBrandCell = '<small class="text-muted d-block">Cat: <span class="fw-medium text-dark">' . e($p->category_name ?? '—') . '</span></small>'
                          . '<small class="text-muted d-block">Brand: <span class="fw-medium text-dark">' . e($p->brand_name ?? '—') . '</span></small>';
            
            $priceCell = '₹' . number_format($p->selling_price, 2);
            $stockCell = '<span class="badge ' . ($p->stock_qty > 10 ? 'bg-success-subtle text-success' : ($p->stock_qty > 0 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger')) . '">' . $p->stock_qty . '</span>';
            
            $statusBadge = $p->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            
            $isVendor = session('shop_login_type') === 'Vendor';
            $editRoute = $isVendor ? route('vendor.product.edit', $p->product_id) : route('shop.product.edit', $p->product_id);
            $showRoute = $isVendor ? '#' : route('shop.product.show', $p->product_id); // we might not have a vendor show route
            
            $actions = '<div class="d-flex gap-1 align-items-center justify-content-center">
                            <a href="' . $editRoute . '" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bx bx-edit"></i></a>
                            <a href="' . $showRoute . '" class="btn btn-sm btn-outline-info" title="View"><i class="bx bx-show"></i></a>
                            <button class="btn btn-sm btn-outline-danger deleteProductBtn" data-id="' . $p->product_id . '" data-name="' . e($p->product_name) . '" title="Delete"><i class="bx bx-trash"></i></button>
                        </div>';

            $data[] = [
                $start + $i + 1,
                $productCell,
                $catBrandCell,
                $priceCell,
                $stockCell,
                $statusBadge,
                $actions
            ];
        }

        return response()->json(['draw' => (int)$request->draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'product_name'  => 'required|string|max:200',
            'category_id'   => 'required|integer',
            'base_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $productId = $request->input('product_id');

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->product_name);
            $slugCheck = DB::table('tbl_shop_products')->where('product_slug', $slug)->where('status', 0);
            if ($productId) $slugCheck->where('product_id', '!=', $productId);
            if ($slugCheck->exists()) {
                $slug = $slug . '-' . time();
            }

            $payload = [
                'product_name'        => trim($request->product_name),
                'product_slug'        => $slug,
                'brand_id'            => $request->brand_id ?: null,
                'category_id'         => $request->category_id ?: null,
                'shop_unit_id'        => $request->shop_unit_id ?: null,
                'short_description'   => trim($request->short_description ?? ''),
                'long_description'    => trim($request->long_description ?? ''),
                'base_price'          => (float)$request->base_price,
                'selling_price'       => (float)$request->selling_price,
                'discount_percent'    => (float)($request->discount_percent ?? 0),
                'cost_price'          => (float)($request->cost_price ?? 0),
                'hsn_code'            => trim($request->hsn_code ?? ''),
                'gst_percent'         => (float)($request->gst_percent ?? 0),
                'stock_qty'           => (int)($request->stock_qty ?? 0),
                'min_order_qty'       => (int)($request->min_order_qty ?? 1),
                'low_stock_threshold' => (int)($request->low_stock_threshold ?? 10),
                'sku'                 => trim($request->sku ?? ''),
                'is_active'           => $request->has('is_active') ? 1 : 0,
                'is_featured'         => $request->has('is_featured') ? 1 : 0,
                'meta_title'          => trim($request->meta_title ?? ''),
                'meta_description'    => trim($request->meta_description ?? ''),
                'meta_keywords'       => trim($request->meta_keywords ?? ''),
                'updated_by'          => getShopCreatedBy(),
                'updated_at'          => now(),
            ];

            if ($productId) {
                DB::table('tbl_shop_products')->where('product_id', $productId)->update($payload);
                logShopActivity('updated', 'ShopProduct', $productId, $request->product_name);
            } else {
                $payload['product_code'] = generateShopCode('PRD');
                $payload['status']       = 0;
                $payload['created_by']   = getShopCreatedBy();
                $payload['created_at']   = now();
                $productId = DB::table('tbl_shop_products')->insertGetId($payload);
                logShopActivity('created', 'ShopProduct', $productId, $request->product_name);
            }

            // Handle Images
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $idx => $img) {
                    $path = uploadShopImage($img, 'products');
                    if ($path) {
                        $isPrimary = ($idx == 0 && DB::table('tbl_shop_product_images')->where('product_id', $productId)->count() == 0) ? 1 : 0;
                        DB::table('tbl_shop_product_images')->insert([
                            'product_id' => $productId,
                            'image_path' => $path,
                            'is_primary' => $isPrimary,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Product saved successfully.', 'product_id' => $productId]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $product = DB::table('tbl_shop_products')->where('product_id', $id)->where('status', 0)->first();
        if (!$product) abort(404);
        
        $images = DB::table('tbl_shop_product_images')->where('product_id', $id)->orderBy('sort_order')->orderBy('is_primary', 'desc')->get();
        
        $brands     = DB::table('mst_shop_brands')->where('status', 0)->orderBy('brand_name')->get();
        $categories = DB::table('mst_shop_categories')->where('status', 0)->orderBy('category_name')->get();
        $units      = DB::table('mst_shop_units')->where('status', 0)->orderBy('unit_name')->get();

        $page_title = 'Edit Product: ' . $product->product_name;
        $page_name  = 'Products';

        return view('shop.product.edit', compact('product', 'images', 'brands', 'categories', 'units', 'page_title', 'page_name'));
    }

    public function show($id)
    {
        $product = DB::table('tbl_shop_products')->where('product_id', $id)->where('status', 0)->first();
        if (!$product) abort(404);

        $images = DB::table('tbl_shop_product_images')->where('product_id', $id)->orderBy('is_primary', 'desc')->get();
        
        $page_title = 'Product Details: ' . $product->product_code;
        $page_name  = 'Products';

        return view('shop.product.show', compact('product', 'images', 'page_title', 'page_name'));
    }

    public function destroy(Request $request)
    {
        $id = $request->product_id;
        DB::table('tbl_shop_products')->where('product_id', $id)->update(['status' => 1, 'updated_by' => getShopCreatedBy(), 'updated_at' => now()]);
        return response()->json(['status' => 'success', 'message' => 'Product deleted successfully.']);
    }

    public function deleteImage(Request $request)
    {
        $img = DB::table('tbl_shop_product_images')->where('image_id', $request->image_id)->first();
        if ($img) {
            deleteShopImage($img->image_path);
            DB::table('tbl_shop_product_images')->where('image_id', $request->image_id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Image deleted.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Image not found.'], 404);
    }

    public function setPrimaryImage(Request $request)
    {
        $img = DB::table('tbl_shop_product_images')->where('image_id', $request->image_id)->first();
        if ($img) {
            DB::table('tbl_shop_product_images')->where('product_id', $img->product_id)->update(['is_primary' => 0]);
            DB::table('tbl_shop_product_images')->where('image_id', $request->image_id)->update(['is_primary' => 1]);
            return response()->json(['status' => 'success', 'message' => 'Primary image set.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Image not found.'], 404);
    }

    public function storeVariant(Request $request)
    {
        $v = Validator::make($request->all(), [
            'product_id' => 'required|exists:tbl_shop_products,product_id',
            'variant_label' => 'required|string|max:150'
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $payload = [
            'product_id' => $request->product_id,
            'variant_label' => trim($request->variant_label),
            'sku' => trim($request->sku ?? ''),
            'price_override' => $request->price_override ? (float)$request->price_override : null,
            'stock_qty' => (int)($request->stock_qty ?? 0),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('tbl_shop_product_variants')->insert($payload);
        return response()->json(['status' => 'success', 'message' => 'Variant added successfully.']);
    }

    public function deleteVariant(Request $request)
    {
        DB::table('tbl_shop_product_variants')->where('product_variant_id', $request->product_variant_id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Variant deleted.']);
    }

    // ── Vendor Methods ──────────────────────────────────────────────
    public function vendorIndex()
    {
        $page_title = 'My Products';
        $page_name  = 'Products';
        
        $brands     = DB::table('mst_shop_brands')->where('status', 0)->orderBy('brand_name')->get();
        $categories = DB::table('mst_shop_categories')->where('status', 0)->orderBy('category_name')->get();
        $units      = DB::table('mst_shop_units')->where('status', 0)->orderBy('unit_name')->get();
        $variants   = DB::table('mst_shop_variants')->where('status', 0)->orderBy('variant_name')->get();
        
        return view('shop.vendor.product.index', compact('page_title', 'page_name', 'brands', 'categories', 'units', 'variants'));
    }

    public function vendorStore(Request $request)
    {
        $vendorId = session('vendor_id');

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:tbl_shop_products,product_code',
            'category_id'  => 'required|integer',
            'base_price'   => 'required|numeric',
            'selling_price'=> 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        $slug = Str::slug($request->product_name) . '-' . uniqid();

        $payload = [
            'vendor_id' => $vendorId,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'product_name' => $request->product_name,
            'product_slug' => $slug,
            'product_code' => strtoupper($request->product_code),
            'sku' => strtoupper($request->sku),
            'base_price' => $request->base_price,
            'selling_price' => $request->selling_price,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'is_active' => $request->is_active ?? 1, // Vendors can activate immediately or wait for approval, let's say 1
            'stock_qty' => $request->stock_qty ?? 0,
            'is_featured' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => $vendorId,
            'created_by_type' => 'vendor'
        ];

        DB::table('tbl_shop_products')->insert($payload);
        return response()->json(['status' => 'success', 'message' => 'Product saved successfully.']);
    }

    public function vendorEdit($id)
    {
        $vendorId = session('vendor_id');
        $product = DB::table('tbl_shop_products')
            ->where('product_id', $id)
            ->where('vendor_id', $vendorId)
            ->first();
            
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.']);
        }
        return response()->json(['status' => 'success', 'data' => $product]);
    }

    public function vendorDestroy(Request $request)
    {
        $vendorId = session('vendor_id');
        DB::table('tbl_shop_products')
            ->where('product_id', $request->product_id)
            ->where('vendor_id', $vendorId)
            ->delete();
        return response()->json(['status' => 'success', 'message' => 'Product deleted.']);
    }
}
