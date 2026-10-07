<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>View Order | Vendor</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="container-fluid pt-3">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold"><i class="bx bx-receipt me-2"></i>Order #{{ $order->order_number }}</h4>
                <a href="{{ route('vendor.order.index') }}" class="btn btn-outline-dark"><i class="bx bx-arrow-back"></i> Back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Order Items (Vendor Specific) -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span>Items from My Store</span>
                        <span class="badge bg-primary rounded-pill">{{ count($items) }} Items</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th class="text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $vendorTotal = 0; @endphp
                                @foreach($items as $item)
                                @php $vendorTotal += $item->total_price; @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $item->product_image ? asset('storage/'.$item->product_image) : 'https://placehold.co/50' }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                                <small class="text-muted">Code: {{ $item->product_code }} | SKU: {{ $item->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold pe-3">₹{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">My Total Earning for this Order:</td>
                                    <td class="text-end fw-bold text-success fs-5 pe-3">₹{{ number_format($vendorTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Order Tracking Status</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 position-relative" style="border-left: 2px solid #e9ecef; margin-left: 10px; padding-left: 20px;">
                            @foreach($tracking as $track)
                            <li class="mb-3 position-relative">
                                <span class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -27px; top: 5px;"></span>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-uppercase" style="font-size: 0.85rem;">{{ str_replace('_', ' ', $track->status) }}</span>
                                    <small class="text-muted">{{ date('d M Y, h:i A', strtotime($track->created_at)) }}</small>
                                </div>
                                <p class="mb-0 mt-1 text-secondary small">{{ $track->description }}</p>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Customer Details -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Customer Info</div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $order->customer_name }}</h6>
                        <p class="text-muted mb-1"><i class="bx bx-envelope me-1"></i> {{ $order->email }}</p>
                        <p class="text-muted mb-0"><i class="bx bx-phone me-1"></i> {{ $order->customer_phone }}</p>
                    </div>
                </div>

                <!-- Shipping Details -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Shipping Address</div>
                    <div class="card-body">
                        @php $addr = json_decode($order->shipping_address); @endphp
                        @if($addr)
                            <h6 class="fw-bold mb-1">{{ $addr->full_name }}</h6>
                            <p class="text-muted mb-1 small">{{ $addr->address_line1 }}</p>
                            @if($addr->address_line2) <p class="text-muted mb-1 small">{{ $addr->address_line2 }}</p> @endif
                            <p class="text-muted mb-1 small">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}</p>
                            <p class="text-muted mb-0 small">Phone: {{ $addr->phone }}</p>
                        @else
                            <p class="text-muted small">No shipping address provided.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('shop.include.footer_link')
</body>
</html>
