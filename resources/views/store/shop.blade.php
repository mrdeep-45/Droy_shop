@extends('store.layouts.app')
@section('title', isset($category) ? $category->category_name . ' | DPShop' : 'Shop All Products | DPShop')

@section('content')

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('store.home') }}" class="text-decoration-none text-muted">Home</a></li>
            @if(isset($category))
                <li class="breadcrumb-item"><a href="{{ route('store.shop') }}" class="text-decoration-none text-muted">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category->category_name }}</li>
            @else
                <li class="breadcrumb-item active" aria-current="page">Shop</li>
            @endif
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Categories</h5>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                        <li>
                            <a href="{{ route('store.shop') }}" class="text-decoration-none {{ !isset($category) ? 'fw-bold text-primary' : 'text-dark' }}">
                                All Products
                            </a>
                        </li>
                        @foreach($categories as $c)
                        <li>
                            <a href="{{ route('store.category', $c->category_slug) }}" class="text-decoration-none {{ (isset($category) && $category->category_id == $c->category_id) ? 'fw-bold text-primary' : 'text-dark' }}">
                                {{ $c->category_name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">
                    @if(isset($category))
                        {{ $category->category_name }}
                    @elseif(request()->has('search'))
                        Search results for "{{ request()->search }}"
                    @else
                        All Products
                    @endif
                </h3>
                <div class="text-muted">
                    Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results
                </div>
            </div>

            <div class="row g-4 mb-4">
                @forelse($products as $p)
                <div class="col-6 col-md-4">
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
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bx bx-package text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                    <a href="{{ route('store.shop') }}" class="btn btn-primary mt-2">Clear Filters</a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection
