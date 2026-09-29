@extends('store.layouts.app')
@section('title', $product->product_name . ' | HexaShop')

@push('styles')
<style>
    .product-gallery {
        position: relative;
    }
    .main-image {
        width: 100%;
        aspect-ratio: 1/1;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #dee2e6;
        cursor: zoom-in;
    }
    .thumb-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border 0.2s;
    }
    .thumb-image:hover, .thumb-image.active {
        border-color: #0d6efd;
    }
    .variant-btn {
        border: 1px solid #dee2e6;
        background: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    .variant-btn:hover {
        border-color: #0d6efd;
    }
    .variant-input:checked + .variant-btn {
        border-color: #0d6efd;
        background: #0d6efd;
        color: #fff;
    }
    .qty-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #dee2e6;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .qty-btn:hover {
        background: #f8f9fa;
    }
    .qty-input {
        width: 60px;
        text-align: center;
        border: none;
        font-weight: 600;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('store.home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('store.shop') }}" class="text-decoration-none text-muted">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
        </ol>
    </nav>

    <div class="row g-5 bg-white p-4 p-md-5 rounded-4 shadow-sm mb-5">
        <!-- Gallery -->
        <div class="col-md-6">
            <div class="product-gallery">
                @if(count($images) > 0)
                    <img src="{{ asset($images[0]->image_path) }}" id="mainImage" class="main-image mb-3">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($images as $img)
                            <img src="{{ asset($img->image_path) }}" class="thumb-image {{ $loop->first ? 'active' : '' }}" onclick="changeImage(this, '{{ asset($img->image_path) }}')">
                        @endforeach
                    </div>
                @else
                    <div class="main-image d-flex align-items-center justify-content-center bg-light text-muted mb-3">
                        <i class="bx bx-image" style="font-size: 5rem;"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Details -->
        <div class="col-md-6">
            @if($product->is_new_arrival)
                <span class="badge bg-success mb-2 px-3 py-2 rounded-pill">New Arrival</span>
            @endif
            
            <h1 class="fw-bold mb-2">{{ $product->product_name }}</h1>
            <p class="text-muted mb-4">SKU: {{ $product->sku ?? 'N/A' }} | Views: {{ $product->view_count }}</p>
            
            <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                <span class="fs-1 fw-bold text-primary">₹{{ number_format($product->selling_price, 2) }}</span>
                @if($product->base_price > $product->selling_price)
                    <span class="fs-4 text-muted text-decoration-line-through">₹{{ number_format($product->base_price, 2) }}</span>
                    <span class="badge bg-danger rounded-pill px-3 py-2 fs-6">-{{ (int)$product->discount_percent }}% OFF</span>
                @endif
            </div>

            @if($product->short_description)
                <p class="fs-5 text-secondary mb-4">{{ $product->short_description }}</p>
            @endif

            <form id="addToCartForm">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                
                @if($product->has_variants == 1 && count($variants) > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Available Variants:</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($variants as $v)
                            <div>
                                <input type="radio" name="variant_id" id="var_{{ $v->product_variant_id }}" value="{{ $v->product_variant_id }}" class="d-none variant-input" {{ $loop->first ? 'checked' : '' }}>
                                <label for="var_{{ $v->product_variant_id }}" class="variant-btn">
                                    {{ $v->variant_label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="d-flex flex-wrap gap-3 mb-4">
                    <div class="d-flex align-items-center border rounded-pill px-2 py-1 bg-white">
                        <button type="button" class="qty-btn border-0" onclick="updateQty(-1)"><i class="bx bx-minus"></i></button>
                        <input type="number" name="quantity" id="qtyInput" value="{{ $product->min_order_qty }}" min="{{ $product->min_order_qty }}" class="qty-input bg-transparent" readonly>
                        <button type="button" class="qty-btn border-0" onclick="updateQty(1)"><i class="bx bx-plus"></i></button>
                    </div>
                    
                    <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold flex-grow-1" id="addToCartBtn">
                        <i class="bx bx-cart-add me-2"></i> Add to Cart
                    </button>
                    
                    <button type="button" id="addToWishlistBtn" class="btn btn-outline-danger btn-lg rounded-pill px-4" title="Add to Wishlist">
                        <i class="bx bx-heart"></i>
                    </button>
                </div>
                <div class="text-success fw-semibold"><i class="bx bx-check-circle me-1"></i> In Stock ({{ $product->stock_qty }} available)</div>
            </form>
            
            <div class="mt-5 pt-4 border-top">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="bx bx-car fs-4 text-muted"></i>
                    <span class="text-secondary fw-semibold">Standard delivery within 3-5 business days.</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="bx bx-refresh fs-4 text-muted"></i>
                    <span class="text-secondary fw-semibold">Easy 14 days returns and exchanges.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Long Description -->
    @if($product->long_description)
    <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm mb-5">
        <h3 class="fw-bold mb-4">Product Details</h3>
        <div class="text-secondary lh-lg">
            {!! $product->long_description !!}
        </div>
    </div>
    @endif

    <!-- Related Products -->
    @if(count($relatedProducts) > 0)
    <div class="mt-5">
        <h3 class="fw-bold mb-4">You May Also Like</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card card h-100">
                    <a href="{{ route('store.product.detail', $p->product_slug) }}" class="product-img-wrap d-block">
                        @if($p->discount_percent > 0)
                            <span class="badge-discount">-{{ (int)$p->discount_percent }}%</span>
                        @endif
                        @if($p->primary_image)
                            <img src="{{ asset($p->primary_image) }}" alt="{{ $p->product_name }}">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                <i class="bx bx-image" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </a>
                    <div class="card-body p-3">
                        <a href="{{ route('store.product.detail', $p->product_slug) }}" class="product-title">{{ $p->product_name }}</a>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="price-wrap">
                                <span class="price-current">₹{{ number_format($p->selling_price, 2) }}</span>
                            </div>
                            <button class="btn btn-light rounded-circle text-primary shadow-sm" style="width:35px;height:35px;padding:0;">
                                <i class="bx bx-cart-add fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function changeImage(el, src) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-image').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function updateQty(change) {
        let input = document.getElementById('qtyInput');
        let min = parseInt(input.getAttribute('min')) || 1;
        let current = parseInt(input.value) || min;
        let newVal = current + change;
        if(newVal >= min) {
            input.value = newVal;
        }
    }

    $('#addToCartBtn').click(function() {
        let btn = $(this);
        let form = $('#addToCartForm').serialize();
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-2"></i> Adding...');
        
        $.post("{{ route('store.cart.add') }}", form, function(res) {
            if(res.status === 'success') {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500
                });
                // Update badge
                let count = parseInt($('#cartBadge').text()) || 0;
                let added = parseInt($('#qtyInput').val()) || 1;
                $('#cartBadge').text(count + added);
            } else {
                Swal.fire('Error', res.message, 'error');
            }
            btn.prop('disabled', false).html('<i class="bx bx-cart-add me-2"></i> Add to Cart');
        });
    $('#addToWishlistBtn').click(function() {
        let btn = $(this);
        let form = $('#addToCartForm').serialize();
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');
        
        $.post("{{ route('store.wishlist.add') }}", form, function(res) {
            if(res.status === 'success') {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 });
                btn.removeClass('btn-outline-danger').addClass('btn-danger');
            } else if(res.status === 'info') {
                Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: res.message, showConfirmButton: false, timer: 1500 });
            } else if(res.redirect) {
                window.location.href = res.redirect;
            } else {
                Swal.fire('Error', res.message, 'error');
            }
            btn.prop('disabled', false).html('<i class="bx bx-heart"></i>');
        });
    });
</script>
@endpush
