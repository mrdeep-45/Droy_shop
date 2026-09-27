<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopVariantController extends Controller
{
    // ─── INDEX ───────────────────────────────────────────────────────
    public function index()
    {
        $page_title = 'Variants Management';
        $page_name  = 'Product Variants';
        return view('shop.master.variant.index', compact('page_title', 'page_name'));
    }

    // ─── VARIANT LIST (DataTable) ─────────────────────────────────────
    public function getList(Request $request)
    {
        $query = DB::table('mst_shop_variants')
            ->where('status', 0)
            ->select('variant_id', 'variant_name', 'variant_type', 'created_at');

        $totalData    = (clone $query)->count();
        $totalFiltered = $totalData;

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where('variant_name', 'like', "%{$search}%");
            $totalFiltered = (clone $query)->count();
        }

        $start    = $request->input('start', 0);
        $length   = $request->input('length', 25);
        $variants = $query->orderBy('variant_name')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($variants as $i => $variant) {
            $valueCount = DB::table('mst_shop_variant_values')
                ->where('variant_id', $variant->variant_id)
                ->where('status', 0)->count();

            $typeIcon = match ($variant->variant_type) {
                'color' => '<i class="bx bx-palette me-1 text-danger"></i>',
                'image' => '<i class="bx bx-image me-1 text-info"></i>',
                default => '<i class="bx bx-text me-1 text-primary"></i>',
            };
            $typeBadge = '<span class="badge" style="background:#f0f3f8;color:#4a5568;border-radius:8px;font-size:0.78rem;">'
                . $typeIcon . ucfirst($variant->variant_type) . '</span>';

            $valuesBadge = '<a href="#" class="badge bg-primary-subtle text-primary viewValuesBtn"
                data-id="' . $variant->variant_id . '"
                data-name="' . e($variant->variant_name) . '"
                data-type="' . $variant->variant_type . '"
                style="border-radius:20px;font-size:0.75rem;text-decoration:none;padding:4px 10px;">
                <i class="bx bx-list-ul me-1"></i>' . $valueCount . ' values
            </a>';

            $actions = '
                <div class="d-flex gap-1">
                    <button class="btn btn-xs btn-outline-success viewValuesBtn"
                        data-id="' . $variant->variant_id . '"
                        data-name="' . e($variant->variant_name) . '"
                        data-type="' . $variant->variant_type . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;" title="Manage Values">
                        <i class="bx bx-list-ul"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-primary editVariantBtn"
                        data-id="' . $variant->variant_id . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;" title="Edit">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger deleteVariantBtn"
                        data-id="' . $variant->variant_id . '"
                        data-name="' . e($variant->variant_name) . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;" title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>';

            $data[] = [
                $start + $i + 1,
                '<span class="fw-semibold">' . e($variant->variant_name) . '</span>',
                $typeBadge,
                $valuesBadge,
                $actions,
            ];
        }

        return response()->json(['draw' => (int)$request->draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $data]);
    }

    // ─── STORE / UPDATE VARIANT ───────────────────────────────────────
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'variant_name' => 'required|string|max:100',
            'variant_type' => 'required|in:text,color,image',
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $variantId = $request->input('variant_id');

        $exists = DB::table('mst_shop_variants')->where('variant_name', trim($request->variant_name))->where('status', 0)
            ->when($variantId, fn($q) => $q->where('variant_id', '!=', $variantId))->exists();
        if ($exists) return response()->json(['status' => 'error', 'message' => 'Variant name already exists.'], 422);

        $payload = ['variant_name' => trim($request->variant_name), 'variant_type' => $request->variant_type];

        if ($variantId) {
            $payload['updated_at'] = now();
            DB::table('mst_shop_variants')->where('variant_id', $variantId)->update($payload);
            logShopActivity('updated', 'ShopVariant', (int)$variantId, $request->variant_name);
            return response()->json(['status' => 'success', 'message' => 'Variant updated successfully.']);
        } else {
            $payload = array_merge($payload, ['status' => 0, 'created_by' => getShopCreatedBy(), 'created_at' => now()]);
            $id = DB::table('mst_shop_variants')->insertGetId($payload);
            logShopActivity('created', 'ShopVariant', $id, $request->variant_name);
            return response()->json(['status' => 'success', 'message' => 'Variant created successfully.', 'variant_id' => $id]);
        }
    }

    // ─── EDIT VARIANT ────────────────────────────────────────────────
    public function edit($id)
    {
        $variant = DB::table('mst_shop_variants')->where('variant_id', $id)->where('status', 0)->first();
        if (!$variant) return response()->json(['status' => 'error', 'message' => 'Variant not found.'], 404);
        return response()->json(['status' => 'success', 'data' => $variant]);
    }

    // ─── DELETE VARIANT ───────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $id      = $request->input('variant_id');
        $variant = DB::table('mst_shop_variants')->where('variant_id', $id)->where('status', 0)->first();
        if (!$variant) return response()->json(['status' => 'error', 'message' => 'Variant not found.'], 404);

        DB::table('mst_shop_variants')->where('variant_id', $id)->update(['status' => 1, 'updated_at' => now()]);
        DB::table('mst_shop_variant_values')->where('variant_id', $id)->update(['status' => 1]);
        logShopActivity('deleted', 'ShopVariant', (int)$id, $variant->variant_name);
        return response()->json(['status' => 'success', 'message' => 'Variant deleted successfully.']);
    }

    // ─── GET VALUES (for sidebar panel) ──────────────────────────────
    public function getValues($variantId)
    {
        $values = DB::table('mst_shop_variant_values')
            ->where('variant_id', $variantId)
            ->where('status', 0)
            ->orderBy('sort_order')
            ->orderBy('value_name')
            ->get();

        return response()->json(['status' => 'success', 'data' => $values]);
    }

    // ─── STORE VALUE ──────────────────────────────────────────────────
    public function storeValue(Request $request)
    {
        $v = Validator::make($request->all(), [
            'variant_id' => 'required|exists:mst_shop_variants,variant_id',
            'value_name' => 'required|string|max:100',
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $valueId   = $request->input('variant_value_id');
        $variantId = $request->input('variant_id');

        $exists = DB::table('mst_shop_variant_values')
            ->where('variant_id', $variantId)
            ->where('value_name', trim($request->value_name))
            ->where('status', 0)
            ->when($valueId, fn($q) => $q->where('variant_value_id', '!=', $valueId))
            ->exists();
        if ($exists) return response()->json(['status' => 'error', 'message' => 'Value already exists for this variant.'], 422);

        $payload = [
            'variant_id'  => $variantId,
            'value_name'  => trim($request->value_name),
            'color_code'  => trim($request->color_code ?? ''),
            'sort_order'  => (int)($request->sort_order ?? 0),
            'updated_at'  => now(),
        ];

        if ($valueId) {
            DB::table('mst_shop_variant_values')->where('variant_value_id', $valueId)->update($payload);
            return response()->json(['status' => 'success', 'message' => 'Value updated.']);
        } else {
            $payload['status']     = 0;
            $payload['created_by'] = getShopCreatedBy();
            $payload['created_at'] = now();
            $id = DB::table('mst_shop_variant_values')->insertGetId($payload);
            return response()->json(['status' => 'success', 'message' => 'Value added.', 'id' => $id]);
        }
    }

    // ─── DELETE VALUE ─────────────────────────────────────────────────
    public function destroyValue(Request $request)
    {
        $id = $request->input('variant_value_id');
        DB::table('mst_shop_variant_values')->where('variant_value_id', $id)->update(['status' => 1, 'updated_at' => now()]);
        return response()->json(['status' => 'success', 'message' => 'Value deleted.']);
    }
}
