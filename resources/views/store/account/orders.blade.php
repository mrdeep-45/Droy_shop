@extends('store.layouts.app')
@section('title', 'My Orders | DPShop')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Account Sidebar -->
        <div class="col-lg-3 mb-4">
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
                        <form action="{{ route('store.logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button class="list-group-item list-group-item-action border-0 text-danger"><i class="bx bx-log-out me-2"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="col-lg-9">
            <h4 class="fw-bold mb-4">My Orders</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(count($orders) > 0)
                <div class="d-flex flex-column gap-4">
                    @foreach($orders as $order)
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small d-block">Order Placed</span>
                                    <span class="fw-bold">{{ date('d M Y', strtotime($order->created_at)) }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">Total Amount</span>
                                    <span class="fw-bold text-primary">₹{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small d-block">Order #{{ $order->order_number }}</span>
                                    <a href="{{ route('store.order.detail', $order->order_id) }}" class="text-decoration-none fw-semibold">View Details <i class="bx bx-chevron-right"></i></a>
                                </div>
                            </div>
                            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold mb-1 text-uppercase">{{ str_replace('_', ' ', $order->order_status) }}</h5>
                                    <p class="text-muted mb-0 small">
                                        @if($order->order_status == 'delivered')
                                            Delivered on {{ date('d M Y', strtotime($order->delivered_at)) }}
                                        @elseif($order->order_status == 'cancelled')
                                            Cancelled on {{ date('d M Y', strtotime($order->cancelled_at)) }}
                                        @else
                                            Your order is being processed.
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('store.order.invoice', $order->order_id) }}" target="_blank" class="btn btn-outline-secondary rounded-pill btn-sm fw-semibold">
                                    <i class="bx bx-receipt me-1"></i> Invoice
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <i class="bx bx-package text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4>No orders found</h4>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                    <a href="{{ route('store.shop') }}" class="btn btn-primary mt-2 rounded-pill fw-bold px-4">Start Shopping</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
