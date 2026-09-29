<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopRawMaterialController extends Controller
{
    public function index()
    {
        $page_title = 'Raw Materials';
        $page_name  = 'Raw Materials';
        
        $units     = DB::table('mst_shop_units')->where('status', 0)->get();
        $suppliers = DB::table('tbl_shop_suppliers')->where('status', 0)->get();
        
        return view('shop.raw_material.index', compact('page_title', 'page_name', 'units', 'suppliers'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('tbl_shop_raw_materials as rm')
            ->leftJoin('mst_shop_units as u', 'rm.shop_unit_id', '=', 'u.shop_unit_id')
            ->leftJoin('tbl_shop_suppliers as s', 'rm.supplier_id', '=', 's.supplier_id')
            ->where('rm.status', 0)
            ->select([
                'rm.raw_material_id', 'rm.material_code', 'rm.material_name', 
                'rm.current_stock', 'rm.min_stock_level', 'rm.cost_price',
                'u.unit_name', 'u.unit_short', 's.supplier_name'
            ]);

        $totalData = (clone $query)->count();

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('rm.material_name', 'like', "%{$search}%")
                  ->orWhere('rm.material_code', 'like', "%{$search}%");
            });
        }
        $totalFiltered = (clone $query)->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $materials = $query->orderBy('rm.material_name')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($materials as $i => $m) {
            $stockClass = $m->current_stock <= $m->min_stock_level ? 'text-danger fw-bold' : 'text-success fw-bold';
            
            $materialCell = '<span class="fw-semibold text-dark">' . e($m->material_name) . '</span><br>' .
                            '<small class="text-muted">' . e($m->material_code) . '</small>';
                            
            $stockCell = '<span class="' . $stockClass . '">' . (float)$m->current_stock . '</span> ' . e($m->unit_short ?? '');

            $actions = '<div class="d-flex gap-1">
                            <button class="btn btn-xs btn-outline-primary editMaterialBtn" data-id="' . $m->raw_material_id . '"><i class="bx bx-edit"></i> Edit</button>
                            <button class="btn btn-xs btn-outline-danger deleteMaterialBtn" data-id="' . $m->raw_material_id . '" data-name="' . e($m->material_name) . '"><i class="bx bx-trash"></i></button>
                        </div>';

            $data[] = [
                $start + $i + 1,
                $materialCell,
                e($m->supplier_name ?? '—'),
                $stockCell,
                '₹' . number_format($m->cost_price, 2),
                $actions
            ];
        }

        return response()->json(['draw' => (int)$request->draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $id = $request->raw_material_id;
        $rules = [
            'material_name'   => 'required|string|max:255',
            'material_code'   => 'nullable|string|max:30|unique:tbl_shop_raw_materials,material_code,' . $id . ',raw_material_id',
            'shop_unit_id'    => 'nullable|exists:mst_shop_units,shop_unit_id',
            'supplier_id'     => 'nullable|exists:tbl_shop_suppliers,supplier_id',
            'cost_price'      => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
            'max_stock_level' => 'nullable|numeric|min:0',
        ];
        
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first(), 'errors' => $v->errors()], 422);

        $data = [
            'material_name'   => $request->material_name,
            'material_code'   => $request->material_code ?? ('RM' . rand(1000,9999) . time()),
            'shop_unit_id'    => $request->shop_unit_id,
            'supplier_id'     => $request->supplier_id,
            'cost_price'      => $request->cost_price ?? 0,
            'min_stock_level' => $request->min_stock_level ?? 0,
            'max_stock_level' => $request->max_stock_level,
            'description'     => $request->description,
        ];

        if ($id) {
            $data['updated_by'] = getShopCreatedBy();
            $data['updated_at'] = now();
            DB::table('tbl_shop_raw_materials')->where('raw_material_id', $id)->update($data);
            $msg = 'Raw Material updated successfully';
        } else {
            $data['current_stock'] = 0; // Starts at 0, purchases will increase it
            $data['status'] = 0;
            $data['created_by'] = getShopCreatedBy();
            $data['created_at'] = now();
            DB::table('tbl_shop_raw_materials')->insert($data);
            $msg = 'Raw Material created successfully';
        }

        return response()->json(['status' => 'success', 'message' => $msg]);
    }

    public function edit($id)
    {
        $rm = DB::table('tbl_shop_raw_materials')->where('raw_material_id', $id)->first();
        if (!$rm) return response()->json(['status' => 'error', 'message' => 'Not found']);
        return response()->json(['status' => 'success', 'data' => $rm]);
    }

    public function destroy(Request $request)
    {
        $id = $request->raw_material_id;
        // Check if used in purchases
        $used = DB::table('tbl_shop_purchase_items')->where('raw_material_id', $id)->exists();
        if($used) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete. This material is linked to purchase orders.']);
        }
        
        DB::table('tbl_shop_raw_materials')->where('raw_material_id', $id)->update(['status' => 1, 'updated_by' => getShopCreatedBy()]);
        return response()->json(['status' => 'success', 'message' => 'Raw material deleted.']);
    }
}
