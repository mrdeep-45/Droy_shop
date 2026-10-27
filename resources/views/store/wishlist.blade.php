@extends('store.layouts.app')
@section('title', 'My Wishlist | DPShop')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">My Wishlist <i class="bx bxs-heart text-danger ms-2"></i></h2>

    @if(count($wishlist) > 0)
        <div class="row g-4">
            @foreach($wishlist as $item)
            <div class="col-6 col-md-4 col-lg-3" id="wishlist-item-{{ $item->wishlist_id }}">
                <div class="product-card card h-100 position-relative">
                    <button class="btn btn-light rounded-circle position-absolute text-danger shadow-sm remove-wishlist" 
                            data-id="{{ $item->wishlist_id }}" 
                            style="top:10px; right:10px; z-index:10; width:35px; height:35px; padding:0;">
                        <i class="bx bx-trash"></i>
                    </button>
                    
                    <a href="{{ route('store.product.detail', $item->product_slug) }}" class="product-img-wrap d-block">
                        @if($item->primary_image)
                            <img src="{{ asset($item->primary_image) }}" alt="{{ $item->product_name }}">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                                <i class="bx bx-image" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </a>
                    
                    <div class="card-body p-3 text-center">
                        <a href="{{ route('store.product.detail', $item->product_slug) }}" class="product-title">{{ $item->product_name }}</a>
                        <div class="fw-bold text-primary mb-3 fs-5">₹{{ number_format($item->selling_price, 2) }}</div>
                        
                        <button class="btn btn-outline-primary w-100 fw-semibold add-to-cart-from-wishlist" data-pid="{{ $item->product_id }}">
                            <i class="bx bx-cart-add me-1"></i> Move to Cart
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="bx bx-heart text-muted mb-3" style="font-size: 4rem;"></i>
            <h4>Your wishlist is empty</h4>
            <p class="text-muted">Save items you love here and buy them later.</p>
            <a href="{{ route('store.shop') }}" class="btn btn-primary px-4 py-2 mt-2 rounded-pill fw-bold">Explore Products</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $('.remove-wishlist').click(function() {
        let id = $(this).data('id');
        let card = $('#wishlist-item-' + id);
        
        $.post("{{ route('store.wishlist.remove') }}", { _token: "{{ csrf_token() }}", wishlist_id: id }, function(res) {
            if(res.status === 'success') {
                card.fadeOut(300, function() { $(this).remove(); });
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 1500
                });
            }
        });
    });

    $('.add-to-cart-from-wishlist').click(function() {
        let pid = $(this).data('pid');
        let btn = $(this);
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');
        
        $.post("{{ route('store.cart.add') }}", { _token: "{{ csrf_token() }}", product_id: pid, quantity: 1 }, function(res) {
            if(res.status === 'success') {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success', title: 'Added to cart!', showConfirmButton: false, timer: 1500
                });
                
                // Update badge
                let count = parseInt($('#cartBadge').text()) || 0;
                $('#cartBadge').text(count + 1);
            } else {
                Swal.fire('Error', res.message, 'error');
            }
            btn.prop('disabled', false).html('<i class="bx bx-cart-add me-1"></i> Move to Cart');
        });
    });
</script>
@endpush
