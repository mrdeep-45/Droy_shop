<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
</head>

<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-8 col-sm-12 mx-auto mt-5">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">Change Password</div>
                            </div>
                            <div class="card-body">
                                <form id="changePasswordForm" novalidate>
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                             <input type="password" class="form-control" id="current_password"
                                            name="current_password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="fa fa-eye-slash"></i>
                                            </button>
                                        </div>
                                       
                                        <div class="invalid-feedback">Please enter current password.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password <span
                                                class="text-danger">*</span></label>
                                                <div class="input-group">
                                                     <input type="password" class="form-control" id="new_password"
                                                        name="new_password" required>
                                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </button>
                                                </div>
                                       
                                        <div class="invalid-feedback">Please enter a new password.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Confirm New Password
                                            <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                             <input type="password" class="form-control" id="new_password_confirmation"
                                            name="new_password_confirmation" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="fa fa-eye-slash"></i>
                                            </button>
                                        </div>
                                       
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary" id="savePasswordBtn">Update
                                            Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('include.footer')
    </div>
    @include('include.footer_link')

    <script>
        $(document).ready(function () {

            // Toggle password visibility
            $(document).on('click', '.toggle-password', function () {
                let input = $(this).closest('.input-group').find('input');
                let icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    // RemixIcon classes (or change to 'fa-eye' / 'fa-eye-slash' for FontAwesome)
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });
            $('#changePasswordForm').submit(function (e) {
                e.preventDefault();

                let currentPass = $('#current_password').val().trim();
                let newPass = $('#new_password').val().trim();
                let confirmPass = $('#new_password_confirmation').val().trim();

                $('.is-invalid').removeClass('is-invalid');

                // Frontend Warnings
                if (!currentPass || !newPass || !confirmPass) {
                    iziToast.warning({ title: 'Warning', message: 'All password fields are mandatory.', position: 'topRight' });
                    return false;
                }

                if (newPass !== confirmPass) {
                    $('#new_password_confirmation').addClass('is-invalid');
                    iziToast.warning({ title: 'Warning', message: 'New password and confirmation do not match.', position: 'topRight' });
                    return false;
                }

                if (currentPass === newPass) {
                    $('#new_password').addClass('is-invalid');
                    iziToast.warning({ title: 'Warning', message: 'New password cannot be the same as the current password.', position: 'topRight' });
                    return false;
                }

                let formData = new FormData(this);
                $('#savePasswordBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('password.update') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#savePasswordBtn').prop('disabled', false).html('Update Password');

                        if (response.success) {
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                            $('#changePasswordForm')[0].reset();
                        } else {
                            let msg = response.message || 'An error occurred.'; 

                            if (msg.includes('Current password')) {
                                $('#current_password').addClass('is-invalid');
                                $('#current_password').next('.invalid-feedback').text(msg);
                            }
                            iziToast.warning({
                                title: 'Warning',
                                message: response.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function (xhr) {
                        $('#savePasswordBtn').prop('disabled', false).html('Update Password');
                        // Standard validation errors (if any) are caught here
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#' + key).addClass('is-invalid');
                                iziToast.warning({ title: 'Warning', message: value[0], position: 'topRight' });
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>