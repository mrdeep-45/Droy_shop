<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Dashboard — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')

@include('shop.include.header')

{{-- ═══════════════ MAIN CONTENT ═══════════════ --}}
<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bxs-dashboard text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Dashboard
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-success-subtle text-success px-3 py-2" style="font-size:0.78rem; border-radius:20px;">
                <i class="bx bx-check-circle me-1"></i>
                System Online
            </span>
            <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                <i class="bx bx-refresh me-1"></i>Refresh
            </button>
        </div>
    </div>

    @if(in_array($loginType, ['ShopSuperAdmin', 'ShopAdmin']))
    {{-- ════════ ADMIN DASHBOARD ════════ --}}

    {{-- Filter Bar --}}
    <div class="shop-card mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="shop-form-label mb-1">Date Range</label>
                    <select class="form-select form-select-sm" id="dashDateFilter">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="year">This Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-primary btn-sm w-100" onclick="refreshDashboard()">
                        <i class="bx bx-search me-1"></i>Apply
                    </button>
                </div>
                <div class="col-md-7 d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                    <a href="{{ route('shop.product.index') }}#add" class="btn btn-success btn-sm">
                        <i class="bx bx-plus me-1"></i>Add Product
                    </a>
                    <a href="{{ route('shop.order.index') }}" class="btn btn-warning btn-sm text-dark">
                        <i class="bx bx-receipt me-1"></i>View Orders
                    </a>
                    <a href="{{ route('shop.vendor.index') }}" class="btn btn-info btn-sm text-white">
                        <i class="bx bx-buildings me-1"></i>Vendors
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row 1 --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card blue">
                <div class="kpi-icon"><i class="bx bx-receipt"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($kpi['totalOrders'] ?? 0) }}</div>
                    <div class="kpi-label">Total Orders</div>
                    <div class="kpi-sub">
                        <i class="bx bx-trending-up"></i>
                        {{ $kpi['todayOrders'] ?? 0 }} orders today
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card green">
                <div class="kpi-icon"><i class="bx bx-rupee"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ formatShopPrice($kpi['totalRevenue'] ?? 0) }}</div>
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-sub">
                        <i class="bx bx-calendar"></i>
                        {{ formatShopPrice($kpi['monthRevenue'] ?? 0) }} this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card purple">
                <div class="kpi-icon"><i class="bx bx-group"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($kpi['totalCustomers'] ?? 0) }}</div>
                    <div class="kpi-label">Total Customers</div>
                    <div class="kpi-sub">
                        <i class="bx bx-user-plus"></i>
                        {{ $kpi['newCustomers'] ?? 0 }} new this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card orange">
                <div class="kpi-icon"><i class="bx bx-package"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ number_format($kpi['totalProducts'] ?? 0) }}</div>
                    <div class="kpi-label">Active Products</div>
                    <div class="kpi-sub">
                        <i class="bx bx-error-circle"></i>
                        {{ $kpi['lowStockProducts'] ?? 0 }} low stock
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row 2 --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="shop-card text-center py-3 px-2 h-100">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:44px;height:44px;background:rgba(255,193,7,0.1);">
                    <i class="bx bx-time text-warning" style="font-size:1.3rem;"></i>
                </div>
                <div class="fw-bold" style="font-size:1.3rem;color:#1a202c;">{{ $kpi['pendingOrders'] ?? 0 }}</div>
                <div class="text-muted" style="font-size:0.76rem;">Pending Orders</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="shop-card text-center py-3 px-2 h-100">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:44px;height:44px;background:rgba(32,201,151,0.1);">
                    <i class="bx bx-check-double text-success" style="font-size:1.3rem;"></i>
                </div>
                <div class="fw-bold" style="font-size:1.3rem;color:#1a202c;">{{ $kpi['deliveredOrders'] ?? 0 }}</div>
                <div class="text-muted" style="font-size:0.76rem;">Delivered</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="shop-card text-center py-3 px-2 h-100">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:44px;height:44px;background:rgba(220,53,69,0.1);">
                    <i class="bx bx-x-circle text-danger" style="font-size:1.3rem;"></i>
                </div>
                <div class="fw-bold" style="font-size:1.3rem;color:#1a202c;">{{ $kpi['cancelledOrders'] ?? 0 }}</div>
                <div class="text-muted" style="font-size:0.76rem;">Cancelled</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="shop-card text-center py-3 px-2 h-100">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:44px;height:44px;background:rgba(111,66,193,0.1);">
                    <i class="bx bx-buildings text-purple" style="font-size:1.3rem;"></i>
                </div>
                <div class="fw-bold" style="font-size:1.3rem;color:#1a202c;">{{ $kpi['totalVendors'] ?? 0 }}</div>
                <div class="text-muted" style="font-size:0.76rem;">
                    Vendors
                    @if(($kpi['pendingVendors'] ?? 0) > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $kpi['pendingVendors'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="shop-card text-center py-3 px-2 h-100">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:44px;height:44px;background:rgba(253,126,20,0.1);">
                    <i class="bx bx-undo" style="font-size:1.3rem;color:#fd7e14;"></i>
                </div>
                <div class="fw-bold" style="font-size:1.3rem;color:#1a202c;">{{ $kpi['pendingReturns'] ?? 0 }}</div>
                <div class="text-muted" style="font-size:0.76rem;">Pending Returns</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="shop-card text-center py-3 px-2 h-100">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:44px;height:44px;background:rgba(220,53,69,0.1);">
                    <i class="bx bx-box text-danger" style="font-size:1.3rem;"></i>
                </div>
                <div class="fw-bold" style="font-size:1.3rem;color:#1a202c;">{{ $kpi['outOfStock'] ?? 0 }}</div>
                <div class="text-muted" style="font-size:0.76rem;">Out of Stock</div>
            </div>
        </div>
    </div>

    {{-- Charts + Recent Orders --}}
    <div class="row g-3 mb-4">
        {{-- Revenue Chart --}}
        <div class="col-xl-8">
            <div class="shop-card h-100">
                <div class="card-header">
                    <span><i class="bx bx-line-chart me-2 text-primary"></i>Revenue Trend (Last 6 Months)</span>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2" style="font-size:0.75rem; border-radius:20px;">
                            <i class="bx bx-rupee"></i> {{ formatShopPrice($kpi['monthRevenue'] ?? 0) }} this month
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="90"></canvas>
                </div>
            </div>
        </div>

        {{-- Order Status Donut --}}
        <div class="col-xl-4">
            <div class="shop-card h-100">
                <div class="card-header">
                    <span><i class="bx bx-doughnut-chart me-2 text-success"></i>Order Status</span>
                </div>
                <div class="card-body d-flex flex-column align-items-center">
                    <canvas id="orderStatusChart" height="180"></canvas>
                    <div class="row g-2 w-100 mt-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:10px;height:10px;border-radius:50%;background:#ffc107;"></div>
                                <span style="font-size:0.76rem;color:#6c757d;">Pending: <strong>{{ $kpi['pendingOrders'] ?? 0 }}</strong></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:10px;height:10px;border-radius:50%;background:#20c997;"></div>
                                <span style="font-size:0.76rem;color:#6c757d;">Delivered: <strong>{{ $kpi['deliveredOrders'] ?? 0 }}</strong></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:10px;height:10px;border-radius:50%;background:#dc3545;"></div>
                                <span style="font-size:0.76rem;color:#6c757d;">Cancelled: <strong>{{ $kpi['cancelledOrders'] ?? 0 }}</strong></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:10px;height:10px;border-radius:50%;background:#0d6efd;"></div>
                                <span style="font-size:0.76rem;color:#6c757d;">Others: <strong>{{ max(0, ($kpi['totalOrders'] ?? 0) - ($kpi['pendingOrders'] ?? 0) - ($kpi['deliveredOrders'] ?? 0) - ($kpi['cancelledOrders'] ?? 0)) }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders + Top Products --}}
    <div class="row g-3 mb-4">
        {{-- Recent Orders Table --}}
        <div class="col-xl-8">
            <div class="shop-card">
                <div class="card-header">
                    <span><i class="bx bx-receipt me-2 text-warning"></i>Recent Orders</span>
                    <a href="{{ route('shop.order.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:0.78rem;">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.83rem;">
                            <thead>
                                <tr style="background:#f8f9fa;">
                                    <th class="ps-3 py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Order #</th>
                                    <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Customer</th>
                                    <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Amount</th>
                                    <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Status</th>
                                    <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Date</th>
                                    <th class="pe-3 py-2 border-0"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kpi['recentOrders'] ?? [] as $order)
                                <tr>
                                    <td class="ps-3 py-2 fw-semibold text-primary">{{ $order->order_number }}</td>
                                    <td class="py-2">{{ $order->customer_name }}</td>
                                    <td class="py-2 fw-semibold">{{ formatShopPrice($order->total_amount) }}</td>
                                    <td class="py-2">{!! getOrderStatusBadge($order->order_status) !!}</td>
                                    <td class="py-2 text-muted" style="font-size:0.78rem;">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                    <td class="pe-3 py-2">
                                        <a href="{{ route('shop.order.show', $order->order_id) }}" class="btn btn-xs btn-outline-secondary" style="font-size:0.72rem;padding:2px 8px;">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bx bx-receipt" style="font-size:2.5rem;display:block;margin-bottom:6px;"></i>
                                        No orders yet. Orders will appear here once customers start shopping.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="col-xl-4">
            <div class="shop-card h-100">
                <div class="card-header">
                    <span><i class="bx bx-trophy me-2 text-warning"></i>Top Products (30 days)</span>
                </div>
                <div class="card-body p-0">
                    @forelse($kpi['topProducts'] ?? [] as $i => $product)
                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                        <div class="d-flex align-items-center justify-content-center rounded fw-bold"
                             style="width:30px;height:30px;background:{{ ['#f0f3ff','#fff5e6','#f0fff4','#fff0f3','#f5f0ff'][$i] }};color:{{ ['#4f46e5','#d97706','#059669','#dc2626','#7c3aed'][$i] }};font-size:0.8rem;flex-shrink:0;">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate" style="font-size:0.82rem;">{{ $product->product_name }}</div>
                            <div class="text-muted" style="font-size:0.74rem;">{{ number_format($product->total_sold) }} sold</div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold text-success" style="font-size:0.82rem;">{{ formatShopPrice($product->total_revenue) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted" style="font-size:0.82rem;">
                        <i class="bx bx-trophy" style="font-size:2rem;display:block;margin-bottom:6px;"></i>
                        No sales data yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @elseif($loginType === 'Vendor')
    {{-- ════════ VENDOR DASHBOARD ════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card blue">
                <div class="kpi-icon"><i class="bx bx-receipt"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpi['totalOrders'] ?? 0 }}</div>
                    <div class="kpi-label">My Orders</div>
                    <div class="kpi-sub"><i class="bx bx-time"></i> {{ $kpi['pendingOrders'] ?? 0 }} pending</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card green">
                <div class="kpi-icon"><i class="bx bx-rupee"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ formatShopPrice($kpi['totalRevenue'] ?? 0) }}</div>
                    <div class="kpi-label">Total Earnings</div>
                    <div class="kpi-sub"><i class="bx bx-calendar"></i> {{ formatShopPrice($kpi['monthRevenue'] ?? 0) }} this month</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card orange">
                <div class="kpi-icon"><i class="bx bx-wallet"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ formatShopPrice($kpi['pendingPayout'] ?? 0) }}</div>
                    <div class="kpi-label">Pending Payout</div>
                    <div class="kpi-sub"><i class="bx bx-info-circle"></i> Awaiting settlement</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card purple">
                <div class="kpi-icon"><i class="bx bx-package"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpi['totalProducts'] ?? 0 }}</div>
                    <div class="kpi-label">My Products</div>
                    <div class="kpi-sub"><i class="bx bx-check-circle"></i> {{ $kpi['activeProducts'] ?? 0 }} active</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vendor Recent Orders --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-receipt me-2"></i>Recent Orders</span>
            <a href="{{ route('vendor.order.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:0.78rem;">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.83rem;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="ps-3 py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Order #</th>
                            <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Customer</th>
                            <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Amount</th>
                            <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Status</th>
                            <th class="pe-3 py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kpi['recentOrders'] ?? [] as $order)
                        <tr>
                            <td class="ps-3 py-2 fw-semibold text-primary">{{ $order->order_number }}</td>
                            <td class="py-2">{{ $order->customer_name }}</td>
                            <td class="py-2 fw-semibold">{{ formatShopPrice($order->total_amount) }}</td>
                            <td class="py-2">{!! getOrderStatusBadge($order->order_status) !!}</td>
                            <td class="pe-3 py-2 text-muted" style="font-size:0.78rem;">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @elseif($loginType === 'DeliveryStaff')
    {{-- ════════ DELIVERY DASHBOARD ════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="kpi-card blue">
                <div class="kpi-icon"><i class="bx bx-package"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpi['assignedOrders'] ?? 0 }}</div>
                    <div class="kpi-label">Assigned Orders</div>
                    <div class="kpi-sub"><i class="bx bx-map-pin"></i> Active</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="kpi-card green">
                <div class="kpi-icon"><i class="bx bx-check-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpi['deliveredToday'] ?? 0 }}</div>
                    <div class="kpi-label">Delivered Today</div>
                    <div class="kpi-sub"><i class="bx bx-calendar"></i> Today</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="kpi-card purple">
                <div class="kpi-icon"><i class="bx bx-trophy"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpi['totalDelivered'] ?? 0 }}</div>
                    <div class="kpi-label">Total Delivered</div>
                    <div class="kpi-sub"><i class="bx bx-history"></i> All time</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="kpi-card red">
                <div class="kpi-icon"><i class="bx bx-error-circle"></i></div>
                <div class="kpi-body">
                    <div class="kpi-value">{{ $kpi['failedDeliveries'] ?? 0 }}</div>
                    <div class="kpi-label">Failed Today</div>
                    <div class="kpi-sub"><i class="bx bx-x"></i> Needs reattempt</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Delivery Orders --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-package me-2"></i>My Pending Deliveries</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.83rem;">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="ps-3 py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Order #</th>
                            <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Customer</th>
                            <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Amount</th>
                            <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Status</th>
                            <th class="pe-3 py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kpi['pendingOrders'] ?? [] as $order)
                        <tr>
                            <td class="ps-3 py-2 fw-semibold text-primary">{{ $order->order_number }}</td>
                            <td class="py-2">{{ $order->customer_name }}</td>
                            <td class="py-2 fw-semibold">{{ formatShopPrice($order->total_amount) }}</td>
                            <td class="py-2"><span class="badge bg-info">{{ ucfirst(str_replace('_',' ',$order->d_status)) }}</span></td>
                            <td class="pe-3 py-2">
                                <a href="{{ route('delivery.order.show', $order->assignment_id) }}" class="btn btn-xs btn-primary" style="font-size:0.72rem;padding:3px 10px;">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No pending deliveries.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</main>

@include('shop.include.footer_link')

<script>
@if(in_array($loginType, ['ShopSuperAdmin', 'ShopAdmin']))
// ── Revenue Chart ────────────────────────────────────────────────────
const chartLabels  = @json($kpi['chartData']['labels'] ?? []);
const chartRevenue = @json($kpi['chartData']['data'] ?? []);

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Revenue (₹)',
            data:  chartRevenue,
            borderColor:     '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.08)',
            borderWidth: 2.5,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#0d6efd',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN', {minimumFractionDigits:2})
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: {
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    font: { size: 11 },
                    callback: v => '₹' + v.toLocaleString('en-IN')
                }
            }
        }
    }
});

// ── Order Status Donut ───────────────────────────────────────────────
new Chart(document.getElementById('orderStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Delivered', 'Cancelled', 'Others'],
        datasets: [{
            data: [
                {{ $kpi['pendingOrders'] ?? 0 }},
                {{ $kpi['deliveredOrders'] ?? 0 }},
                {{ $kpi['cancelledOrders'] ?? 0 }},
                {{ max(0, ($kpi['totalOrders'] ?? 0) - ($kpi['pendingOrders'] ?? 0) - ($kpi['deliveredOrders'] ?? 0) - ($kpi['cancelledOrders'] ?? 0)) }},
            ],
            backgroundColor: ['#ffc107', '#20c997', '#dc3545', '#0d6efd'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed } }
        }
    }
});

function refreshDashboard() {
    showShopLoader();
    setTimeout(hideShopLoader, 800);
}
@endif
</script>
</body>
</html>
