<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Vendor Management — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-store text-shop-primary me-2"></i>Vendor Management</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <span>Vendors</span>
            </div>
        </div>
    </div>

    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Vendors</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="vendorTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Shop Name</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Approval</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th width="120">Actions</th>
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
    const table = $('#vendorTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.vendor.list') }}",
        columns: [
            { data: 'id' },
            { data: 'shop_name' },
            { data: 'name' },
            { data: 'email' },
            { data: 'approval_badge', orderable: false },
            { data: 'status_badge', orderable: false },
            { data: 'created_at' },
            { data: 'action', orderable: false },
        ]
    });

    window.approveVendor = function(id) {
        Swal.fire({ title: 'Approve vendor?', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, approve' })
        .then(r => {
            if (!r.isConfirmed) return;
            $.post('{{ route("shop.vendor.approve") }}', { vendor_id: id, _token: '{{ csrf_token() }}' }, function(res) {
                Swal.fire('Approved!', res.message, 'success').then(() => table.ajax.reload());
            });
        });
    };

    window.rejectVendor = function(id) {
        Swal.fire({ title: 'Reject vendor?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, reject', confirmButtonColor: '#dc3545' })
        .then(r => {
            if (!r.isConfirmed) return;
            $.post('{{ route("shop.vendor.reject") }}', { vendor_id: id, _token: '{{ csrf_token() }}' }, function(res) {
                Swal.fire('Rejected', res.message, 'info').then(() => table.ajax.reload());
            });
        });
    };

    window.toggleVendorStatus = function(id, current) {
        $.post('{{ route("shop.vendor.status") }}', { vendor_id: id, _token: '{{ csrf_token() }}' }, function(res) {
            table.ajax.reload(null, false);
        });
    };
});
</script>
</body>
</html>
