<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Vendor Earnings — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-wallet text-shop-primary me-2"></i>Vendor Earnings</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <span>Vendor Earnings</span>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="shop-card p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Delivered Earnings</div>
                    <h3 class="mt-2 mb-0 fw-bold text-success">₹{{ number_format($totalEarnings, 2) }}</h3>
                    <small class="text-muted">Total earned from completed orders</small>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-circle">
                    <i class="bx bx-rupee fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="shop-card p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Pending / In Transit</div>
                    <h3 class="mt-2 mb-0 fw-bold text-warning">₹{{ number_format($pendingEarnings, 2) }}</h3>
                    <small class="text-muted">Orders currently in progress</small>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-circle">
                    <i class="bx bx-time fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="shop-card p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Lost / Cancelled</div>
                    <h3 class="mt-2 mb-0 fw-bold text-danger">₹{{ number_format($lostEarnings, 2) }}</h3>
                    <small class="text-muted">Cancelled or returned items</small>
                </div>
                <div class="p-3 bg-danger-subtle text-danger rounded-circle">
                    <i class="bx bx-x-circle fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="shop-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Vendor Earnings Ledger</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="adminVendorEarningTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Order No</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Earning</th>
                            <th>Order Status</th>
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
    $('#adminVendorEarningTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.vendor_earning.list') }}",
        order: [[0, 'desc']],
        columns: [
            { data: 'created_at', name: 'o.created_at' },
            { data: 'vendor_name', name: 'v.shop_name' },
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
