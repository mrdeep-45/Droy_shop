<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Customer Directory — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-group text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <span>Customers</span>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Registered Customers</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="customerTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th width="110">Orders</th>
                            <th width="110">Verification</th>
                            <th width="90">Status</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

@include('shop.include.footer_link')

<script>
$(function() {
    const table = $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.customer.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'customer_info', name: 'name' },
            { data: 'contact_info', name: 'email' },
            { data: 'location', name: 'city' },
            { data: 'orders_count', orderable: false, searchable: false },
            { data: 'verified_badge', name: 'is_email_verified' },
            { data: 'status_badge', name: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $(document).on('click', '.toggle-status-btn', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status == 1 ? 'block' : 'unblock';

        Swal.fire({
            title: `Confirm ${actionText}?`,
            text: `Are you sure you want to ${actionText} this customer account?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Yes, ${actionText}!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.customer.status') }}", {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                }, function(res) {
                    if (res.status === 'success') {
                        table.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Updated', text: res.message, timer: 1500, showConfirmButton: false });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
