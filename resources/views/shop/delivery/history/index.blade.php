<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Delivery History — DPShop Delivery</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-history text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('delivery.dashboard') }}">Dashboard</a> / <span>Delivery History</span>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-check me-2 text-primary"></i>Delivered / Resolved Packages</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.83rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th class="ps-3">Order #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $item)
                        <tr>
                            <td class="ps-3 fw-bold text-primary">{{ $item->order_number }}</td>
                            <td>
                                <div>{{ $item->customer_name }}</div>
                                <small class="text-muted">{{ $item->customer_phone }}</small>
                            </td>
                            <td class="fw-semibold">{{ formatShopPrice($item->total_amount) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ strtoupper($item->payment_method) }}</span></td>
                            <td>
                                @if($item->delivery_status === 'delivered')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bx bx-check-double me-1"></i>Delivered</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bx bx-x me-1"></i>Failed</span>
                                @endif
                            </td>
                            <td>{{ $item->delivered_at ? \Carbon\Carbon::parse($item->delivered_at)->format('d M Y, h:i A') : 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No completed delivery history found.</td>
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
