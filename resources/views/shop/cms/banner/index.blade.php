<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Banners & Sliders — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-image text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Marketing / <span>Banners & Sliders</span>
            </div>
        </div>
        <button class="btn btn-primary" id="addBannerBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add Banner / Slide
        </button>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>Marketing Banners & Carousels</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="bannerTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Banner Preview</th>
                            <th width="150">Position</th>
                            <th>Target URL</th>
                            <th width="100">Status</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- ══════ ADD / EDIT BANNER MODAL ══════ --}}
<div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="bannerModalTitle">Add Banner / Slider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bannerForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="banner_id" id="banner_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Banner Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="banner_title" id="banner_title" required placeholder="e.g. Summer Mega Sale 50% Off">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Position <span class="text-danger">*</span></label>
                            <select class="form-select" name="banner_position" id="banner_position">
                                <option value="homepage_main">Homepage Hero Slider</option>
                                <option value="homepage_secondary">Homepage Secondary Banner</option>
                                <option value="category">Category Page Top</option>
                                <option value="sidebar">Sidebar Promo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Subtitle / Caption</label>
                        <input type="text" class="form-control" name="banner_subtitle" id="banner_subtitle" placeholder="e.g. Grab the latest apparel at discount prices">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Click Link URL</label>
                            <input type="url" class="form-control" name="banner_link" id="banner_link" placeholder="https://example.com/collection/summer">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Button CTA Text</label>
                            <input type="text" class="form-control" name="banner_cta_text" id="banner_cta_text" placeholder="e.g. Shop Now">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Desktop Banner Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="banner_image" id="banner_image" accept="image/*">
                            <small class="text-muted">Recommended: 1920x600 px</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Mobile Banner Image (Optional)</label>
                            <input type="file" class="form-control" name="banner_mobile_image" id="banner_mobile_image" accept="image/*">
                            <small class="text-muted">Recommended: 800x800 px</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Display Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="sort_order" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label fw-semibold" for="is_active" style="font-size:0.82rem;">Active & Visible on Website</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBannerBtn">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')

<script>
$(function() {
    const table = $('#bannerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.banner.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'banner_preview', name: 'banner_title' },
            { data: 'position_badge', name: 'banner_position' },
            { data: 'link_display', name: 'banner_link' },
            { data: 'status_badge', name: 'is_active' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#addBannerBtn').on('click', function() {
        $('#bannerForm')[0].reset();
        $('#banner_id').val('');
        $('#bannerModalTitle').text('Add Banner / Slider');
        $('#banner_image').prop('required', true);
        $('#bannerModal').modal('show');
    });

    $('#bannerForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $('#saveBannerBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('shop.banner.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#saveBannerBtn').prop('disabled', false).text('Save Banner');
                if (res.status === 'success') {
                    $('#bannerModal').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function(xhr) {
                $('#saveBannerBtn').prop('disabled', false).text('Save Banner');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    $(document).on('click', '.edit-banner-btn', function() {
        const id = $(this).data('id');
        $.get("{{ url('/shop/banner/edit') }}/" + id, function(res) {
            if (res.status === 'success') {
                const b = res.data;
                $('#banner_id').val(b.banner_id);
                $('#banner_title').val(b.banner_title);
                $('#banner_subtitle').val(b.banner_subtitle);
                $('#banner_position').val(b.banner_position);
                $('#banner_link').val(b.banner_link);
                $('#banner_cta_text').val(b.banner_cta_text);
                $('#sort_order').val(b.sort_order);
                $('#is_active').prop('checked', b.is_active == 1);
                $('#banner_image').prop('required', false);
                $('#bannerModalTitle').text('Edit Banner');
                $('#bannerModal').modal('show');
            }
        });
    });

    $(document).on('click', '.delete-banner-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this banner?',
            text: "It will no longer display on the storefront.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.banner.delete') }}", {
                    _token: "{{ csrf_token() }}",
                    id: id
                }, function(res) {
                    if (res.status === 'success') {
                        table.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Deleted', text: res.message, timer: 1500, showConfirmButton: false });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
