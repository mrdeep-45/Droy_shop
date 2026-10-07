<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Earnings | Vendor</title>
    @include('shop.include.header_link')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .earning-card { border-radius: 12px; padding: 20px; color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .earning-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .earning-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .earning-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .earning-icon { font-size: 2.5rem; opacity: 0.8; }
        .earning-value { font-size: 1.8rem; font-weight: 700; margin: 10px 0 5px; }
        .earning-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; }
    </style>
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="container-fluid pt-3">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-0 fw-bold"><i class="bx bx-wallet me-2"></i>My Earnings</h4>
                <p class="text-muted">Track your revenue and payouts.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="earning-card earning-success d-flex align-items-center justify-content-between">
                    <div>
                        <div class="earning-label">Available Earnings</div>
                        <div class="earning-value">₹{{ number_format($totalEarnings, 2) }}</div>
                        <small style="opacity:0.8;">From delivered orders</small>
                    </div>
                    <i class="bx bx-rupee earning-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="earning-card earning-warning d-flex align-items-center justify-content-between">
                    <div>
                        <div class="earning-label">Pending Earnings</div>
                        <div class="earning-value">₹{{ number_format($pendingEarnings, 2) }}</div>
                        <small style="opacity:0.8;">In transit or processing</small>
                    </div>
                    <i class="bx bx-time earning-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="earning-card earning-danger d-flex align-items-center justify-content-between">
                    <div>
                        <div class="earning-label">Lost / Refunded</div>
                        <div class="earning-value">₹{{ number_format($lostEarnings, 2) }}</div>
                        <small style="opacity:0.8;">Cancelled or returned items</small>
                    </div>
                    <i class="bx bx-x-circle earning-icon"></i>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white fw-bold py-3">Earning Ledger</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle w-100" id="earningTable">
                        <thead class="table-light">
                            <tr>
                                <th>Order Date</th>
                                <th>Order No</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Earning</th>
                                <th>Status</th>
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
    $('#earningTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('vendor.earning.list') }}",
        order: [[0, 'desc']],
        columns: [
            { data: 'created_at', name: 'o.created_at' },
            { data: 'order_number', name: 'o.order_number', className: 'fw-semibold text-primary' },
            { data: 'product_name', name: 'i.product_name' },
            { data: 'quantity', name: 'i.quantity', className: 'text-center' },
            { data: 'earning', name: 'earning', className: 'text-end fw-bold text-success' },
            { data: 'order_status', name: 'o.order_status', className: 'text-center' }
        ]
    });
});
</script>
</body>
</html>
