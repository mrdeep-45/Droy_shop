<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Order Details | Admin</title>
    @include('shop.include.header_link')
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
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold"><i class="bx bx-receipt me-2"></i>Order #{{ $order->order_number }}</h4>
                <div>
                    <a href="{{ route('shop.order.invoice', $order->order_id) }}" target="_blank" class="btn btn-secondary me-2"><i class="bx bx-printer"></i> Print Invoice</a>
                    <a href="{{ route('shop.order.index') }}" class="btn btn-outline-dark"><i class="bx bx-arrow-back"></i> Back</a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Info -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bx bx-list-ul me-1"></i> Order Items</div>
                    <div class="card-body p-0">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product_name }}</div>
                                        <small class="text-muted">Code: {{ $item->product_code }}</small>
                                    </td>
                                    <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">₹{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal:</td>
                                    <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Discount @if($order->coupon_code) ({{ $order->coupon_code }}) @endif:</td>
                                    <td class="text-end text-success">-₹{{ number_format($order->coupon_discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td class="text-end">₹{{ number_format($order->shipping_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fs-5 fw-bold">Grand Total:</td>
                                    <td class="text-end fs-5 fw-bold text-primary">₹{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Tracking History -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bx bx-history me-1"></i> Order History</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach($tracking as $track)
                            <li class="mb-3 border-bottom pb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-uppercase badge bg-info text-dark">{{ str_replace('_', ' ', $track->status) }}</span>
                                    <small class="text-muted">{{ date('d-M-Y h:i A', strtotime($track->created_at)) }}</small>
                                </div>
                                <p class="mb-0 mt-1 text-secondary">{{ $track->description }}</p>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Status Update -->
                <div class="card shadow-sm border-0 mb-4 border-top border-primary border-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Update Status</h6>
                        <form action="{{ route('shop.order.status') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <div class="input-group">
                                <select name="order_status" class="form-select text-uppercase" required>
                                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $order->order_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="ready_to_ship" {{ $order->order_status == 'ready_to_ship' ? 'selected' : '' }}>Ready To Ship</option>
                                    <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bx bx-user me-1"></i> Customer Info</div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $order->customer_name }}</h6>
                        <p class="text-muted mb-1"><i class="bx bx-envelope"></i> {{ $order->email }}</p>
                        <p class="text-muted mb-0"><i class="bx bx-phone"></i> {{ $order->customer_phone }}</p>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bx bx-map me-1"></i> Shipping Address</div>
                    <div class="card-body">
                        @php
                            $addr = json_decode($order->shipping_address);
                        @endphp
                        @if($addr)
                            <h6 class="fw-bold mb-1">{{ $addr->full_name }}</h6>
                            <p class="mb-1 text-muted">{{ $addr->address_line1 }}, {{ $addr->address_line2 ?? '' }}</p>
                            <p class="mb-1 text-muted">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}</p>
                            <p class="mb-0 text-muted">Phone: {{ $addr->phone }}</p>
                        @else
                            <p class="text-muted mb-0">No address details available.</p>
                        @endif
                    </div>
                </div>

                <!-- Assign Delivery Staff -->
                @if(in_array($order->order_status, ['ready_to_ship', 'shipped']))
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bx bx-truck me-1"></i> Assign Delivery</div>
                    <div class="card-body">
                        <form action="{{ route('shop.order.assign') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <div class="mb-2">
                                <select name="delivery_staff_id" class="form-select" required>
                                    <option value="">Select Delivery Partner</option>
                                    @foreach($deliveryStaff as $staff)
                                        <option value="{{ $staff->delivery_staff_id }}">{{ $staff->name }} ({{ $staff->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Assign Partner</button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>

@include('shop.include.footer_link')
</body>
</html>
