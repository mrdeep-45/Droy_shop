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
            <h4 class="mb-0 text-uppercase fw-bold text-dark"><i class="bx bx-store-alt me-2"></i>{{ $page_title }}</h4>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="inventoryTable" class="table table-hover table-striped align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Variants</th>
                            <th>Stock Qty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bx bx-slider-alt me-1"></i> Adjust Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustStockForm">
                @csrf
                <input type="hidden" name="product_id" id="adj_product_id">
                <div class="modal-body p-4">
                    <div class="alert alert-danger d-none" id="adjError"></div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted mb-0">Product</label>
                        <h5 class="fw-bold text-dark" id="adj_product_name"></h5>
                        <p class="text-primary mb-0 fw-semibold">Current Stock: <span id="adj_current_stock">0</span></p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Adjustment Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="adjustment_type" required>
                                <option value="add">Add (+)</option>
                                <option value="deduct">Deduct (-)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Quantity to Adjust <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="quantity" required min="1" step="1">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Notes / Reason</label>
                            <input type="text" class="form-control" name="notes" placeholder="e.g. Stock Count, Damage, Returned">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveAdjBtn">Confirm Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
<script>
$(document).ready(function() {
    var table = $('#inventoryTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: "{{ route('shop.inventory.list') }}" },
        order: [[0, 'desc']],
        columns: [
            { className: 'text-center' }, null, null, { className: 'text-center' }, { className: 'text-center' }, { className: 'text-center' }
        ]
    });

    $(document).on('click', '.adjustStockBtn', function() {
        $('#adjError').addClass('d-none');
        $('#adjustStockForm')[0].reset();
        
        let id = $(this).data('id');
        let name = $(this).data('name');
        let stock = $(this).data('stock');
        
        $('#adj_product_id').val(id);
        $('#adj_product_name').text(name);
        $('#adj_current_stock').text(stock);
        
        $('#adjustStockModal').modal('show');
    });

    $('#adjustStockForm').on('submit', function(e) {
        e.preventDefault();
        $('#adjError').addClass('d-none');
        let btn = $('#saveAdjBtn');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Saving...');
        
        $.post("{{ route('shop.inventory.adjust') }}", $(this).serialize(), function(r) {
            if(r.status === 'success') {
                $('#adjustStockModal').modal('hide');
                table.ajax.reload(null, false); // false to retain paging
                Swal.fire('Success', r.message, 'success');
            } else {
                $('#adjError').removeClass('d-none').text(r.message);
            }
            btn.prop('disabled', false).html('Confirm Adjustment');
        }).fail(function(xhr) {
            $('#adjError').removeClass('d-none').text(xhr.responseJSON?.message || 'Error occurred');
            btn.prop('disabled', false).html('Confirm Adjustment');
        });
    });
});
</script>
</body>
</html>
