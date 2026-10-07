<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ShopDeliveryStaffController extends Controller
{
    public function myOrders()
    {
        return view('shop.delivery.orders.index');
    }

    public function getList(Request $request)
    {
        $staffId = session('delivery_staff_id');

        if ($request->ajax()) {
            $data = DB::table('tbl_shop_delivery_assignments as da')
                ->join('tbl_shop_orders as o', 'da.order_id', '=', 'o.order_id')
                ->join('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
                ->where('da.delivery_staff_id', $staffId)
                ->whereIn('da.delivery_status', ['assigned', 'picked', 'out_for_delivery'])
                ->select([
                    'da.assignment_id',
                    'o.order_id',
                    'o.order_number',
                    'c.name as customer_name',
                    'o.shipping_address',
                    'o.payment_method',
                    'o.payment_status',
                    'o.total_amount',
                    'da.delivery_status',
                    'da.assigned_at',
                    'o.order_status' // For checking if cancelled mid-way
                ]);

            return DataTables::of($data)
                ->editColumn('assigned_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->assigned_at));
                })
                ->editColumn('total_amount', function ($row) {
                    return '₹' . number_format($row->total_amount, 2);
                })
                ->editColumn('payment_status', function ($row) {
                    $color = $row->payment_status == 'paid' ? 'success' : 'warning';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->payment_method) . ' (' . ucfirst($row->payment_status) . ')</span>';
                })
                ->editColumn('delivery_status', function ($row) {
                    if ($row->order_status == 'cancelled') return '<span class="badge bg-danger">Cancelled by User</span>';
                    $badges = [
                        'assigned' => 'bg-secondary',
                        'picked' => 'bg-info',
                        'out_for_delivery' => 'bg-primary'
                    ];
                    $badge = $badges[$row->delivery_status] ?? 'bg-dark';
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->delivery_status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('delivery.order.show', $row->order_id) . '" class="btn btn-sm btn-primary">Process</a>';
                })
                ->rawColumns(['payment_status', 'delivery_status', 'action'])
                ->make(true);
        }
    }

    public function showOrder($id)
    {
        $staffId = session('delivery_staff_id');

        $assignment = DB::table('tbl_shop_delivery_assignments as da')
            ->where('da.order_id', $id)
            ->where('da.delivery_staff_id', $staffId)
            ->first();

        if (!$assignment) abort(403, 'Unauthorized. Order not assigned to you.');

        $order = DB::table('tbl_shop_orders as o')
            ->join('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
            ->where('o.order_id', $id)
            ->select('o.*', 'c.name as customer_name', 'c.email', 'c.phone as customer_phone')
            ->first();

        $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();

        return view('shop.delivery.orders.show', compact('order', 'assignment', 'items'));
    }

    public function updateStatus(Request $request)
    {
        $staffId = session('delivery_staff_id');
        $id = $request->order_id;

        $assignment = DB::table('tbl_shop_delivery_assignments')
            ->where('order_id', $id)
            ->where('delivery_staff_id', $staffId)
            ->first();

        if (!$assignment) return redirect()->back()->with('error', 'Unauthorized');

        DB::beginTransaction();
        try {
            DB::table('tbl_shop_delivery_assignments')
                ->where('assignment_id', $assignment->assignment_id)
                ->update([
                    'delivery_status' => $request->status,
                    'updated_at' => now()
                ]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $id,
                'status' => $request->status,
                'description' => 'Delivery staff marked as: ' . str_replace('_', ' ', $request->status),
                'created_by' => $staffId,
                'created_by_type' => 'delivery',
                'created_at' => now()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Delivery status updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update status');
        }
    }

    public function verifyOtp(Request $request)
    {
        $staffId = session('delivery_staff_id');
        $id = $request->order_id;
        $otp = $request->delivery_otp;

        $assignment = DB::table('tbl_shop_delivery_assignments')
            ->where('order_id', $id)
            ->where('delivery_staff_id', $staffId)
            ->first();

        if (!$assignment) return redirect()->back()->with('error', 'Unauthorized');

        if ($assignment->delivery_otp != $otp) {
            return redirect()->back()->with('error', 'Invalid OTP! Delivery failed.');
        }

        DB::beginTransaction();
        try {
            DB::table('tbl_shop_delivery_assignments')
                ->where('assignment_id', $assignment->assignment_id)
                ->update([
                    'delivery_status' => 'delivered',
                    'otp_verified_at' => now(),
                    'delivered_at' => now(),
                    'updated_at' => now()
                ]);

            DB::table('tbl_shop_orders')->where('order_id', $id)->update([
                'order_status' => 'delivered',
                'payment_status' => 'paid', // Cash collected
                'delivered_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('tbl_shop_order_items')->where('order_id', $id)->update([
                'item_status' => 'delivered'
            ]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $id,
                'status' => 'delivered',
                'description' => 'Package delivered successfully. OTP Verified.',
                'created_by' => $staffId,
                'created_by_type' => 'delivery',
                'created_at' => now()
            ]);

            DB::commit();
            return redirect()->route('delivery.order.index')->with('success', 'Order Delivered Successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete delivery');
        }
    }

    public function history()
    {
        return view('shop.delivery.history.index');
    }

    public function getHistoryList(Request $request)
    {
        $staffId = session('delivery_staff_id');

        if ($request->ajax()) {
            $data = DB::table('tbl_shop_delivery_assignments as da')
                ->join('tbl_shop_orders as o', 'da.order_id', '=', 'o.order_id')
                ->join('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
                ->where('da.delivery_staff_id', $staffId)
                ->whereIn('da.delivery_status', ['delivered', 'failed'])
                ->select([
                    'da.assignment_id',
                    'o.order_id',
                    'o.order_number',
                    'c.name as customer_name',
                    'o.total_amount',
                    'da.delivery_status',
                    'da.delivered_at'
                ]);

            return DataTables::of($data)
                ->editColumn('delivered_at', function ($row) {
                    return $row->delivered_at ? date('d M Y h:i A', strtotime($row->delivered_at)) : 'N/A';
                })
                ->editColumn('total_amount', function ($row) {
                    return '₹' . number_format($row->total_amount, 2);
                })
                ->editColumn('delivery_status', function ($row) {
                    $badge = $row->delivery_status == 'delivered' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' . $badge . '">' . ucfirst($row->delivery_status) . '</span>';
                })
                ->rawColumns(['delivery_status'])
                ->make(true);
        }
    }
}
