<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Customer Analytics — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-group text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Reports / <span>Customer Analytics</span>
            </div>
        </div>
        <a href="{{ route('shop.customer.index') }}" class="btn btn-outline-primary" style="border-radius:10px;font-size:0.85rem;">
            <i class="bx bx-user me-1"></i> Customer Directory
        </a>
    </div>

    {{-- KPI Highlights --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-user-plus"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">TOTAL REGISTERED CUSTOMERS</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ number_format($totalCustomers) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-badge-check"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">VERIFIED ACCOUNTS</div>
                    <h5 class="fw-bold mb-0 text-success">{{ number_format($verifiedCustomers) }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Spending Customers Table --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-crown me-2 text-warning"></i>Top Customers by Lifetime Value</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.83rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="ps-3">Customer</th>
                            <th>Contact Info</th>
                            <th>Orders Placed</th>
                            <th>Last Active</th>
                            <th class="text-end pe-3">Lifetime Spend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCustomers as $c)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-primary">{{ $c->name }}</div>
                                <small class="text-muted">ID: #{{ $c->customer_id }}</small>
                            </td>
                            <td>
                                <div><i class="bx bx-envelope me-1 text-muted"></i>{{ $c->email }}</div>
                                <small class="text-muted"><i class="bx bx-phone me-1"></i>{{ $c->phone ?? 'N/A' }}</small>
                            </td>
                            <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $c->total_orders }} orders</span></td>
                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($c->last_order_date)->format('d M Y') }}</small></td>
                            <td class="text-end pe-3 fw-bold text-dark">{{ formatShopPrice($c->lifetime_spent) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No purchase records found yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

@include('shop.include.footer_link')
</body>
</html>
