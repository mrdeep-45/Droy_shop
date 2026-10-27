@extends('store.layouts.app')
@section('title', 'Shopping Cart | DPShop')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Shopping Cart</h2>

    @if(count($cartItems) > 0)
        <div class="row g-5">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-0">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0">Product Details</th>
                                    <th class="py-3 border-0 text-center">Quantity</th>
                                    <th class="py-3 border-0 text-end">Price</th>
                                    <th class="py-3 border-0 text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                <tr id="cart-row-{{ $item->cart_id }}">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->primary_image)
                                                <img src="{{ asset($item->primary_image) }}" alt="{{ $item->product_name }}" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 70px; height: 70px;">
                                                    <i class="bx bx-image fs-3"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('store.product.detail', $item->product_slug) }}" class="fw-bold text-dark text-decoration-none d-block mb-1">{{ $item->product_name }}</a>
                                                <button class="btn btn-sm text-danger p-0 border-0 fs-7 remove-cart-btn" data-id="{{ $item->cart_id }}"><i class="bx bx-trash me-1"></i> Remove</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center justify-content-center border rounded-pill px-2 py-1 mx-auto bg-white" style="width: 100px;">
                                            <button class="btn p-0 border-0 qty-btn-minus" data-id="{{ $item->cart_id }}"><i class="bx bx-minus"></i></button>
                                            <input type="number" class="form-control form-control-sm border-0 text-center bg-transparent fw-bold qty-input" id="qty-{{ $item->cart_id }}" value="{{ $item->quantity }}" readonly>
                                            <button class="btn p-0 border-0 qty-btn-plus" data-id="{{ $item->cart_id }}" data-max="{{ $item->stock_qty }}"><i class="bx bx-plus"></i></button>
                                        </div>
                                    </td>
                                    <td class="py-3 text-end fw-semibold text-muted">₹{{ number_format($item->cart_price, 2) }}</td>
                                    <td class="py-3 text-end pe-4 fw-bold text-primary item-total" id="total-{{ $item->cart_id }}">₹{{ number_format($item->cart_price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <a href="{{ route('store.shop') }}" class="btn btn-outline-primary rounded-pill fw-semibold"><i class="bx bx-left-arrow-alt me-1"></i> Continue Shopping</a>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold border-bottom pb-3 mb-4">Order Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Subtotal</span>
                            <span class="fw-semibold text-dark" id="summarySubtotal">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($discount > 0)
                        <div class="d-flex justify-content-between mb-3 text-success">
                            <span>Discount @if($couponCode) <small>({{ $couponCode }})</small> @endif</span>
                            <span class="fw-bold">-₹{{ number_format($discount, 2) }}</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-4 pb-3 border-bottom text-muted">
                            <span>Shipping</span>
                            <span class="fw-semibold text-dark">Calculated at checkout</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-4 fw-bold text-primary" id="summaryTotal">₹{{ number_format($total, 2) }}</span>
                        </div>

                        <!-- Coupon Form -->
                        <div class="mb-4">
                            @if($couponCode)
                                <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3 mb-0 rounded-pill">
                                    <span><i class="bx bxs-discount me-2"></i> {{ $couponCode }} applied</span>
                                    <form action="{{ route('store.cart.remove_coupon') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn-close" style="font-size:0.7rem;"></button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('store.cart.apply_coupon') }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="text" name="coupon_code" class="form-control rounded-pill" placeholder="Enter coupon code" required>
                                    <button type="submit" class="btn btn-dark rounded-pill px-3 fw-semibold">Apply</button>
                                </form>
                            @endif
                        </div>

                        <a href="{{ route('store.checkout') }}" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                            Proceed to Checkout <i class="bx bx-right-arrow-alt ms-1"></i>
                        </a>
                        
                        <div class="mt-4 text-center">
                            <i class="bx bxs-lock-alt text-success me-1"></i> <span class="text-muted small">Secure Checkout</span>
                            <div class="mt-2 opacity-75">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" height="20" class="mx-1">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" height="20" class="mx-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="bx bx-cart text-muted mb-3" style="font-size: 4rem;"></i>
            <h4>Your cart is currently empty</h4>
            <p class="text-muted">Before proceed to checkout you must add some products to your shopping cart.</p>
            <a href="{{ route('store.shop') }}" class="btn btn-primary px-4 py-2 mt-2 rounded-pill fw-bold">Return to Shop</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function updateCart(id, qty) {
        $.post("{{ route('store.cart.update') }}", { _token: "{{ csrf_token() }}", cart_id: id, quantity: qty }, function(res) {
            if(res.status === 'success') {
                window.location.reload();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        });
    }

    $('.qty-btn-minus').click(function() {
        let id = $(this).data('id');
        let input = $('#qty-' + id);
        let val = parseInt(input.val()) - 1;
        if (val > 0) updateCart(id, val);
    });

    $('.qty-btn-plus').click(function() {
        let id = $(this).data('id');
        let max = parseInt($(this).data('max'));
        let input = $('#qty-' + id);
        let val = parseInt(input.val()) + 1;
        if (val <= max) {
            updateCart(id, val);
        } else {
            Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Max stock reached', showConfirmButton: false, timer: 2000 });
        }
    });

    $('.remove-cart-btn').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Remove item?',
            text: "Are you sure you want to remove this from your cart?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('store.cart.remove') }}", { _token: "{{ csrf_token() }}", cart_id: id }, function(res) {
                    if(res.status === 'success') {
                        window.location.reload();
                    }
                });
            }
        });
    });
</script>
@endpush
