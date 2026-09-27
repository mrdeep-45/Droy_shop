<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopInventoryController extends Controller
{
    // ─── ADMIN INVENTORY INDEX ───────────────────────────────────────
    public function index()
    {
        $page_title = 'Inventory Management';
        $page_name  = 'Inventory';
        return view('shop.inventory.index', compact('page_title', 'page_name'));
    }

    // ─── ADMIN DATATABLE LIST ────────────────────────────────────────
    public function getList(Request $request)
    {
        $query = DB::table('tbl_shop_products as p')
            ->leftJoin('mst_shop_categories as c', 'p.category_id', '=', 'c.category_id')
            ->where('p.status', 0)
            ->select([
                'p.product_id', 'p.product_code', 'p.product_name', 'p.sku',
                'p.stock_qty', 'p.low_stock_threshold', 'p.has_variants',
                'c.category_name'
            ]);

        $totalData = (clone $query)->count();

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('p.product_name', 'like', "%{$search}%")
                  ->orWhere('p.product_code', 'like', "%{$search}%")
                  ->orWhere('p.sku', 'like', "%{$search}%");
            });
        }
        $totalFiltered = (clone $query)->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $products = $query->orderBy('p.product_name')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($products as $i => $p) {
            $stockClass = $p->stock_qty <= $p->low_stock_threshold ? 'text-danger fw-bold' : 'text-success fw-bold';
            
            $productCell = '<span class="fw-semibold text-dark">' . e($p->product_name) . '</span><br>' .
                           '<small class="text-muted">' . e($p->product_code) . ' | SKU: ' . e($p->sku ?? 'N/A') . '</small>';

            $variantInfo = $p->has_variants 
                ? '<span class="badge bg-info-subtle text-info"><i class="bx bx-layer"></i> Has Variants</span>' 
                : '<span class="badge bg-light text-secondary">Single Product</span>';

            $actions = '<div class="d-flex gap-1">
                            <button class="btn btn-xs btn-outline-primary adjustStockBtn" data-id="' . $p->product_id . '" data-name="' . e($p->product_name) . '" data-stock="' . $p->stock_qty . '" title="Adjust Stock"><i class="bx bx-slider-alt"></i> Adjust</button>
                            <a href="' . route('shop.inventory.product', $p->product_id) . '" class="btn btn-xs btn-outline-secondary" title="View Ledger"><i class="bx bx-history"></i> Ledger</a>
                        </div>';

            $data[] = [
                $start + $i + 1,
                $productCell,
                e($p->category_name ?? '—'),
                $variantInfo,
                '<span class="' . $stockClass . '">' . $p->stock_qty . '</span>',
                $actions
            ];
        }

        return response()->json(['draw' => (int)$request->draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $data]);
    }

    // ─── ADJUST STOCK ───────────────────────────────────────────────
    public function adjust(Request $request)
    {
        $v = Validator::make($request->all(), [
            'product_id'       => 'required|exists:tbl_shop_products,product_id',
            'adjustment_type'  => 'required|in:add,deduct',
            'quantity'         => 'required|numeric|min:1',
            'notes'            => 'nullable|string'
        ]);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        DB::beginTransaction();
        try {
            $product = DB::table('tbl_shop_products')->where('product_id', $request->product_id)->lockForUpdate()->first();
            $qty = (float)$request->quantity;
            
            $beforeStock = $product->stock_qty;
            $afterStock  = $request->adjustment_type == 'add' ? ($beforeStock + $qty) : ($beforeStock - $qty);

            if ($afterStock < 0) {
                return response()->json(['status' => 'error', 'message' => 'Insufficient stock for deduction.'], 422);
            }

            // Update Product Stock
            DB::table('tbl_shop_products')->where('product_id', $request->product_id)->update(['stock_qty' => $afterStock, 'updated_at' => now()]);

            // Create Inventory Ledger Entry
            DB::table('tbl_shop_inventory')->insert([
                'product_id'       => $request->product_id,
                'transaction_type' => 'adjustment',
                'quantity'         => $request->adjustment_type == 'add' ? $qty : -$qty,
                'before_stock'     => $beforeStock,
                'after_stock'      => $afterStock,
                'reference_type'   => 'Manual Adjustment',
                'notes'            => trim($request->notes),
                'created_by'       => getShopCreatedBy(),
                'created_at'       => now()
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Stock adjusted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ─── PRODUCT LEDGER VIEW ─────────────────────────────────────────
    public function productLedger($id)
    {
        $product = DB::table('tbl_shop_products')->where('product_id', $id)->first();
        if (!$product) abort(404);

        $ledger = DB::table('tbl_shop_inventory')
            ->where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $page_title = 'Inventory Ledger: ' . $product->product_name;
        $page_name  = 'Inventory';

        return view('shop.inventory.ledger', compact('product', 'ledger', 'page_title', 'page_name'));
    }

    // ─── VENDOR INVENTORY (READ ONLY FOR NOW) ────────────────────────
    public function vendorIndex()
    {
        $page_title = 'My Inventory';
        $page_name  = 'Inventory';
        
        $vendorId = session('shop_vendor_id');
        $products = DB::table('tbl_shop_products')
            ->where('vendor_id', $vendorId)
            ->where('status', 0)
            ->get();

        return view('shop.vendor.inventory.index', compact('products', 'page_title', 'page_name'));
    }
}
