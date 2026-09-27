<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>{{ $page_title }} — HexaShop</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
<div class="container-fluid pt-3">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-edit me-2"></i>{{ $page_title }}</h4>
            <a href="{{ route('shop.product.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form id="productEditForm">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-info-circle me-1"></i> General Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-danger d-none" id="editError"></div>
                        <div class="alert alert-success d-none" id="editSuccess"></div>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="product_name" value="{{ $product->product_name }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select select2" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->category_id }}" {{ $product->category_id == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand</label>
                                <select class="form-select select2" name="brand_id">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->brand_id }}" {{ $product->brand_id == $brand->brand_id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Base Price (MRP) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control" name="base_price" value="{{ $product->base_price }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control" name="selling_price" value="{{ $product->selling_price }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control" name="cost_price" value="{{ $product->cost_price }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_qty" value="{{ $product->stock_qty }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" class="form-control" name="sku" value="{{ $product->sku }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Short Description</label>
                                <textarea class="form-control" name="short_description" rows="2">{{ $product->short_description }}</textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Long Description</label>
                                <textarea class="form-control" id="long_description" name="long_description" rows="4">{{ $product->long_description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-search-alt me-1"></i> SEO Meta Tags</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title" value="{{ $product->meta_title }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords" value="{{ $product->meta_keywords }}" placeholder="Comma separated">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="2">{{ $product->meta_description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variants Section -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-layer me-1"></i> Product Variants</h6>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input updateFlag" type="checkbox" data-field="has_variants" {{ $product->has_variants ? 'checked' : '' }} id="hasVariantsCheck">
                            <label class="form-check-label" for="hasVariantsCheck">Enable Variants</label>
                        </div>
                    </div>
                    
                    @if($product->has_variants)
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Manage combinations like Size, Color, etc. (e.g. Red / XL). You can set specific price overrides and stock quantities for each variant.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle" id="variantsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Variant Label</th>
                                        <th>SKU</th>
                                        <th>Price (₹)</th>
                                        <th>Stock Qty</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $variantsList = DB::table('tbl_shop_product_variants')->where('product_id', $product->product_id)->get(); @endphp
                                    @foreach($variantsList as $v)
                                    <tr>
                                        <td>{{ $v->variant_label }}</td>
                                        <td>{{ $v->sku ?? '—' }}</td>
                                        <td>{{ $v->price_override ? '₹' . number_format($v->price_override, 2) : 'Default' }}</td>
                                        <td>{{ $v->stock_qty }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-outline-danger deleteVariantBtn" data-id="{{ $v->product_variant_id }}"><i class="bx bx-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr id="addVariantRow">
                                        <td><input type="text" class="form-control form-control-sm" id="var_label" placeholder="e.g. Red / XL"></td>
                                        <td><input type="text" class="form-control form-control-sm" id="var_sku" placeholder="SKU"></td>
                                        <td><input type="number" step="0.01" class="form-control form-control-sm" id="var_price" placeholder="Override Price (Optional)"></td>
                                        <td><input type="number" class="form-control form-control-sm" id="var_stock" value="0"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-primary" id="saveVariantBtn"><i class="bx bx-plus"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="card-body p-3 text-center text-muted">
                        <i class="bx bx-layer fs-3 mb-2"></i><br>
                        Enable variants to manage sizes, colors, and stock for different options.
                    </div>
                    @endif
                </div>

                <div class="d-flex gap-2 mb-5">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" id="updateBtn">
                        <i class="bx bx-save me-1"></i> Update Product
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar for Images & Status -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-image me-1"></i> Product Images</h6>
                </div>
                <div class="card-body p-4">
                    <form id="imageUploadForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <!-- Required fields for store method since we reuse it -->
                        <input type="hidden" name="product_name" value="{{ $product->product_name }}">
                        <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                        <input type="hidden" name="base_price" value="{{ $product->base_price }}">
                        <input type="hidden" name="selling_price" value="{{ $product->selling_price }}">

                        <div class="mb-3">
                            <input type="file" class="form-control" name="product_images[]" multiple accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100 mb-3" id="imgUploadBtn">
                            <i class="bx bx-upload me-1"></i> Upload Images
                        </button>
                    </form>

                    <div class="row g-2 mt-2" id="imageGallery">
                        @forelse($images as $img)
                            <div class="col-6 position-relative">
                                <img src="{{ asset('storage/' . $img->image_path) }}" class="img-fluid rounded border shadow-sm {{ $img->is_primary ? 'border-primary border-2' : '' }}" style="height:120px; object-fit:cover; width:100%;">
                                @if($img->is_primary)
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-1" style="font-size:0.6rem;">Primary</span>
                                @endif
                                <div class="position-absolute bottom-0 end-0 m-1 d-flex gap-1">
                                    @if(!$img->is_primary)
                                        <button class="btn btn-xs btn-success setPrimaryBtn p-1" data-id="{{ $img->image_id }}" title="Set Primary"><i class="bx bx-check"></i></button>
                                    @endif
                                    <button class="btn btn-xs btn-danger deleteImgBtn p-1" data-id="{{ $img->image_id }}" title="Delete"><i class="bx bx-trash"></i></button>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-3 bg-light rounded border">
                                <i class="bx bx-image-alt fs-2"></i><br><small>No images uploaded.</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-toggle-right me-1"></i> Status & Flags</h6>
                </div>
                <div class="card-body p-4">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input updateFlag" type="checkbox" data-field="is_active" {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active (Visible)</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input updateFlag" type="checkbox" data-field="is_featured" {{ $product->is_featured ? 'checked' : '' }}>
                        <label class="form-check-label">Featured Product</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input updateFlag" type="checkbox" data-field="is_new_arrival" {{ $product->is_new_arrival ? 'checked' : '' }}>
                        <label class="form-check-label">New Arrival</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input updateFlag" type="checkbox" data-field="is_bestseller" {{ $product->is_bestseller ? 'checked' : '' }}>
                        <label class="form-check-label">Bestseller</label>
                    </div>
                    <small class="text-muted mt-3 d-block"><i class="bx bx-info-circle"></i> Toggle flags to update them instantly.</small>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    $('#productEditForm').on('submit', function(e) {
        e.preventDefault();
        $('#editError, #editSuccess').addClass('d-none');
        let btn = $('#updateBtn');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Updating...');
        
        $.ajax({
            url: "{{ route('shop.product.store') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false, processData: false,
            success: function(r) {
                if (r.status === 'success') {
                    $('#editSuccess').removeClass('d-none').text(r.message);
                    window.scrollTo(0,0);
                } else {
                    $('#editError').removeClass('d-none').text(r.message);
                }
                btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Update Product');
            },
            error: function(xhr) {
                $('#editError').removeClass('d-none').text(xhr.responseJSON?.message || 'Error occurred');
                btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Update Product');
            }
        });
    });

    $('#imageUploadForm').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#imgUploadBtn');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Uploading...');
        
        $.ajax({
            url: "{{ route('shop.product.store') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false, processData: false,
            success: function(r) {
                if(r.status === 'success') { location.reload(); }
                else { alert(r.message); btn.prop('disabled', false).html('<i class="bx bx-upload"></i> Upload'); }
            },
            error: function() {
                alert('Upload failed');
                btn.prop('disabled', false).html('<i class="bx bx-upload"></i> Upload');
            }
        });
    });

    $(document).on('click', '.deleteImgBtn', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        if(confirm('Are you sure you want to delete this image?')) {
            $.post("{{ route('shop.product.image.delete') }}", { _token: "{{ csrf_token() }}", image_id: id }, function(r) {
                if(r.status === 'success') location.reload();
            });
        }
    });

    $(document).on('click', '.setPrimaryBtn', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        $.post("{{ route('shop.product.image.primary') }}", { _token: "{{ csrf_token() }}", image_id: id }, function(r) {
            if(r.status === 'success') location.reload();
        });
    });

    $('.updateFlag').on('change', function() {
        let field = $(this).data('field');
        let isChecked = $(this).is(':checked') ? 1 : 0;
        
        let fd = new FormData($('#productEditForm')[0]);
        fd.append(field, isChecked);
        if(!isChecked) { fd.delete(field); }
        
        $.ajax({
            url: "{{ route('shop.product.store') }}",
            type: "POST", data: fd,
            contentType: false, processData: false,
            success: function(r) {
                if(r.status === 'success') {
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    Toast.fire({ icon: 'success', title: 'Flag updated!' });
                }
            }
        });
    });

    // Variants JS
    $('#saveVariantBtn').on('click', function() {
        let label = $('#var_label').val();
        let sku = $('#var_sku').val();
        let price = $('#var_price').val();
        let stock = $('#var_stock').val();

        if(!label) { alert('Variant label is required'); return; }

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');

        $.post("{{ route('shop.product.variant.store') }}", {
            _token: "{{ csrf_token() }}",
            product_id: "{{ $product->product_id }}",
            variant_label: label,
            sku: sku,
            price_override: price,
            stock_qty: stock
        }, function(r) {
            if(r.status === 'success') { location.reload(); }
            else { alert(r.message); btn.prop('disabled', false).html('<i class="bx bx-plus"></i>'); }
        }).fail(function() {
            alert('Failed to add variant');
            btn.prop('disabled', false).html('<i class="bx bx-plus"></i>');
        });
    });

    $(document).on('click', '.deleteVariantBtn', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        if(confirm('Delete this variant?')) {
            $.post("{{ route('shop.product.variant.delete') }}", { _token: "{{ csrf_token() }}", product_variant_id: id }, function(r) {
                if(r.status === 'success') location.reload();
            });
        }
    });
});
</script>
</body>
</html>
