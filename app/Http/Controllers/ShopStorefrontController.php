<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopStorefrontController extends Controller
{
    // Customer Dashboard / Orders
    public function orders()
    {
        $customerId = session('customer_id');
        $orders = DB::table('tbl_shop_orders')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('store.account.orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $customerId = session('customer_id');
        $order = DB::table('tbl_shop_orders')
            ->where('order_id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$order) abort(404);

        $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();
        $tracking = DB::table('tbl_shop_order_tracking')->where('order_id', $id)->orderBy('created_at', 'desc')->get();

        return view('store.account.order_detail', compact('order', 'items', 'tracking'));
    }

    public function cancelOrder(Request $request, $id)
    {
        $customerId = session('customer_id');
        $order = DB::table('tbl_shop_orders')
            ->where('order_id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$order) return response()->json(['status' => 'error', 'message' => 'Order not found']);

        // Only allow cancellation if pending or confirmed
        if (!in_array($order->order_status, ['pending', 'confirmed'])) {
            return response()->json(['status' => 'error', 'message' => 'Order cannot be cancelled at this stage']);
        }

        DB::beginTransaction();
        try {
            DB::table('tbl_shop_orders')->where('order_id', $id)->update([
                'order_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason ?? 'Cancelled by customer',
                'updated_at' => now()
            ]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $id,
                'status' => 'cancelled',
                'description' => 'Order was cancelled by customer.',
                'created_by' => $customerId,
                'created_by_type' => 'customer',
                'created_at' => now()
            ]);

            // Reverse Inventory
            $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();
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
                    'reference_type' => 'Order Cancelled (Customer)',
                    'reference_id' => $id,
                    'reference_number' => $order->order_number,
                    'created_at' => now()
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Cancellation failed');
        }
    }

    public function requestReturn(Request $request, $id)
    {
        $customerId = session('customer_id');
        $order = DB::table('tbl_shop_orders')
            ->where('order_id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$order) return response()->json(['status' => 'error', 'message' => 'Order not found']);

        // Only allow return if delivered
        if ($order->order_status != 'delivered') {
            return response()->json(['status' => 'error', 'message' => 'Only delivered orders can be returned']);
        }

        DB::beginTransaction();
        try {
            DB::table('tbl_shop_orders')->where('order_id', $id)->update([
                'order_status' => 'return_requested',
                'updated_at' => now()
            ]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $id,
                'status' => 'return_requested',
                'description' => 'Return requested: ' . ($request->reason ?? 'No reason provided'),
                'created_by' => $customerId,
                'created_by_type' => 'customer',
                'created_at' => now()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Return request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit return request');
        }
    }

    public function downloadInvoice($id)
    {
        $customerId = session('customer_id');
        $order = DB::table('tbl_shop_orders')
            ->join('shop_customers as c', 'tbl_shop_orders.customer_id', '=', 'c.customer_id')
            ->where('tbl_shop_orders.order_id', $id)
            ->where('tbl_shop_orders.customer_id', $customerId)
            ->select('tbl_shop_orders.*', 'c.name as customer_name', 'c.email', 'c.phone as customer_phone')
            ->first();

        if (!$order) abort(404);

        $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();

        return view('shop.orders.invoice', compact('order', 'items'));
    }
}
