<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Review Return | Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="container-fluid pt-3">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold"><i class="bx bx-refresh me-2"></i>Review Return for Order #{{ $order->order_number }}</h4>
                <a href="{{ route('shop.return.index') }}" class="btn btn-outline-dark"><i class="bx bx-arrow-back"></i> Back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Items -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Items to Return</div>
                    <div class="card-body p-0">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th class="text-end">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product_name }}</div>
                                        <small class="text-muted">Code: {{ $item->product_code }}</small>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold text-danger">₹{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end fs-5 fw-bold">Total Refund Amount:</td>
                                    <td class="text-end fs-5 fw-bold text-danger">₹{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Return Request History</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach($tracking as $track)
                            <li class="mb-3 border-bottom pb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-uppercase badge {{ $track->status == 'return_requested' ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ str_replace('_', ' ', $track->status) }}</span>
                                    <small class="text-muted">{{ date('d-M-Y h:i A', strtotime($track->created_at)) }}</small>
                                </div>
                                <p class="mb-0 mt-1 text-secondary">{{ $track->description }}</p>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4 border-top border-warning border-3">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Action Panel</h5>
                        @if($order->order_status == 'return_requested')
                            <p class="text-muted small">Please inspect the return request and choose to approve or reject the refund.</p>
                            
                            <form action="{{ route('shop.return.approve') }}" method="POST" class="mb-3" onsubmit="return confirm('Are you sure you want to approve this return and refund the amount?');">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <button type="submit" class="btn btn-success w-100 fw-bold"><i class="bx bx-check-circle me-1"></i> Approve & Refund</button>
                            </form>

                            <hr>

                            <form action="{{ route('shop.return.reject') }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this return request?');">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <div class="mb-2">
                                    <label class="form-label fw-semibold small">Reason for Rejection <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="2" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100 fw-bold"><i class="bx bx-x-circle me-1"></i> Reject Return</button>
                            </form>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="bx bx-info-circle me-1"></i> This return request has already been processed ({{ ucfirst($order->order_status) }}).
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold"><i class="bx bx-user me-1"></i> Customer Details</div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $order->customer_name }}</h6>
                        <p class="text-muted mb-1"><i class="bx bx-envelope"></i> {{ $order->email }}</p>
                        <p class="text-muted mb-0"><i class="bx bx-phone"></i> {{ $order->customer_phone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('shop.include.footer_link')
</body>
</html>
