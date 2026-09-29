<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Manage Coupons | Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">
    <div class="container-fluid pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold"><i class="bx bxs-discount me-2"></i> Manage Coupons</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
                <i class="bx bx-plus me-1"></i> Add Coupon
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Limits</th>
                            <th>Valid Period</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $c)
                        <tr>
                            <td><span class="badge bg-primary fs-6">{{ $c->coupon_code }}</span></td>
                            <td>{{ $c->coupon_name }}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($c->discount_type) }}</span></td>
                            <td>
                                @if($c->discount_type == 'percent')
                                    {{ (float)$c->discount_value }}% 
                                    @if($c->max_discount_amount) <br><small class="text-muted">(Max: ₹{{ $c->max_discount_amount }})</small> @endif
                                @else
                                    ₹{{ (float)$c->discount_value }}
                                @endif
                            </td>
                            <td>
                                <small>
                                    Min Order: ₹{{ $c->min_order_amount }}<br>
                                    Usage: {{ $c->used_count }} / {{ $c->usage_limit ?? '∞' }}<br>
                                    Per User: {{ $c->per_user_limit }}
                                </small>
                            </td>
                            <td>
                                <small>
                                    From: {{ $c->valid_from ? date('d-M-Y', strtotime($c->valid_from)) : 'Anytime' }}<br>
                                    To: {{ $c->valid_until ? date('d-M-Y', strtotime($c->valid_until)) : 'Forever' }}
                                </small>
                            </td>
                            <td>
                                @if($c->status == 0)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editCouponModal{{ $c->coupon_id }}">Edit</button>
                                <form action="{{ route('shop.coupons.delete', $c->coupon_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editCouponModal{{ $c->coupon_id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('shop.coupons.update', $c->coupon_id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Coupon</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label>Coupon Code <span class="text-danger">*</span></label>
                                                    <input type="text" name="coupon_code" class="form-control text-uppercase" value="{{ $c->coupon_code }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Coupon Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="coupon_name" class="form-control" value="{{ $c->coupon_name }}" required>
                                                </div>
                                                <div class="col-md-12">
                                                    <label>Description</label>
                                                    <input type="text" name="description" class="form-control" value="{{ $c->description }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Discount Type <span class="text-danger">*</span></label>
                                                    <select name="discount_type" class="form-select" required>
                                                        <option value="percent" {{ $c->discount_type=='percent'?'selected':'' }}>Percentage (%)</option>
                                                        <option value="fixed" {{ $c->discount_type=='fixed'?'selected':'' }}>Fixed Amount (₹)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Discount Value <span class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ $c->discount_value }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Min Order Amount</label>
                                                    <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ $c->min_order_amount }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Max Discount (for %)</label>
                                                    <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="{{ $c->max_discount_amount }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Total Usage Limit</label>
                                                    <input type="number" name="usage_limit" class="form-control" placeholder="Leave empty for unlimited" value="{{ $c->usage_limit }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Valid From</label>
                                                    <input type="datetime-local" name="valid_from" class="form-control" value="{{ $c->valid_from ? date('Y-m-d\TH:i', strtotime($c->valid_from)) : '' }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Valid Until</label>
                                                    <input type="datetime-local" name="valid_until" class="form-control" value="{{ $c->valid_until ? date('Y-m-d\TH:i', strtotime($c->valid_until)) : '' }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="0" {{ $c->status==0?'selected':'' }}>Active</option>
                                                        <option value="1" {{ $c->status==1?'selected':'' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No coupons found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        </div>
    </div>
</main>

<!-- Add Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('shop.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" name="coupon_code" class="form-control text-uppercase" placeholder="e.g. SAVE20" required>
                        </div>
                        <div class="col-md-6">
                            <label>Coupon Name <span class="text-danger">*</span></label>
                            <input type="text" name="coupon_name" class="form-control" placeholder="e.g. Winter Sale 20% Off" required>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Discount Type <span class="text-danger">*</span></label>
                            <select name="discount_type" class="form-select" required>
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Discount Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="discount_value" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Min Order Amount</label>
                            <input type="number" step="0.01" name="min_order_amount" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label>Max Discount (for %)</label>
                            <input type="number" step="0.01" name="max_discount_amount" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Total Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-control" placeholder="Leave empty for unlimited">
                        </div>
                        <div class="col-md-4">
                            <label>Valid From</label>
                            <input type="datetime-local" name="valid_from" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Valid Until</label>
                            <input type="datetime-local" name="valid_until" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="0">Active</option>
                                <option value="1">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')
</body>
</html>
