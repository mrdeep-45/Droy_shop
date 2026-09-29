<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ShopReturnController extends Controller
{
    public function index()
    {
        return view('shop.returns.index');
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tbl_shop_orders as o')
                ->join('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
                ->whereIn('o.order_status', ['return_requested', 'returned'])
                ->select([
                    'o.order_id',
                    'o.order_number',
                    'c.name as customer_name',
                    'o.total_amount',
                    'o.order_status',
                    'o.updated_at' // usually when return requested
                ]);

            return DataTables::of($data)
                ->editColumn('updated_at', function ($row) {
                    return date('d-M-Y h:i A', strtotime($row->updated_at));
                })
                ->editColumn('total_amount', function ($row) {
                    return '₹' . number_format($row->total_amount, 2);
                })
                ->editColumn('order_status', function ($row) {
                    $badge = $row->order_status == 'returned' ? 'bg-secondary' : 'bg-warning text-dark';
                    return '<span class="badge ' . $badge . '">' . ucfirst(str_replace('_', ' ', $row->order_status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('shop.return.show', $row->order_id) . '" class="btn btn-sm btn-primary"><i class="bx bx-show"></i> Review</a>';
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
            ->whereIn('o.order_status', ['return_requested', 'returned'])
            ->select('o.*', 'c.name as customer_name', 'c.email', 'c.phone as customer_phone')
            ->first();

        if (!$order) abort(404);

        $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();
        $tracking = DB::table('tbl_shop_order_tracking')->where('order_id', $id)->orderBy('created_at', 'desc')->get();

        return view('shop.returns.show', compact('order', 'items', 'tracking'));
    }

    public function approve(Request $request)
    {
        $request->validate(['order_id' => 'required']);
        $id = $request->order_id;
        
        DB::beginTransaction();
        try {
            $order = DB::table('tbl_shop_orders')->where('order_id', $id)->first();
            
            DB::table('tbl_shop_orders')->where('order_id', $id)->update([
                'order_status' => 'returned',
                'payment_status' => 'refunded',
                'updated_at' => now(),
                'updated_by' => session('id')
            ]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $id,
                'status' => 'returned',
                'description' => 'Return Request Approved and Processed. Amount refunded: ₹' . $order->total_amount,
                'created_by' => session('id'),
                'created_by_type' => 'admin',
                'created_at' => now()
            ]);

            // Reverse Inventory since it's returned
            $items = DB::table('tbl_shop_order_items')->where('order_id', $id)->get();
            foreach($items as $item) {
                $product = DB::table('tbl_shop_products')->where('product_id', $item->product_id)->first();
                $newStock = $product->stock_qty + $item->quantity;
                DB::table('tbl_shop_products')->where('product_id', $item->product_id)->update(['stock_qty' => $newStock]);
                
                DB::table('tbl_shop_inventory')->insert([
                    'product_id' => $item->product_id,
                    'transaction_type' => 'return',
                    'quantity' => $item->quantity,
                    'before_stock' => $product->stock_qty,
                    'after_stock' => $newStock,
                    'reference_type' => 'Order Returned',
                    'reference_id' => $id,
                    'reference_number' => $order->order_number,
                    'created_at' => now()
                ]);
            }

            DB::commit();
            return redirect()->route('shop.return.index')->with('success', 'Return approved and stock refunded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve return');
        }
    }

    public function reject(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'reason' => 'required'
        ]);
        
        $id = $request->order_id;
        
        DB::beginTransaction();
        try {
            DB::table('tbl_shop_orders')->where('order_id', $id)->update([
                'order_status' => 'delivered', // Revert back to delivered
                'updated_at' => now(),
                'updated_by' => session('id')
            ]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id' => $id,
                'status' => 'delivered',
                'description' => 'Return Request Rejected. Reason: ' . $request->reason,
                'created_by' => session('id'),
                'created_by_type' => 'admin',
                'created_at' => now()
            ]);

            DB::commit();
            return redirect()->route('shop.return.index')->with('success', 'Return request rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject return');
        }
    }
}
