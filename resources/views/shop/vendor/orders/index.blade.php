<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>My Orders | Vendor Dashboard</title>
    @include('shop.include.header_link')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="container-fluid pt-3">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="mb-0 text-uppercase fw-bold text-dark"><i class="bx bx-receipt me-2"></i>My Received Orders</h4>
                <p class="text-muted">View orders containing your products.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle w-100" id="ordersTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Order No</th>
                                <th>Customer</th>
                                <th>My Earning</th>
                                <th>Payment</th>
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
    $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('vendor.order.list') }}",
        order: [[0, 'desc']],
        columns: [
            { data: 0, name: 'created_at' },
            { data: 1, name: 'order_number' },
            { data: 2, name: 'customer_name' },
            { data: 3, name: 'vendor_total_amount', className: 'text-end fw-bold text-success' },
            { data: 4, name: 'payment_status', orderable: false, searchable: false },
            { data: 5, name: 'order_status', className: 'text-center' },
            { data: 6, name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ]
    });
});
</script>
</body>
</html>
