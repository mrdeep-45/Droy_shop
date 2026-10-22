<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Blog Articles — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-news text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Marketing / <span>Blog</span>
            </div>
        </div>
        <button class="btn btn-primary" id="addBlogBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Write New Article
        </button>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Articles & Guides</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="blogTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Article</th>
                            <th width="120">Views</th>
                            <th width="120">Status</th>
                            <th width="130">Published</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- ══════ ADD / EDIT BLOG MODAL ══════ --}}
<div class="modal fade" id="blogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="blogModalTitle">Write New Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blogForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="blog_id" id="blog_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Article Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="blog_title" id="blog_title" required placeholder="e.g. 10 Essential Wardrobe Essentials for 2026">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Category</label>
                            <input type="text" class="form-control" name="blog_category" id="blog_category" placeholder="Fashion, Styling, News" value="Fashion & Trends">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Custom URL Slug</label>
                            <input type="text" class="form-control" name="blog_slug" id="blog_slug" placeholder="e.g. 10-essential-wardrobe-essentials">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Cover / Feature Image</label>
                            <input type="file" class="form-control" name="blog_image" id="blog_image" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Short Excerpt / Teaser</label>
                        <textarea class="form-control" name="blog_excerpt" id="blog_excerpt" rows="2" placeholder="Brief summary of the article..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Article Body (HTML supported) <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="blog_content" id="blog_content" rows="10" required placeholder="Write the full story here..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Tags (comma separated)</label>
                            <input type="text" class="form-control" name="tags" id="tags" placeholder="denim, summer, casual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Meta Title (SEO)</label>
                            <input type="text" class="form-control" name="meta_title" id="meta_title">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" id="meta_keywords">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Meta Description</label>
                            <input type="text" class="form-control" name="meta_description" id="meta_description">
                        </div>
                    </div>

                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" checked>
                        <label class="form-check-label fw-semibold" for="is_published" style="font-size:0.82rem;">Publish Immediately</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBlogBtn">Save Article</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')

<script>
$(function() {
    const table = $('#blogTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.blog.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'article_info', name: 'blog_title' },
            { data: 'views', name: 'view_count' },
            { data: 'status_badge', name: 'is_published' },
            { data: 'date', name: 'published_at' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#addBlogBtn').on('click', function() {
        $('#blogForm')[0].reset();
        $('#blog_id').val('');
        $('#blogModalTitle').text('Write New Article');
        $('#blogModal').modal('show');
    });

    $('#blogForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $('#saveBlogBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('shop.blog.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#saveBlogBtn').prop('disabled', false).text('Save Article');
                if (res.status === 'success') {
                    $('#blogModal').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function(xhr) {
                $('#saveBlogBtn').prop('disabled', false).text('Save Article');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    $(document).on('click', '.edit-blog-btn', function() {
        const id = $(this).data('id');
        $.get("{{ url('/shop/blog/edit') }}/" + id, function(res) {
            if (res.status === 'success') {
                const b = res.data;
                $('#blog_id').val(b.blog_id);
                $('#blog_title').val(b.blog_title);
                $('#blog_slug').val(b.blog_slug);
                $('#blog_category').val(b.blog_category);
                $('#blog_excerpt').val(b.blog_excerpt);
                $('#blog_content').val(b.blog_content);
                $('#tags').val(b.tags);
                $('#meta_title').val(b.meta_title);
                $('#meta_keywords').val(b.meta_keywords);
                $('#meta_description').val(b.meta_description);
                $('#is_published').prop('checked', b.is_published == 1);
                $('#blogModalTitle').text('Edit Article');
                $('#blogModal').modal('show');
            }
        });
    });

    $(document).on('click', '.delete-blog-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this article?',
            text: "This story will be removed permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.blog.delete') }}", {
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
