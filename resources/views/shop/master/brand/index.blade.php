<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Brands — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-diamond text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Catalog / <span>Brands</span>
            </div>
        </div>
        <button class="btn btn-primary" id="addBrandBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add Brand
        </button>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Brands</span>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-primary-subtle text-primary px-3 py-2" style="font-size:0.75rem;border-radius:20px;" id="brandCount">Loading...</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="brandTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="80">Logo</th>
                            <th>Brand Name / Slug</th>
                            <th width="120">Featured</th>
                            <th width="100">Sort Order</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- ══════ ADD / EDIT BRAND MODAL ══════ --}}
<div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="brandModalLabel">
                    <i class="bx bx-diamond me-2 text-primary"></i>Add New Brand
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="brandForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="brand_id" name="brand_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3">

                        {{-- Brand Logo Upload --}}
                        <div class="col-12 text-center mb-2">
                            <div class="position-relative d-inline-block">
                                <div class="img-preview-box" style="width:100px;height:100px;border-radius:12px;" onclick="document.getElementById('brand_logo_input').click()">
                                    <img id="logoPreview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;border-radius:10px;">
                                    <i class="bx bx-image-add text-muted" id="logoPlaceholder" style="font-size:2rem;"></i>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted" style="font-size:0.76rem;">Click to upload logo</small>
                                </div>
                                <input type="file" id="brand_logo_input" name="brand_logo" accept="image/*" style="display:none;">
                            </div>
                        </div>

                        {{-- Brand Name --}}
                        <div class="col-md-6">
                            <label class="shop-form-label">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shop-form-control" id="brand_name" name="brand_name"
                                   placeholder="e.g. Nike, Adidas" maxlength="200" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Sort Order --}}
                        <div class="col-md-3">
                            <label class="shop-form-label">Sort Order</label>
                            <input type="number" class="form-control shop-form-control" id="sort_order" name="sort_order"
                                   value="0" min="0" placeholder="0">
                        </div>

                        {{-- Featured Toggle --}}
                        <div class="col-md-3 d-flex align-items-end pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                       style="width:2.5em;height:1.3em;cursor:pointer;">
                                <label class="form-check-label fw-semibold ms-2" for="is_featured"
                                       style="font-size:0.82rem;color:#4a5568;">Featured Brand</label>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="shop-form-label">Brand Description</label>
                            <textarea class="form-control shop-form-control" id="brand_description" name="brand_description"
                                      rows="2" placeholder="Short description about the brand..."></textarea>
                        </div>

                        {{-- SEO Section --}}
                        <div class="col-12">
                            <div style="background:#f8f9fa;border-radius:10px;padding:14px 16px;">
                                <div class="fw-semibold mb-2" style="font-size:0.82rem;color:#4a5568;">
                                    <i class="bx bx-search-alt me-1 text-primary"></i>SEO Settings (Optional)
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control shop-form-control" id="meta_title" name="meta_title"
                                               placeholder="Meta title" style="font-size:0.81rem;">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control shop-form-control" id="meta_description" name="meta_description"
                                               placeholder="Meta description" style="font-size:0.81rem;">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;gap:10px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-size:0.85rem;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="brandSaveBtn" style="border-radius:8px;font-size:0.85rem;font-weight:600;min-width:120px;">
                        <i class="bx bx-save me-1"></i> Save Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')

<script>
$(function () {

    // ── DataTable Init ───────────────────────────────────────────────
    const brandDT = $('#brandTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url:  '{{ route("shop.brand.list") }}',
            type: 'GET',
        },
        columns: [
            { data: 0, orderable: true },
            { data: 1, orderable: false },
            { data: 2, orderable: true },
            { data: 3, orderable: true },
            { data: 4, orderable: true },
            { data: 5, orderable: false },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<div class="d-flex align-items-center gap-2 py-2"><span class="spinner-border spinner-border-sm text-primary"></span> Loading...</div>',
            emptyTable: '<div class="text-center py-5 text-muted"><i class="bx bx-diamond" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>No brands found. Add your first brand!</div>',
        },
        drawCallback: function(settings) {
            const total = settings.fnRecordsTotal();
            $('#brandCount').text(total + ' brand' + (total !== 1 ? 's' : ''));
        }
    });

    // ── Open Add Modal ───────────────────────────────────────────────
    $('#addBrandBtn').on('click', function () {
        resetBrandForm();
        $('#brandModalLabel').html('<i class="bx bx-diamond me-2 text-primary"></i>Add New Brand');
        $('#brandModal').modal('show');
    });

    // ── Logo Preview ─────────────────────────────────────────────────
    $('#brand_logo_input').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#logoPreview').attr('src', e.target.result).show();
                $('#logoPlaceholder').hide();
            };
            reader.readAsDataURL(file);
        }
    });

    // ── Form Submit (Add / Edit) ─────────────────────────────────────
    $('#brandForm').on('submit', function (e) {
        e.preventDefault();
        clearValidation();

        const formData = new FormData(this);
        const btn = $('#brandSaveBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        showShopLoader();

        $.ajax({
            url:         '{{ route("shop.brand.store") }}',
            type:        'POST',
            data:        formData,
            contentType: false,
            processData: false,
        }).done(function (r) {
            hideShopLoader();
            if (r.status === 'success') {
                toastr.success(r.message);
                $('#brandModal').modal('hide');
                brandDT.ajax.reload(null, false);
            } else {
                toastr.error(r.message);
            }
        }).fail(function (xhr) {
            hideShopLoader();
            if (xhr.status === 422) {
                const errs = xhr.responseJSON?.errors || {};
                Object.keys(errs).forEach(function (k) {
                    $('[name="' + k + '"]').addClass('is-invalid')
                        .siblings('.invalid-feedback').text(errs[k][0]);
                });
                toastr.error(xhr.responseJSON?.message || 'Validation failed.');
            } else {
                toastr.error('Something went wrong.');
            }
        }).always(function () {
            btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Brand');
        });
    });

    // ── Edit Button ──────────────────────────────────────────────────
    $(document).on('click', '.editBrandBtn', function () {
        const id = $(this).data('id');
        showShopLoader();

        $.get('{{ url("shop/brand/edit") }}/' + id, function (r) {
            hideShopLoader();
            if (r.status !== 'success') { toastr.error(r.message); return; }
            const d = r.data;

            resetBrandForm();
            $('#brand_id').val(d.brand_id);
            $('#brand_name').val(d.brand_name);
            $('#sort_order').val(d.sort_order);
            $('#brand_description').val(d.brand_description);
            $('#meta_title').val(d.meta_title);
            $('#meta_description').val(d.meta_description);
            $('#is_featured').prop('checked', d.is_featured == 1);

            if (d.brand_logo) {
                $('#logoPreview').attr('src', '{{ asset("") }}' + d.brand_logo).show();
                $('#logoPlaceholder').hide();
            }

            $('#brandModalLabel').html('<i class="bx bx-edit me-2 text-primary"></i>Edit Brand');
            $('#brandModal').modal('show');
        }).fail(function () {
            hideShopLoader();
            toastr.error('Failed to load brand data.');
        });
    });

    // ── Delete Button ────────────────────────────────────────────────
    $(document).on('click', '.deleteBrandBtn', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Delete "' + name + '"?',
            text:  'This brand will be removed. Products linked to it will not be affected.',
            icon:  'warning',
            showCancelButton:   true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  '<i class="bx bx-trash me-1"></i> Delete',
            cancelButtonText:   'Cancel',
            reverseButtons:     true,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            showShopLoader();
            $.post('{{ route("shop.brand.delete") }}', { brand_id: id })
                .done(function (r) {
                    hideShopLoader();
                    if (r.status === 'success') {
                        toastr.success(r.message);
                        brandDT.ajax.reload(null, false);
                    } else {
                        toastr.error(r.message);
                    }
                }).fail(function (xhr) {
                    hideShopLoader();
                    toastr.error(xhr.responseJSON?.message || 'Delete failed.');
                });
        });
    });

    // ── Helpers ──────────────────────────────────────────────────────
    function resetBrandForm() {
        $('#brandForm')[0].reset();
        $('#brand_id').val('');
        $('#logoPreview').attr('src', '').hide();
        $('#logoPlaceholder').show();
        clearValidation();
    }

    function clearValidation() {
        $('#brandForm .is-invalid').removeClass('is-invalid');
        $('#brandForm .invalid-feedback').text('');
    }
});
</script>
</body>
</html>
