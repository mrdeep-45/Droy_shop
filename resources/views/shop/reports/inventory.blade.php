<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Inventory Report — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-cube-alt text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Reports / <span>Inventory Levels</span>
            </div>
        </div>
        <a href="{{ route('shop.inventory.index') }}" class="btn btn-outline-primary" style="border-radius:10px;font-size:0.85rem;">
            <i class="bx bx-list-check me-1"></i> Stock Adjustments
        </a>
    </div>

    {{-- KPI Highlights --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-package"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">CATALOG ITEMS</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ number_format($totalItems) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-layer"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">TOTAL STOCK UNITS</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ number_format($totalUnits) }} pcs</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-rupee"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">EST. STOCK VALUE</div>
                    <h5 class="fw-bold mb-0 text-dark">{{ formatShopPrice($stockValuation) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.4rem;">
                    <i class="bx bx-error-circle"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;font-weight:600;">LOW / OUT OF STOCK</div>
                    <h5 class="fw-bold mb-0 text-danger">{{ $lowStockCount + $outOfStockCount }}</h5>
                    <small class="text-muted">{{ $outOfStockCount }} out of stock</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Urgent Attention Table --}}
    <div class="shop-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bx bx-alarm-exclamation me-2 text-danger"></i>Low Stock & Depleted Inventory Items</span>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">Needs Reorder</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.83rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="ps-3">SKU</th>
                            <th>Product Name</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Price</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $prod)
                        <tr>
                            <td class="ps-3 fw-bold text-muted">{{ $prod->sku ?? 'PRD-' . $prod->product_id }}</td>
                            <td class="fw-semibold text-primary">{{ $prod->product_name }}</td>
                            <td>
                                @if($prod->stock_qty <= 0)
                                    <span class="badge bg-danger">0 Out of Stock</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $prod->stock_qty }} remaining</span>
                                @endif
                            </td>
                            <td>{{ $prod->low_stock_threshold ?? 5 }}</td>
                            <td class="fw-semibold">{{ formatShopPrice($prod->selling_price) }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('shop.purchase.index') }}" class="btn btn-xs btn-outline-primary" style="font-size:0.75rem;padding:3px 10px;">
                                    <i class="bx bx-cart-add me-1"></i>Purchase Order
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">All products have healthy inventory levels!</td>
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
