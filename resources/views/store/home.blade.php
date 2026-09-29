@extends('store.layouts.app')
@section('title', 'Welcome to HexaShop | Premium Garments')

@section('content')

<!-- Hero Section -->
<section class="hero-section text-center text-md-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-5 mb-md-0">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 fw-semibold">New Collection 2026</span>
                <h1 class="hero-title">Elevate Your<br>Everyday Style.</h1>
                <p class="hero-subtitle">Discover premium quality garments designed for comfort, durability, and timeless fashion. Shop the latest trends today.</p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-md-start">
                    <a href="{{ route('store.shop') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">Shop Now</a>
                    <a href="#categories" class="btn btn-outline-secondary btn-lg rounded-pill px-4 fw-semibold bg-white">Explore Categories</a>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <!-- A premium generic lifestyle placeholder from Unsplash -->
                <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Fashion Model" class="img-fluid rounded-4 shadow-lg" style="object-fit: cover; max-height: 500px;">
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <i class="bx bx-package text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold mb-1">Free Shipping</h6>
                <p class="text-muted small mb-0">On orders over ₹999</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bx bx-check-shield text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold mb-1">Secure Payment</h6>
                <p class="text-muted small mb-0">100% secure checkout</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bx bx-refresh text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold mb-1">Easy Returns</h6>
                <p class="text-muted small mb-0">14-day return policy</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bx bx-support text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold mb-1">24/7 Support</h6>
                <p class="text-muted small mb-0">Dedicated support team</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section id="categories" class="py-5 mt-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Shop by Category</h3>
                <p class="text-muted mb-0">Find exactly what you're looking for</p>
            </div>
            <a href="{{ route('store.shop') }}" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3">View All <i class="bx bx-right-arrow-alt"></i></a>
        </div>
        
        <div class="row g-4">
            @forelse($categories as $cat)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('store.category', $cat->category_slug) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center h-100 cat-card" style="border-radius: 12px; transition: transform 0.3s;">
                        <div class="card-body p-4">
                            @if($cat->category_image)
                                <img src="{{ asset($cat->category_image) }}" alt="{{ $cat->category_name }}" class="mb-3" style="width:60px;height:60px;object-fit:cover;border-radius:50%;">
                            @else
                                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary mb-3 rounded-circle" style="width:60px;height:60px;font-size:24px;">
                                    <i class="bx bx-closet"></i>
                                </div>
                            @endif
                            <h6 class="fw-semibold text-dark mb-0">{{ $cat->category_name }}</h6>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">No categories found.</div>
            @endforelse
        </div>
    </div>
</section>
<style>
    .cat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; border: 1px solid #0d6efd; }
</style>

<!-- New Arrivals -->
@if(count($newArrivals) > 0)
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold mb-2">New Arrivals</h3>
            <p class="text-muted">The latest additions to our collection.</p>
        </div>
        
        <div class="row g-4">
            @foreach($newArrivals as $p)
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
                                @if($p->base_price > $p->selling_price)
                                    <span class="price-old">₹{{ number_format($p->base_price, 2) }}</span>
                                @endif
                            </div>
                            <button class="btn btn-light rounded-circle text-primary shadow-sm" style="width:35px;height:35px;padding:0;" title="Add to Cart">
                                <i class="bx bx-cart-add fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if(count($featuredProducts) > 0)
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold mb-2">Featured Products</h3>
            <p class="text-muted">Handpicked favorites just for you.</p>
        </div>
        
        <div class="row g-4">
            @foreach($featuredProducts as $p)
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
                                @if($p->base_price > $p->selling_price)
                                    <span class="price-old">₹{{ number_format($p->base_price, 2) }}</span>
                                @endif
                            </div>
                            <button class="btn btn-light rounded-circle text-primary shadow-sm" style="width:35px;height:35px;padding:0;" title="Add to Cart">
                                <i class="bx bx-cart-add fs-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
