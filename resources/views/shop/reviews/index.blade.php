<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Customer Reviews & Ratings — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-star text-warning me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Customers / <span>Reviews & Ratings</span>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Product Ratings & Feedback</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="reviewTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="200">Product</th>
                            <th width="180">Customer</th>
                            <th width="130">Rating</th>
                            <th>Review & Feedback</th>
                            <th width="100">Status</th>
                            <th width="110">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- ══════ REPLY TO REVIEW MODAL ══════ --}}
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold"><i class="bx bx-reply text-primary me-2"></i>Official Store Reply</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyForm">
                @csrf
                <input type="hidden" name="review_id" id="reply_review_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Store Response to Customer</label>
                        <textarea class="form-control" name="admin_reply" id="admin_reply" rows="4" required placeholder="Write a professional reply to the customer's feedback..."></textarea>
                    </div>
                    <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Saving this reply will automatically notify the customer via in-app notification and email.</small>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveReplyBtn">Post Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')

<script>
$(function() {
    const table = $('#reviewTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.review.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'product_info', name: 'tbl_shop_products.product_name' },
            { data: 'customer_info', name: 'shop_customers.name' },
            { data: 'rating_stars', name: 'rating' },
            { data: 'review_details', name: 'review_body' },
            { data: 'status_badge', name: 'is_approved' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    // Toggle Approve / Hide
    $(document).on('click', '.toggle-approve-btn', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status == 1 ? 'approve' : 'hide';

        Swal.fire({
            title: `Confirm ${actionText}?`,
            text: `Do you want to ${actionText} this review on the storefront?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.review.approve') }}", {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                }, function(res) {
                    if (res.status === 'success') {
                        table.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false });
                    }
                });
            }
        });
    });

    // Open Reply Modal
    $(document).on('click', '.reply-review-btn', function() {
        const id = $(this).data('id');
        const reply = $(this).data('reply');
        $('#reply_review_id').val(id);
        $('#admin_reply').val(reply || '');
        $('#replyModal').modal('show');
    });

    // Submit Reply
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        $('#saveReplyBtn').prop('disabled', true).text('Sending...');

        $.ajax({
            url: "{{ route('shop.review.reply') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $('#saveReplyBtn').prop('disabled', false).text('Post Reply');
                if (res.status === 'success') {
                    $('#replyModal').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Reply Sent!', text: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function(xhr) {
                $('#saveReplyBtn').prop('disabled', false).text('Post Reply');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error sending reply';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    // Delete Review
    $(document).on('click', '.delete-review-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Review?',
            text: "This customer rating will be deleted permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.review.delete') }}", {
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
