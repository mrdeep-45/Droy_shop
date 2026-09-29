@extends('store.layouts.app')
@section('title', 'Checkout | HexaShop')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Checkout</h2>
        <p class="text-muted">Complete your order by providing payment and shipping details.</p>
    </div>

    <form id="checkoutForm">
        @csrf
        <div class="row g-5">
            <!-- Left Side: Shipping & Payment -->
            <div class="col-lg-7">
                <!-- Shipping Address -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0"><i class="bx bx-map text-primary me-2"></i> Shipping Address</h5>
                    </div>
                    <div class="card-body p-4">
                        @if(count($addresses) > 0)
                            <div class="row g-3">
                                @foreach($addresses as $addr)
                                <div class="col-md-6">
                                    <div class="card border {{ $addr->is_default ? 'border-primary' : '' }} h-100">
                                        <div class="card-body position-relative">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="address_id" id="addr_{{ $addr->address_id }}" value="{{ $addr->address_id }}" {{ $addr->is_default ? 'checked' : '' }} required>
                                                <label class="form-check-label fw-bold" for="addr_{{ $addr->address_id }}">
                                                    {{ $addr->full_name }}
                                                </label>
                                            </div>
                                            <div class="text-muted small mt-2 ps-4">
                                                {{ $addr->address_line1 }}, {{ $addr->address_line2 }}<br>
                                                {{ $addr->city }}, {{ $addr->state }} {{ $addr->pincode }}<br>
                                                Phone: {{ $addr->phone }}
                                            </div>
                                            @if($addr->address_type)
                                                <span class="badge bg-light text-dark position-absolute top-0 end-0 m-2">{{ ucfirst($addr->address_type) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bx bx-error-circle me-1"></i> You don't have any saved addresses. <br>
                                <!-- In a real app, here we would provide a modal or form to add an address -->
                                <button type="button" class="btn btn-sm btn-primary mt-2">Add New Address</button>
                            </div>
                            <!-- Mock hidden input so form doesn't crash if they try to submit -->
                            <input type="hidden" name="address_id" value="">
                        @endif
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="fw-bold mb-0"><i class="bx bx-credit-card text-primary me-2"></i> Payment Method</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group">
                            <label class="list-group-item d-flex gap-3 align-items-center p-3 border rounded-3 mb-2 cursor-pointer">
                                <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" value="razorpay" disabled>
                                <span class="pt-1 form-checked-content">
                                    <strong>Online Payment (Razorpay)</strong>
                                    <span class="d-block text-muted small">Credit Card, Debit Card, Net Banking, UPI (Coming soon)</span>
                                </span>
                                <i class="bx bx-credit-card-alt fs-2 text-muted ms-auto"></i>
                            </label>
                            
                            <label class="list-group-item d-flex gap-3 align-items-center p-3 border rounded-3 mb-2 cursor-pointer" style="border-color: #0d6efd !important; background-color: #f8fbff;">
                                <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" value="cod" checked>
                                <span class="pt-1 form-checked-content">
                                    <strong>Cash on Delivery (COD)</strong>
                                    <span class="d-block text-muted small">Pay with cash upon delivery.</span>
                                </span>
                                <i class="bx bx-money fs-2 text-success ms-auto"></i>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold border-bottom pb-3 mb-4">Order Summary</h4>
                        
                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach($cartItems as $item)
                            <div class="d-flex gap-3 align-items-center">
                                <div class="position-relative">
                                    <img src="{{ $item->primary_image ? asset($item->primary_image) : 'https://placehold.co/60x60' }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">{{ $item->quantity }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold" style="font-size:0.9rem;">{{ $item->product_name }}</h6>
                                </div>
                                <div class="fw-semibold">₹{{ number_format($item->unit_price * $item->quantity, 2) }}</div>
                            </div>
                            @endforeach
                        </div>

                        <hr class="text-muted opacity-25 mb-3">

                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Subtotal</span>
                            <span class="fw-semibold text-dark">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($couponDiscount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount @if($couponCode) <small>({{ $couponCode }})</small> @endif</span>
                            <span class="fw-bold">-₹{{ number_format($couponDiscount, 2) }}</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Shipping</span>
                            <span class="fw-semibold text-dark">{{ $shippingCharge > 0 ? '₹'.number_format($shippingCharge, 2) : 'Free' }}</span>
                        </div>
                        
                        <hr class="text-muted opacity-25 mb-3">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-4 fw-bold text-primary">₹{{ number_format($total, 2) }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm" id="placeOrderBtn">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();
        
        // Quick validation for address
        if(!$('input[name="address_id"]:checked').val()) {
            Swal.fire('Error', 'Please select or add a shipping address', 'error');
            return;
        }

        let btn = $('#placeOrderBtn');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-2"></i> Processing...');
        
        $.post("{{ route('store.checkout.process') }}", $(this).serialize(), function(res) {
            if(res.status === 'success') {
                window.location.href = "{{ url('checkout/success') }}/" + res.order_id;
            } else {
                Swal.fire('Error', res.message, 'error');
                btn.prop('disabled', false).html('Place Order');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'An error occurred during checkout', 'error');
            btn.prop('disabled', false).html('Place Order');
        });
    });
</script>
@endpush
