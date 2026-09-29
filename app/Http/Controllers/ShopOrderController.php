<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ShopOrderController extends Controller
{
    public function index()
    {
        return view('shop.orders.index');
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tbl_shop_orders as o')
                ->join('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
                ->select([
                    'o.order_id',
                    'o.order_number',
                    'c.name as customer_name',
                    'o.total_amount',
                    'o.payment_method',
                    'o.payment_status',
                    'o.order_status',
                    'o.created_at'
                ]);

            return DataTables::of($data)
                ->editColumn('created_at', function ($row) {
                    return date('d-M-Y h:i A', strtotime($row->created_at));
                })
                ->editColumn('total_amount', function ($row) {
                    return '₹' . number_format($row->total_amount, 2);
                })
                ->editColumn('order_status', function ($row) {
                    $badges = [
                        'pending' => 'bg-warning',
                        'confirmed' => 'bg-info',
                        'processing' => 'bg-primary',
                        'ready_to_ship' => 'bg-secondary',
                        'shipped' => 'bg-dark',
                        'out_for_delivery' => 'bg-info text-dark',
                        'delivered' => 'bg-success',
                        'cancelled' => 'bg-danger',
                        'return_requested' => 'bg-warning text-dark',
                        'returned' => 'bg-secondary'
                    ];
                    $badge = $badges[$row->order_status] ?? 'bg-secondary';
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->order_status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('shop.order.show', $row->order_id) . '" class="btn btn-sm btn-primary"><i class="bx bx-show"></i> View</a>';
                    return $btn;
                })
                ->rawColumns(['order_status', 'action'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $order = DB::table('tbl_shop_orders as o')
            ->join('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
            ->where('o.order_id', $id)
            ->select('o.*', 'c.name as customer_name', 'c.email', 'c.phone as customer_phone')
            ->first();

        if (!$order) abort(404);

        $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();
        $tracking = DB::table('tbl_shop_order_tracking')->where('order_id', $id)->orderBy('created_at', 'desc')->get();
        $deliveryStaff = DB::table('shop_delivery_staff')->where('status', 0)->get();

        return view('shop.orders.show', compact('order', 'items', 'tracking', 'deliveryStaff'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'order_status' => 'required'
        ]);

        $order = DB::table('tbl_shop_orders')->where('order_id', $request->order_id)->first();
        if (!$order) return response()->json(['status' => 'error', 'message' => 'Order not found']);

        DB::beginTransaction();
        try {
            $updateData = ['order_status' => $request->order_status, 'updated_at' => now(), 'updated_by' => session('id')];
            
            if ($request->order_status == 'delivered') {
                $updateData['delivered_at'] = now();
            } elseif ($request->order_status == 'cancelled') {
                $updateData['cancelled_at'] = now();
                
                // Reverse Inventory
                $items = DB::table('tbl_shop_order_items')->where('order_id', $order->order_id)->get();
                foreach($items as $item) {
                    $product = DB::table('tbl_shop_products')->where('product_id', $item->product_id)->first();
                    $newStock = $product->stock_qty + $item->quantity;
                    DB::table('tbl_shop_products')->where('product_id', $item->product_id)->update(['stock_qty' => $newStock]);
                    
                    DB::table('tbl_shop_inventory')->insert([
                        'product_id' => $item->product_id,
                        'transaction_type' => 'adjustment',
                        'quantity' => $item->quantity,
                        'before_stock' => $product->stock_qty,
                        'after_stock' => $newStock,
                        'reference_type' => 'Order Cancelled',
                        'reference_id' => $order->order_id,
                        'reference_number' => $order->order_number,
                        'created_at' => now()
                    ]);
                }
            }

            DB::table('tbl_shop_orders')->where('order_id', $request->order_id)->update($updateData);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $request->order_id,
                'status' => $request->order_status,
                'description' => 'Order status updated to ' . str_replace('_', ' ', $request->order_status),
                'created_by' => session('id'),
                'created_by_type' => 'admin',
                'created_at' => now()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Order status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update order status');
        }
    }

    public function generateInvoice($id)
    {
        // Simple invoice print view (Module 21)
        $order = DB::table('tbl_shop_orders as o')
            ->join('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
            ->where('o.order_id', $id)
            ->select('o.*', 'c.name as customer_name', 'c.email', 'c.phone as customer_phone')
            ->first();

        if (!$order) abort(404);

        $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();

        return view('shop.orders.invoice', compact('order', 'items'));
    }

    public function assignDelivery(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'delivery_staff_id' => 'required'
        ]);

        DB::beginTransaction();
        try {
            DB::table('tbl_shop_delivery_assignments')->insert([
                'order_id' => $request->order_id,
                'delivery_staff_id' => $request->delivery_staff_id,
                'assigned_by' => session('id'),
                'delivery_otp' => rand(1000, 9999),
                'created_at' => now()
            ]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $request->order_id,
                'status' => 'out_for_delivery',
                'description' => 'Order assigned to delivery partner',
                'created_by' => session('id'),
                'created_by_type' => 'admin',
                'created_at' => now()
            ]);

            DB::table('tbl_shop_orders')->where('order_id', $request->order_id)->update(['order_status' => 'out_for_delivery', 'updated_at' => now()]);

            DB::commit();
            return redirect()->back()->with('success', 'Delivery staff assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign delivery staff');
        }
    }
}
