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
            <h4 class="mb-0 text-uppercase fw-bold text-dark"><i class="bx bx-cart-download me-2"></i>{{ $page_title }}</h4>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createPOModal">
                <i class="bx bx-plus-circle me-1"></i> Create Purchase Order
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="purchaseTable" class="table table-hover table-striped align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create PO Modal -->
<div class="modal fade" id="createPOModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bx bx-plus me-1"></i> Create Purchase Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="purchaseForm">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-danger d-none" id="poError"></div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->supplier_id }}">{{ $s->supplier_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="purchase_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="purchase_status">
                                <option value="ordered">Ordered (Awaiting Delivery)</option>
                                <option value="received">Received (Update Stock Instantly)</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">Order Items</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn"><i class="bx bx-plus"></i> Add Item</button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%">Raw Material</th>
                                        <th>Quantity</th>
                                        <th>Unit Price (₹)</th>
                                        <th>Total (₹)</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows added via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes / Terms</label>
                            <textarea class="form-control" name="notes" rows="4" placeholder="Terms of delivery, payment conditions, etc."></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-white p-3 rounded border shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-semibold text-muted">Subtotal:</span>
                                    <span class="fw-bold" id="lbl_subtotal">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 align-items-center">
                                    <span class="fw-semibold text-muted">Discount (₹):</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm text-end w-50" name="discount_amount" id="in_discount" value="0">
                                </div>
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <span class="fw-semibold text-muted">Shipping (₹):</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm text-end w-50" name="shipping_charge" id="in_shipping" value="0">
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold fs-5 text-dark">Total Amount:</span>
                                    <span class="fw-bold fs-5 text-primary" id="lbl_total">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="savePOBtn">Create PO</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
<script>
$(document).ready(function() {
    $('.select2').select2({ dropdownParent: $('#createPOModal'), width: '100%' });

    var table = $('#purchaseTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: "{{ route('shop.purchase.list') }}" },
        order: [[0, 'desc']],
        columns: [
            { className: 'text-center' }, null, null, null, null, null, { className: 'text-center' }
        ]
    });

    let itemIndex = 0;
    
    // Raw Materials options string for select
    const rmOptions = `<option value="">Select Material</option>
        @foreach($rawMaterials as $rm)
            <option value="{{ $rm->raw_material_id }}" data-price="{{ $rm->cost_price }}">{{ $rm->material_name }} ({{ $rm->material_code }})</option>
        @endforeach`;

    function addRow() {
        let tr = `<tr>
            <td><select class="form-select rm-select" name="items[${itemIndex}][raw_material_id]" required>${rmOptions}</select></td>
            <td><input type="number" step="0.01" class="form-control qty-input" name="items[${itemIndex}][quantity]" required min="0.01"></td>
            <td><input type="number" step="0.01" class="form-control price-input" name="items[${itemIndex}][unit_price]" required min="0"></td>
            <td class="row-total fw-bold text-end align-middle">₹0.00</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bx bx-trash"></i></button></td>
        </tr>`;
        $('#itemsTable tbody').append(tr);
        itemIndex++;
    }

    $('#addItemBtn').click(addRow);
    
    // Auto fill price on select
    $(document).on('change', '.rm-select', function() {
        let price = $(this).find('option:selected').data('price') || 0;
        $(this).closest('tr').find('.price-input').val(price);
        calculateTotal();
    });

    $(document).on('input', '.qty-input, .price-input, #in_discount, #in_shipping', function() {
        calculateTotal();
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    function calculateTotal() {
        let subtotal = 0;
        $('#itemsTable tbody tr').each(function() {
            let q = parseFloat($(this).find('.qty-input').val()) || 0;
            let p = parseFloat($(this).find('.price-input').val()) || 0;
            let rowTot = q * p;
            subtotal += rowTot;
            $(this).find('.row-total').text('₹' + rowTot.toFixed(2));
        });
        
        let discount = parseFloat($('#in_discount').val()) || 0;
        let shipping = parseFloat($('#in_shipping').val()) || 0;
        let grandTotal = (subtotal - discount) + shipping;
        
        $('#lbl_subtotal').text('₹' + subtotal.toFixed(2));
        $('#lbl_total').text('₹' + grandTotal.toFixed(2));
    }

    // Initialize with one row
    $('#createPOModal').on('shown.bs.modal', function() {
        if($('#itemsTable tbody tr').length === 0) addRow();
    });

    $('#purchaseForm').on('submit', function(e) {
        e.preventDefault();
        if($('#itemsTable tbody tr').length === 0) {
            Swal.fire('Error', 'Please add at least one item', 'error');
            return;
        }

        $('#poError').addClass('d-none');
        let btn = $('#savePOBtn');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Creating...');
        
        $.post("{{ route('shop.purchase.store') }}", $(this).serialize(), function(r) {
            if(r.status === 'success') {
                $('#createPOModal').modal('hide');
                table.ajax.reload(null, false);
                Swal.fire('Success', r.message, 'success').then(() => {
                    window.location.href = "{{ url('shop/purchase/show') }}/" + r.id;
                });
            } else {
                $('#poError').removeClass('d-none').text(r.message);
                btn.prop('disabled', false).html('Create PO');
            }
        }).fail(function(xhr) {
            $('#poError').removeClass('d-none').text(xhr.responseJSON?.message || 'Error occurred');
            btn.prop('disabled', false).html('Create PO');
        });
    });
});
</script>
</body>
</html>
