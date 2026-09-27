<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopUnitController extends Controller
{
    public function index()
    {
        $page_title = 'Units Management';
        $page_name  = 'Units';
        return view('shop.master.unit.index', compact('page_title', 'page_name'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('mst_shop_units')
            ->where('status', 0)
            ->select('shop_unit_id', 'unit_name', 'unit_short', 'created_at');

        $totalData = (clone $query)->count();
        $totalFiltered = $totalData;

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('unit_name', 'like', "%{$search}%")->orWhere('unit_short', 'like', "%{$search}%");
            });
            $totalFiltered = (clone $query)->count();
        }

        $start  = $request->input('start', 0);
        $length = $request->input('length', 25);
        $units  = $query->orderBy('unit_name')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($units as $i => $unit) {
            // Product usage count
            $usageCount = DB::table('tbl_shop_products')->where('shop_unit_id', $unit->shop_unit_id)->where('status', 0)->count();

            $actions = '
                <div class="d-flex gap-1">
                    <button class="btn btn-xs btn-outline-primary editUnitBtn"
                        data-id="' . $unit->shop_unit_id . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger deleteUnitBtn"
                        data-id="' . $unit->shop_unit_id . '"
                        data-name="' . e($unit->unit_name) . '"
                        style="padding:3px 10px;font-size:0.75rem;border-radius:6px;">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>';

            $data[] = [
                $start + $i + 1,
                '<span class="fw-semibold">' . e($unit->unit_name) . '</span>',
                '<span class="badge" style="background:#f0f3f8;color:#4a5568;border-radius:8px;padding:4px 10px;font-size:0.8rem;">' . e($unit->unit_short ?? '—') . '</span>',
                $usageCount > 0 ? '<span class="badge bg-primary-subtle text-primary" style="border-radius:20px;">' . $usageCount . ' products</span>' : '<span class="text-muted">—</span>',
                $actions,
            ];
        }

        return response()->json(['draw' => (int)$request->draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), ['unit_name' => 'required|string|max:100', 'unit_short' => 'nullable|string|max:20']);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $unitId = $request->input('shop_unit_id');

        $exists = DB::table('mst_shop_units')->where('unit_name', trim($request->unit_name))->where('status', 0)
            ->when($unitId, fn($q) => $q->where('shop_unit_id', '!=', $unitId))->exists();
        if ($exists) return response()->json(['status' => 'error', 'message' => 'Unit name already exists.'], 422);

        $payload = ['unit_name' => trim($request->unit_name), 'unit_short' => trim($request->unit_short ?? ''), 'updated_by' => getShopCreatedBy(), 'updated_at' => now()];

        if ($unitId) {
            DB::table('mst_shop_units')->where('shop_unit_id', $unitId)->update($payload);
            logShopActivity('updated', 'ShopUnit', (int)$unitId, $request->unit_name);
            return response()->json(['status' => 'success', 'message' => 'Unit updated successfully.']);
        } else {
            $payload = array_merge($payload, ['status' => 0, 'created_by' => getShopCreatedBy(), 'created_at' => now()]);
            $id = DB::table('mst_shop_units')->insertGetId($payload);
            logShopActivity('created', 'ShopUnit', $id, $request->unit_name);
            return response()->json(['status' => 'success', 'message' => 'Unit added successfully.']);
        }
    }

    public function edit($id)
    {
        $unit = DB::table('mst_shop_units')->where('shop_unit_id', $id)->where('status', 0)->first();
        if (!$unit) return response()->json(['status' => 'error', 'message' => 'Unit not found.'], 404);
        return response()->json(['status' => 'success', 'data' => $unit]);
    }

    public function destroy(Request $request)
    {
        $id   = $request->input('shop_unit_id');
        $unit = DB::table('mst_shop_units')->where('shop_unit_id', $id)->where('status', 0)->first();
        if (!$unit) return response()->json(['status' => 'error', 'message' => 'Unit not found.'], 404);

        $usage = DB::table('tbl_shop_products')->where('shop_unit_id', $id)->where('status', 0)->count();
        if ($usage > 0) return response()->json(['status' => 'error', 'message' => "Cannot delete. Used in {$usage} product(s)."], 422);

        DB::table('mst_shop_units')->where('shop_unit_id', $id)->update(['status' => 1, 'updated_at' => now()]);
        logShopActivity('deleted', 'ShopUnit', (int)$id, $unit->unit_name);
        return response()->json(['status' => 'success', 'message' => 'Unit deleted successfully.']);
    }
}
