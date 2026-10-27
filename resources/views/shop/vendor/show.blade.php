<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Vendor — {{ $vendor->shop_name ?? 'Profile' }} — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('shop.vendor.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-arrow-back"></i>
            </a>
            <div>
                <h4 class="mb-0">{{ $vendor->shop_name }}</h4>
                <div class="shop-breadcrumb">
                    <a href="{{ route('shop.dashboard') }}">Home</a> /
                    <a href="{{ route('shop.vendor.index') }}">Vendors</a> /
                    <span>{{ $vendor->shop_name }}</span>
                </div>
            </div>
            <div class="ms-3">
                @if($vendor->is_approved == 1)
                    <span class="badge bg-success fs-6">✓ Approved</span>
                @elseif($vendor->is_approved == 2)
                    <span class="badge bg-danger fs-6">✗ Rejected</span>
                @else
                    <span class="badge bg-warning text-dark fs-6">⏳ Pending</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 px-3 pb-4">

        {{-- Vendor Info Card --}}
        <div class="col-md-4">
            <div class="shop-card h-100">
                <div class="card-header"><i class="bx bx-user me-2 text-primary"></i>Vendor Details</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-3">
                        <tr><td class="text-muted">Name</td><td class="fw-semibold">{{ $vendor->name }}</td></tr>
                        <tr><td class="text-muted">Email</td><td>{{ $vendor->email }}</td></tr>
                        <tr><td class="text-muted">Phone</td><td>{{ $vendor->phone }}</td></tr>
                        <tr><td class="text-muted">Shop</td><td>{{ $vendor->shop_name }}</td></tr>
                        <tr><td class="text-muted">GST</td><td>{{ $vendor->gst_number ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Status</td><td>
                            <span class="badge bg-{{ $vendor->status == 1 ? 'success' : 'danger' }}">
                                {{ $vendor->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </td></tr>
                        <tr><td class="text-muted">Joined</td><td>{{ \Carbon\Carbon::parse($vendor->created_at)->format('d M Y') }}</td></tr>
                    </table>

                    @if($vendor->is_approved == 0)
                    <div class="d-flex gap-2">
                        <button onclick="approveVendor({{ $vendor->id }})" class="btn btn-success flex-grow-1">
                            <i class="bx bx-check me-1"></i>Approve
                        </button>
                        <button onclick="rejectVendor({{ $vendor->id }})" class="btn btn-danger flex-grow-1">
                            <i class="bx bx-x me-1"></i>Reject
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats + Products + Orders --}}
        <div class="col-md-8">

            {{-- Stats Row --}}
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="shop-card text-center py-3">
                        <div class="fs-2 fw-bold text-primary">{{ $products->count() }}</div>
                        <small class="text-muted">Products</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="shop-card text-center py-3">
                        <div class="fs-2 fw-bold text-success">{{ $orders->count() }}</div>
                        <small class="text-muted">Orders</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="shop-card text-center py-3">
                        <div class="fs-2 fw-bold text-warning">₹{{ number_format($totalEarnings, 0) }}</div>
                        <small class="text-muted">Earnings</small>
                    </div>
                </div>
            </div>

            {{-- Products --}}
            <div class="shop-card mb-4">
                <div class="card-header"><i class="bx bx-package me-2 text-primary"></i>Products</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Name</th><th>Price</th><th>Stock</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($products as $p)
                                <tr>
                                    <td>{{ $p->name }}</td>
                                    <td>₹{{ number_format($p->price, 2) }}</td>
                                    <td>{{ $p->stock_quantity ?? 0 }}</td>
                                    <td><span class="badge bg-{{ $p->status == 1 ? 'success' : 'secondary' }}">{{ $p->status == 1 ? 'Active' : 'Inactive' }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No products yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Orders --}}
            <div class="shop-card">
                <div class="card-header"><i class="bx bx-receipt me-2 text-primary"></i>Recent Orders</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Order #</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $o)
                                <tr>
                                    <td><a href="{{ route('shop.order.show', $o->id) }}">{{ $o->order_number }}</a></td>
                                    <td>₹{{ number_format($o->total_amount, 2) }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($o->status) }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($o->created_at)->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

@include('shop.include.footer_link')

<script>
function approveVendor(id) {
    Swal.fire({ title: 'Approve vendor?', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, approve' })
    .then(r => {
        if (!r.isConfirmed) return;
        $.post('{{ route("shop.vendor.approve") }}', { vendor_id: id, _token: '{{ csrf_token() }}' }, function(res) {
            Swal.fire('Approved!', res.message, 'success').then(() => location.reload());
        });
    });
}
function rejectVendor(id) {
    Swal.fire({ title: 'Reject vendor?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, reject', confirmButtonColor: '#dc3545' })
    .then(r => {
        if (!r.isConfirmed) return;
        $.post('{{ route("shop.vendor.reject") }}', { vendor_id: id, _token: '{{ csrf_token() }}' }, function(res) {
            Swal.fire('Rejected', res.message, 'info').then(() => location.reload());
        });
    });
}
</script>
</body>
</html>
