<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShopCustomerModel;

class ShopCustomerManageController extends Controller
{
    public function index()
    {
        $page_title = 'Customers';
        $page_name  = 'Customer Management';
        return view('shop.customer.index', compact('page_title', 'page_name'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('shop_customers')
            ->select([
                'customer_id',
                'customer_code',
                'name',
                'email',
                'phone',
                'profile_image',
                'city',
                'state',
                'is_email_verified',
                'status',
                'created_at'
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('customer_info', function ($row) {
                $img = $row->profile_image
                    ? asset('storage/' . $row->profile_image)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($row->name) . '&background=0d6efd&color=fff';

                return '<div class="d-flex align-items-center gap-2">
                            <img src="' . $img . '" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;">
                            <div>
                                <a href="' . route('shop.customer.show', $row->customer_id) . '" class="fw-semibold text-primary text-decoration-none">' . e($row->name) . '</a>
                                <div class="text-muted" style="font-size:0.75rem;">' . e($row->customer_code ?? 'CUST-' . str_pad($row->customer_id, 4, '0', STR_PAD_LEFT)) . '</div>
                            </div>
                        </div>';
            })
            ->addColumn('contact_info', function ($row) {
                return '<div><i class="bx bx-envelope text-muted me-1"></i>' . e($row->email) . '</div>' .
                       '<div><i class="bx bx-phone text-muted me-1"></i>' . e($row->phone ?? 'N/A') . '</div>';
            })
            ->addColumn('location', function ($row) {
                $city  = $row->city ?? '';
                $state = $row->state ?? '';
                $loc   = trim($city . ($city && $state ? ', ' : '') . $state);
                return $loc ?: '<span class="text-muted">Not specified</span>';
            })
            ->addColumn('verified_badge', function ($row) {
                return $row->is_email_verified == 1
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bx bx-check-shield me-1"></i>Verified</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bx bx-time me-1"></i>Unverified</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->status == 0
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>'
                    : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Blocked</span>';
            })
            ->addColumn('orders_count', function ($row) {
                $count = DB::table('tbl_shop_orders')
                    ->where('customer_id', $row->customer_id)
                    ->where('status', 0)
                    ->count();
                return '<span class="badge bg-light text-dark border">' . $count . ' Orders</span>';
            })
            ->addColumn('actions', function ($row) {
                $toggleStatusBtn = $row->status == 0
                    ? '<button class="btn btn-sm btn-outline-danger toggle-status-btn" data-id="' . $row->customer_id . '" data-status="1" title="Block Customer"><i class="bx bx-block"></i></button>'
                    : '<button class="btn btn-sm btn-outline-success toggle-status-btn" data-id="' . $row->customer_id . '" data-status="0" title="Unblock Customer"><i class="bx bx-check"></i></button>';

                return '<div class="d-flex gap-1">
                            <a href="' . route('shop.customer.show', $row->customer_id) . '" class="btn btn-sm btn-outline-primary" title="View Profile & Orders">
                                <i class="bx bx-show"></i>
                            </a>
                            ' . $toggleStatusBtn . '
                        </div>';
            })
            ->rawColumns(['customer_info', 'contact_info', 'location', 'verified_badge', 'status_badge', 'orders_count', 'actions'])
            ->make(true);
    }

    public function show($id)
    {
        $customer = ShopCustomerModel::find($id);
        if (!$customer) {
            return redirect()->route('shop.customer.index')->with('error', 'Customer not found.');
        }

        $orders = DB::table('tbl_shop_orders')
            ->where('customer_id', $id)
            ->where('status', 0)
            ->orderByDesc('order_id')
            ->get();

        $totalSpent = DB::table('tbl_shop_orders')
            ->where('customer_id', $id)
            ->where('status', 0)
            ->whereNotIn('order_status', ['cancelled'])
            ->sum('total_amount');

        $reviews = DB::table('tbl_shop_reviews')
            ->join('tbl_shop_products', 'tbl_shop_products.product_id', '=', 'tbl_shop_reviews.product_id')
            ->where('tbl_shop_reviews.customer_id', $id)
            ->where('tbl_shop_reviews.status', 0)
            ->select('tbl_shop_reviews.*', 'tbl_shop_products.product_name')
            ->orderByDesc('review_id')
            ->get();

        $page_title = 'Customer Profile';
        $page_name  = $customer->name;

        return view('shop.customer.show', compact('page_title', 'page_name', 'customer', 'orders', 'totalSpent', 'reviews'));
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required',
            'status' => 'required|in:0,1',
        ]);

        DB::table('shop_customers')
            ->where('customer_id', $request->id)
            ->update([
                'status'     => (int)$request->status,
                'updated_at' => now(),
            ]);

        $action = $request->status == 1 ? 'blocked' : 'unblocked';
        return response()->json(['status' => 'success', 'message' => "Customer {$action} successfully!"]);
    }
}
