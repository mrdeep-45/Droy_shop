@extends('store.layouts.app')
@section('title', 'My Addresses')
@section('content')
<div class="container py-5">
    <div class="row">
        {{-- Account Sidebar --}}
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="p-4 bg-primary text-white rounded-top-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">
                                {{ substr(session('customer_name', 'U'), 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ session('customer_name') }}</h6>
                                <small class="opacity-75">Customer</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-flush py-2">
                        <a href="{{ route('store.orders') }}" class="list-group-item list-group-item-action border-0"><i class="bx bx-package me-2"></i> My Orders</a>
                        <a href="{{ route('store.wishlist') }}" class="list-group-item list-group-item-action border-0"><i class="bx bx-heart me-2"></i> Wishlist</a>
                        <a href="{{ route('store.address') }}" class="list-group-item list-group-item-action border-0 active fw-semibold"><i class="bx bx-map-pin me-2"></i> Addresses</a>
                        <form action="{{ route('store.logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button class="list-group-item list-group-item-action border-0 text-danger"><i class="bx bx-log-out me-2"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- Content --}}
        <div class="col-md-9">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold mb-0">My Addresses</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="bx bx-plus me-1"></i> Add Address
                </button>
            </div>

            @if($addresses->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bx bx-map-pin fs-1"></i>
                    <p class="mt-2">No saved addresses yet.</p>
                </div>
            @else
            <div class="row g-3">
                @foreach($addresses as $addr)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 {{ $addr->is_default ? 'border-primary' : '' }}">
                        <div class="card-body">
                            @if($addr->is_default)
                                <span class="badge bg-primary mb-2">Default</span>
                            @endif
                            <p class="fw-semibold mb-1">{{ $addr->name }}</p>
                            <p class="mb-1 text-muted small">{{ $addr->address }}</p>
                            <p class="mb-1 text-muted small">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}</p>
                            <p class="mb-2 text-muted small">{{ $addr->country }}</p>
                            <p class="mb-2"><i class="bx bx-phone me-1"></i>{{ $addr->phone }}</p>
                            <div class="d-flex gap-2">
                                @if(!$addr->is_default)
                                <button onclick="setDefault({{ $addr->id }})" class="btn btn-outline-primary btn-sm">Set Default</button>
                                @endif
                                <button onclick="deleteAddress({{ $addr->id }})" class="btn btn-outline-danger btn-sm">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Address Modal --}}
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address *</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-6">
                        <label class="form-label">City *</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">State *</label>
                        <input type="text" name="state" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Pincode *</label>
                        <input type="text" name="pincode" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="India">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefault">
                            <label class="form-check-label" for="isDefault">Set as default address</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#addAddressForm').on('submit', function(e) {
    e.preventDefault();
    $.post('{{ route("store.address.store") }}', $(this).serialize(), function(res) {
        if (res.success) {
            Swal.fire('Saved!', res.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
});

function setDefault(id) {
    $.post('{{ route("store.address.default") }}', { address_id: id, _token: '{{ csrf_token() }}' }, function(res) {
        if (res.success) location.reload();
    });
}

function deleteAddress(id) {
    Swal.fire({ title: 'Remove address?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, remove', confirmButtonColor: '#dc3545' })
    .then(r => {
        if (!r.isConfirmed) return;
        $.post('{{ route("store.address.delete") }}', { address_id: id, _token: '{{ csrf_token() }}' }, function(res) {
            if (res.success) location.reload();
        });
    });
}
</script>
@endpush
@endsection
