<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>CMS Pages — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-file-blank text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Marketing / <span>CMS Pages</span>
            </div>
        </div>
        <button class="btn btn-primary" id="addPageBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add New Page
        </button>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Content Pages</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="cmsTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Page Title & URL</th>
                            <th width="140">Footer Visibility</th>
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

{{-- ══════ ADD / EDIT CMS PAGE MODAL ══════ --}}
<div class="modal fade" id="cmsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="cmsModalTitle">Add CMS Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cmsForm">
                @csrf
                <input type="hidden" name="page_id" id="page_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Page Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="page_title" id="page_title" required placeholder="e.g. Terms & Conditions, Privacy Policy">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Custom URL Slug (Optional)</label>
                            <input type="text" class="form-control" name="page_slug" id="page_slug" placeholder="e.g. terms-and-conditions">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Page Content (HTML supported) <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="page_content" id="page_content" rows="10" required placeholder="Write the content of this page here..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Meta Title (SEO)</label>
                            <input type="text" class="form-control" name="meta_title" id="meta_title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" placeholder="terms, refund, privacy">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Meta Description</label>
                        <textarea class="form-control" name="meta_description" id="meta_description" rows="2"></textarea>
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="sort_order" value="0">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" name="show_in_footer" id="show_in_footer" value="1" checked>
                                <label class="form-check-label fw-semibold" for="show_in_footer" style="font-size:0.82rem;">Show in Footer Navigation</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label fw-semibold" for="is_active" style="font-size:0.82rem;">Published & Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="savePageBtn">Save Page</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')

<script>
$(function() {
    const table = $('#cmsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.cms.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'page_info', name: 'page_title' },
            { data: 'footer_badge', name: 'show_in_footer' },
            { data: 'status_badge', name: 'is_active' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#addPageBtn').on('click', function() {
        $('#cmsForm')[0].reset();
        $('#page_id').val('');
        $('#cmsModalTitle').text('Add CMS Page');
        $('#cmsModal').modal('show');
    });

    $('#cmsForm').on('submit', function(e) {
        e.preventDefault();
        $('#savePageBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('shop.cms.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $('#savePageBtn').prop('disabled', false).text('Save Page');
                if (res.status === 'success') {
                    $('#cmsModal').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function(xhr) {
                $('#savePageBtn').prop('disabled', false).text('Save Page');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error occurred';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    $(document).on('click', '.edit-page-btn', function() {
        const id = $(this).data('id');
        $.get("{{ url('/shop/cms/edit') }}/" + id, function(res) {
            if (res.status === 'success') {
                const p = res.data;
                $('#page_id').val(p.page_id);
                $('#page_title').val(p.page_title);
                $('#page_slug').val(p.page_slug);
                $('#page_content').val(p.page_content);
                $('#meta_title').val(p.meta_title);
                $('#meta_keywords').val(p.meta_keywords);
                $('#meta_description').val(p.meta_description);
                $('#sort_order').val(p.sort_order);
                $('#show_in_footer').prop('checked', p.show_in_footer == 1);
                $('#is_active').prop('checked', p.is_active == 1);
                $('#cmsModalTitle').text('Edit CMS Page');
                $('#cmsModal').modal('show');
            }
        });
    });

    $(document).on('click', '.delete-page-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this page?',
            text: "This static page will no longer be viewable.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.cms.delete') }}", {
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
