<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ShopVendorEarningController extends Controller
{
    /**
     * Admin: Vendor Earnings Overview & Ledger
     */
    public function index()
    {
        $totalEarnings = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
            ->whereNotNull('i.vendor_id')
            ->where('o.order_status', 'delivered')
            ->sum('i.total_price');

        $pendingEarnings = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
            ->whereNotNull('i.vendor_id')
            ->whereIn('o.order_status', ['pending', 'confirmed', 'processing', 'ready_to_ship', 'shipped', 'out_for_delivery'])
            ->sum('i.total_price');

        $lostEarnings = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
            ->whereNotNull('i.vendor_id')
            ->whereIn('o.order_status', ['cancelled', 'returned'])
            ->sum('i.total_price');

        return view('shop.vendor.earning.admin_index', compact('totalEarnings', 'pendingEarnings', 'lostEarnings'));
    }

    /**
     * Admin: DataTable list for all vendor earnings
     */
    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tbl_shop_order_items as i')
                ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
                ->leftJoin('tbl_shop_vendors as v', 'i.vendor_id', '=', 'v.id')
                ->whereNotNull('i.vendor_id')
                ->select([
                    'o.order_number',
                    'o.order_status',
                    'o.created_at',
                    'v.shop_name',
                    'v.name as vendor_person_name',
                    'i.product_name',
                    'i.quantity',
                    'i.total_price as earning'
                ]);

            return DataTables::of($data)
                ->addColumn('vendor_name', function ($row) {
                    return $row->shop_name ?: ($row->vendor_person_name ?: 'Vendor #' . $row->order_number);
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y, h:i A', strtotime($row->created_at));
                })
                ->editColumn('earning', function ($row) {
                    return '₹' . number_format($row->earning, 2);
                })
                ->editColumn('order_status', function ($row) {
                    $badges = [
                        'delivered' => 'bg-success',
                        'cancelled' => 'bg-danger',
                        'returned'  => 'bg-secondary'
                    ];
                    $badge = $badges[$row->order_status] ?? 'bg-warning text-dark';
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->order_status)) . '</span>';
                })
                ->rawColumns(['order_status'])
                ->make(true);
        }
    }

    public function vendorIndex()
    {
        $vendorId = session('vendor_id');

        // Calculate Kpis
        // Total earnings: items belonging to vendor in 'delivered' or 'completed' status
        $totalEarnings = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
            ->where('i.vendor_id', $vendorId)
            ->where('o.order_status', 'delivered')
            ->sum('i.total_price');
            
        // Pending Earnings (Orders placed but not yet delivered)
        $pendingEarnings = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
            ->where('i.vendor_id', $vendorId)
            ->whereIn('o.order_status', ['pending', 'confirmed', 'processing', 'ready_to_ship', 'shipped', 'out_for_delivery'])
            ->sum('i.total_price');
            
        // Cancelled/Refunded Earnings
        $lostEarnings = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
            ->where('i.vendor_id', $vendorId)
            ->whereIn('o.order_status', ['cancelled', 'returned'])
            ->sum('i.total_price');

        return view('shop.vendor.earning.index', compact('totalEarnings', 'pendingEarnings', 'lostEarnings'));
    }

    public function vendorEarningList(Request $request)
    {
        $vendorId = session('vendor_id');

        if ($request->ajax()) {
            $data = DB::table('tbl_shop_order_items as i')
                ->join('tbl_shop_orders as o', 'i.order_id', '=', 'o.order_id')
                ->where('i.vendor_id', $vendorId)
                ->select([
                    'o.order_number',
                    'o.order_status',
                    'o.created_at',
                    'i.product_name',
                    'i.quantity',
                    'i.total_price as earning'
                ]);

            return DataTables::of($data)
                ->editColumn('created_at', function ($row) {
                    return date('d M Y, h:i A', strtotime($row->created_at));
                })
                ->editColumn('earning', function ($row) {
                    return '₹' . number_format($row->earning, 2);
                })
                ->editColumn('order_status', function ($row) {
                    $badges = [
                        'delivered' => 'bg-success',
                        'cancelled' => 'bg-danger',
                        'returned' => 'bg-secondary'
                    ];
                    $badge = $badges[$row->order_status] ?? 'bg-warning text-dark';
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->order_status)) . '</span>';
                })
                ->rawColumns(['order_status'])
                ->make(true);
        }
    }
}
