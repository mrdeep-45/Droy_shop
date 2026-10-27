<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Change Password — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-lock-alt text-shop-primary me-2"></i>Change Password</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> /
                <a href="{{ route('shop.profile') }}">Profile</a> /
                <span>Change Password</span>
            </div>
        </div>
    </div>

    <div class="row justify-content-center px-3 pb-4">
        <div class="col-md-6">
            <div class="shop-card">
                <div class="card-header">
                    <span><i class="bx bx-shield-quarter me-2 text-primary"></i>Update Your Password</span>
                </div>
                <div class="card-body p-4">
                    <form id="passwordForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Enter current password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('current_password', this)"><i class="bx bx-show"></i></button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" required placeholder="Minimum 6 characters">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('new_password', this)"><i class="bx bx-show"></i></button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" minlength="6" required placeholder="Repeat new password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('new_password_confirmation', this)"><i class="bx bx-show"></i></button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('shop.profile') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Back to Profile
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="savePassBtn">
                                <i class="bx bx-check-circle me-1"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

@include('shop.include.footer_link')

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    } else {
        input.type = 'password';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    }
}

$('#passwordForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#savePassBtn');
    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Updating...');

    $.ajax({
        url: "{{ route('shop.change_password.update') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(res) {
            btn.prop('disabled', false).html('<i class="bx bx-check-circle me-1"></i>Update Password');
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('shop.profile') }}";
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="bx bx-check-circle me-1"></i>Update Password');
            let msg = 'Failed to update password.';
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
