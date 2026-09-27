<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HexaShop — Customer Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #f8f9fa; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        
        .login-wrapper { width: 100%; max-width: 460px; margin: 20px; padding: 40px; background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); }
        .store-logo { text-align: center; margin-bottom: 30px; }
        .store-logo i { font-size: 3rem; color: #0d6efd; background: #f0f6ff; padding: 16px; border-radius: 20px; margin-bottom: 12px; }
        .store-logo h2 { font-weight: 800; color: #1a202c; font-size: 1.8rem; margin: 0; }
        .store-logo p { color: #8a97b0; font-size: 0.95rem; margin-top: 5px; }

        .form-label-custom { font-size: 0.88rem; font-weight: 600; color: #4a5568; margin-bottom: 6px; display: block; }
        .form-control-custom { border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 16px; font-size: 0.95rem; width: 100%; background: #f8fafc; transition: all 0.2s; }
        .form-control-custom:focus { background: #fff; border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13,110,253,0.1); outline: none; }
        
        .btn-login {
            width: 100%; background: #0d6efd; border: none; border-radius: 12px;
            color: #fff; font-weight: 700; font-size: 1rem; padding: 14px; cursor: pointer; margin-top: 10px; transition: all 0.2s;
        }
        .btn-login:hover { background: #0b5ed7; box-shadow: 0 8px 20px rgba(13,110,253,0.25); transform: translateY(-1px); }
        
        .error-box, .success-box { border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; margin-bottom: 20px; display: none; }
        .error-box { background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; }
        .success-box { background: #f0fff4; border: 1px solid #c6f6d5; color: #276749; }
        
        .divider { display: flex; align-items: center; margin: 24px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
        .divider span { padding: 0 16px; color: #9aacbf; font-size: 0.85rem; font-weight: 500; }
        
        .register-link { text-align: center; font-size: 0.95rem; color: #4a5568; font-weight: 500; }
        .register-link a { color: #0d6efd; text-decoration: none; font-weight: 700; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="store-logo">
        <i class="bx bx-user-plus"></i>
        <h2>Create Account</h2>
        <p>Join HexaShop and start shopping!</p>
    </div>

    <div class="error-box" id="regError"></div>
    <div class="success-box" id="regSuccess"></div>

    <form id="customer-reg-form">
        <div class="mb-3">
            <label class="form-label-custom">Full Name</label>
            <input type="text" class="form-control-custom" id="reg_name" name="name" placeholder="John Doe" required>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Email Address</label>
            <input type="email" class="form-control-custom" id="reg_email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Phone Number</label>
            <input type="text" class="form-control-custom" id="reg_phone" name="phone" placeholder="Your phone number" required>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Password</label>
            <input type="password" class="form-control-custom" id="reg_password" name="password" placeholder="Create a password (min. 6 chars)" required minlength="6">
        </div>
        <div class="mb-4">
            <label class="form-label-custom">Confirm Password</label>
            <input type="password" class="form-control-custom" id="reg_password_confirmation" name="password_confirmation" placeholder="Confirm your password" required minlength="6">
        </div>
        <button type="submit" class="btn-login" id="regBtn">Create Account</button>
    </form>
    
    <div class="divider"><span>Already a member?</span></div>
    
    <div class="register-link">
        <a href="{{ route('store.login') }}">Sign in here</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

$('#customer-reg-form').on('submit', function(e) {
    e.preventDefault();
    $('#regError, #regSuccess').hide();
    const btn = $('#regBtn');
    btn.prop('disabled', true).html('Creating account...');

    $.post('{{ route("store.customer.register.process") }}', $(this).serialize())
    .done(function(r) {
        if (r.success) {
            $('#regSuccess').text(r.message).show();
            setTimeout(() => window.location.href = r.redirect_url, 1500);
        } else {
            $('#regError').text(r.message).show();
            btn.prop('disabled', false).html('Create Account');
        }
    }).fail(function(xhr) {
        let msg = xhr.responseJSON?.message || 'Registration failed.';
        if (xhr.status === 422) {
            const errs = xhr.responseJSON?.errors;
            if (errs) {
                msg = Object.values(errs)[0][0]; // Show first validation error
            }
        }
        $('#regError').text(msg).show();
        btn.prop('disabled', false).html('Create Account');
    });
});
</script>
</body>
</html>
