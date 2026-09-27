<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopBrandController extends Controller
{
    // ─── INDEX (List Page) ───────────────────────────────────────────
    public function index()
    {
        $page_title = 'Brands Management';
        $page_name  = 'Brands';
        return view('shop.master.brand.index', compact('page_title', 'page_name'));
    }

    // ─── DATATABLE AJAX LIST ─────────────────────────────────────────
    public function getList(Request $request)
    {
        $query = DB::table('mst_shop_brands')
            ->where('status', 0)
            ->select([
                'brand_id', 'brand_name', 'brand_slug', 'brand_logo',
                'is_featured', 'sort_order', 'created_at',
            ]);

        $totalData    = (clone $query)->count();
        $totalFiltered = $totalData;

        // Global search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('brand_name', 'like', "%{$search}%")
                  ->orWhere('brand_slug', 'like', "%{$search}%");
            });
            $totalFiltered = (clone $query)->count();
        }

        // Order
        $orderCol  = $request->input('order.0.column', 0);
        $orderDir  = $request->input('order.0.dir', 'desc');
        $columns   = ['brand_id', 'brand_name', 'brand_logo', 'is_featured', 'sort_order', 'brand_id'];
        $orderField = $columns[$orderCol] ?? 'brand_id';
        $query->orderBy($orderField, $orderDir);

        // Pagination
        $start  = $request->input('start', 0);
        $length = $request->input('length', 25);
        $brands = $query->offset($start)->limit($length)->get();

        $data = [];
        foreach ($brands as $i => $brand) {
            $logoHtml = $brand->brand_logo
                ? '<img src="' . asset($brand->brand_logo) . '" alt="' . e($brand->brand_name) . '" style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:1px solid #e9ecef;">'
                : '<div style="width:50px;height:50px;border-radius:8px;background:#f0f3f8;display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#9aacbf;"><i class="bx bx-image"></i></div>';

            $featuredBadge = $brand->is_featured
                ? '<span class="badge bg-success-subtle text-success px-2 py-1" style="font-size:0.72rem;border-radius:20px;"><i class="bx bx-star me-1"></i>Featured</span>'
                : '<span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size:0.72rem;border-radius:20px;">Normal</span>';

            $actions = '
                <div class="d-flex gap-1">
                    <button class="btn btn-xs btn-outline-primary editBrandBtn"
                        data-id="' . $brand->brand_id . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;"
                        title="Edit">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger deleteBrandBtn"
                        data-id="' . $brand->brand_id . '"
                        data-name="' . e($brand->brand_name) . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;"
                        title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>';

            $data[] = [
                $start + $i + 1,
                $logoHtml,
                '<span class="fw-semibold">' . e($brand->brand_name) . '</span>'
                    . '<br><small class="text-muted">' . e($brand->brand_slug ?? '') . '</small>',
                $featuredBadge,
                $brand->sort_order,
                $actions,
            ];
        }

        return response()->json([
            'draw'            => (int) $request->draw,
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    // ─── STORE / UPDATE ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $rules = [
            'brand_name'  => 'required|string|max:200',
            'brand_logo'  => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'sort_order'  => 'nullable|integer|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $brandId = $request->input('brand_id');

        // Duplicate name check
        $nameExists = DB::table('mst_shop_brands')
            ->where('brand_name', trim($request->brand_name))
            ->where('status', 0)
            ->when($brandId, fn($q) => $q->where('brand_id', '!=', $brandId))
            ->exists();

        if ($nameExists) {
            return response()->json(['status' => 'error', 'message' => 'Brand name already exists.'], 422);
        }

        DB::beginTransaction();
        try {
            $slug = generateShopSlug(trim($request->brand_name), 'mst_shop_brands', 'brand_slug', $brandId ? (int)$brandId : null);

            $logoPath = null;
            if ($request->hasFile('brand_logo')) {
                $logoPath = uploadShopImage($request->file('brand_logo'), 'brands');
            }

            $payload = [
                'brand_name'        => trim($request->brand_name),
                'brand_slug'        => $slug,
                'brand_description' => trim($request->brand_description ?? ''),
                'meta_title'        => trim($request->meta_title ?? ''),
                'meta_description'  => trim($request->meta_description ?? ''),
                'sort_order'        => (int) ($request->sort_order ?? 0),
                'is_featured'       => $request->has('is_featured') ? 1 : 0,
                'updated_by'        => getShopCreatedBy(),
                'updated_at'        => now(),
            ];

            if ($logoPath) {
                $payload['brand_logo'] = $logoPath;
            }

            if ($brandId) {
                // UPDATE
                $old = DB::table('mst_shop_brands')->where('brand_id', $brandId)->first();

                // Delete old logo if new one uploaded
                if ($logoPath && $old && $old->brand_logo) {
                    deleteShopImage($old->brand_logo);
                }

                DB::table('mst_shop_brands')->where('brand_id', $brandId)->update($payload);

                logShopActivity('updated', 'ShopBrand', (int)$brandId, $request->brand_name,
                    (array)$old, array_merge($payload, ['brand_id' => $brandId]));

                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Brand updated successfully.']);
            } else {
                // INSERT
                $payload['status']     = 0;
                $payload['created_by'] = getShopCreatedBy();
                $payload['created_at'] = now();

                $newId = DB::table('mst_shop_brands')->insertGetId($payload);
                logShopActivity('created', 'ShopBrand', $newId, $request->brand_name, null, $payload);

                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Brand added successfully.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ─── EDIT (Fetch for Modal) ───────────────────────────────────────
    public function edit($id)
    {
        $brand = DB::table('mst_shop_brands')->where('brand_id', $id)->where('status', 0)->first();
        if (!$brand) {
            return response()->json(['status' => 'error', 'message' => 'Brand not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $brand]);
    }

    // ─── DELETE ──────────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $id    = $request->input('brand_id');
        $brand = DB::table('mst_shop_brands')->where('brand_id', $id)->where('status', 0)->first();

        if (!$brand) {
            return response()->json(['status' => 'error', 'message' => 'Brand not found.'], 404);
        }

        // Check if brand is used in products
        $productCount = DB::table('tbl_shop_products')->where('brand_id', $id)->where('status', 0)->count();
        if ($productCount > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Cannot delete. This brand is used in {$productCount} product(s).",
            ], 422);
        }

        DB::table('mst_shop_brands')->where('brand_id', $id)->update([
            'status'     => 1,
            'updated_by' => getShopCreatedBy(),
            'updated_at' => now(),
        ]);

        logShopActivity('deleted', 'ShopBrand', (int)$id, $brand->brand_name);
        return response()->json(['status' => 'success', 'message' => 'Brand deleted successfully.']);
    }

    // ─── AJAX SELECT2 LIST ────────────────────────────────────────────
    public function ajaxList(Request $request)
    {
        $search = $request->input('q', '');
        $brands = DB::table('mst_shop_brands')
            ->where('status', 0)
            ->where('brand_name', 'like', "%{$search}%")
            ->select('brand_id as id', 'brand_name as text')
            ->orderBy('brand_name')
            ->limit(50)
            ->get();

        return response()->json(['results' => $brands]);
    }
}
