<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ShopVendorManageController extends Controller
{
    /**
     * Admin: List all vendors.
     */
    public function index()
    {
        return view('shop.vendor.index');
    }

    /**
     * DataTables list for admin vendor grid.
     */
    public function getList(Request $request)
    {
        $vendors = DB::table('tbl_shop_vendors as v')
            ->select([
                'v.id',
                'v.shop_name',
                'v.name',
                'v.email',
                'v.phone',
                'v.status',
                'v.is_approved',
                'v.created_at',
            ])
            ->orderByDesc('v.created_at');

        return DataTables::of($vendors)
            ->addColumn('status_badge', function ($row) {
                $color = $row->status == 1 ? 'success' : 'danger';
                $label = $row->status == 1 ? 'Active' : 'Inactive';
                return "<span class='badge bg-{$color}'>{$label}</span>";
            })
            ->addColumn('approval_badge', function ($row) {
                if ($row->is_approved == 1) {
                    return "<span class='badge bg-success'>Approved</span>";
                } elseif ($row->is_approved == 2) {
                    return "<span class='badge bg-danger'>Rejected</span>";
                }
                return "<span class='badge bg-warning text-dark'>Pending</span>";
            })
            ->addColumn('action', function ($row) {
                $html  = "<a href='" . route('shop.vendor.show', $row->id) . "' class='btn btn-sm btn-info'><i class='bx bx-show'></i></a> ";
                if ($row->is_approved == 0) {
                    $html .= "<button onclick='approveVendor({$row->id})' class='btn btn-sm btn-success'><i class='bx bx-check'></i></button> ";
                    $html .= "<button onclick='rejectVendor({$row->id})' class='btn btn-sm btn-danger'><i class='bx bx-x'></i></button> ";
                }
                $html .= "<button onclick='toggleVendorStatus({$row->id},{$row->status})' class='btn btn-sm btn-secondary'><i class='bx bx-toggle-left'></i></button>";
                return $html;
            })
            ->rawColumns(['status_badge', 'approval_badge', 'action'])
            ->make(true);
    }

    /**
     * Show a single vendor profile.
     */
    public function show($id)
    {
        $vendor = DB::table('tbl_shop_vendors')->where('id', $id)->first();
        if (!$vendor) {
            return redirect()->route('shop.vendor.index')->with('error', 'Vendor not found.');
        }

        $products = DB::table('tbl_shop_products')
            ->where('vendor_id', $id)
            ->orderByDesc('created_at')
            ->get();

        $orders = DB::table('tbl_shop_orders as o')
            ->join('tbl_shop_order_items as oi', 'oi.order_id', '=', 'o.id')
            ->where('oi.vendor_id', $id)
            ->select('o.id', 'o.order_number', 'o.total_amount', 'o.status', 'o.created_at')
            ->distinct()
            ->orderByDesc('o.created_at')
            ->limit(20)
            ->get();

        $totalEarnings = DB::table('tbl_shop_vendor_earnings')
            ->where('vendor_id', $id)
            ->sum('amount');

        return view('shop.vendor.show', compact('vendor', 'products', 'orders', 'totalEarnings'));
    }

    /**
     * Approve a vendor.
     */
    public function approve(Request $request)
    {
        DB::table('tbl_shop_vendors')
            ->where('id', $request->vendor_id)
            ->update(['is_approved' => 1, 'status' => 1]);

        return response()->json(['success' => true, 'message' => 'Vendor approved successfully.']);
    }

    /**
     * Reject a vendor.
     */
    public function reject(Request $request)
    {
        DB::table('tbl_shop_vendors')
            ->where('id', $request->vendor_id)
            ->update(['is_approved' => 2, 'status' => 0]);

        return response()->json(['success' => true, 'message' => 'Vendor rejected.']);
    }

    /**
     * Toggle vendor active/inactive.
     */
    public function toggleStatus(Request $request)
    {
        $vendor = DB::table('tbl_shop_vendors')->where('id', $request->vendor_id)->first();
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Vendor not found.']);
        }
        $newStatus = $vendor->status == 1 ? 0 : 1;
        DB::table('tbl_shop_vendors')->where('id', $request->vendor_id)->update(['status' => $newStatus]);

        return response()->json(['success' => true, 'message' => 'Status updated.', 'new_status' => $newStatus]);
    }
}
