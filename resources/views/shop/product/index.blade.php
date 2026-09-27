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
            <h4 class="mb-0 text-uppercase fw-bold text-dark"><i class="bx bx-box me-2"></i>{{ $page_title }}</h4>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bx bx-plus-circle me-1"></i> Add New Product
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="productTable" class="table table-hover table-striped align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product Details</th>
                            <th>Category/Brand</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bx bx-plus me-1"></i> Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-danger d-none" id="productError"></div>
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand</label>
                            <select class="form-select select2" name="brand_id">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->brand_id }}">{{ $brand->brand_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Base Price (MRP) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="base_price" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Selling Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="selling_price" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Initial Stock Qty</label>
                            <input type="number" class="form-control" name="stock_qty" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit</label>
                            <select class="form-select select2" name="shop_unit_id">
                                <option value="">Select Unit</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->shop_unit_id }}">{{ $u->unit_name }} ({{ $u->unit_short }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Product Images</label>
                            <input type="file" class="form-control" name="product_images[]" multiple accept="image/*">
                            <small class="text-muted">You can select multiple images.</small>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="isActiveCheck">
                                <label class="form-check-label" for="isActiveCheck">Active (Visible on Store)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveProductBtn">Save & Continue to Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
<script>
$(document).ready(function() {
    $('.select2').select2({ dropdownParent: $('#addProductModal'), width: '100%' });

    var table = $('#productTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: "{{ route('shop.product.list') }}" },
        order: [[0, 'desc']],
        columns: [
            { className: 'text-center' }, null, null, null, { className: 'text-center' }, { className: 'text-center' }, { className: 'text-center' }
        ]
    });

    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        $('#productError').addClass('d-none');
        let btn = $('#saveProductBtn');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Saving...');
        
        let formData = new FormData(this);
        
        $.ajax({
            url: "{{ route('shop.product.store') }}",
            type: "POST",
            data: formData,
            contentType: false, processData: false,
            success: function(r) {
                if (r.status === 'success') {
                    $('#addProductModal').modal('hide');
                    $('#productForm')[0].reset();
                    table.ajax.reload();
                    Swal.fire('Success', r.message, 'success').then(() => {
                        window.location.href = '/shop/product/edit/' + r.product_id; // Redirect to full edit page
                    });
                } else {
                    $('#productError').removeClass('d-none').text(r.message);
                }
                btn.prop('disabled', false).html('Save & Continue to Edit');
            },
            error: function(xhr) {
                $('#productError').removeClass('d-none').text(xhr.responseJSON?.message || 'Error occurred');
                btn.prop('disabled', false).html('Save & Continue to Edit');
            }
        });
    });

    $(document).on('click', '.deleteProductBtn', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Delete Product?',
            text: "This will move the product to trash.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.product.delete') }}", { _token: "{{ csrf_token() }}", product_id: id }, function(r) {
                    if (r.status === 'success') {
                        table.ajax.reload();
                        Swal.fire('Deleted!', r.message, 'success');
                    } else {
                        Swal.fire('Error', r.message, 'error');
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
