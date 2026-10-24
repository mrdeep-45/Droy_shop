<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ShopReportController extends Controller
{
    // ═════════════════════════════════════════════════════════════════
    // ADMIN REPORTS (Module 30)
    // ═════════════════════════════════════════════════════════════════

    /**
     * Sales Report & Revenue Analytics
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // High Level Metrics
        $totalSales = DB::table('tbl_shop_orders')
            ->where('status', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('order_status', ['cancelled'])
            ->sum('total_amount');

        $totalOrders = DB::table('tbl_shop_orders')
            ->where('status', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedOrders = DB::table('tbl_shop_orders')
            ->where('status', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', 'delivered')
            ->count();

        $totalDiscount = DB::table('tbl_shop_orders')
            ->where('status', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('discount_amount');

        $avgOrderValue = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0;

        // Sales Trend Data for Chart
        $trendRecords = DB::table('tbl_shop_orders')
            ->where('status', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('order_status', ['cancelled'])
            ->selectRaw('DATE(created_at) as sale_date, SUM(total_amount) as daily_revenue, COUNT(order_id) as daily_orders')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'ASC')
            ->get();

        $chartLabels  = $trendRecords->pluck('sale_date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $chartRevenue = $trendRecords->pluck('daily_revenue')->toArray();
        $chartOrders  = $trendRecords->pluck('daily_orders')->toArray();

        // Payment Method Breakdown
        $paymentMethods = DB::table('tbl_shop_orders')
            ->where('status', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('order_status', ['cancelled'])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Top Selling Products in Period
        $topProducts = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'o.order_id', '=', 'i.order_id')
            ->where('o.status', 0)
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->whereNotIn('o.order_status', ['cancelled'])
            ->selectRaw('i.product_id, i.product_name, SUM(i.quantity) as total_qty, SUM(i.total_price) as total_revenue')
            ->groupBy('i.product_id', 'i.product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $page_title = 'Sales Report';
        $page_name  = 'Sales & Revenue Analytics';

        return view('shop.reports.sales', compact(
            'page_title', 'page_name', 'startDate', 'endDate',
            'totalSales', 'totalOrders', 'completedOrders', 'totalDiscount',
            'avgOrderValue', 'chartLabels', 'chartRevenue', 'chartOrders',
            'paymentMethods', 'topProducts'
        ));
    }

    /**
     * Inventory Valuation & Stock Movement Report
     */
    public function inventoryReport(Request $request)
    {
        $page_title = 'Inventory Report';
        $page_name  = 'Stock Levels & Valuation';

        $totalItems = DB::table('tbl_shop_products')->where('status', 0)->count();
        $totalUnits = DB::table('tbl_shop_products')->where('status', 0)->sum('stock_qty');
        
        $stockValuation = DB::table('tbl_shop_products')
            ->where('status', 0)
            ->selectRaw('SUM(stock_qty * IF(base_price > 0, base_price, selling_price)) as total_val')
            ->value('total_val') ?? 0;

        $lowStockCount = DB::table('tbl_shop_products')
            ->where('status', 0)
            ->whereColumn('stock_qty', '<=', 'low_stock_threshold')
            ->where('stock_qty', '>', 0)
            ->count();

        $outOfStockCount = DB::table('tbl_shop_products')
            ->where('status', 0)
            ->where('stock_qty', '<=', 0)
            ->count();

        $lowStockProducts = DB::table('tbl_shop_products')
            ->where('status', 0)
            ->where(function($q) {
                $q->whereColumn('stock_qty', '<=', 'low_stock_threshold')
                  ->orWhere('stock_qty', '<=', 0);
            })
            ->select('product_id', 'product_name', 'sku', 'stock_qty', 'low_stock_threshold', 'selling_price')
            ->orderBy('stock_qty', 'asc')
            ->limit(15)
            ->get();

        return view('shop.reports.inventory', compact(
            'page_title', 'page_name', 'totalItems', 'totalUnits',
            'stockValuation', 'lowStockCount', 'outOfStockCount', 'lowStockProducts'
        ));
    }

    /**
     * Multi-Vendor Performance Report
     */
    public function vendorReport(Request $request)
    {
        $page_title = 'Vendor Performance';
        $page_name  = 'Vendor Sales & Commission Report';

        $vendors = DB::table('shop_vendors as v')
            ->where('v.status', 0)
            ->leftJoin('tbl_shop_order_items as i', 'i.vendor_id', '=', 'v.vendor_id')
            ->leftJoin('tbl_shop_orders as o', function($join) {
                $join->on('o.order_id', '=', 'i.order_id')
                     ->where('o.status', 0)
                     ->whereNotIn('o.order_status', ['cancelled']);
            })
            ->select(
                'v.vendor_id',
                'v.business_name',
                'v.owner_name',
                'v.commission_percent',
                DB::raw('COUNT(DISTINCT i.product_id) as total_products'),
                DB::raw('COALESCE(SUM(i.quantity), 0) as items_sold'),
                DB::raw('COALESCE(SUM(i.total_price), 0) as gross_sales')
            )
            ->groupBy('v.vendor_id', 'v.business_name', 'v.owner_name', 'v.commission_percent')
            ->orderByDesc('gross_sales')
            ->get()
            ->map(function($v) {
                $commPercent = $v->commission_percent ?? 10;
                $v->platform_commission = ($v->gross_sales * $commPercent) / 100;
                $v->vendor_net = $v->gross_sales - $v->platform_commission;
                return $v;
            });

        $totalVendorSales = $vendors->sum('gross_sales');
        $totalCommission  = $vendors->sum('platform_commission');
        $totalVendorNet   = $vendors->sum('vendor_net');

        return view('shop.reports.vendor', compact(
            'page_title', 'page_name', 'vendors',
            'totalVendorSales', 'totalCommission', 'totalVendorNet'
        ));
    }

    /**
     * Customer Acquisition & Lifetime Value Report
     */
    public function customerReport(Request $request)
    {
        $page_title = 'Customer Analytics';
        $page_name  = 'Customer Growth & Top Buyers';

        $totalCustomers = DB::table('shop_customers')->where('status', 0)->count();
        $verifiedCustomers = DB::table('shop_customers')->where('status', 0)->where('is_email_verified', 1)->count();

        // Top Spenders
        $topCustomers = DB::table('shop_customers as c')
            ->join('tbl_shop_orders as o', 'o.customer_id', '=', 'c.customer_id')
            ->where('c.status', 0)
            ->where('o.status', 0)
            ->whereNotIn('o.order_status', ['cancelled'])
            ->select(
                'c.customer_id',
                'c.name',
                'c.email',
                'c.phone',
                DB::raw('COUNT(o.order_id) as total_orders'),
                DB::raw('SUM(o.total_amount) as lifetime_spent'),
                DB::raw('MAX(o.created_at) as last_order_date')
            )
            ->groupBy('c.customer_id', 'c.name', 'c.email', 'c.phone')
            ->orderByDesc('lifetime_spent')
            ->limit(15)
            ->get();

        return view('shop.reports.customer', compact(
            'page_title', 'page_name', 'totalCustomers', 'verifiedCustomers', 'topCustomers'
        ));
    }

    // ═════════════════════════════════════════════════════════════════
    // VENDOR REPORTS (Module 31)
    // ═════════════════════════════════════════════════════════════════

    /**
     * Vendor Dashboard / Reports Overview
     */
    public function vendorDashboard(Request $request)
    {
        $vendorId = session('vendor_id');
        $page_title = 'Vendor Analytics';
        $page_name  = 'Store Performance & Reports';

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // Total Gross Revenue for Vendor in period
        $grossRevenue = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'o.order_id', '=', 'i.order_id')
            ->where('i.vendor_id', $vendorId)
            ->where('o.status', 0)
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->whereNotIn('o.order_status', ['cancelled'])
            ->sum('i.total_price');

        // Total units sold
        $totalUnitsSold = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'o.order_id', '=', 'i.order_id')
            ->where('i.vendor_id', $vendorId)
            ->where('o.status', 0)
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->whereNotIn('o.order_status', ['cancelled'])
            ->sum('i.quantity');

        // Total orders with vendor items
        $totalOrdersCount = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'o.order_id', '=', 'i.order_id')
            ->where('i.vendor_id', $vendorId)
            ->where('o.status', 0)
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->distinct('i.order_id')
            ->count('i.order_id');

        // Vendor commission rate
        $commissionPercent = DB::table('shop_vendors')->where('vendor_id', $vendorId)->value('commission_percent') ?? 10.0;
        $commissionDeducted = ($grossRevenue * $commissionPercent) / 100;
        $netEarnings = $grossRevenue - $commissionDeducted;

        // Daily trend
        $dailyTrends = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'o.order_id', '=', 'i.order_id')
            ->where('i.vendor_id', $vendorId)
            ->where('o.status', 0)
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->whereNotIn('o.order_status', ['cancelled'])
            ->selectRaw('DATE(o.created_at) as sale_date, SUM(i.total_price) as revenue, SUM(i.quantity) as units')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'ASC')
            ->get();

        $chartLabels = $dailyTrends->pluck('sale_date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $chartRevenue = $dailyTrends->pluck('revenue')->toArray();
        $chartUnits = $dailyTrends->pluck('units')->toArray();

        // Top selling products for this vendor
        $vendorTopProducts = DB::table('tbl_shop_order_items as i')
            ->join('tbl_shop_orders as o', 'o.order_id', '=', 'i.order_id')
            ->where('i.vendor_id', $vendorId)
            ->where('o.status', 0)
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->whereNotIn('o.order_status', ['cancelled'])
            ->selectRaw('i.product_id, i.product_name, SUM(i.quantity) as units_sold, SUM(i.total_price) as product_revenue')
            ->groupBy('i.product_id', 'i.product_name')
            ->orderByDesc('product_revenue')
            ->limit(8)
            ->get();

        return view('shop.vendor.reports.index', compact(
            'page_title', 'page_name', 'startDate', 'endDate',
            'grossRevenue', 'totalUnitsSold', 'totalOrdersCount',
            'commissionPercent', 'commissionDeducted', 'netEarnings',
            'chartLabels', 'chartRevenue', 'chartUnits', 'vendorTopProducts'
        ));
    }
}
