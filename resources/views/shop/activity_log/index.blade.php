<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Activity Log — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-history text-shop-primary me-2"></i>Activity Log</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <span>Activity Log</span>
            </div>
        </div>
    </div>

    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Admin / Vendor / Staff Activity</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="logTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Date &amp; Time</th>
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
    $('#logTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[5, 'desc']],
        ajax: "{{ route('shop.activity_log.list') }}",
        columns: [
            { data: 'id' },
            { data: 'user_badge', orderable: false },
            { data: 'action' },
            { data: 'description' },
            { data: 'ip_address' },
            { data: 'created_at' },
        ]
    });
});
</script>
</body>
</html>
