@extends('store.layouts.app')
@section('title', 'Order Placed Successfully | DPShop')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="bg-white p-5 rounded-4 shadow-sm">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 100px; height: 100px;">
                        <i class="bx bx-check" style="font-size: 5rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-3">Order Placed Successfully!</h2>
                <p class="text-muted fs-5 mb-4">Thank you for your purchase. Your order has been received and is currently being processed.</p>
                
                <div class="bg-light p-4 rounded-3 mb-4 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order Number:</span>
                        <span class="fw-bold">{{ $order->order_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Payment Method:</span>
                        <span class="fw-bold text-uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Amount:</span>
                        <span class="fw-bold text-primary fs-5">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ route('store.shop') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">Continue Shopping</a>
                    <a href="#" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">View Order Details</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
