<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ShopPurchaseController extends Controller
{
    public function index()
    {
        $page_title = 'Purchase Orders';
        $page_name  = 'Purchases';
        
        $suppliers = DB::table('tbl_shop_suppliers')->where('status', 0)->get();
        // Since we can purchase products or raw materials:
        $rawMaterials = DB::table('tbl_shop_raw_materials')->where('status', 0)->get();
        
        return view('shop.purchase.index', compact('page_title', 'page_name', 'suppliers', 'rawMaterials'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('tbl_shop_purchases as p')
            ->join('tbl_shop_suppliers as s', 'p.supplier_id', '=', 's.supplier_id')
            ->where('p.status', 0)
            ->select([
                'p.purchase_id', 'p.purchase_number', 'p.purchase_date', 
                'p.total_amount', 'p.purchase_status', 'p.payment_status',
                's.supplier_name'
            ]);

        $totalData = (clone $query)->count();

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('p.purchase_number', 'like', "%{$search}%")
                  ->orWhere('s.supplier_name', 'like', "%{$search}%");
            });
        }
        $totalFiltered = (clone $query)->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 25);
        $purchases = $query->orderBy('p.purchase_id', 'desc')->offset($start)->limit($length)->get();

        $data = [];
        foreach ($purchases as $i => $p) {
            
            $statusBadge = '<span class="badge bg-secondary">Draft</span>';
            if ($p->purchase_status == 'ordered') $statusBadge = '<span class="badge bg-primary">Ordered</span>';
            if ($p->purchase_status == 'received') $statusBadge = '<span class="badge bg-success">Received</span>';
            if ($p->purchase_status == 'cancelled') $statusBadge = '<span class="badge bg-danger">Cancelled</span>';

            $paymentBadge = '<span class="badge bg-danger">Pending</span>';
            if ($p->payment_status == 'partial') $paymentBadge = '<span class="badge bg-warning text-dark">Partial</span>';
            if ($p->payment_status == 'paid') $paymentBadge = '<span class="badge bg-success">Paid</span>';

            $actions = '<a href="' . route('shop.purchase.show', $p->purchase_id) . '" class="btn btn-xs btn-outline-info"><i class="bx bx-show"></i> View</a>';

            $data[] = [
                $start + $i + 1,
                '<span class="fw-bold text-primary">' . $p->purchase_number . '</span>',
                e($p->supplier_name),
                Carbon::parse($p->purchase_date)->format('d-M-Y'),
                '₹' . number_format($p->total_amount, 2),
                $statusBadge . ' ' . $paymentBadge,
                $actions
            ];
        }

        return response()->json(['draw' => (int)$request->draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'supplier_id'     => 'required|exists:tbl_shop_suppliers,supplier_id',
            'purchase_date'   => 'required|date',
            'items'           => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:tbl_shop_raw_materials,raw_material_id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        DB::beginTransaction();
        try {
            // Generate PO Number
            $poNum = 'PO-' . date('Ymd') . '-' . rand(1000, 9999);

            $subtotal = 0;
            $itemsData = [];
            
            foreach ($request->items as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $total = $qty * $price;
                $subtotal += $total;

                $rm = DB::table('tbl_shop_raw_materials')->where('raw_material_id', $item['raw_material_id'])->first();

                $itemsData[] = [
                    'raw_material_id' => $item['raw_material_id'],
                    'item_name'       => $rm->material_name,
                    'quantity'        => $qty,
                    'unit_price'      => $price,
                    'total_price'     => $total,
                    'received_qty'    => $request->purchase_status == 'received' ? $qty : 0,
                    'created_at'      => now(),
                ];
                
                // If received immediately, update stock
                if ($request->purchase_status == 'received') {
                    $newStock = $rm->current_stock + $qty;
                    DB::table('tbl_shop_raw_materials')->where('raw_material_id', $item['raw_material_id'])->update([
                        'current_stock' => $newStock,
                        'updated_at'    => now()
                    ]);
                    
                    // Note: Raw Materials don't have tbl_shop_inventory yet (only products do).
                    // If we wanted to track RM ledger, we would need a tbl_shop_rm_inventory.
                    // For now, updating current_stock is sufficient per Module 11 requirement.
                }
            }

            $discount = (float)($request->discount_amount ?? 0);
            $shipping = (float)($request->shipping_charge ?? 0);
            $totalAmount = ($subtotal - $discount) + $shipping;

            $purchaseId = DB::table('tbl_shop_purchases')->insertGetId([
                'purchase_number' => $poNum,
                'supplier_id'     => $request->supplier_id,
                'purchase_date'   => $request->purchase_date,
                'subtotal'        => $subtotal,
                'discount_amount' => $discount,
                'shipping_charge' => $shipping,
                'total_amount'    => $totalAmount,
                'balance_amount'  => $totalAmount,
                'purchase_status' => $request->purchase_status ?? 'draft',
                'notes'           => $request->notes,
                'created_by'      => getShopCreatedBy(),
                'created_at'      => now(),
            ]);

            foreach ($itemsData as &$idata) {
                $idata['purchase_id'] = $purchaseId;
            }
            DB::table('tbl_shop_purchase_items')->insert($itemsData);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Purchase order created successfully.', 'id' => $purchaseId]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $purchase = DB::table('tbl_shop_purchases as p')
            ->join('tbl_shop_suppliers as s', 'p.supplier_id', '=', 's.supplier_id')
            ->where('p.purchase_id', $id)
            ->select('p.*', 's.supplier_name', 's.contact_person', 's.email', 's.phone', 's.address', 's.city', 's.state')
            ->first();

        if (!$purchase) abort(404);

        $items = DB::table('tbl_shop_purchase_items as pi')
            ->leftJoin('tbl_shop_raw_materials as rm', 'pi.raw_material_id', '=', 'rm.raw_material_id')
            ->leftJoin('mst_shop_units as u', 'rm.shop_unit_id', '=', 'u.shop_unit_id')
            ->where('pi.purchase_id', $id)
            ->select('pi.*', 'rm.material_code', 'u.unit_short')
            ->get();

        $page_title = 'View Purchase Order';
        $page_name  = 'Purchases';

        return view('shop.purchase.show', compact('purchase', 'items', 'page_title', 'page_name'));
    }
}
