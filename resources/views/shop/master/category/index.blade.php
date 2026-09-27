<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Categories — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-category text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Catalog / <span>Categories</span>
            </div>
        </div>
        <button class="btn btn-primary" id="addCategoryBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add Category
        </button>
    </div>

    {{-- Filter Tabs --}}
    <div class="shop-card mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="fw-semibold" style="font-size:0.82rem;color:#4a5568;">Filter:</span>
                <div class="tab-btn-group">
                    <button class="tab-btn active" data-filter="all">All Categories</button>
                    <button class="tab-btn" data-filter="parent">
                        <i class="bx bx-category me-1"></i>Top-Level Only
                    </button>
                    <button class="tab-btn" data-filter="sub">
                        <i class="bx bx-subdirectory-right me-1"></i>Sub-categories Only
                    </button>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2" style="font-size:0.75rem;border-radius:20px;" id="categoryCount">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>Category List</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="categoryTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="60">Image</th>
                            <th>Category Name / Slug</th>
                            <th width="180">Parent Category</th>
                            <th width="100">Featured</th>
                            <th width="90">Sort</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- ══════ ADD / EDIT CATEGORY MODAL ══════ --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="categoryModalLabel">
                    <i class="bx bx-category me-2 text-primary"></i>Add New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="category_id" name="category_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3">

                        {{-- Image Uploads --}}
                        <div class="col-12">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto text-center">
                                    <label class="shop-form-label d-block mb-2">Category Image</label>
                                    <div class="img-preview-box" style="width:90px;height:90px;border-radius:10px;" onclick="document.getElementById('category_image_input').click()">
                                        <img id="catImgPreview" src="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:8px;">
                                        <i class="bx bx-image-add text-muted" id="catImgPlaceholder" style="font-size:1.8rem;"></i>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size:0.72rem;">Square, max 3MB</small>
                                    <input type="file" id="category_image_input" name="category_image" accept="image/*" style="display:none;">
                                </div>
                                <div class="col text-center">
                                    <label class="shop-form-label d-block mb-2">Category Banner</label>
                                    <div class="img-preview-box" style="width:240px;height:90px;border-radius:10px;" onclick="document.getElementById('category_banner_input').click()">
                                        <img id="catBannerPreview" src="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:8px;">
                                        <i class="bx bx-image text-muted" id="catBannerPlaceholder" style="font-size:1.8rem;"></i>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size:0.72rem;">Landscape, max 5MB</small>
                                    <input type="file" id="category_banner_input" name="category_banner" accept="image/*" style="display:none;">
                                </div>
                            </div>
                        </div>

                        {{-- Category Name --}}
                        <div class="col-md-6">
                            <label class="shop-form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shop-form-control" id="category_name" name="category_name"
                                   placeholder="e.g. Men's Clothing" maxlength="200" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Parent Category --}}
                        <div class="col-md-4">
                            <label class="shop-form-label">Parent Category</label>
                            <select class="form-select shop-form-control shop-select2" id="parent_id" name="parent_id"
                                    style="border-radius:8px;">
                                <option value="">— Top Level Category —</option>
                                @foreach($parentCategories as $pc)
                                    <option value="{{ $pc->category_id }}">{{ $pc->category_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted" style="font-size:0.75rem;">Leave blank to create a top-level category</div>
                        </div>

                        {{-- Sort Order --}}
                        <div class="col-md-2">
                            <label class="shop-form-label">Sort Order</label>
                            <input type="number" class="form-control shop-form-control" id="cat_sort_order" name="sort_order"
                                   value="0" min="0">
                        </div>

                        {{-- Featured --}}
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="cat_is_featured" name="is_featured"
                                       style="width:2.5em;height:1.3em;cursor:pointer;">
                                <label class="form-check-label fw-semibold ms-2" for="cat_is_featured" style="font-size:0.82rem;color:#4a5568;">
                                    Show in Featured Categories (Homepage)
                                </label>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="shop-form-label">Description</label>
                            <textarea class="form-control shop-form-control" id="category_description" name="category_description"
                                      rows="2" placeholder="Short description..."></textarea>
                        </div>

                        {{-- SEO --}}
                        <div class="col-12">
                            <div style="background:#f8f9fa;border-radius:10px;padding:14px 16px;">
                                <div class="fw-semibold mb-2" style="font-size:0.82rem;color:#4a5568;">
                                    <i class="bx bx-search-alt me-1 text-primary"></i>SEO Settings
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control shop-form-control" name="meta_title"
                                               placeholder="Meta title" style="font-size:0.81rem;">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control shop-form-control" name="meta_description"
                                               placeholder="Meta description" style="font-size:0.81rem;">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control shop-form-control" name="meta_keywords"
                                               placeholder="Meta keywords" style="font-size:0.81rem;">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;gap:10px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-size:0.85rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="catSaveBtn" style="border-radius:8px;font-size:0.85rem;font-weight:600;min-width:130px;">
                        <i class="bx bx-save me-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')
<script>
$(function () {
    let activeFilter = 'all';

    // ── Init Select2 ─────────────────────────────────────────────────
    $('#parent_id').select2({ theme: 'bootstrap-5', dropdownParent: $('#categoryModal'), width: '100%', allowClear: true, placeholder: '— Top Level Category —' });

    // ── DataTable ────────────────────────────────────────────────────
    const catDT = $('#categoryTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url:  '{{ route("shop.category.list") }}',
            type: 'GET',
            data: function (d) { d.filter_type = activeFilter === 'all' ? '' : activeFilter; }
        },
        columns: [
            { data: 0 }, { data: 1, orderable: false },
            { data: 2 }, { data: 3 },
            { data: 4 }, { data: 5 }, { data: 6, orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<div class="d-flex align-items-center gap-2 py-2"><span class="spinner-border spinner-border-sm text-primary"></span> Loading...</div>',
            emptyTable: '<div class="text-center py-5 text-muted"><i class="bx bx-category" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>No categories found.</div>',
        },
        drawCallback: function (s) {
            const total = s.fnRecordsTotal();
            $('#categoryCount').text(total + ' categor' + (total === 1 ? 'y' : 'ies'));
        }
    });

    // ── Filter Tabs ──────────────────────────────────────────────────
    $(document).on('click', '.tab-btn', function () {
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        catDT.ajax.reload(null, false);
    });

    // ── Image Previews ────────────────────────────────────────────────
    $('#category_image_input').on('change', function () {
        previewImage(this, '#catImgPreview', '#catImgPlaceholder');
    });
    $('#category_banner_input').on('change', function () {
        previewImage(this, '#catBannerPreview', '#catBannerPlaceholder');
    });

    function previewImage(input, imgSel, placeholderSel) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => { $(imgSel).attr('src', e.target.result).show(); $(placeholderSel).hide(); };
        reader.readAsDataURL(file);
    }

    // ── Open Add Modal ────────────────────────────────────────────────
    $('#addCategoryBtn').on('click', function () {
        resetCategoryForm();
        $('#categoryModalLabel').html('<i class="bx bx-category me-2 text-primary"></i>Add New Category');
        $('#categoryModal').modal('show');
    });

    // ── Form Submit ───────────────────────────────────────────────────
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = $('#catSaveBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        showShopLoader();

        $.ajax({ url: '{{ route("shop.category.store") }}', type: 'POST', data: formData, contentType: false, processData: false })
            .done(function (r) {
                hideShopLoader();
                if (r.status === 'success') { toastr.success(r.message); $('#categoryModal').modal('hide'); catDT.ajax.reload(null, false); }
                else { toastr.error(r.message); }
            })
            .fail(function (xhr) {
                hideShopLoader();
                toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
            })
            .always(() => btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Category'));
    });

    // ── Edit ─────────────────────────────────────────────────────────
    $(document).on('click', '.editCategoryBtn', function () {
        const id = $(this).data('id');
        showShopLoader();
        $.get('{{ url("shop/category/edit") }}/' + id, function (r) {
            hideShopLoader();
            if (r.status !== 'success') { toastr.error(r.message); return; }
            const d = r.data;
            resetCategoryForm();
            $('#category_id').val(d.category_id);
            $('#category_name').val(d.category_name);
            $('#parent_id').val(d.parent_id || '').trigger('change');
            $('#cat_sort_order').val(d.sort_order);
            $('#category_description').val(d.category_description);
            $('[name="meta_title"]').val(d.meta_title);
            $('[name="meta_description"]').val(d.meta_description);
            $('[name="meta_keywords"]').val(d.meta_keywords);
            $('#cat_is_featured').prop('checked', d.is_featured == 1);
            if (d.category_image) { $('#catImgPreview').attr('src', '{{ asset("") }}' + d.category_image).show(); $('#catImgPlaceholder').hide(); }
            if (d.category_banner) { $('#catBannerPreview').attr('src', '{{ asset("") }}' + d.category_banner).show(); $('#catBannerPlaceholder').hide(); }
            $('#categoryModalLabel').html('<i class="bx bx-edit me-2 text-primary"></i>Edit Category');
            $('#categoryModal').modal('show');
        }).fail(() => { hideShopLoader(); toastr.error('Failed to load data.'); });
    });

    // ── Delete ────────────────────────────────────────────────────────
    $(document).on('click', '.deleteCategoryBtn', function () {
        const id = $(this).data('id'), name = $(this).data('name');
        Swal.fire({ title: 'Delete "' + name + '"?', text: 'Sub-categories and linked products will NOT be deleted, but will lose this category.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete', reverseButtons: true })
            .then(result => {
                if (!result.isConfirmed) return;
                showShopLoader();
                $.post('{{ route("shop.category.delete") }}', { category_id: id })
                    .done(r => { hideShopLoader(); if (r.status === 'success') { toastr.success(r.message); catDT.ajax.reload(null, false); } else { toastr.error(r.message); } })
                    .fail(xhr => { hideShopLoader(); toastr.error(xhr.responseJSON?.message || 'Delete failed.'); });
            });
    });

    function resetCategoryForm() {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#parent_id').val('').trigger('change');
        $('#catImgPreview').hide().attr('src', ''); $('#catImgPlaceholder').show();
        $('#catBannerPreview').hide().attr('src', ''); $('#catBannerPlaceholder').show();
    }
});
</script>
</body>
</html>
