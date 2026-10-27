<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Sales & Revenue Report — DPShop Admin</title>
    @include('shop.include.header_link')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-bar-chart-alt-2 text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Reports / <span>Sales Analytics</span>
            </div>
        </div>

        {{-- Date Filter Form --}}
        <form method="GET" action="{{ route('shop.report.sales') }}" class="d-flex align-items-center gap-2">
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate->format('Y-m-d') }}" style="border-radius:8px;">
            <span class="text-muted">to</span>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate->format('Y-m-d') }}" style="border-radius:8px;">
            <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;"><i class="bx bx-filter-alt me-1"></i>Filter</button>
        </form>
    </div>

    {{-- KPI Highlights --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-rupee"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">TOTAL REVENUE</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ formatShopPrice($totalSales) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-shopping-bag"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">TOTAL ORDERS</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ number_format($totalOrders) }}</h5>
                    <small class="text-success">{{ $completedOrders }} Delivered</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-calculator"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">AVG ORDER VALUE</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ formatShopPrice($avgOrderValue) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-tag-alt"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">DISCOUNTS GIVEN</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ formatShopPrice($totalDiscount) }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Chart & Payment Breakdown --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="shop-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bx bx-line-chart me-2 text-primary"></i>Daily Revenue Trend</span>
                    <small class="text-muted">{{ $startDate->format('d M') }} — {{ $endDate->format('d M Y') }}</small>
                </div>
                <div class="card-body">
                    <canvas id="salesTrendChart" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="shop-card h-100">
                <div class="card-header">
                    <span><i class="bx bx-pie-chart-alt-2 me-2 text-primary"></i>Payment Methods</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" style="font-size:0.83rem;">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Orders</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentMethods as $pm)
                                <tr>
                                    <td class="fw-semibold text-capitalize">{{ $pm->payment_method }}</td>
                                    <td>{{ $pm->count }}</td>
                                    <td class="text-end fw-bold">{{ formatShopPrice($pm->total) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No orders found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Performing Products --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-trophy me-2 text-warning"></i>Top Selling Products in Period</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.83rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Product Name</th>
                            <th>Units Sold</th>
                            <th class="text-end pe-3">Total Gross Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $idx => $prod)
                        <tr>
                            <td class="ps-3">{{ $idx + 1 }}</td>
                            <td class="fw-semibold text-primary">{{ $prod->product_name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $prod->total_qty }} pcs</span></td>
                            <td class="text-end pe-3 fw-bold">{{ formatShopPrice($prod->total_revenue) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No sales recorded for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

@include('shop.include.footer_link')

<script>
$(function() {
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Revenue (₹)',
                data: @json($chartRevenue),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.08)',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) { return '₹' + v.toLocaleString(); }
                    }
                }
            }
        }
    });
});
</script>
</body>
</html>
