<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>My Assigned Deliveries — HexaShop Delivery</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-package text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('delivery.dashboard') }}">Dashboard</a> / <span>Assigned Orders</span>
            </div>
        </div>
        <div>
            <span class="badge bg-primary px-3 py-2" style="border-radius:20px;font-size:0.8rem;">
                <i class="bx bx-run me-1"></i> {{ count($assignments) }} Active Deliveries
            </span>
        </div>
    </div>

    {{-- Cards Grid for Mobile/Desktop Friendly Delivery View --}}
    <div class="row g-3">
        @forelse($assignments as $item)
        @php
            $addr = is_string($item->shipping_address) ? json_decode($item->shipping_address, true) : $item->shipping_address;
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="shop-card h-100 shadow-sm border-0" style="border-radius:14px;overflow:hidden;position:relative;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:#f8f9fa;">
                    <div>
                        <span class="fw-bold text-primary">{{ $item->order_number }}</span>
                        <div class="text-muted" style="font-size:0.75rem;">Assigned {{ \Carbon\Carbon::parse($item->assigned_at)->diffForHumans() }}</div>
                    </div>
                    <div>
                        @if($item->delivery_status === 'assigned')
                            <span class="badge bg-secondary">Assigned</span>
                        @elseif($item->delivery_status === 'picked')
                            <span class="badge bg-info">Picked Up</span>
                        @elseif($item->delivery_status === 'out_for_delivery')
                            <span class="badge bg-warning text-dark">Out for Delivery</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:40px;height:40px;font-size:1.2rem;">
                            <i class="bx bx-user"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $item->customer_name }}</div>
                            <a href="tel:{{ $item->customer_phone }}" class="text-decoration-none text-muted" style="font-size:0.8rem;">
                                <i class="bx bx-phone me-1 text-primary"></i>{{ $item->customer_phone }}
                            </a>
                        </div>
                    </div>

                    <div class="p-2 mb-3 rounded" style="background:#f1f5f9;font-size:0.8rem;">
                        <div class="fw-semibold text-secondary mb-1"><i class="bx bx-map-pin text-danger me-1"></i>Delivery Address:</div>
                        <div class="text-dark">
                            {{ $addr['address_line1'] ?? ($addr['address'] ?? 'Customer Address') }}
                            @if(!empty($addr['city'])), {{ $addr['city'] }} @endif
                            @if(!empty($addr['pincode'])) - {{ $addr['pincode'] }} @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pt-2 border-top">
                        <div>
                            <span class="text-muted" style="font-size:0.78rem;">Payment ({{ strtoupper($item->payment_method) }})</span>
                            <div class="fw-bold {{ $item->payment_status === 'paid' ? 'text-success' : 'text-danger' }}">
                                {{ $item->payment_status === 'paid' ? 'Paid' : 'Cash On Delivery' }}
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-muted" style="font-size:0.78rem;">Total Bill</span>
                            <div class="fs-6 fw-bold text-primary">{{ formatShopPrice($item->total_amount) }}</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('delivery.order.show', $item->assignment_id) }}" class="btn btn-primary" style="border-radius:10px;font-weight:600;font-size:0.85rem;">
                            <i class="bx bx-navigation me-1"></i> Manage & Verify OTP
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="shop-card text-center py-5">
                <i class="bx bx-check-double text-success" style="font-size:3.5rem;"></i>
                <h5 class="mt-3 fw-bold">All Caught Up!</h5>
                <p class="text-muted">You have no pending orders assigned right now. Relax and stay safe on the road!</p>
            </div>
        </div>
        @endforelse
    </div>

</main>

@include('shop.include.footer_link')
</body>
</html>
