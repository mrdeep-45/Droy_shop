<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>{{ $page_title }} — HexaShop</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
<div class="container-fluid pt-3">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-receipt me-2"></i>Purchase Order: <span class="text-primary">{{ $purchase->purchase_number }}</span></h4>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="window.print()"><i class="bx bx-printer me-1"></i> Print PO</button>
                <a href="{{ route('shop.purchase.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Purchases
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4 print-card">
                <div class="card-body p-4 p-md-5">
                    <!-- Header -->
                    <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">PURCHASE ORDER</h2>
                            <p class="text-muted mb-0">PO Number: <strong>{{ $purchase->purchase_number }}</strong></p>
                            <p class="text-muted mb-0">Date: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-M-Y') }}</p>
                        </div>
                        <div class="text-end">
                            <h4 class="fw-bold text-primary mb-0">HexaShop</h4>
                            <p class="text-muted mb-0">Admin Warehouse</p>
                            <p class="text-muted mb-0">123 Business Park, City</p>
                        </div>
                    </div>

                    <!-- Parties -->
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="fw-bold text-muted text-uppercase mb-2">Vendor / Supplier</h6>
                            <h5 class="fw-bold text-dark mb-1">{{ $purchase->supplier_name }}</h5>
                            @if($purchase->contact_person)<p class="mb-0 text-muted">Attn: {{ $purchase->contact_person }}</p>@endif
                            @if($purchase->address)<p class="mb-0 text-muted">{{ $purchase->address }}, {{ $purchase->city }}, {{ $purchase->state }}</p>@endif
                            @if($purchase->phone)<p class="mb-0 text-muted"><i class="bx bx-phone"></i> {{ $purchase->phone }}</p>@endif
                            @if($purchase->email)<p class="mb-0 text-muted"><i class="bx bx-envelope"></i> {{ $purchase->email }}</p>@endif
                        </div>
                        <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                            <h6 class="fw-bold text-muted text-uppercase mb-2">Order Status</h6>
                            <div class="mb-2">
                                @if($purchase->purchase_status == 'draft') <span class="badge bg-secondary fs-6">Draft</span>
                                @elseif($purchase->purchase_status == 'ordered') <span class="badge bg-primary fs-6">Ordered</span>
                                @elseif($purchase->purchase_status == 'received') <span class="badge bg-success fs-6">Received</span>
                                @else <span class="badge bg-danger fs-6">Cancelled</span>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold text-muted text-uppercase mb-1 mt-3">Payment Status</h6>
                                @if($purchase->payment_status == 'pending') <span class="badge bg-danger fs-6">Pending Payment</span>
                                @elseif($purchase->payment_status == 'partial') <span class="badge bg-warning text-dark fs-6">Partially Paid</span>
                                @else <span class="badge bg-success fs-6">Fully Paid</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 text-center" style="width: 50px;">#</th>
                                    <th class="py-2">Item Description</th>
                                    <th class="py-2 text-end">Quantity</th>
                                    <th class="py-2 text-end">Unit Price</th>
                                    <th class="py-2 text-end">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->item_name }}</div>
                                        <small class="text-muted">{{ $item->material_code }}</small>
                                    </td>
                                    <td class="text-end">{{ (float)$item->quantity }} {{ $item->unit_short }}</td>
                                    <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-semibold">₹{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="row justify-content-end">
                        <div class="col-sm-5">
                            <table class="table table-borderless table-sm text-end">
                                <tr>
                                    <td class="text-muted fw-semibold">Subtotal</td>
                                    <td class="fw-semibold">₹{{ number_format($purchase->subtotal, 2) }}</td>
                                </tr>
                                @if($purchase->discount_amount > 0)
                                <tr>
                                    <td class="text-muted fw-semibold">Discount</td>
                                    <td class="text-danger">-₹{{ number_format($purchase->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($purchase->shipping_charge > 0)
                                <tr>
                                    <td class="text-muted fw-semibold">Shipping / Freight</td>
                                    <td>+₹{{ number_format($purchase->shipping_charge, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="text-dark fw-bold fs-5 pt-2">Total</td>
                                    <td class="text-primary fw-bold fs-5 pt-2">₹{{ number_format($purchase->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($purchase->notes)
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold text-muted text-uppercase mb-2">Notes & Terms</h6>
                        <p class="text-muted mb-0">{!! nl2br(e($purchase->notes)) !!}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 d-print-none">
            <!-- Sidebar actions could go here (Mark as Received, Make Payment, etc.) -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bx bx-cog me-1"></i> Actions</h6>
                </div>
                <div class="card-body p-4">
                    @if($purchase->purchase_status != 'received')
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-info-circle me-1"></i> Stock is updated only when the PO is marked as "Received".
                    </div>
                    <button class="btn btn-success w-100 mb-2 disabled" title="Feature coming soon"><i class="bx bx-check-double me-1"></i> Mark as Received</button>
                    @else
                    <div class="alert alert-success mb-3">
                        <i class="bx bx-check-circle me-1"></i> Stock has been updated for these items.
                    </div>
                    @endif
                    
                    <button class="btn btn-outline-primary w-100 disabled" title="Feature coming soon"><i class="bx bx-rupee me-1"></i> Record Payment</button>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
<style>
    @media print {
        body * { visibility: hidden; }
        .print-card, .print-card * { visibility: visible; }
        .print-card { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; padding: 0 !important; }
        .d-print-none { display: none !important; }
    }
</style>
</body>
</html>
