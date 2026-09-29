@extends('store.layouts.app')
@section('title', 'Order #' . $order->order_number . ' | HexaShop')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Account Sidebar -->
        <div class="col-lg-3 mb-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="p-4 bg-primary text-white rounded-top-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">
                                {{ substr(session('customer_name', 'U'), 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ session('customer_name') }}</h6>
                                <small class="opacity-75">Customer</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-flush py-2">
                        <a href="{{ route('store.orders') }}" class="list-group-item list-group-item-action border-0 active fw-semibold"><i class="bx bx-package me-2"></i> My Orders</a>
                        <a href="{{ route('store.wishlist') }}" class="list-group-item list-group-item-action border-0"><i class="bx bx-heart me-2"></i> Wishlist</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Order #{{ $order->order_number }}</h4>
                    <span class="text-muted small">Placed on {{ date('d M Y, h:i A', strtotime($order->created_at)) }}</span>
                </div>
                <div>
                    @if(in_array($order->order_status, ['pending', 'confirmed']))
                        <form action="{{ route('store.order.cancel', $order->order_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            <button class="btn btn-outline-danger rounded-pill fw-semibold me-2">Cancel Order</button>
                        </form>
                    @endif
                    @if($order->order_status == 'delivered')
                        <form action="{{ route('store.order.return', $order->order_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to request a return?');">
                            @csrf
                            <button class="btn btn-outline-warning rounded-pill fw-semibold me-2">Request Return</button>
                        </form>
                    @endif
                    <a href="{{ route('store.order.invoice', $order->order_id) }}" target="_blank" class="btn btn-primary rounded-pill fw-semibold"><i class="bx bx-receipt me-1"></i> Invoice</a>
                </div>
            </div>

            <!-- Tracker -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4">Tracking History</h6>
                    <div class="position-relative">
                        <div class="progress" style="height: 4px; margin-bottom: 2rem;">
                            @php
                                $pct = 0;
                                if(in_array($order->order_status, ['pending', 'confirmed'])) $pct = 25;
                                if(in_array($order->order_status, ['processing', 'ready_to_ship'])) $pct = 50;
                                if($order->order_status == 'shipped') $pct = 75;
                                if($order->order_status == 'out_for_delivery') $pct = 90;
                                if($order->order_status == 'delivered') $pct = 100;
                                if($order->order_status == 'cancelled') $pct = 100;
                            @endphp
                            <div class="progress-bar {{ $order->order_status == 'cancelled' ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <div class="timeline mt-4">
                        @foreach($tracking as $track)
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bx bx-check"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-uppercase">{{ str_replace('_', ' ', $track->status) }}</h6>
                                <p class="text-muted mb-1 small">{{ $track->description }}</p>
                                <small class="text-muted">{{ date('d M Y, h:i A', strtotime($track->created_at)) }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Items & Summary -->
            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Order Items</h6>
                            <div class="d-flex flex-column gap-3">
                                @foreach($items as $item)
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="position-relative">
                                        <img src="{{ $item->product_image ? asset($item->product_image) : 'https://placehold.co/60x60' }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold" style="font-size:0.9rem;">{{ $item->product_name }}</h6>
                                        <small class="text-muted">Code: {{ $item->product_code }}</small>
                                    </div>
                                    <div class="fw-semibold">₹{{ number_format($item->total_price, 2) }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Order Summary</h6>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->coupon_discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount @if($order->coupon_code) ({{ $order->coupon_code }}) @endif</span>
                                <span>-₹{{ number_format($order->coupon_discount, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-3 text-muted border-bottom pb-3">
                                <span>Shipping</span>
                                <span>₹{{ number_format($order->shipping_charge, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fs-5 fw-bold">Total</span>
                                <span class="fs-5 fw-bold text-primary">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold mb-2">Shipping Details</h6>
                                @php $addr = json_decode($order->shipping_address); @endphp
                                @if($addr)
                                    <p class="mb-0 text-muted small">
                                        <strong>{{ $addr->full_name }}</strong><br>
                                        {{ $addr->address_line1 }}<br>
                                        {{ $addr->city }}, {{ $addr->state }} {{ $addr->pincode }}<br>
                                        Phone: {{ $addr->phone }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
