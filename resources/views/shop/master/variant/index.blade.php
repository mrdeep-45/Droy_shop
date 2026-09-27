<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Variants — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-grid-alt text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb"><a href="{{ route('shop.dashboard') }}">Home</a> / Catalog / <span>Variants</span></div>
        </div>
        <button class="btn btn-primary" id="addVariantBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add Variant Type
        </button>
    </div>

    {{-- Variant Types Table --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>Variant Types</span>
            <span class="badge bg-primary-subtle text-primary px-3 py-2" style="font-size:0.75rem;border-radius:20px;" id="variantCount"></span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="variantTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Variant Name</th>
                            <th width="130">Type</th>
                            <th width="150">Values</th>
                            <th width="140">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</main>

{{-- ══════ ADD / EDIT VARIANT MODAL ══════ --}}
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="variantModalLabel"><i class="bx bx-grid-alt me-2 text-primary"></i>Add Variant Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="variantForm">
                @csrf
                <input type="hidden" id="variant_id" name="variant_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="shop-form-label">Variant Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control shop-form-control" id="variant_name" name="variant_name"
                               placeholder="e.g. Size, Color, Material" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="shop-form-label">Variant Type <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <label class="type-option" id="type-text">
                                <input type="radio" name="variant_type" value="text" checked>
                                <div class="type-option-box">
                                    <i class="bx bx-text" style="font-size:1.4rem;display:block;margin-bottom:4px;"></i>
                                    <span>Text</span>
                                </div>
                            </label>
                            <label class="type-option" id="type-color">
                                <input type="radio" name="variant_type" value="color">
                                <div class="type-option-box">
                                    <i class="bx bx-palette" style="font-size:1.4rem;display:block;margin-bottom:4px;color:#dc3545;"></i>
                                    <span>Color</span>
                                </div>
                            </label>
                            <label class="type-option" id="type-image">
                                <input type="radio" name="variant_type" value="image">
                                <div class="type-option-box">
                                    <i class="bx bx-image" style="font-size:1.4rem;display:block;margin-bottom:4px;color:#0dcaf0;"></i>
                                    <span>Image</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;gap:10px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-size:0.85rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="variantSaveBtn" style="border-radius:8px;font-size:0.85rem;font-weight:600;min-width:130px;">
                        <i class="bx bx-save me-1"></i> Save Variant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════ VARIANT VALUES DRAWER ══════ --}}
<div class="modal fade" id="variantValuesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="valuesModalTitle"><i class="bx bx-list-ul me-2 text-success"></i>Variant Values</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="row g-3">
                    {{-- Add Value Form --}}
                    <div class="col-md-4">
                        <div style="background:#f8f9fa;border-radius:12px;padding:16px;">
                            <div class="fw-semibold mb-3" style="font-size:0.83rem;color:#4a5568;">
                                <i class="bx bx-plus-circle me-1 text-primary"></i>Add New Value
                            </div>
                            <form id="variantValueForm">
                                @csrf
                                <input type="hidden" id="vv_variant_id" name="variant_id">
                                <input type="hidden" id="vv_variant_value_id" name="variant_value_id">
                                <div class="mb-2">
                                    <label class="shop-form-label mb-1">Value Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shop-form-control" id="vv_value_name" name="value_name"
                                           placeholder="e.g. Red, XL, Cotton" maxlength="100" required style="font-size:0.82rem;">
                                </div>
                                <div id="colorPickerWrap" style="display:none;">
                                    <div class="mb-2">
                                        <label class="shop-form-label mb-1">Color Code</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="color" class="form-control form-control-color" id="vv_color_swatch"
                                                   style="width:40px;height:36px;padding:2px;border-radius:6px;cursor:pointer;">
                                            <input type="text" class="form-control shop-form-control" id="vv_color_code" name="color_code"
                                                   placeholder="#FF0000" maxlength="20" style="font-size:0.82rem;">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="shop-form-label mb-1">Sort Order</label>
                                    <input type="number" class="form-control shop-form-control" id="vv_sort_order" name="sort_order"
                                           value="0" min="0" style="font-size:0.82rem;">
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary flex-grow-1" id="vvSaveBtn" style="border-radius:8px;font-size:0.81rem;font-weight:600;">
                                        <i class="bx bx-plus me-1"></i>Add Value
                                    </button>
                                    <button type="button" id="vvResetBtn" class="btn btn-light" style="border-radius:8px;">
                                        <i class="bx bx-refresh"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Values List --}}
                    <div class="col-md-8">
                        <div class="fw-semibold mb-2" style="font-size:0.83rem;color:#4a5568;">
                            <i class="bx bx-list-check me-1 text-primary"></i>Existing Values
                            <span class="badge bg-primary-subtle text-primary ms-1" id="valueCount" style="border-radius:20px;font-size:0.72rem;"></span>
                        </div>
                        <div id="valuesList" style="max-height:400px;overflow-y:auto;">
                            <div class="text-center py-4 text-muted" id="valuesLoading">
                                <span class="spinner-border spinner-border-sm me-1"></span> Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('shop.include.footer_link')
<style>
.type-option input[type="radio"] { display:none; }
.type-option-box {
    border: 2px solid #e9ecef; border-radius: 10px; padding: 12px 16px;
    text-align: center; cursor: pointer; font-size: 0.78rem; font-weight: 600;
    color: #6c757d; transition: all 0.15s; min-width: 80px;
}
.type-option input[type="radio"]:checked + .type-option-box {
    border-color: #0d6efd; background: #f0f3ff; color: #0d6efd;
}
.type-option-box:hover { border-color: #0d6efd; }

.value-item {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 10px; border: 1px solid #f0f3f8; border-radius: 8px;
    margin-bottom: 6px; background: #fff; font-size: 0.82rem;
}
.value-item:hover { background: #f8f9fa; }
.value-color-swatch {
    width: 24px; height: 24px; border-radius: 6px;
    border: 2px solid rgba(0,0,0,0.1); flex-shrink: 0;
}
</style>
<script>
$(function () {
    let activeVariantId   = null;
    let activeVariantType = 'text';

    // ── DataTable ────────────────────────────────────────────────────
    const variantDT = $('#variantTable').DataTable({
        processing: true, serverSide: true, responsive: true,
        ajax: { url: '{{ route("shop.variant.list") }}', type: 'GET' },
        columns: [{ data: 0 }, { data: 1 }, { data: 2 }, { data: 3, orderable: false }, { data: 4, orderable: false }],
        order: [[1, 'asc']],
        pageLength: 25,
        language: {
            processing: '<div class="d-flex align-items-center gap-2 py-2"><span class="spinner-border spinner-border-sm text-primary"></span> Loading...</div>',
            emptyTable: '<div class="text-center py-4 text-muted"><i class="bx bx-grid-alt" style="font-size:2rem;display:block;margin-bottom:6px;"></i>No variants added yet.</div>',
        },
        drawCallback: s => { const t = s.fnRecordsTotal(); $('#variantCount').text(t + ' type' + (t !== 1 ? 's' : '')); }
    });

    // ── Open Add Modal ────────────────────────────────────────────────
    $('#addVariantBtn').on('click', function () {
        resetVariantForm();
        $('#variantModalLabel').html('<i class="bx bx-grid-alt me-2 text-primary"></i>Add Variant Type');
        $('#variantModal').modal('show');
    });

    // ── Variant Form Submit ───────────────────────────────────────────
    $('#variantForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#variantSaveBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        showShopLoader();
        $.post('{{ route("shop.variant.store") }}', $(this).serialize())
            .done(r => {
                hideShopLoader();
                if (r.status === 'success') { toastr.success(r.message); $('#variantModal').modal('hide'); variantDT.ajax.reload(null, false); }
                else { toastr.error(r.message); }
            })
            .fail(xhr => { hideShopLoader(); toastr.error(xhr.responseJSON?.message || 'Error.'); })
            .always(() => btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Variant'));
    });

    // ── Edit Variant ──────────────────────────────────────────────────
    $(document).on('click', '.editVariantBtn', function () {
        const id = $(this).data('id');
        showShopLoader();
        $.get('{{ url("shop/variant/edit") }}/' + id, function (r) {
            hideShopLoader();
            if (r.status !== 'success') { toastr.error(r.message); return; }
            const d = r.data;
            resetVariantForm();
            $('#variant_id').val(d.variant_id);
            $('#variant_name').val(d.variant_name);
            $('input[name="variant_type"][value="' + d.variant_type + '"]').prop('checked', true);
            $('#variantModalLabel').html('<i class="bx bx-edit me-2 text-primary"></i>Edit Variant Type');
            $('#variantModal').modal('show');
        }).fail(() => { hideShopLoader(); toastr.error('Failed to load.'); });
    });

    // ── Delete Variant ────────────────────────────────────────────────
    $(document).on('click', '.deleteVariantBtn', function () {
        const id = $(this).data('id'), name = $(this).data('name');
        Swal.fire({ title: 'Delete "' + name + '"?', text: 'All values for this variant will also be deleted.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete', reverseButtons: true })
            .then(result => {
                if (!result.isConfirmed) return;
                showShopLoader();
                $.post('{{ route("shop.variant.delete") }}', { variant_id: id })
                    .done(r => { hideShopLoader(); if (r.status === 'success') { toastr.success(r.message); variantDT.ajax.reload(null, false); } else { toastr.error(r.message); } })
                    .fail(xhr => { hideShopLoader(); toastr.error(xhr.responseJSON?.message || 'Delete failed.'); });
            });
    });

    // ── Open Values Panel ─────────────────────────────────────────────
    $(document).on('click', '.viewValuesBtn', function (e) {
        e.preventDefault();
        activeVariantId   = $(this).data('id');
        activeVariantType = $(this).data('type');
        const variantName = $(this).data('name');

        $('#vv_variant_id').val(activeVariantId);
        $('#valuesModalTitle').html('<i class="bx bx-list-ul me-2 text-success"></i>Values for: <strong>' + variantName + '</strong>');
        $('#colorPickerWrap').toggle(activeVariantType === 'color');
        $('#vvSaveBtn').html('<i class="bx bx-plus me-1"></i>Add Value');

        resetValueForm();
        loadValues();
        $('#variantValuesModal').modal('show');
    });

    function loadValues() {
        $('#valuesLoading').show();
        $('#valuesList').html('<div class="text-center py-4 text-muted" id="valuesLoading"><span class="spinner-border spinner-border-sm me-1"></span> Loading...</div>');

        $.get('{{ url("shop/variant") }}/' + activeVariantId + '/values', function (r) {
            if (r.status !== 'success') { toastr.error(r.message); return; }
            const values = r.data;
            $('#valueCount').text(values.length);
            let html = '';

            if (values.length === 0) {
                html = '<div class="text-center py-4 text-muted" style="font-size:0.82rem;"><i class="bx bx-info-circle" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>No values yet. Add one!</div>';
            } else {
                values.forEach(v => {
                    const colorSwatch = activeVariantType === 'color' && v.color_code
                        ? '<div class="value-color-swatch" style="background:' + v.color_code + ';"></div>'
                        : '';

                    html += `
                        <div class="value-item" id="vitem-${v.variant_value_id}">
                            ${colorSwatch}
                            <div class="flex-grow-1">
                                <span class="fw-semibold">${escHtml(v.value_name)}</span>
                                ${activeVariantType === 'color' && v.color_code ? '<small class="text-muted ms-1">' + v.color_code + '</small>' : ''}
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-xs btn-outline-primary editValueBtn"
                                    data-id="${v.variant_value_id}"
                                    data-name="${escHtml(v.value_name)}"
                                    data-color="${v.color_code || ''}"
                                    data-sort="${v.sort_order}"
                                    style="padding:2px 8px;font-size:0.72rem;border-radius:5px;">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-danger deleteValueBtn"
                                    data-id="${v.variant_value_id}"
                                    data-name="${escHtml(v.value_name)}"
                                    style="padding:2px 8px;font-size:0.72rem;border-radius:5px;">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>`;
                });
            }
            $('#valuesList').html(html);
        }).fail(() => toastr.error('Failed to load values.'));
    }

    // ── Add/Edit Value Submit ──────────────────────────────────────────
    $('#variantValueForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#vvSaveBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        $.post('{{ route("shop.variant.value.store") }}', $(this).serialize())
            .done(r => {
                if (r.status === 'success') { toastr.success(r.message); resetValueForm(); loadValues(); variantDT.ajax.reload(null, false); }
                else { toastr.error(r.message); }
            })
            .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Error.'))
            .always(() => btn.prop('disabled', false).html('<i class="bx bx-plus me-1"></i>Add Value'));
    });

    // ── Edit Value ────────────────────────────────────────────────────
    $(document).on('click', '.editValueBtn', function () {
        const id    = $(this).data('id');
        const name  = $(this).data('name');
        const color = $(this).data('color');
        const sort  = $(this).data('sort');
        $('#vv_variant_value_id').val(id);
        $('#vv_value_name').val(name);
        $('#vv_sort_order').val(sort);
        if (color) { $('#vv_color_code').val(color); $('#vv_color_swatch').val(color); }
        $('#vvSaveBtn').html('<i class="bx bx-save me-1"></i>Update Value');
        $('#vv_value_name').focus();
    });

    // ── Delete Value ─────────────────────────────────────────────────
    $(document).on('click', '.deleteValueBtn', function () {
        const id = $(this).data('id'), name = $(this).data('name');
        Swal.fire({ title: 'Delete "' + name + '"?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete', reverseButtons: true })
            .then(result => {
                if (!result.isConfirmed) return;
                $.post('{{ route("shop.variant.value.delete") }}', { variant_value_id: id })
                    .done(r => { if (r.status === 'success') { toastr.success(r.message); loadValues(); variantDT.ajax.reload(null, false); } else { toastr.error(r.message); } })
                    .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Delete failed.'));
            });
    });

    // Sync color swatch ↔ text input
    $('#vv_color_swatch').on('input', function () { $('#vv_color_code').val($(this).val()); });
    $('#vv_color_code').on('input', function () { if (/^#[0-9A-Fa-f]{6}$/.test($(this).val())) $('#vv_color_swatch').val($(this).val()); });

    $('#vvResetBtn').on('click', resetValueForm);

    function resetVariantForm() { $('#variantForm')[0].reset(); $('#variant_id').val(''); $('input[name="variant_type"][value="text"]').prop('checked', true); }
    function resetValueForm() { $('#variantValueForm')[0].reset(); $('#vv_variant_value_id').val(''); $('#vv_variant_id').val(activeVariantId); $('#vvSaveBtn').html('<i class="bx bx-plus me-1"></i>Add Value'); }
    function escHtml(text) { return text ? text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') : ''; }
});
</script>
</body>
</html>
