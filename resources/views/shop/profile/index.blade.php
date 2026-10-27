<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>My Profile — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-user-circle text-shop-primary me-2"></i>My Profile</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <span>Profile</span>
            </div>
        </div>
    </div>

    <div class="row g-4 px-3 pb-4">
        {{-- Profile Summary Card --}}
        <div class="col-md-4">
            <div class="shop-card text-center p-4">
                <div class="mb-3">
                    @php
                        $userImg = $user->profile_image ?? $user->logo ?? null;
                    @endphp
                    @if($userImg && file_exists(public_path($userImg)))
                        <img src="{{ asset($userImg) }}" alt="Avatar" class="rounded-circle shadow-sm" style="width:110px; height:110px; object-fit:cover; border:3px solid #6366f1;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:110px; height:110px; font-size:2.8rem;">
                            {{ strtoupper(substr($user->name ?? $user->owner_name ?? $user->business_name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name ?? $user->owner_name ?? $user->business_name ?? 'Admin User' }}</h5>
                <span class="badge bg-primary-subtle text-primary mb-3">{{ $roleLabel ?? 'Admin' }}</span>
                <div class="text-muted small mb-2"><i class="bx bx-envelope me-1"></i>{{ $user->email ?? '—' }}</div>
                <div class="text-muted small"><i class="bx bx-phone me-1"></i>{{ $user->phone ?? '—' }}</div>
                
                <hr class="my-3">
                <a href="{{ route('shop.change_password') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bx bx-lock-alt me-1"></i>Change Password
                </a>
            </div>
        </div>

        {{-- Edit Profile Form --}}
        <div class="col-md-8">
            <div class="shop-card">
                <div class="card-header">
                    <span><i class="bx bx-edit me-2 text-primary"></i>Edit Profile Information</span>
                </div>
                <div class="card-body p-4">
                    <form id="profileForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name ?? $user->owner_name ?? '' }}" required>
                            </div>

                            @if($loginType === 'Vendor')
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Business / Shop Name</label>
                                <input type="text" name="business_name" class="form-control" value="{{ $user->business_name ?? $user->shop_name ?? '' }}">
                            </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email ?? '' }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}">
                            </div>

                            @if(in_array($loginType, ['ShopAdmin', 'ShopSuperAdmin']))
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Profile Photo</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                                <small class="text-muted">JPG, PNG, WebP up to 2MB.</small>
                            </div>
                            @endif

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4" id="saveProfileBtn">
                                    <i class="bx bx-save me-1"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

@include('shop.include.footer_link')

<script>
$('#profileForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#saveProfileBtn');
    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Saving...');

    const formData = new FormData(this);

    $.ajax({
        url: "{{ route('shop.profile.update') }}",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i>Save Changes');
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', res.message || 'Could not update profile.', 'error');
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i>Save Changes');
            let msg = 'Validation error occurred.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire('Error', msg, 'error');
        }
    });
});
</script>
</body>
</html>
