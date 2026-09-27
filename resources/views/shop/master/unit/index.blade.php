<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Units — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-ruler text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb"><a href="{{ route('shop.dashboard') }}">Home</a> / Catalog / <span>Units</span></div>
        </div>
        <button class="btn btn-primary" id="addUnitBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add Unit
        </button>
    </div>

    <div class="row g-3">
        {{-- Table --}}
        <div class="col-xl-8">
            <div class="shop-card">
                <div class="card-header">
                    <span><i class="bx bx-list-ul me-2 text-primary"></i>All Units</span>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2" style="font-size:0.75rem;border-radius:20px;" id="unitCount"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="unitTable" class="table table-hover w-100" style="font-size:0.83rem;">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Unit Name</th>
                                    <th width="120">Short Name</th>
                                    <th width="130">Used In</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inline Add/Edit Form --}}
        <div class="col-xl-4">
            <div class="shop-card sticky-top" style="top:80px;">
                <div class="card-header">
                    <span id="unitFormTitle"><i class="bx bx-plus-circle me-2 text-primary"></i>Add New Unit</span>
                </div>
                <div class="card-body">
                    <form id="unitForm">
                        @csrf
                        <input type="hidden" id="shop_unit_id" name="shop_unit_id">
                        <div class="mb-3">
                            <label class="shop-form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shop-form-control" id="unit_name" name="unit_name"
                                   placeholder="e.g. Kilogram, Piece, Meter" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="shop-form-label">Short Name / Abbreviation</label>
                            <input type="text" class="form-control shop-form-control" id="unit_short" name="unit_short"
                                   placeholder="e.g. Kg, Pcs, Mtr" maxlength="20">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" id="unitSaveBtn" style="border-radius:8px;font-size:0.85rem;font-weight:600;">
                                <i class="bx bx-save me-1"></i> Save Unit
                            </button>
                            <button type="button" class="btn btn-light" id="unitResetBtn" style="border-radius:8px;">
                                <i class="bx bx-refresh"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Sample units hint --}}
                    <div class="mt-4 pt-3 border-top">
                        <div class="fw-semibold mb-2" style="font-size:0.78rem;color:#9aacbf;text-transform:uppercase;letter-spacing:0.5px;">Common Units</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(['Piece (Pcs)', 'Kilogram (Kg)', 'Gram (Gm)', 'Meter (Mtr)', 'Dozen (Doz)', 'Set', 'Liter (Ltr)', 'Box'] as $hint)
                            <span class="badge" style="background:#f0f3f8;color:#4a5568;border-radius:20px;font-size:0.72rem;padding:4px 8px;cursor:pointer;" onclick="fillUnitHint('{{ $hint }}')">{{ $hint }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('shop.include.footer_link')
<script>
$(function () {
    const unitDT = $('#unitTable').DataTable({
        processing: true, serverSide: true, responsive: true,
        ajax: { url: '{{ route("shop.unit.list") }}', type: 'GET' },
        columns: [{ data: 0 }, { data: 1 }, { data: 2 }, { data: 3 }, { data: 4, orderable: false }],
        order: [[1, 'asc']],
        pageLength: 25,
        language: {
            processing: '<div class="d-flex align-items-center gap-2 py-2"><span class="spinner-border spinner-border-sm text-primary"></span> Loading...</div>',
            emptyTable: '<div class="text-center py-4 text-muted"><i class="bx bx-ruler" style="font-size:2rem;display:block;margin-bottom:6px;"></i>No units added yet.</div>',
        },
        drawCallback: function (s) { const t = s.fnRecordsTotal(); $('#unitCount').text(t + ' unit' + (t !== 1 ? 's' : '')); }
    });

    // ── Form Submit ──────────────────────────────────────────────────
    $('#unitForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#unitSaveBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        showShopLoader();
        $.post('{{ route("shop.unit.store") }}', $(this).serialize())
            .done(function (r) {
                hideShopLoader();
                if (r.status === 'success') { toastr.success(r.message); resetUnitForm(); unitDT.ajax.reload(null, false); }
                else { toastr.error(r.message); }
            })
            .fail(xhr => { hideShopLoader(); toastr.error(xhr.responseJSON?.message || 'Error saving unit.'); })
            .always(() => btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Unit'));
    });

    // ── Edit ─────────────────────────────────────────────────────────
    $(document).on('click', '.editUnitBtn', function () {
        const id = $(this).data('id');
        showShopLoader();
        $.get('{{ url("shop/unit/edit") }}/' + id, function (r) {
            hideShopLoader();
            if (r.status !== 'success') { toastr.error(r.message); return; }
            const d = r.data;
            $('#shop_unit_id').val(d.shop_unit_id);
            $('#unit_name').val(d.unit_name);
            $('#unit_short').val(d.unit_short);
            $('#unitFormTitle').html('<i class="bx bx-edit me-2 text-primary"></i>Edit Unit');
            $('#unit_name').focus();
        }).fail(() => { hideShopLoader(); toastr.error('Failed to load.'); });
    });

    // ── Delete ────────────────────────────────────────────────────────
    $(document).on('click', '.deleteUnitBtn', function () {
        const id = $(this).data('id'), name = $(this).data('name');
        Swal.fire({ title: 'Delete "' + name + '"?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete', reverseButtons: true })
            .then(result => {
                if (!result.isConfirmed) return;
                showShopLoader();
                $.post('{{ route("shop.unit.delete") }}', { shop_unit_id: id })
                    .done(r => { hideShopLoader(); if (r.status === 'success') { toastr.success(r.message); unitDT.ajax.reload(null, false); } else { toastr.error(r.message); } })
                    .fail(xhr => { hideShopLoader(); toastr.error(xhr.responseJSON?.message || 'Delete failed.'); });
            });
    });

    $('#unitResetBtn').on('click', resetUnitForm);

    function resetUnitForm() {
        $('#unitForm')[0].reset();
        $('#shop_unit_id').val('');
        $('#unitFormTitle').html('<i class="bx bx-plus-circle me-2 text-primary"></i>Add New Unit');
    }
});

function fillUnitHint(hint) {
    const parts = hint.split(' (');
    $('#unit_name').val(parts[0].trim());
    if (parts[1]) $('#unit_short').val(parts[1].replace(')', '').trim());
    $('#unit_name').focus();
}
</script>
</body>
</html>
