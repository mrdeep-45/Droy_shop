<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Suppliers — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-store-alt text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb"><a href="{{ route('shop.dashboard') }}">Home</a> / Inventory / <span>Suppliers</span></div>
        </div>
        <button class="btn btn-primary" id="addSupplierBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add Supplier
        </button>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="shop-card py-3 px-3 text-center" id="statTotal">
                <div class="fw-bold" style="font-size:1.5rem;color:#0d6efd;" id="statTotalVal">—</div>
                <div class="text-muted" style="font-size:0.78rem;">Total Suppliers</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card py-3 px-3 text-center">
                <div class="fw-bold" style="font-size:1.5rem;color:#20c997;" id="statActiveVal">—</div>
                <div class="text-muted" style="font-size:0.78rem;">Active</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card py-3 px-3 text-center">
                <div class="fw-bold" style="font-size:1.5rem;color:#fd7e14;" id="statPOVal">—</div>
                <div class="text-muted" style="font-size:0.78rem;">Total POs</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="shop-card py-3 px-3 text-center">
                <div class="fw-bold" style="font-size:1.5rem;color:#6f42c1;" id="statValueVal">—</div>
                <div class="text-muted" style="font-size:0.78rem;">Total Purchase Value</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="shop-card mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="fw-semibold" style="font-size:0.82rem;color:#4a5568;">Status:</span>
                <div class="tab-btn-group">
                    <button class="tab-btn active" data-filter="">All</button>
                    <button class="tab-btn" data-filter="1"><i class="bx bx-check-circle me-1"></i>Active</button>
                    <button class="tab-btn" data-filter="0"><i class="bx bx-x-circle me-1"></i>Inactive</button>
                </div>
            </div>
        </div>
    </div>

    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>Supplier List</span>
            <span class="badge bg-primary-subtle text-primary px-3 py-2" style="font-size:0.75rem;border-radius:20px;" id="supplierCount">Loading...</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="supplierTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Supplier / Code</th>
                            <th>Contact</th>
                            <th width="130">Phone</th>
                            <th width="160">Location</th>
                            <th width="150">Purchases</th>
                            <th width="100">Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- ══════ ADD / EDIT SUPPLIER MODAL ══════ --}}
<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="supplierModalLabel">
                    <i class="bx bx-store-alt me-2 text-primary"></i>Add New Supplier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="supplierForm">
                @csrf
                <input type="hidden" id="supplier_id" name="supplier_id">
                <div class="modal-body" style="padding:24px;">

                    {{-- Nav Tabs --}}
                    <ul class="nav nav-tabs mb-3" style="border-bottom:2px solid #f0f3f8;">
                        <li class="nav-item">
                            <button class="nav-link active shop-tab-link" data-tab="basic" type="button" style="font-size:0.83rem;font-weight:600;">
                                <i class="bx bx-user me-1"></i>Basic Info
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link shop-tab-link" data-tab="address" type="button" style="font-size:0.83rem;font-weight:600;">
                                <i class="bx bx-map me-1"></i>Address
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link shop-tab-link" data-tab="banking" type="button" style="font-size:0.83rem;font-weight:600;">
                                <i class="bx bx-credit-card me-1"></i>Banking &amp; GST
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link shop-tab-link" data-tab="terms" type="button" style="font-size:0.83rem;font-weight:600;">
                                <i class="bx bx-file me-1"></i>Terms
                            </button>
                        </li>
                    </ul>

                    {{-- Tab: Basic Info --}}
                    <div id="tab-basic">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="shop-form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shop-form-control" name="supplier_name" id="supplier_name" placeholder="Full business/company name" required maxlength="200">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="shop-form-label">Contact Person</label>
                                <input type="text" class="form-control shop-form-control" name="contact_person" id="contact_person" placeholder="Primary contact name" maxlength="150">
                            </div>
                            <div class="col-md-5">
                                <label class="shop-form-label">Email Address</label>
                                <input type="email" class="form-control shop-form-control" name="email" id="sup_email" placeholder="supplier@example.com" maxlength="150">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">Phone</label>
                                <input type="text" class="form-control shop-form-control" name="phone" id="sup_phone" placeholder="+91 9876543210" maxlength="20">
                            </div>
                            <div class="col-md-3">
                                <label class="shop-form-label">Alternate Phone</label>
                                <input type="text" class="form-control shop-form-control" name="alternate_phone" placeholder="+91 9876543210" maxlength="20">
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-1">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked
                                           style="width:2.5em;height:1.3em;cursor:pointer;">
                                    <label class="form-check-label fw-semibold ms-2" for="is_active" style="font-size:0.82rem;color:#4a5568;">
                                        Active Supplier
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Address --}}
                    <div id="tab-address" style="display:none;">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="shop-form-label">Full Address</label>
                                <textarea class="form-control shop-form-control" name="address" rows="2" placeholder="Building, Street, Area..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">City</label>
                                <input type="text" class="form-control shop-form-control" name="city" placeholder="City" maxlength="100">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">State</label>
                                <input type="text" class="form-control shop-form-control" name="state" placeholder="State" maxlength="100">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">Pincode</label>
                                <input type="text" class="form-control shop-form-control" name="pincode" placeholder="000000" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="shop-form-label">Country</label>
                                <input type="text" class="form-control shop-form-control" name="country" value="India" maxlength="100">
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Banking & GST --}}
                    <div id="tab-banking" style="display:none;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="shop-form-label">GST Number</label>
                                <input type="text" class="form-control shop-form-control" name="gst_number" placeholder="22AAAAA0000A1Z5" maxlength="20" style="text-transform:uppercase;">
                            </div>
                            <div class="col-md-6">
                                <label class="shop-form-label">PAN Number</label>
                                <input type="text" class="form-control shop-form-control" name="pan_number" placeholder="AAAAA0000A" maxlength="20" style="text-transform:uppercase;">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">Bank Name</label>
                                <input type="text" class="form-control shop-form-control" name="bank_name" placeholder="Bank of India" maxlength="150">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">Account Number</label>
                                <input type="text" class="form-control shop-form-control" name="bank_account" placeholder="Account number" maxlength="30">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">IFSC Code</label>
                                <input type="text" class="form-control shop-form-control" name="ifsc_code" placeholder="SBIN0000001" maxlength="20" style="text-transform:uppercase;">
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Terms --}}
                    <div id="tab-terms" style="display:none;">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="shop-form-label">Payment Terms</label>
                                <select class="form-select shop-form-control" name="payment_terms">
                                    <option value="">Select terms</option>
                                    <option value="Advance">Advance Payment</option>
                                    <option value="Cash">Cash on Delivery</option>
                                    <option value="Net15">Net 15 Days</option>
                                    <option value="Net30">Net 30 Days</option>
                                    <option value="Net45">Net 45 Days</option>
                                    <option value="Net60">Net 60 Days</option>
                                    <option value="Net90">Net 90 Days</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">Credit Days</label>
                                <input type="number" class="form-control shop-form-control" name="credit_days" value="0" min="0" placeholder="30">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">Credit Limit (₹)</label>
                                <input type="number" class="form-control shop-form-control" name="credit_limit" value="0" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="shop-form-label">Opening Balance (₹)</label>
                                <input type="number" class="form-control shop-form-control" name="opening_balance" value="0" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-12">
                                <label class="shop-form-label">Notes</label>
                                <textarea class="form-control shop-form-control" name="notes" rows="2" placeholder="Any additional notes about this supplier..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;gap:10px;justify-content:space-between;">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary shop-tab-link" data-tab="basic" style="border-radius:8px;font-size:0.8rem;" id="prevTabBtn">
                            <i class="bx bx-chevron-left me-1"></i>Previous
                        </button>
                        <button type="button" class="btn btn-outline-primary shop-tab-link" data-tab="address" style="border-radius:8px;font-size:0.8rem;" id="nextTabBtn">
                            Next <i class="bx bx-chevron-right ms-1"></i>
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-size:0.85rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="supplierSaveBtn" style="border-radius:8px;font-size:0.85rem;font-weight:600;min-width:140px;">
                            <i class="bx bx-save me-1"></i> Save Supplier
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')
<script>
$(function () {
    let activeFilter = '';
    const tabs       = ['basic', 'address', 'banking', 'terms'];
    let   activeTab  = 'basic';

    // ── Load Stats ───────────────────────────────────────────────────
    function loadStats() {
        const total  = parseInt($('#supplierCount').text()) || 0;
        const active = parseInt($('.tab-btn[data-filter="1"]').data('count') || 0);
        const pos    = parseInt($('#statPOVal').data('raw') || 0);
        $.get('{{ route("shop.supplier.list") }}', { start: 0, length: 9999, draw: 1 }, function (r) {
            $('#statTotalVal').text(r.recordsTotal);
        });
    }

    // ── DataTable ────────────────────────────────────────────────────
    const supDT = $('#supplierTable').DataTable({
        processing: true, serverSide: true, responsive: true,
        ajax: {
            url:  '{{ route("shop.supplier.list") }}',
            type: 'GET',
            data: function (d) { d.filter_status = activeFilter; }
        },
        columns: [
            { data: 0 }, { data: 1 }, { data: 2 }, { data: 3 },
            { data: 4 }, { data: 5, orderable: false }, { data: 6 }, { data: 7, orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<div class="d-flex align-items-center gap-2 py-2"><span class="spinner-border spinner-border-sm text-primary"></span> Loading...</div>',
            emptyTable: '<div class="text-center py-5 text-muted"><i class="bx bx-store-alt" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>No suppliers found.</div>',
        },
        drawCallback: function (s) {
            const total = s.fnRecordsTotal();
            $('#supplierCount').text(total + ' supplier' + (total !== 1 ? 's' : ''));
            $('#statTotalVal').text(total);
        }
    });

    // ── Filter Tabs ──────────────────────────────────────────────────
    $(document).on('click', '.tab-btn[data-filter]', function () {
        $('.tab-btn[data-filter]').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        supDT.ajax.reload(null, false);
    });

    // ── Modal Tabs ────────────────────────────────────────────────────
    function switchModalTab(tabId) {
        tabs.forEach(t => { $('#tab-' + t).hide(); });
        $('#tab-' + tabId).show();
        $('.shop-tab-link[data-tab]').removeClass('active');
        $('.nav-link.shop-tab-link[data-tab="' + tabId + '"]').addClass('active');
        activeTab = tabId;

        const idx = tabs.indexOf(tabId);
        $('#prevTabBtn').toggle(idx > 0).data('tab', tabs[idx - 1] || 'basic');
        $('#nextTabBtn').toggle(idx < tabs.length - 1).data('tab', tabs[idx + 1] || 'terms');
    }

    $(document).on('click', '.shop-tab-link[data-tab]', function () {
        switchModalTab($(this).data('tab'));
    });

    // ── Open Add Modal ────────────────────────────────────────────────
    $('#addSupplierBtn').on('click', function () {
        resetSupplierForm();
        $('#supplierModalLabel').html('<i class="bx bx-store-alt me-2 text-primary"></i>Add New Supplier');
        switchModalTab('basic');
        $('#supplierModal').modal('show');
    });

    // ── Form Submit ───────────────────────────────────────────────────
    $('#supplierForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#supplierSaveBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        showShopLoader();
        $.post('{{ route("shop.supplier.store") }}', $(this).serialize())
            .done(function (r) {
                hideShopLoader();
                if (r.status === 'success') { toastr.success(r.message); $('#supplierModal').modal('hide'); supDT.ajax.reload(null, false); }
                else { toastr.error(r.message); }
            })
            .fail(xhr => { hideShopLoader(); toastr.error(xhr.responseJSON?.message || 'Error.'); })
            .always(() => btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Supplier'));
    });

    // ── Edit ─────────────────────────────────────────────────────────
    $(document).on('click', '.editSupplierBtn', function () {
        const id = $(this).data('id');
        showShopLoader();
        $.get('{{ url("shop/supplier/edit") }}/' + id, function (r) {
            hideShopLoader();
            if (r.status !== 'success') { toastr.error(r.message); return; }
            const d = r.data;
            resetSupplierForm();
            $('#supplier_id').val(d.supplier_id);
            $('#supplier_name').val(d.supplier_name);
            $('#contact_person').val(d.contact_person);
            $('#sup_email').val(d.email);
            $('#sup_phone').val(d.phone);
            $('[name="alternate_phone"]').val(d.alternate_phone);
            $('[name="address"]').val(d.address);
            $('[name="city"]').val(d.city);
            $('[name="state"]').val(d.state);
            $('[name="pincode"]').val(d.pincode);
            $('[name="country"]').val(d.country || 'India');
            $('[name="gst_number"]').val(d.gst_number);
            $('[name="pan_number"]').val(d.pan_number);
            $('[name="bank_name"]').val(d.bank_name);
            $('[name="bank_account"]').val(d.bank_account);
            $('[name="ifsc_code"]').val(d.ifsc_code);
            $('[name="payment_terms"]').val(d.payment_terms);
            $('[name="credit_days"]').val(d.credit_days);
            $('[name="credit_limit"]').val(d.credit_limit);
            $('[name="opening_balance"]').val(d.opening_balance);
            $('[name="notes"]').val(d.notes);
            $('#is_active').prop('checked', d.is_active == 1);
            $('#supplierModalLabel').html('<i class="bx bx-edit me-2 text-primary"></i>Edit Supplier');
            switchModalTab('basic');
            $('#supplierModal').modal('show');
        }).fail(() => { hideShopLoader(); toastr.error('Failed to load data.'); });
    });

    // ── Delete ────────────────────────────────────────────────────────
    $(document).on('click', '.deleteSupplierBtn', function () {
        const id = $(this).data('id'), name = $(this).data('name');
        Swal.fire({ title: 'Delete "' + name + '"?', text: 'This will remove the supplier record permanently.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete', reverseButtons: true })
            .then(result => {
                if (!result.isConfirmed) return;
                showShopLoader();
                $.post('{{ route("shop.supplier.delete") }}', { supplier_id: id })
                    .done(r => { hideShopLoader(); if (r.status === 'success') { toastr.success(r.message); supDT.ajax.reload(null, false); } else { toastr.error(r.message); } })
                    .fail(xhr => { hideShopLoader(); toastr.error(xhr.responseJSON?.message || 'Delete failed.'); });
            });
    });

    function resetSupplierForm() {
        $('#supplierForm')[0].reset();
        $('#supplier_id').val('');
        $('#is_active').prop('checked', true);
    }
});
</script>
</body>
</html>
