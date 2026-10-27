<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Delivery Assignments — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-map-pin text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Delivery / <span>Assignments</span>
            </div>
        </div>
        <a href="{{ route('shop.delivery.index') }}" class="btn btn-outline-primary" style="border-radius:10px;font-size:0.85rem;">
            <i class="bx bx-cycling me-1"></i> Manage Delivery Staff
        </a>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Assigned Shipments</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="assignmentTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Assigned Staff</th>
                            <th width="90">OTP Code</th>
                            <th width="120">Status</th>
                            <th width="140">Assigned At</th>
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
    $('#assignmentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.delivery_assign.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'order_info', name: 'tbl_shop_orders.order_number' },
            { data: 'customer', name: 'shop_customers.name' },
            { data: 'staff', name: 'shop_delivery_staff.name' },
            { data: 'otp_code', name: 'delivery_otp' },
            { data: 'status_badge', name: 'delivery_status' },
            { data: 'assigned_date', name: 'assigned_at' }
        ],
        order: [[6, 'desc']]
    });
});
</script>
</body>
</html>
