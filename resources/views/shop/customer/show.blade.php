<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Customer Profile — {{ $customer->name }}</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-user text-shop-primary me-2"></i>{{ $customer->name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <a href="{{ route('shop.customer.index') }}">Customers</a> / <span>{{ $customer->name }}</span>
            </div>
        </div>
        <a href="{{ route('shop.customer.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;font-size:0.85rem;">
            <i class="bx bx-arrow-back me-1"></i> Back to Customers
        </a>
    </div>

    <div class="row g-4 mb-4">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="shop-card text-center p-4 mb-4">
                @php
                    $img = $customer->profile_image
                        ? asset('storage/' . $customer->profile_image)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($customer->name) . '&background=0d6efd&color=fff&size=128';
                @endphp
                <img src="{{ $img }}" class="rounded-circle shadow-sm mb-3" style="width:96px;height:96px;object-fit:cover;">
                <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
                <div class="text-muted mb-2" style="font-size:0.82rem;">{{ $customer->customer_code ?? 'CUST-' . str_pad($customer->customer_id, 4, '0', STR_PAD_LEFT) }}</div>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($customer->is_email_verified)
                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bx bx-check-shield me-1"></i>Verified</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bx bx-time me-1"></i>Unverified</span>
                    @endif

                    @if($customer->status == 0)
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Blocked</span>
                    @endif
                </div>

                <div class="border-top pt-3 text-start" style="font-size:0.85rem;">
                    <div class="mb-2"><strong class="text-secondary"><i class="bx bx-envelope me-2"></i>Email:</strong> {{ $customer->email }}</div>
                    <div class="mb-2"><strong class="text-secondary"><i class="bx bx-phone me-2"></i>Phone:</strong> {{ $customer->phone ?? 'Not provided' }}</div>
                    <div class="mb-2"><strong class="text-secondary"><i class="bx bx-map-pin me-2"></i>Address:</strong> {{ $customer->address ?? ($customer->city ? $customer->city . ', ' . $customer->state : 'Not provided') }}</div>
                    <div><strong class="text-secondary"><i class="bx bx-calendar me-2"></i>Joined:</strong> {{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}</div>
                </div>
            </div>

            <div class="shop-card p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted" style="font-size:0.8rem;">Lifetime Spend</span>
                    <span class="badge bg-primary-subtle text-primary">{{ count($orders) }} Orders</span>
                </div>
                <h3 class="fw-bold text-primary mb-0">{{ formatShopPrice($totalSpent) }}</h3>
            </div>
        </div>

        {{-- Orders & Reviews --}}
        <div class="col-lg-8">
            {{-- Order History --}}
            <div class="shop-card mb-4">
                <div class="card-header">
                    <span><i class="bx bx-receipt me-2 text-primary"></i>Order History</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.83rem;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="ps-3">Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $o)
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">{{ $o->order_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($o->created_at)->format('d M Y') }}</td>
                                    <td class="fw-semibold">{{ formatShopPrice($o->total_amount) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($o->payment_method) }}</span></td>
                                    <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $o->order_status)) }}</span></td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('shop.order.show', $o->order_id) }}" class="btn btn-xs btn-outline-primary" style="font-size:0.75rem;padding:3px 8px;">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No orders placed by this customer yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Reviews by Customer --}}
            <div class="shop-card">
                <div class="card-header">
                    <span><i class="bx bx-star me-2 text-warning"></i>Customer Reviews & Ratings ({{ count($reviews) }})</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.83rem;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th class="text-end pe-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $r)
                                <tr>
                                    <td class="ps-3 fw-semibold text-primary">{{ $r->product_name }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-warning">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="bx {{ $i <= $r->rating ? 'bxs-star' : 'bx-star' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td>
                                        @if($r->review_title)<strong>{{ $r->review_title }}:</strong> @endif
                                        <span class="text-muted">{{ $r->review_body }}</span>
                                    </td>
                                    <td class="text-end pe-3 text-muted">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No reviews left by this customer.</td>
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
