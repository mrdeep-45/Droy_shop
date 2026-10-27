<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>{{ $page_title }} — DPShop</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
<div class="container-fluid pt-3">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-dark"><i class="bx bx-history me-2"></i>{{ $page_title }}</h4>
            <a href="{{ route('shop.inventory.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to Inventory
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-primary-subtle rounded-3">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">{{ $product->product_name }}</h5>
                        <p class="mb-0 text-muted">{{ $product->product_code }} | SKU: {{ $product->sku ?? 'N/A' }}</p>
                    </div>
                    <div class="text-end">
                        <h6 class="text-muted mb-1 fs-7">Current Total Stock</h6>
                        <h3 class="fw-bold text-primary mb-0">{{ $product->stock_qty }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-1"></i> Transaction History</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date & Time</th>
                            <th>Transaction Type</th>
                            <th>Reference</th>
                            <th class="text-center">Before Stock</th>
                            <th class="text-center">Quantity Adjusted</th>
                            <th class="text-center">After Stock</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledger as $row)
                            <tr>
                                <td class="ps-4 text-muted" style="font-size:0.85rem;">{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y H:i A') }}</td>
                                <td>
                                    @php
                                        $typeBadgeClass = 'bg-secondary';
                                        $icon = 'bx-right-arrow-alt';
                                        if($row->transaction_type == 'purchase') { $typeBadgeClass = 'bg-primary'; $icon = 'bx-cart-download'; }
                                        elseif($row->transaction_type == 'sale') { $typeBadgeClass = 'bg-success'; $icon = 'bx-cart'; }
                                        elseif($row->transaction_type == 'adjustment') { $typeBadgeClass = 'bg-warning text-dark'; $icon = 'bx-slider-alt'; }
                                        elseif($row->transaction_type == 'return') { $typeBadgeClass = 'bg-info'; $icon = 'bx-undo'; }
                                    @endphp
                                    <span class="badge {{ $typeBadgeClass }} px-2 py-1"><i class="bx {{ $icon }} me-1"></i>{{ ucfirst($row->transaction_type) }}</span>
                                </td>
                                <td><small class="fw-semibold">{{ $row->reference_type ?? '—' }}</small></td>
                                <td class="text-center text-secondary">{{ (float)$row->before_stock }}</td>
                                <td class="text-center fw-bold {{ $row->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $row->quantity > 0 ? '+' : '' }}{{ (float)$row->quantity }}
                                </td>
                                <td class="text-center fw-bold text-dark">{{ (float)$row->after_stock }}</td>
                                <td><small class="text-muted">{{ $row->notes ?? '—' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-history fs-1 mb-2"></i><br>
                                    No inventory transactions found for this product.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>
@include('shop.include.footer_link')
</body>
</html>
