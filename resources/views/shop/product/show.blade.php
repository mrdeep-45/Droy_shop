<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>{{ $page_title }} — DPShop</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
<div class="container-fluid pt-3">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-show me-2"></i>{{ $page_title }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('shop.product.edit', $product->product_id) }}" class="btn btn-primary shadow-sm">
                    <i class="bx bx-edit me-1"></i> Edit Product
                </a>
                <a href="{{ route('shop.product.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Primary Image -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4 text-center">
                    @php $primaryImg = $images->where('is_primary', 1)->first(); @endphp
                    @if($primaryImg)
                        <img src="{{ asset('storage/' . $primaryImg->image_path) }}" class="img-fluid rounded border shadow-sm" style="max-height:300px; object-fit:contain;">
                    @elseif($images->count() > 0)
                        <img src="{{ asset('storage/' . $images->first()->image_path) }}" class="img-fluid rounded border shadow-sm" style="max-height:300px; object-fit:contain;">
                    @else
                        <div class="bg-light rounded border py-5 text-muted">
                            <i class="bx bx-image-alt" style="font-size:4rem;"></i>
                            <p class="mb-0 mt-2">No Image Available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Other Images -->
            @if($images->count() > 1)
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-images me-1"></i> Gallery</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($images as $img)
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="rounded border shadow-sm {{ $img->is_primary ? 'border-primary border-2' : '' }}" style="width:60px; height:60px; object-fit:cover;">
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-1">{{ $product->product_name }}</h3>
                    <p class="text-muted mb-3">{{ $product->product_code }} | SKU: {{ $product->sku ?? 'N/A' }}</p>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($product->is_featured) <span class="badge bg-warning text-dark fs-6 px-3 py-2">Featured</span> @endif
                        @if($product->is_bestseller) <span class="badge bg-danger fs-6 px-3 py-2">Bestseller</span> @endif
                        @if($product->is_new_arrival) <span class="badge bg-info fs-6 px-3 py-2">New Arrival</span> @endif
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="bg-light rounded p-3 text-center border">
                                <h6 class="text-muted mb-1 fs-7">Selling Price</h6>
                                <h4 class="mb-0 fw-bold text-success">₹{{ number_format($product->selling_price, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="bg-light rounded p-3 text-center border">
                                <h6 class="text-muted mb-1 fs-7">Base Price (MRP)</h6>
                                <h4 class="mb-0 fw-bold text-secondary">₹{{ number_format($product->base_price, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="bg-light rounded p-3 text-center border">
                                <h6 class="text-muted mb-1 fs-7">Stock Qty</h6>
                                <h4 class="mb-0 fw-bold {{ $product->stock_qty > 0 ? 'text-primary' : 'text-danger' }}">{{ $product->stock_qty }}</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="bg-light rounded p-3 text-center border">
                                <h6 class="text-muted mb-1 fs-7">Views</h6>
                                <h4 class="mb-0 fw-bold text-info">{{ $product->view_count }}</h4>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">Product Details</h6>
                    
                    <div class="row mb-2">
                        <div class="col-sm-3 text-muted fw-semibold">Category</div>
                        <div class="col-sm-9">{{ DB::table('mst_shop_categories')->where('category_id', $product->category_id)->value('category_name') ?? '—' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-3 text-muted fw-semibold">Brand</div>
                        <div class="col-sm-9">{{ DB::table('mst_shop_brands')->where('brand_id', $product->brand_id)->value('brand_name') ?? '—' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-3 text-muted fw-semibold">Short Description</div>
                        <div class="col-sm-9">{{ $product->short_description ?? '—' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-3 text-muted fw-semibold">Long Description</div>
                        <div class="col-sm-9 text-break">{!! nl2br(e($product->long_description)) ?? '—' !!}</div>
                    </div>

                    <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3 mt-4">SEO Details</h6>
                    <div class="row mb-2">
                        <div class="col-sm-3 text-muted fw-semibold">Meta Title</div>
                        <div class="col-sm-9">{{ $product->meta_title ?? '—' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-3 text-muted fw-semibold">Meta Keywords</div>
                        <div class="col-sm-9">{{ $product->meta_keywords ?? '—' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-3 text-muted fw-semibold">Meta Description</div>
                        <div class="col-sm-9">{{ $product->meta_description ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
</body>
</html>
