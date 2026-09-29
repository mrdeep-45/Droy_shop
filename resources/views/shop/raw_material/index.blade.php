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
            <h4 class="mb-0 text-uppercase fw-bold text-dark"><i class="bx bx-cylinder me-2"></i>{{ $page_title }}</h4>
            <button class="btn btn-primary shadow-sm" id="addMaterialBtn">
                <i class="bx bx-plus-circle me-1"></i> Add New Material
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="materialTable" class="table table-hover table-striped align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Material Details</th>
                            <th>Default Supplier</th>
                            <th>Current Stock</th>
                            <th>Est. Cost Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Material Modal -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="materialModalLabel"><i class="bx bx-plus me-1"></i> Add Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="materialForm">
                @csrf
                <input type="hidden" name="raw_material_id" id="raw_material_id">
                <div class="modal-body p-4">
                    <div class="alert alert-danger d-none" id="matError"></div>
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Material Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="material_name" id="material_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Material Code</label>
                            <input type="text" class="form-control" name="material_code" id="material_code" placeholder="Auto-generated if empty">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit of Measurement</label>
                            <select class="form-select select2" name="shop_unit_id" id="shop_unit_id">
                                <option value="">Select Unit</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->shop_unit_id }}">{{ $u->unit_name }} ({{ $u->unit_short }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Default Supplier</label>
                            <select class="form-select select2" name="supplier_id" id="supplier_id">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->supplier_id }}">{{ $s->supplier_name }} ({{ $s->supplier_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Est. Cost Price</label>
                            <input type="number" step="0.01" class="form-control" name="cost_price" id="cost_price" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Min Stock Level</label>
                            <input type="number" step="0.01" class="form-control" name="min_stock_level" id="min_stock_level" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Stock Level</label>
                            <input type="number" step="0.01" class="form-control" name="max_stock_level" id="max_stock_level">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveMatBtn">Save Material</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
<script>
$(document).ready(function() {
    $('.select2').select2({ dropdownParent: $('#materialModal'), width: '100%' });

    var table = $('#materialTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: "{{ route('shop.rawmaterial.list') }}" },
        order: [[0, 'desc']],
        columns: [
            { className: 'text-center' }, null, null, { className: 'text-center' }, { className: 'text-center' }, { className: 'text-center' }
        ]
    });

    $('#addMaterialBtn').on('click', function() {
        $('#matError').addClass('d-none');
        $('#materialForm')[0].reset();
        $('#raw_material_id').val('');
        $('#shop_unit_id, #supplier_id').val('').trigger('change');
        $('#materialModalLabel').html('<i class="bx bx-plus me-1"></i> Add Material');
        $('#materialModal').modal('show');
    });

    $(document).on('click', '.editMaterialBtn', function() {
        let id = $(this).data('id');
        $.get("{{ url('shop/rawmaterial/edit') }}/" + id, function(r) {
            if(r.status === 'success') {
                $('#matError').addClass('d-none');
                $('#materialForm')[0].reset();
                $('#raw_material_id').val(r.data.raw_material_id);
                $('#material_name').val(r.data.material_name);
                $('#material_code').val(r.data.material_code);
                $('#cost_price').val(r.data.cost_price);
                $('#min_stock_level').val(r.data.min_stock_level);
                $('#max_stock_level').val(r.data.max_stock_level);
                $('#description').val(r.data.description);
                $('#shop_unit_id').val(r.data.shop_unit_id).trigger('change');
                $('#supplier_id').val(r.data.supplier_id).trigger('change');
                
                $('#materialModalLabel').html('<i class="bx bx-edit me-1"></i> Edit Material');
                $('#materialModal').modal('show');
            }
        });
    });

    $('#materialForm').on('submit', function(e) {
        e.preventDefault();
        $('#matError').addClass('d-none');
        let btn = $('#saveMatBtn');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Saving...');
        
        $.post("{{ route('shop.rawmaterial.store') }}", $(this).serialize(), function(r) {
            if(r.status === 'success') {
                $('#materialModal').modal('hide');
                table.ajax.reload(null, false);
                Swal.fire('Success', r.message, 'success');
            } else {
                $('#matError').removeClass('d-none').text(r.message);
            }
            btn.prop('disabled', false).html('Save Material');
        }).fail(function(xhr) {
            $('#matError').removeClass('d-none').text(xhr.responseJSON?.message || 'Error occurred');
            btn.prop('disabled', false).html('Save Material');
        });
    });

    $(document).on('click', '.deleteMaterialBtn', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        Swal.fire({
            title: 'Delete Material?',
            text: "Are you sure you want to delete '" + name + "'?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.rawmaterial.delete') }}", { _token: "{{ csrf_token() }}", raw_material_id: id }, function(r) {
                    if (r.status === 'success') {
                        table.ajax.reload(null, false);
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
