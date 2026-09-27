<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopSupplierController extends Controller
{
    // ─── INDEX ───────────────────────────────────────────────────────
    public function index()
    {
        $page_title = 'Suppliers Management';
        $page_name  = 'Suppliers';
        return view('shop.master.supplier.index', compact('page_title', 'page_name'));
    }

    // ─── DATATABLE LIST ──────────────────────────────────────────────
    public function getList(Request $request)
    {
        $query = DB::table('tbl_shop_suppliers')
            ->where('status', 0)
            ->select([
                'supplier_id', 'supplier_code', 'supplier_name', 'contact_person',
                'email', 'phone', 'city', 'state', 'gst_number',
                'payment_terms', 'is_active', 'created_at',
            ]);

        // Filter by active
        if ($request->filled('filter_status')) {
            $query->where('is_active', $request->filter_status);
        }

        $totalData    = (clone $query)->count();
        $totalFiltered = $totalData;

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
            $totalFiltered = (clone $query)->count();
        }

        $orderDir   = $request->input('order.0.dir', 'desc');
        $query->orderBy('supplier_id', $orderDir);

        $start     = $request->input('start', 0);
        $length    = $request->input('length', 25);
        $suppliers = $query->offset($start)->limit($length)->get();

        $data = [];
        foreach ($suppliers as $i => $sup) {
            $statusBadge = $sup->is_active
                ? '<span class="badge bg-success-subtle text-success" style="font-size:0.7rem;border-radius:20px;padding:4px 10px;"><i class="bx bx-check-circle me-1"></i>Active</span>'
                : '<span class="badge bg-danger-subtle text-danger" style="font-size:0.7rem;border-radius:20px;padding:4px 10px;"><i class="bx bx-x-circle me-1"></i>Inactive</span>';

            $purchaseCount = DB::table('tbl_shop_purchases')->where('supplier_id', $sup->supplier_id)->where('status', 0)->count();
            $totalPurchaseValue = DB::table('tbl_shop_purchases')->where('supplier_id', $sup->supplier_id)->where('status', 0)->sum('total_amount');

            $supplierCell = '<span class="fw-semibold">' . e($sup->supplier_name) . '</span>'
                . '<br><small class="text-muted">' . e($sup->supplier_code) . '</small>';

            $contactCell = e($sup->contact_person ?? '—')
                . '<br><a href="mailto:' . e($sup->email) . '" style="font-size:0.76rem;color:#0d6efd;">' . e($sup->email) . '</a>';

            $locationCell = e(implode(', ', array_filter([$sup->city, $sup->state])));

            $purchaseCell = '<span class="badge bg-info-subtle text-info" style="font-size:0.7rem;border-radius:20px;padding:3px 8px;">' . $purchaseCount . ' orders</span>'
                . '<br><small class="fw-semibold text-success">₹' . number_format($totalPurchaseValue, 2) . '</small>';

            $actions = '
                <div class="d-flex gap-1 flex-wrap">
                    <a href="' . route('shop.supplier.show', $sup->supplier_id) . '"
                       class="btn btn-xs btn-outline-info"
                       style="padding:3px 8px;font-size:0.75rem;border-radius:6px;" title="View Details">
                        <i class="bx bx-show"></i>
                    </a>
                    <button class="btn btn-xs btn-outline-primary editSupplierBtn"
                        data-id="' . $sup->supplier_id . '"
                        style="padding:3px 8px;font-size:0.75rem;border-radius:6px;" title="Edit">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger deleteSupplierBtn"
                        data-id="' . $sup->supplier_id . '"
                        data-name="' . e($sup->supplier_name) . '"
                        style="padding:3px 8px;font-size:0.75rem;border-radius:6px;" title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>';

            $data[] = [
                $start + $i + 1,
                $supplierCell,
                $contactCell,
                e($sup->phone ?? '—'),
                $locationCell,
                $purchaseCell,
                $statusBadge,
                $actions,
            ];
        }

        return response()->json(['draw' => (int)$request->draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $data]);
    }

    // ─── STORE / UPDATE ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $rules = [
            'supplier_name'  => 'required|string|max:200',
            'email'          => 'nullable|email|max:150',
            'phone'          => 'nullable|string|max:20',
            'opening_balance'=> 'nullable|numeric|min:0',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return response()->json(['status' => 'error', 'message' => $v->errors()->first()], 422);

        $supplierId = $request->input('supplier_id');

        // Duplicate check
        if ($request->filled('email')) {
            $emailExists = DB::table('tbl_shop_suppliers')
                ->where('email', trim($request->email))->where('status', 0)
                ->when($supplierId, fn($q) => $q->where('supplier_id', '!=', $supplierId))->exists();
            if ($emailExists) return response()->json(['status' => 'error', 'message' => 'Email address already registered.'], 422);
        }

        DB::beginTransaction();
        try {
            $payload = [
                'supplier_name'   => trim($request->supplier_name),
                'contact_person'  => trim($request->contact_person ?? ''),
                'email'           => trim($request->email ?? ''),
                'phone'           => trim($request->phone ?? ''),
                'alternate_phone' => trim($request->alternate_phone ?? ''),
                'gst_number'      => strtoupper(trim($request->gst_number ?? '')),
                'pan_number'      => strtoupper(trim($request->pan_number ?? '')),
                'address'         => trim($request->address ?? ''),
                'city'            => trim($request->city ?? ''),
                'state'           => trim($request->state ?? ''),
                'country'         => trim($request->country ?? 'India'),
                'pincode'         => trim($request->pincode ?? ''),
                'bank_name'       => trim($request->bank_name ?? ''),
                'bank_account'    => trim($request->bank_account ?? ''),
                'ifsc_code'       => strtoupper(trim($request->ifsc_code ?? '')),
                'payment_terms'   => trim($request->payment_terms ?? ''),
                'credit_days'     => (int)($request->credit_days ?? 0),
                'credit_limit'    => (float)($request->credit_limit ?? 0),
                'opening_balance' => (float)($request->opening_balance ?? 0),
                'is_active'       => $request->has('is_active') ? 1 : 0,
                'notes'           => trim($request->notes ?? ''),
                'updated_by'      => getShopCreatedBy(),
                'updated_at'      => now(),
            ];

            if ($supplierId) {
                $old = DB::table('tbl_shop_suppliers')->where('supplier_id', $supplierId)->first();
                DB::table('tbl_shop_suppliers')->where('supplier_id', $supplierId)->update($payload);
                logShopActivity('updated', 'ShopSupplier', (int)$supplierId, $request->supplier_name, (array)$old, $payload);
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Supplier updated successfully.']);
            } else {
                $code = generateShopCode('SP');
                $payload['supplier_code'] = $code;
                $payload['status']        = 0;
                $payload['created_by']    = getShopCreatedBy();
                $payload['created_at']    = now();
                $newId = DB::table('tbl_shop_suppliers')->insertGetId($payload);
                logShopActivity('created', 'ShopSupplier', $newId, $request->supplier_name);
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Supplier added successfully.', 'supplier_code' => $code]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ─── EDIT ────────────────────────────────────────────────────────
    public function edit($id)
    {
        $sup = DB::table('tbl_shop_suppliers')->where('supplier_id', $id)->where('status', 0)->first();
        if (!$sup) return response()->json(['status' => 'error', 'message' => 'Supplier not found.'], 404);
        return response()->json(['status' => 'success', 'data' => $sup]);
    }

    // ─── SHOW (Detail Page) ───────────────────────────────────────────
    public function show($id)
    {
        $supplier = DB::table('tbl_shop_suppliers')->where('supplier_id', $id)->where('status', 0)->first();
        if (!$supplier) abort(404);

        $purchases = DB::table('tbl_shop_purchases as p')
            ->where('p.supplier_id', $id)
            ->where('p.status', 0)
            ->orderBy('p.created_at', 'desc')
            ->limit(10)
            ->select('p.purchase_number', 'p.purchase_date', 'p.total_amount', 'p.payment_status', 'p.receive_status', 'p.purchase_id')
            ->get();

        $stats = [
            'total_orders'  => DB::table('tbl_shop_purchases')->where('supplier_id', $id)->where('status', 0)->count(),
            'total_value'   => DB::table('tbl_shop_purchases')->where('supplier_id', $id)->where('status', 0)->sum('total_amount'),
            'pending_orders'=> DB::table('tbl_shop_purchases')->where('supplier_id', $id)->where('status', 0)->where('receive_status', 'pending')->count(),
        ];

        $page_title = 'Supplier Details';
        $page_name  = 'Supplier Details';
        return view('shop.master.supplier.show', compact('supplier', 'purchases', 'stats', 'page_title', 'page_name'));
    }

    // ─── DELETE ──────────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $id  = $request->input('supplier_id');
        $sup = DB::table('tbl_shop_suppliers')->where('supplier_id', $id)->where('status', 0)->first();
        if (!$sup) return response()->json(['status' => 'error', 'message' => 'Supplier not found.'], 404);

        $purchaseCount = DB::table('tbl_shop_purchases')->where('supplier_id', $id)->where('status', 0)->count();
        if ($purchaseCount > 0) {
            return response()->json(['status' => 'error', 'message' => "Cannot delete. {$purchaseCount} purchase order(s) linked to this supplier."], 422);
        }

        DB::table('tbl_shop_suppliers')->where('supplier_id', $id)->update(['status' => 1, 'updated_by' => getShopCreatedBy(), 'updated_at' => now()]);
        logShopActivity('deleted', 'ShopSupplier', (int)$id, $sup->supplier_name);
        return response()->json(['status' => 'success', 'message' => 'Supplier deleted successfully.']);
    }

    // ─── AJAX SELECT2 ────────────────────────────────────────────────
    public function ajaxList(Request $request)
    {
        $search    = $request->input('q', '');
        $suppliers = DB::table('tbl_shop_suppliers')
            ->where('status', 0)->where('is_active', 1)
            ->where(fn($q) => $q->where('supplier_name', 'like', "%{$search}%")->orWhere('supplier_code', 'like', "%{$search}%"))
            ->select('supplier_id as id', DB::raw("CONCAT(supplier_name, ' (', supplier_code, ')') as text"))
            ->orderBy('supplier_name')->limit(50)->get();
        return response()->json(['results' => $suppliers]);
    }
}
