<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>{{ $page_title }} — DPShop Delivery</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-package text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('delivery.dashboard') }}">Dashboard</a> / <a href="{{ route('delivery.order.index') }}">Assigned Orders</a> / <span>{{ $assignment->order_number }}</span>
            </div>
        </div>
        <a href="{{ route('delivery.order.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;font-size:0.85rem;">
            <i class="bx bx-arrow-back me-1"></i> Back to Orders
        </a>
    </div>

    @php
        $addr = is_string($assignment->shipping_address) ? json_decode($assignment->shipping_address, true) : $assignment->shipping_address;
        $fullAddress = ($addr['address_line1'] ?? ($addr['address'] ?? '')) . ' ' . ($addr['city'] ?? '') . ' ' . ($addr['state'] ?? '') . ' ' . ($addr['pincode'] ?? '');
    @endphp

    <div class="row g-4">
        {{-- Left: Order & Customer Details --}}
        <div class="col-lg-7">
            {{-- Customer Card --}}
            <div class="shop-card mb-4">
                <div class="card-header">
                    <span><i class="bx bx-user me-2 text-primary"></i>Customer & Shipping Details</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $assignment->customer_name }}</h5>
                            <div class="text-muted" style="font-size:0.85rem;"><i class="bx bx-envelope me-1"></i>{{ $assignment->customer_email }}</div>
                        </div>
                        <a href="tel:{{ $assignment->customer_phone }}" class="btn btn-success" style="border-radius:10px;">
                            <i class="bx bx-phone-call me-1"></i> Call Customer
                        </a>
                    </div>

                    <div class="p-3 rounded mb-3" style="background:#f8f9fa;border:1px solid #edf2f7;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-secondary" style="font-size:0.8rem;"><i class="bx bx-map-pin text-danger me-1"></i>Delivery Location</span>
                            <a href="https://maps.google.com/?q={{ urlencode($fullAddress) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;border-radius:6px;">
                                <i class="bx bx-navigation me-1"></i> Google Maps
                            </a>
                        </div>
                        <div class="fw-medium text-dark" style="font-size:0.9rem;">
                            {{ $addr['address_line1'] ?? ($addr['address'] ?? 'N/A') }}
                        </div>
                        <div class="text-muted" style="font-size:0.82rem;">
                            {{ $addr['city'] ?? '' }}{{ !empty($addr['state']) ? ', ' . $addr['state'] : '' }} {{ !empty($addr['pincode']) ? ' - ' . $addr['pincode'] : '' }}
                        </div>
                    </div>

                    @if(!empty($assignment->customer_notes))
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size:0.82rem;border-radius:8px;">
                        <i class="bx bx-info-circle me-1"></i> <strong>Customer Note:</strong> {{ $assignment->customer_notes }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Order Items --}}
            <div class="shop-card">
                <div class="card-header">
                    <span><i class="bx bx-list-check me-2 text-primary"></i>Package Items ({{ count($items) }})</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.85rem;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="ps-3">Item</th>
                                    <th>Qty</th>
                                    <th class="text-end pe-3">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $item->product_name }}</div>
                                        @if($item->variant_title)
                                        <small class="text-muted">Option: {{ $item->variant_title }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end pe-3 fw-bold">{{ formatShopPrice($item->total_price) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Status Controls & OTP Verification --}}
        <div class="col-lg-5">
            {{-- Billing & Collection Box --}}
            <div class="shop-card mb-4" style="background:linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);color:#fff;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="opacity:0.85;font-size:0.85rem;">Amount to Collect</span>
                        <span class="badge {{ $assignment->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1" style="border-radius:20px;">
                            {{ strtoupper($assignment->payment_method) }} — {{ strtoupper($assignment->payment_status) }}
                        </span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ formatShopPrice($assignment->total_amount) }}</h2>
                    <small style="opacity:0.8;">
                        @if($assignment->payment_method === 'cod' && $assignment->payment_status !== 'paid')
                            <i class="bx bx-error-circle me-1"></i> Collect cash from customer before handing over package!
                        @else
                            <i class="bx bx-check-circle me-1"></i> Payment already completed online.
                        @endif
                    </small>
                </div>
            </div>

            {{-- Delivery Progression --}}
            <div class="shop-card mb-4">
                <div class="card-header">
                    <span><i class="bx bx-cycling me-2 text-primary"></i>Delivery Stage</span>
                </div>
                <div class="card-body">
                    <form id="statusUpdateForm">
                        @csrf
                        <input type="hidden" name="assignment_id" value="{{ $assignment->assignment_id }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Update Delivery Status</label>
                            <select class="form-select" name="delivery_status" id="delivery_status_select">
                                <option value="picked" {{ $assignment->delivery_status === 'picked' ? 'selected' : '' }}>1. Order Picked Up from Hub</option>
                                <option value="out_for_delivery" {{ $assignment->delivery_status === 'out_for_delivery' ? 'selected' : '' }}>2. Out For Delivery</option>
                                <option value="failed">3. Failed Delivery (Reschedule)</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="failureReasonBox">
                            <label class="form-label fw-semibold text-danger" style="font-size:0.82rem;">Reason for Failure</label>
                            <textarea class="form-control" name="failure_reason" rows="2" placeholder="e.g. Customer unreachable, address locked, customer requested delivery tomorrow"></textarea>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100" id="updateStatusBtn" style="border-radius:10px;font-weight:600;">
                            Update Status Stage
                        </button>
                    </form>
                </div>
            </div>

            {{-- OTP VERIFICATION CARD (Final step) --}}
            <div class="shop-card" style="border:2px dashed #0d6efd;">
                <div class="card-header" style="background:#e7f1ff;">
                    <span class="text-primary fw-bold"><i class="bx bx-shield-quarter me-2"></i>Complete Delivery with OTP</span>
                </div>
                <div class="card-body p-4 text-center">
                    <p class="text-muted" style="font-size:0.85rem;">
                        Ask the customer for the <strong>4-digit Delivery OTP</strong> provided in their tracking screen/email upon package handover.
                    </p>

                    <form id="otpVerifyForm">
                        @csrf
                        <input type="hidden" name="assignment_id" value="{{ $assignment->assignment_id }}">

                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg text-center fw-bold text-primary" 
                                   name="delivery_otp" id="delivery_otp" maxlength="6" 
                                   placeholder="• • • •" 
                                   style="letter-spacing:10px;font-size:1.8rem;border-radius:12px;" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100" id="verifyOtpBtn" style="border-radius:12px;font-weight:700;">
                            <i class="bx bx-check-double me-1"></i> Verify OTP & Mark Delivered
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

@include('shop.include.footer_link')

<script>
$(function() {
    $('#delivery_status_select').on('change', function() {
        if ($(this).val() === 'failed') {
            $('#failureReasonBox').removeClass('d-none');
        } else {
            $('#failureReasonBox').addClass('d-none');
        }
    });

    // Update status
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();
        $('#updateStatusBtn').prop('disabled', true).text('Updating...');

        $.ajax({
            url: "{{ route('delivery.order.status') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $('#updateStatusBtn').prop('disabled', false).text('Update Status Stage');
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Status Updated', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function(xhr) {
                $('#updateStatusBtn').prop('disabled', false).text('Update Status Stage');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to update status';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    // OTP Verify
    $('#otpVerifyForm').on('submit', function(e) {
        e.preventDefault();
        $('#verifyOtpBtn').prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> Verifying...');

        $.ajax({
            url: "{{ route('delivery.order.otp_verify') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $('#verifyOtpBtn').prop('disabled', false).html('<i class="bx bx-check-double me-1"></i> Verify OTP & Mark Delivered');
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Delivered Successfully!',
                        text: res.message,
                        confirmButtonText: 'Go to Deliveries'
                    }).then(() => {
                        window.location.href = "{{ route('delivery.order.index') }}";
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Verification Failed', text: res.message });
                }
            },
            error: function(xhr) {
                $('#verifyOtpBtn').prop('disabled', false).html('<i class="bx bx-check-double me-1"></i> Verify OTP & Mark Delivered');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Invalid OTP or server error';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });
});
</script>
</body>
</html>
