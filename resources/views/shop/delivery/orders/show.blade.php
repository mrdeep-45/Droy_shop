<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Process Delivery | Delivery Staff</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="container-fluid pt-3">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold"><i class="bx bx-package me-2"></i>Process Delivery: #{{ $order->order_number }}</h4>
                <a href="{{ route('delivery.order.index') }}" class="btn btn-outline-dark"><i class="bx bx-arrow-back"></i> Back</a>
            </div>
        </div>

        <div class="row">
            <!-- Left Side: Order & Items -->
            <div class="col-md-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bx bx-map-pin me-1"></i> Delivery Details</div>
                    <div class="card-body">
                        @php $addr = json_decode($order->shipping_address); @endphp
                        <h5 class="fw-bold mb-2">{{ $addr->full_name ?? $order->customer_name }}</h5>
                        <p class="mb-1 text-muted"><i class="bx bx-map"></i> {{ $addr->address_line1 ?? '' }}, {{ $addr->address_line2 ?? '' }}</p>
                        <p class="mb-1 text-muted"><i class="bx bx-buildings"></i> {{ $addr->city ?? '' }}, {{ $addr->state ?? '' }} - {{ $addr->pincode ?? '' }}</p>
                        <p class="mb-0 fw-bold text-primary"><i class="bx bx-phone-call"></i> {{ $addr->phone ?? $order->customer_phone }}</p>
                        
                        <div class="mt-4 p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold">Payment to Collect ({{ strtoupper($order->payment_method) }})</h6>
                            <h3 class="mb-0 text-danger fw-bold">₹{{ number_format($order->total_amount, 2) }}</h3>
                            @if($order->payment_status == 'paid')
                                <span class="badge bg-success mt-2">ALREADY PAID ONLINE</span>
                            @else
                                <span class="badge bg-warning text-dark mt-2">COLLECT CASH FROM CUSTOMER</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Package Contents</div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($items as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $item->product_name }}</h6>
                                    <small class="text-muted">Qty: {{ $item->quantity }} | Code: {{ $item->product_code }}</small>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Side: Action Panel -->
            <div class="col-md-5">
                @if($order->order_status == 'cancelled')
                    <div class="alert alert-danger">
                        <h5 class="alert-heading fw-bold"><i class="bx bx-x-circle"></i> Order Cancelled</h5>
                        <p class="mb-0">This order was cancelled by the customer or admin. You do not need to deliver this.</p>
                    </div>
                @elseif($assignment->delivery_status == 'delivered')
                    <div class="alert alert-success">
                        <h5 class="alert-heading fw-bold"><i class="bx bx-check-circle"></i> Successfully Delivered</h5>
                        <p class="mb-0">You delivered this package on {{ date('d M Y, h:i A', strtotime($assignment->delivered_at)) }}.</p>
                    </div>
                @else
                    <!-- Status Update Form -->
                    <div class="card shadow-sm border-0 border-top border-3 border-primary mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Update Progress</h5>
                            <form action="{{ route('delivery.order.status') }}" method="POST">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                
                                <div class="mb-3">
                                    <select name="status" class="form-select fw-semibold" required>
                                        <option value="picked" {{ $assignment->delivery_status == 'picked' ? 'selected' : '' }}>Picked from Warehouse</option>
                                        <option value="out_for_delivery" {{ $assignment->delivery_status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-dark w-100 fw-bold">Update Status</button>
                            </form>
                        </div>
                    </div>

                    <!-- Final OTP Delivery Form -->
                    @if($assignment->delivery_status == 'out_for_delivery')
                    <div class="card shadow-sm border-0 border-top border-3 border-success mb-4">
                        <div class="card-body text-center p-4">
                            <h5 class="fw-bold mb-3 text-success"><i class="bx bx-check-shield"></i> Complete Delivery</h5>
                            <p class="text-muted small mb-4">Ask the customer for the 4-digit Delivery OTP to mark this order as successfully delivered.</p>
                            
                            <form action="{{ route('delivery.order.otp_verify') }}" method="POST">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                
                                <div class="mb-4">
                                    <input type="text" name="delivery_otp" class="form-control form-control-lg text-center fw-bold fs-3" placeholder="----" maxlength="4" required style="letter-spacing: 15px;">
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold rounded-pill shadow-sm">
                                    Verify OTP & Deliver
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning text-center small mb-0">
                        <i class="bx bx-info-circle"></i> Mark as "Out for Delivery" first to enable OTP verification panel.
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</main>

@include('shop.include.footer_link')
</body>
</html>
