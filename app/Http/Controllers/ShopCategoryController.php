<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopCategoryController extends Controller
{
    // ─── INDEX ───────────────────────────────────────────────────────
    public function index()
    {
        $page_title = 'Categories Management';
        $page_name  = 'Categories';

        // Parent categories for dropdown
        $parentCategories = DB::table('mst_shop_categories')
            ->whereNull('parent_id')
            ->where('status', 0)
            ->orderBy('category_name')
            ->select('category_id', 'category_name')
            ->get();

        return view('shop.master.category.index', compact('page_title', 'page_name', 'parentCategories'));
    }

    // ─── DATATABLE LIST ──────────────────────────────────────────────
    public function getList(Request $request)
    {
        $query = DB::table('mst_shop_categories as c')
            ->leftJoin('mst_shop_categories as p', 'p.category_id', '=', 'c.parent_id')
            ->where('c.status', 0)
            ->select([
                'c.category_id', 'c.category_name', 'c.category_slug',
                'c.category_image', 'c.is_featured', 'c.sort_order',
                'c.parent_id', 'p.category_name as parent_name',
            ]);

        // Filter by parent type
        if ($request->filled('filter_type')) {
            if ($request->filter_type === 'parent') {
                $query->whereNull('c.parent_id');
            } elseif ($request->filter_type === 'sub') {
                $query->whereNotNull('c.parent_id');
            }
        }

        $totalData    = (clone $query)->count();
        $totalFiltered = $totalData;

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('c.category_name', 'like', "%{$search}%")
                  ->orWhere('c.category_slug', 'like', "%{$search}%")
                  ->orWhere('p.category_name', 'like', "%{$search}%");
            });
            $totalFiltered = (clone $query)->count();
        }

        $orderCol   = $request->input('order.0.column', 0);
        $orderDir   = $request->input('order.0.dir', 'desc');
        $cols       = ['c.category_id', 'c.category_image', 'c.category_name', 'p.category_name', 'c.is_featured', 'c.sort_order', 'c.category_id'];
        $orderField = $cols[$orderCol] ?? 'c.category_id';
        $query->orderBy($orderField, $orderDir);

        $start      = $request->input('start', 0);
        $length     = $request->input('length', 25);
        $categories = $query->offset($start)->limit($length)->get();

        $data = [];
        foreach ($categories as $i => $cat) {
            $imgHtml = $cat->category_image
                ? '<img src="' . asset($cat->category_image) . '" alt="' . e($cat->category_name) . '" style="width:45px;height:45px;object-fit:cover;border-radius:8px;">'
                : '<div style="width:45px;height:45px;border-radius:8px;background:#f0f3f8;display:flex;align-items:center;justify-content:center;font-size:1rem;color:#9aacbf;"><i class="bx bx-category"></i></div>';

            $parentBadge = $cat->parent_id
                ? '<span class="badge" style="background:#fff3e0;color:#e65100;border-radius:20px;font-size:0.7rem;padding:3px 8px;"><i class="bx bx-subdirectory-right me-1"></i>' . e($cat->parent_name ?? 'Unknown') . '</span>'
                : '<span class="badge" style="background:#e8f5e9;color:#2e7d32;border-radius:20px;font-size:0.7rem;padding:3px 8px;"><i class="bx bx-category me-1"></i>Top Level</span>';

            // Count subcategories
            $subCount = DB::table('mst_shop_categories')
                ->where('parent_id', $cat->category_id)
                ->where('status', 0)
                ->count();

            $nameCell = '<span class="fw-semibold">' . e($cat->category_name) . '</span>';
            if ($subCount > 0) {
                $nameCell .= ' <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:0.68rem;border-radius:20px;">' . $subCount . ' sub</span>';
            }
            $nameCell .= '<br><small class="text-muted">' . e($cat->category_slug ?? '') . '</small>';

            $featuredBadge = $cat->is_featured
                ? '<span class="badge bg-success-subtle text-success" style="font-size:0.7rem;border-radius:20px;"><i class="bx bx-star me-1"></i>Yes</span>'
                : '<span class="text-muted" style="font-size:0.78rem;">—</span>';

            $actions = '
                <div class="d-flex gap-1">
                    <button class="btn btn-xs btn-outline-primary editCategoryBtn"
                        data-id="' . $cat->category_id . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;" title="Edit">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger deleteCategoryBtn"
                        data-id="' . $cat->category_id . '"
                        data-name="' . e($cat->category_name) . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;" title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>';

            $data[] = [
                $start + $i + 1,
                $imgHtml,
                $nameCell,
                $parentBadge,
                $featuredBadge,
                $cat->sort_order,
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

    // ─── STORE / UPDATE ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $rules = [
            'category_name' => 'required|string|max:200',
            'category_image'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'category_banner'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $categoryId = $request->input('category_id');
        $parentId   = $request->input('parent_id') ?: null;

        // Prevent a category from being its own parent
        if ($categoryId && $categoryId == $parentId) {
            return response()->json(['status' => 'error', 'message' => 'A category cannot be its own parent.'], 422);
        }

        DB::beginTransaction();
        try {
            $slug = generateShopSlug(trim($request->category_name), 'mst_shop_categories', 'category_slug', $categoryId ? (int)$categoryId : null);

            $imagePath  = null;
            $bannerPath = null;

            if ($request->hasFile('category_image')) {
                $imagePath = uploadShopImage($request->file('category_image'), 'categories');
            }
            if ($request->hasFile('category_banner')) {
                $bannerPath = uploadShopImage($request->file('category_banner'), 'categories/banners');
            }

            $payload = [
                'parent_id'            => $parentId,
                'category_name'        => trim($request->category_name),
                'category_slug'        => $slug,
                'category_description' => trim($request->category_description ?? ''),
                'meta_title'           => trim($request->meta_title ?? ''),
                'meta_description'     => trim($request->meta_description ?? ''),
                'meta_keywords'        => trim($request->meta_keywords ?? ''),
                'sort_order'           => (int) ($request->sort_order ?? 0),
                'is_featured'          => $request->has('is_featured') ? 1 : 0,
                'updated_by'           => getShopCreatedBy(),
                'updated_at'           => now(),
            ];

            if ($imagePath)  $payload['category_image']  = $imagePath;
            if ($bannerPath) $payload['category_banner'] = $bannerPath;

            if ($categoryId) {
                $old = DB::table('mst_shop_categories')->where('category_id', $categoryId)->first();
                if ($imagePath && $old->category_image)  deleteShopImage($old->category_image);
                if ($bannerPath && $old->category_banner) deleteShopImage($old->category_banner);

                DB::table('mst_shop_categories')->where('category_id', $categoryId)->update($payload);
                logShopActivity('updated', 'ShopCategory', (int)$categoryId, $request->category_name);
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Category updated successfully.']);
            } else {
                $payload['status']     = 0;
                $payload['created_by'] = getShopCreatedBy();
                $payload['created_at'] = now();

                $newId = DB::table('mst_shop_categories')->insertGetId($payload);
                logShopActivity('created', 'ShopCategory', $newId, $request->category_name);
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Category added successfully.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ─── EDIT ────────────────────────────────────────────────────────
    public function edit($id)
    {
        $cat = DB::table('mst_shop_categories')->where('category_id', $id)->where('status', 0)->first();
        if (!$cat) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $cat]);
    }

    // ─── DELETE ──────────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $id  = $request->input('category_id');
        $cat = DB::table('mst_shop_categories')->where('category_id', $id)->where('status', 0)->first();

        if (!$cat) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $productCount = DB::table('tbl_shop_products')->where('category_id', $id)->where('status', 0)->count();
        if ($productCount > 0) {
            return response()->json(['status' => 'error', 'message' => "Cannot delete. {$productCount} product(s) use this category."], 422);
        }

        $subCount = DB::table('mst_shop_categories')->where('parent_id', $id)->where('status', 0)->count();
        if ($subCount > 0) {
            return response()->json(['status' => 'error', 'message' => "Cannot delete. This category has {$subCount} sub-categorie(s). Delete sub-categories first."], 422);
        }

        DB::table('mst_shop_categories')->where('category_id', $id)->update([
            'status'     => 1,
            'updated_by' => getShopCreatedBy(),
            'updated_at' => now(),
        ]);

        logShopActivity('deleted', 'ShopCategory', (int)$id, $cat->category_name);
        return response()->json(['status' => 'success', 'message' => 'Category deleted successfully.']);
    }

    // ─── GET SUBCATEGORIES (for dependent dropdowns in product form) ──
    public function getSubcategories($parentId)
    {
        $subs = DB::table('mst_shop_categories')
            ->where('parent_id', $parentId)
            ->where('status', 0)
            ->orderBy('category_name')
            ->select('category_id', 'category_name')
            ->get();

        return response()->json(['status' => 'success', 'data' => $subs]);
    }

    // ─── AJAX SELECT2 LIST ────────────────────────────────────────────
    public function ajaxList(Request $request)
    {
        $search   = $request->input('q', '');
        $parentId = $request->input('parent_id');

        $query = DB::table('mst_shop_categories')
            ->where('status', 0)
            ->where('category_name', 'like', "%{$search}%");

        if ($parentId !== null) {
            $query->where('parent_id', $parentId ?: null);
        }

        $cats = $query->select('category_id as id', 'category_name as text')
            ->orderBy('category_name')
            ->limit(100)
            ->get();

        return response()->json(['results' => $cats]);
    }
}
