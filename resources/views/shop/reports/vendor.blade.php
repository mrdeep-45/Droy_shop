<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Vendor Performance Report — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-buildings text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Reports / <span>Vendor Analytics</span>
            </div>
        </div>
        <a href="{{ route('shop.vendor.index') }}" class="btn btn-outline-primary" style="border-radius:10px;font-size:0.85rem;">
            <i class="bx bx-buildings me-1"></i> Manage Vendors
        </a>
    </div>

    {{-- KPI Highlights --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-cart"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">TOTAL VENDOR SALES</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ formatShopPrice($totalVendorSales) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-pie-chart-alt"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">PLATFORM COMMISSION EARNED</div>
                    <h5 class="fw-bold mb-0 text-success">{{ formatShopPrice($totalCommission) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-wallet"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">NET VENDOR PAYOUTS</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ formatShopPrice($totalVendorNet) }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Vendor Ledger Table --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>Store Performance & Commission Breakdown</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.83rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="ps-3">Vendor / Store</th>
                            <th>Products Listed</th>
                            <th>Items Sold</th>
                            <th>Gross Revenue</th>
                            <th>Commission Rate</th>
                            <th>Platform Fee</th>
                            <th class="text-end pe-3">Net Vendor Earning</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $v)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-primary">{{ $v->business_name }}</div>
                                <small class="text-muted">{{ $v->owner_name }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $v->total_products }} items</span></td>
                            <td>{{ number_format($v->items_sold) }} pcs</td>
                            <td class="fw-bold">{{ formatShopPrice($v->gross_sales) }}</td>
                            <td>{{ $v->commission_percent ?? 10 }}%</td>
                            <td class="text-success fw-semibold">{{ formatShopPrice($v->platform_commission) }}</td>
                            <td class="text-end pe-3 fw-bold text-dark">{{ formatShopPrice($v->vendor_net) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No vendor activity recorded.</td>
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
