<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Assigned Orders | Delivery</title>
    @include('shop.include.header_link')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="container-fluid pt-3">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-12">
                <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-map-pin me-2"></i>My Delivery Queue</h4>
                <p class="text-muted">Orders assigned to you for delivery.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100" id="deliveryTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Assigned At</th>
                                <th>Order No</th>
                                <th>Customer Name</th>
                                <th>Shipping Details</th>
                                <th>Amount to Collect</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@include('shop.include.footer_link')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#deliveryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('delivery.order.list') }}",
        order: [[0, 'desc']],
        columns: [
            { data: 'assigned_at', name: 'assigned_at' },
            { data: 'order_number', name: 'o.order_number', className: 'fw-bold text-primary' },
            { data: 'customer_name', name: 'c.name' },
            { 
                data: 'shipping_address', 
                name: 'o.shipping_address',
                render: function(data) {
                    try {
                        let addr = JSON.parse(data);
                        return addr.city + ', ' + addr.pincode;
                    } catch(e) { return '-'; }
                }
            },
            { data: 'total_amount', name: 'o.total_amount', className: 'text-end fw-bold text-danger' },
            { data: 'delivery_status', name: 'da.delivery_status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ]
    });
});
</script>
</body>
</html>
