<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>{{ $supplier->supplier_name }} — Supplier Details</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-store-alt text-shop-primary me-2"></i>Supplier Details</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> /
                <a href="{{ route('shop.supplier.index') }}">Suppliers</a> /
                <span>{{ $supplier->supplier_name }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('shop.supplier.index') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:0.85rem;">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
            <a href="{{ route('shop.purchase.index') }}?supplier_id={{ $supplier->supplier_id }}" class="btn btn-primary" style="border-radius:8px;font-size:0.85rem;font-weight:600;">
                <i class="bx bx-cart-add me-1"></i> Create PO
            </a>
        </div>
    </div>

    <div class="row g-3">

        {{-- Left: Supplier Profile --}}
        <div class="col-xl-4">
            <div class="shop-card">
                <div class="card-body text-center" style="padding:28px;">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width:72px;height:72px;background:linear-gradient(135deg,#0d6efd,#0a58ca);font-size:1.8rem;color:#fff;font-weight:700;">
                        {{ strtoupper(substr($supplier->supplier_name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $supplier->supplier_name }}</h5>
                    <div class="text-muted mb-1" style="font-size:0.82rem;">{{ $supplier->supplier_code }}</div>
                    @if($supplier->contact_person)
                    <div class="text-muted" style="font-size:0.8rem;"><i class="bx bx-user me-1"></i>{{ $supplier->contact_person }}</div>
                    @endif

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        @if($supplier->is_active)
                        <span class="badge bg-success-subtle text-success px-3 py-2" style="border-radius:20px;font-size:0.78rem;"><i class="bx bx-check-circle me-1"></i>Active</span>
                        @else
                        <span class="badge bg-danger-subtle text-danger px-3 py-2" style="border-radius:20px;font-size:0.78rem;"><i class="bx bx-x-circle me-1"></i>Inactive</span>
                        @endif
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="card-body border-top" style="padding:16px 24px;">
                    <div class="fw-semibold mb-2" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;color:#9aacbf;">Contact</div>
                    @if($supplier->email)
                    <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.83rem;">
                        <i class="bx bx-envelope text-primary"></i>
                        <a href="mailto:{{ $supplier->email }}" style="color:#0d6efd;text-decoration:none;">{{ $supplier->email }}</a>
                    </div>
                    @endif
                    @if($supplier->phone)
                    <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.83rem;">
                        <i class="bx bx-phone text-success"></i>
                        <a href="tel:{{ $supplier->phone }}" style="color:#1a202c;text-decoration:none;">{{ $supplier->phone }}</a>
                    </div>
                    @endif
                    @if($supplier->address)
                    <div class="d-flex align-items-start gap-2 mt-2" style="font-size:0.83rem;">
                        <i class="bx bx-map text-warning mt-1"></i>
                        <div>
                            {{ $supplier->address }}<br>
                            @if($supplier->city || $supplier->state) {{ implode(', ', array_filter([$supplier->city, $supplier->state, $supplier->pincode])) }} @endif
                        </div>
                    </div>
                    @endif
                </div>

                {{-- GST/PAN --}}
                @if($supplier->gst_number || $supplier->pan_number)
                <div class="card-body border-top" style="padding:16px 24px;">
                    <div class="fw-semibold mb-2" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;color:#9aacbf;">Tax Details</div>
                    @if($supplier->gst_number)
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.82rem;">
                        <span class="text-muted">GST:</span>
                        <span class="fw-semibold font-monospace">{{ $supplier->gst_number }}</span>
                    </div>
                    @endif
                    @if($supplier->pan_number)
                    <div class="d-flex justify-content-between" style="font-size:0.82rem;">
                        <span class="text-muted">PAN:</span>
                        <span class="fw-semibold font-monospace">{{ $supplier->pan_number }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Right: Stats + Orders --}}
        <div class="col-xl-8">
            {{-- Stats --}}
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="shop-card text-center py-3">
                        <div class="fw-bold" style="font-size:1.4rem;color:#0d6efd;">{{ $stats['total_orders'] }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Purchase Orders</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="shop-card text-center py-3">
                        <div class="fw-bold" style="font-size:1.4rem;color:#20c997;">₹{{ number_format($stats['total_value'], 2) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Purchase Value</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="shop-card text-center py-3">
                        <div class="fw-bold" style="font-size:1.4rem;color:#fd7e14;">{{ $stats['pending_orders'] }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Pending Orders</div>
                    </div>
                </div>
            </div>

            {{-- Payment Terms --}}
            @if($supplier->payment_terms || $supplier->credit_days || $supplier->credit_limit)
            <div class="shop-card mb-3">
                <div class="card-header"><span><i class="bx bx-file me-2 text-primary"></i>Payment Terms</span></div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="text-muted mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;">Terms</div>
                            <div class="fw-semibold" style="font-size:0.85rem;">{{ $supplier->payment_terms ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;">Credit Days</div>
                            <div class="fw-semibold" style="font-size:0.85rem;">{{ $supplier->credit_days }} days</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted mb-1" style="font-size:0.75px;text-transform:uppercase;letter-spacing:0.5px;">Credit Limit</div>
                            <div class="fw-semibold" style="font-size:0.85rem;">₹{{ number_format($supplier->credit_limit, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Purchases --}}
            <div class="shop-card">
                <div class="card-header">
                    <span><i class="bx bx-receipt me-2 text-warning"></i>Recent Purchase Orders</span>
                    <a href="{{ route('shop.purchase.index') }}?supplier_id={{ $supplier->supplier_id }}"
                       class="btn btn-sm btn-outline-primary" style="font-size:0.78rem;">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.82rem;">
                            <thead>
                                <tr style="background:#f8f9fa;">
                                    <th class="ps-3 py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">PO #</th>
                                    <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Date</th>
                                    <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Amount</th>
                                    <th class="py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Payment</th>
                                    <th class="pe-3 py-2 border-0" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#6c757d;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $po)
                                <tr>
                                    <td class="ps-3 py-2 fw-semibold text-primary">
                                        <a href="{{ route('shop.purchase.show', $po->purchase_id) }}" style="text-decoration:none;">{{ $po->purchase_number }}</a>
                                    </td>
                                    <td class="py-2 text-muted">{{ \Carbon\Carbon::parse($po->purchase_date)->format('d M Y') }}</td>
                                    <td class="py-2 fw-semibold text-success">₹{{ number_format($po->total_amount, 2) }}</td>
                                    <td class="py-2">
                                        @if($po->payment_status === 'paid')
                                            <span class="badge bg-success-subtle text-success" style="font-size:0.7rem;border-radius:20px;">Paid</span>
                                        @elseif($po->payment_status === 'partial')
                                            <span class="badge bg-warning-subtle text-warning" style="font-size:0.7rem;border-radius:20px;">Partial</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger" style="font-size:0.7rem;border-radius:20px;">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="pe-3 py-2">
                                        @if($po->purchase_status === 'received')
                                            <span class="badge bg-success-subtle text-success" style="font-size:0.7rem;border-radius:20px;">Received</span>
                                        @elseif($po->purchase_status === 'ordered')
                                            <span class="badge bg-primary-subtle text-primary" style="font-size:0.7rem;border-radius:20px;">Ordered</span>
                                        @elseif($po->purchase_status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger" style="font-size:0.7rem;border-radius:20px;">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size:0.7rem;border-radius:20px;">Draft</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted" style="font-size:0.82rem;">
                                        <i class="bx bx-receipt" style="font-size:2rem;display:block;margin-bottom:6px;"></i>
                                        No purchase orders yet.
                                    </td>
                                </tr>
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
</body>
</html>
