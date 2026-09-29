<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Manage Returns | Admin</title>
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
                <h4 class="mb-0 text-uppercase fw-bold text-dark"><i class="bx bx-refresh me-2"></i>Returns & Refunds</h4>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle w-100" id="returnsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Date Requested</th>
                                <th>Order No</th>
                                <th>Customer</th>
                                <th>Amount to Refund</th>
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
    $('#returnsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.return.list') }}",
        order: [[0, 'desc']],
        columns: [
            { data: 'updated_at', name: 'updated_at' },
            { data: 'order_number', name: 'order_number' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'total_amount', name: 'total_amount', className: 'text-end fw-bold text-danger' },
            { data: 'order_status', name: 'order_status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ]
    });
});
</script>
</body>
</html>
