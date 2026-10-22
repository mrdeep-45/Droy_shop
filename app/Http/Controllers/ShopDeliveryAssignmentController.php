<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShopDeliveryAssignmentModel;

class ShopDeliveryAssignmentController extends Controller
{
    public function index()
    {
        $page_title = 'Delivery Assignments';
        $page_name  = 'Order Delivery Assignments';

        $deliveryStaff = DB::table('shop_delivery_staff')
            ->where('status', 0)
            ->where('is_available', 1)
            ->get();

        return view('shop.delivery.assignments.index', compact('page_title', 'page_name', 'deliveryStaff'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('tbl_shop_delivery_assignments')
            ->join('tbl_shop_orders', 'tbl_shop_orders.order_id', '=', 'tbl_shop_delivery_assignments.order_id')
            ->join('shop_delivery_staff', 'shop_delivery_staff.delivery_staff_id', '=', 'tbl_shop_delivery_assignments.delivery_staff_id')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_orders.customer_id')
            ->select(
                'tbl_shop_delivery_assignments.*',
                'tbl_shop_orders.order_number',
                'tbl_shop_orders.total_amount',
                'tbl_shop_orders.payment_method',
                'shop_delivery_staff.name as staff_name',
                'shop_delivery_staff.phone as staff_phone',
                'shop_customers.name as customer_name'
            );

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('order_info', function ($row) {
                return '<a href="' . route('shop.order.show', $row->order_id) . '" class="fw-bold text-primary">' . e($row->order_number) . '</a>
                        <div class="text-muted" style="font-size:0.75rem;">' . formatShopPrice($row->total_amount) . ' (' . strtoupper($row->payment_method) . ')</div>';
            })
            ->addColumn('customer', function ($row) {
                return '<div class="fw-semibold">' . e($row->customer_name) . '</div>';
            })
            ->addColumn('staff', function ($row) {
                return '<div class="fw-semibold"><i class="bx bx-cycling text-primary me-1"></i>' . e($row->staff_name) . '</div>
                        <small class="text-muted">' . e($row->staff_phone) . '</small>';
            })
            ->addColumn('otp_code', function ($row) {
                return '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 font-monospace" style="letter-spacing:2px;font-size:0.85rem;">' . e($row->delivery_otp ?? '----') . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return match ($row->delivery_status) {
                    'delivered'        => '<span class="badge bg-success">Delivered</span>',
                    'out_for_delivery' => '<span class="badge bg-warning text-dark">Out for Delivery</span>',
                    'picked'           => '<span class="badge bg-info">Picked</span>',
                    'failed'           => '<span class="badge bg-danger">Failed</span>',
                    default            => '<span class="badge bg-secondary">Assigned</span>',
                };
            })
            ->addColumn('assigned_date', function ($row) {
                return '<div style="font-size:0.8rem;">' . \Carbon\Carbon::parse($row->assigned_at)->format('d M Y, h:i A') . '</div>';
            })
            ->rawColumns(['order_info', 'customer', 'staff', 'otp_code', 'status_badge', 'assigned_date'])
            ->make(true);
    }
}
