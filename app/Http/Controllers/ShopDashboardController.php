<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ShopDashboardController
 * Serves role-specific dashboard data for the shop admin panel.
 */
class ShopDashboardController extends Controller
{
    public function index()
    {
        $loginType  = session('shop_login_type');
        $page_title = 'Dashboard';
        $page_name  = 'Shop Dashboard';

        // ── Gather KPI data based on login role ──────────────────────
        $kpi = $this->getKpiData($loginType);

        return view('shop.dashboard', compact('page_title', 'page_name', 'kpi', 'loginType'));
    }

    /**
     * AJAX endpoint to refresh dashboard KPI cards (for live updates)
     */
    public function getDashboardData(Request $request)
    {
        try {
            $loginType = session('shop_login_type');
            $kpi       = $this->getKpiData($loginType);

            return response()->json([
                'status' => 'success',
                'data'   => $kpi,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ─── KPI DATA BUILDER ────────────────────────────────────────────

    private function getKpiData(string $loginType): array
    {
        return match ($loginType) {
            'ShopSuperAdmin', 'ShopAdmin' => $this->getAdminKpi(),
            'Vendor'                      => $this->getVendorKpi(),
            'DeliveryStaff'               => $this->getDeliveryKpi(),
            default                       => [],
        };
    }

    private function getAdminKpi(): array
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Orders
        $totalOrders   = DB::table('tbl_shop_orders')->where('status', 0)->count();
        $todayOrders   = DB::table('tbl_shop_orders')->where('status', 0)->whereDate('created_at', $today)->count();
        $pendingOrders = DB::table('tbl_shop_orders')->where('status', 0)->where('order_status', 'pending')->count();
        $deliveredOrders = DB::table('tbl_shop_orders')->where('status', 0)->where('order_status', 'delivered')->count();
        $cancelledOrders = DB::table('tbl_shop_orders')->where('status', 0)->where('order_status', 'cancelled')->count();

        // Revenue
        $totalRevenue    = DB::table('tbl_shop_orders')->where('status', 0)->where('payment_status', 'paid')->sum('total_amount') ?? 0;
        $monthRevenue    = DB::table('tbl_shop_orders')->where('status', 0)->where('payment_status', 'paid')->where('created_at', '>=', $thisMonth)->sum('total_amount') ?? 0;
        $todayRevenue    = DB::table('tbl_shop_orders')->where('status', 0)->where('payment_status', 'paid')->whereDate('created_at', $today)->sum('total_amount') ?? 0;

        // Customers
        $totalCustomers  = DB::table('shop_customers')->where('status', 0)->count();
        $newCustomers    = DB::table('shop_customers')->where('status', 0)->where('created_at', '>=', $thisMonth)->count();

        // Products
        $totalProducts   = DB::table('tbl_shop_products')->where('status', 0)->where('is_active', 1)->count();
        $lowStockProducts= DB::table('tbl_shop_products')->where('status', 0)->whereRaw('stock_qty <= low_stock_threshold')->count();
        $outOfStock      = DB::table('tbl_shop_products')->where('status', 0)->where('stock_qty', 0)->count();

        // Vendors
        $totalVendors    = DB::table('shop_vendors')->where('status', 0)->count();
        $pendingVendors  = DB::table('shop_vendors')->where('status', 0)->where('is_approved', 0)->count();

        // Returns
        $pendingReturns  = DB::table('tbl_shop_returns')->where('status', 0)->where('return_status', 'requested')->count();

        // Reviews pending approval
        $pendingReviews  = DB::table('tbl_shop_reviews')->where('status', 0)->where('is_approved', 0)->count();

        // Recent Orders (last 10)
        $recentOrders    = DB::table('tbl_shop_orders')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_orders.customer_id')
            ->select('tbl_shop_orders.*', 'shop_customers.name as customer_name')
            ->where('tbl_shop_orders.status', 0)
            ->orderBy('tbl_shop_orders.order_id', 'desc')
            ->limit(10)
            ->get();

        // Monthly revenue chart data (last 6 months)
        $chartData       = $this->getMonthlyRevenueChart();

        // Top selling products (last 30 days)
        $topProducts     = DB::table('tbl_shop_order_items')
            ->join('tbl_shop_products', 'tbl_shop_products.product_id', '=', 'tbl_shop_order_items.product_id')
            ->select('tbl_shop_order_items.product_name', DB::raw('SUM(tbl_shop_order_items.quantity) as total_sold'), DB::raw('SUM(tbl_shop_order_items.total_price) as total_revenue'))
            ->where('tbl_shop_order_items.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('tbl_shop_order_items.product_id', 'tbl_shop_order_items.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return compact(
            'totalOrders', 'todayOrders', 'pendingOrders', 'deliveredOrders', 'cancelledOrders',
            'totalRevenue', 'monthRevenue', 'todayRevenue',
            'totalCustomers', 'newCustomers',
            'totalProducts', 'lowStockProducts', 'outOfStock',
            'totalVendors', 'pendingVendors',
            'pendingReturns', 'pendingReviews',
            'recentOrders', 'chartData', 'topProducts'
        );
    }

    private function getVendorKpi(): array
    {
        $vendorId  = session('vendor_id');
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $totalOrders   = DB::table('tbl_shop_orders')->where('status', 0)->whereExists(function ($q) use ($vendorId) {
            $q->from('tbl_shop_order_items')->whereColumn('tbl_shop_order_items.order_id', 'tbl_shop_orders.order_id')->where('vendor_id', $vendorId);
        })->count();

        $pendingOrders = DB::table('tbl_shop_orders')->where('status', 0)->where('order_status', 'pending')->whereExists(function ($q) use ($vendorId) {
            $q->from('tbl_shop_order_items')->whereColumn('tbl_shop_order_items.order_id', 'tbl_shop_orders.order_id')->where('vendor_id', $vendorId);
        })->count();

        $totalRevenue  = DB::table('tbl_shop_vendor_earnings')->where('vendor_id', $vendorId)->sum('net_earning') ?? 0;
        $monthRevenue  = DB::table('tbl_shop_vendor_earnings')->where('vendor_id', $vendorId)->where('created_at', '>=', $thisMonth)->sum('net_earning') ?? 0;
        $pendingPayout = DB::table('tbl_shop_vendor_earnings')->where('vendor_id', $vendorId)->where('earning_status', 'pending')->sum('net_earning') ?? 0;

        $totalProducts = DB::table('tbl_shop_products')->where('vendor_id', $vendorId)->where('status', 0)->count();
        $activeProducts= DB::table('tbl_shop_products')->where('vendor_id', $vendorId)->where('status', 0)->where('is_active', 1)->count();

        $recentOrders  = DB::table('tbl_shop_orders')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_orders.customer_id')
            ->whereExists(function ($q) use ($vendorId) {
                $q->from('tbl_shop_order_items')->whereColumn('tbl_shop_order_items.order_id', 'tbl_shop_orders.order_id')->where('vendor_id', $vendorId);
            })
            ->select('tbl_shop_orders.*', 'shop_customers.name as customer_name')
            ->where('tbl_shop_orders.status', 0)
            ->orderByDesc('tbl_shop_orders.order_id')
            ->limit(8)
            ->get();

        return compact('totalOrders', 'pendingOrders', 'totalRevenue', 'monthRevenue', 'pendingPayout', 'totalProducts', 'activeProducts', 'recentOrders');
    }

    private function getDeliveryKpi(): array
    {
        $staffId  = session('delivery_staff_id');
        $today    = Carbon::today();

        $assignedOrders   = DB::table('tbl_shop_delivery_assignments')->where('delivery_staff_id', $staffId)->whereIn('delivery_status', ['assigned', 'picked', 'out_for_delivery'])->count();
        $deliveredToday   = DB::table('tbl_shop_delivery_assignments')->where('delivery_staff_id', $staffId)->where('delivery_status', 'delivered')->whereDate('delivered_at', $today)->count();
        $totalDelivered   = DB::table('tbl_shop_delivery_assignments')->where('delivery_staff_id', $staffId)->where('delivery_status', 'delivered')->count();
        $failedDeliveries = DB::table('tbl_shop_delivery_assignments')->where('delivery_staff_id', $staffId)->where('delivery_status', 'failed')->whereDate('created_at', $today)->count();

        $pendingOrders = DB::table('tbl_shop_delivery_assignments')
            ->join('tbl_shop_orders', 'tbl_shop_orders.order_id', '=', 'tbl_shop_delivery_assignments.order_id')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_orders.customer_id')
            ->where('tbl_shop_delivery_assignments.delivery_staff_id', $staffId)
            ->whereIn('tbl_shop_delivery_assignments.delivery_status', ['assigned', 'picked', 'out_for_delivery'])
            ->select('tbl_shop_orders.*', 'shop_customers.name as customer_name', 'tbl_shop_delivery_assignments.delivery_status as d_status', 'tbl_shop_delivery_assignments.assignment_id')
            ->orderByDesc('tbl_shop_delivery_assignments.assignment_id')
            ->get();

        return compact('assignedOrders', 'deliveredToday', 'totalDelivered', 'failedDeliveries', 'pendingOrders');
    }

    private function getMonthlyRevenueChart(): array
    {
        $months  = [];
        $revenues= [];

        for ($i = 5; $i >= 0; $i--) {
            $date    = Carbon::now()->subMonths($i);
            $months[]= $date->format('M Y');
            $revenues[] = (float) DB::table('tbl_shop_orders')
                ->where('status', 0)
                ->where('payment_status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
        }

        return ['labels' => $months, 'data' => $revenues];
    }

    /**
     * Global quick search for header search bar (Products, Orders, Customers)
     */
    public function globalSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // 1. Search Products
        $products = DB::table('tbl_shop_products as p')
            ->leftJoin('tbl_shop_product_images as pi', function($join) {
                $join->on('p.product_id', '=', 'pi.product_id')
                     ->where('pi.is_primary', '=', 1);
            })
            ->where('p.status', 0)
            ->where(function($query) use ($q) {
                $query->where('p.product_name', 'like', "%{$q}%")
                      ->orWhere('p.product_code', 'like', "%{$q}%")
                      ->orWhere('p.sku', 'like', "%{$q}%");
            })
            ->select('p.product_id', 'p.product_name', 'p.product_code', 'p.selling_price', 'pi.image_path')
            ->limit(5)
            ->get();

        foreach ($products as $prod) {
            $results[] = [
                'type'     => 'Product',
                'icon'     => 'bx-package',
                'title'    => $prod->product_name,
                'subtitle' => $prod->product_code . ' • ₹' . number_format($prod->selling_price, 2),
                'url'      => route('shop.product.show', $prod->product_id),
                'image'    => function_exists('getShopImageUrl') ? getShopImageUrl($prod->image_path) : null,
            ];
        }

        // 2. Search Orders
        $orders = DB::table('tbl_shop_orders as o')
            ->leftJoin('shop_customers as c', 'o.customer_id', '=', 'c.customer_id')
            ->where(function($query) use ($q) {
                $query->where('o.order_number', 'like', "%{$q}%")
                      ->orWhere('c.name', 'like', "%{$q}%")
                      ->orWhere('c.phone', 'like', "%{$q}%");
            })
            ->select('o.order_id', 'o.order_number', 'o.total_amount', 'o.order_status', 'c.name as customer_name')
            ->limit(5)
            ->get();

        foreach ($orders as $ord) {
            $results[] = [
                'type'     => 'Order',
                'icon'     => 'bx-receipt',
                'title'    => 'Order #' . $ord->order_number,
                'subtitle' => ($ord->customer_name ? $ord->customer_name . ' • ' : '') . '₹' . number_format($ord->total_amount, 2) . ' (' . ucfirst($ord->order_status) . ')',
                'url'      => route('shop.order.show', $ord->order_id),
                'image'    => null,
            ];
        }

        // 3. Search Customers
        $customers = DB::table('shop_customers')
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->select('customer_id', 'name', 'email', 'phone')
            ->limit(5)
            ->get();

        foreach ($customers as $cust) {
            $results[] = [
                'type'     => 'Customer',
                'icon'     => 'bx-user',
                'title'    => $cust->name,
                'subtitle' => $cust->email . ($cust->phone ? ' • ' . $cust->phone : ''),
                'url'      => route('shop.customer.show', $cust->customer_id),
                'image'    => null,
            ];
        }

        return response()->json(['results' => $results]);
    }
}
