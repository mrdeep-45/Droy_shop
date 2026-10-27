<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DPShop — Customer Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #f8f9fa; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        
        .login-wrapper { width: 100%; max-width: 440px; margin: 20px; padding: 40px; background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); }
        .store-logo { text-align: center; margin-bottom: 30px; }
        .store-logo i { font-size: 3rem; color: #0d6efd; background: #f0f6ff; padding: 16px; border-radius: 20px; margin-bottom: 12px; }
        .store-logo h2 { font-weight: 800; color: #1a202c; font-size: 1.8rem; margin: 0; }
        .store-logo p { color: #8a97b0; font-size: 0.95rem; margin-top: 5px; }

        .form-label-custom { font-size: 0.88rem; font-weight: 600; color: #4a5568; margin-bottom: 6px; display: block; }
        .form-control-custom { border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 16px; font-size: 0.95rem; width: 100%; background: #f8fafc; transition: all 0.2s; }
        .form-control-custom:focus { background: #fff; border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13,110,253,0.1); outline: none; }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap i { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #9aacbf; cursor: pointer; font-size: 1.2rem; }
        
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
        <i class="bx bx-shopping-bag"></i>
        <h2>Welcome Back</h2>
        <p>Sign in to your account</p>
    </div>

    <div class="error-box" id="loginError"></div>
    <div class="success-box" id="loginSuccess"></div>

    <form id="customer-form">
        <div class="mb-3">
            <label class="form-label-custom">Email Address</label>
            <input type="email" class="form-control-custom" id="customer_email" placeholder="you@example.com" required>
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label-custom mb-0">Password</label>
                <a href="#" style="font-size:0.85rem;color:#0d6efd;text-decoration:none;font-weight:500;">Forgot?</a>
            </div>
            <div class="input-icon-wrap">
                <input type="password" class="form-control-custom" id="customer_password" placeholder="Enter your password" required>
                <i class="bx bx-hide" onclick="this.classList.toggle('bx-show'); this.classList.toggle('bx-hide'); const i=document.getElementById('customer_password'); i.type = i.type==='password'?'text':'password';"></i>
            </div>
        </div>
        <button type="submit" class="btn-login" id="customerLoginBtn">Sign In</button>
    </form>
    
    <div class="divider"><span>New to DPShop?</span></div>
    
    <div class="register-link">
        Don't have an account? <a href="{{ route('store.register') }}">Sign up here</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

$('#customer-form').on('submit', function(e) {
    e.preventDefault();
    $('#loginError, #loginSuccess').hide();
    const btn = $('#customerLoginBtn');
    btn.prop('disabled', true).html('Signing in...');

    $.post('{{ route("store.customer.login.process") }}', {
        email: $('#customer_email').val(), password: $('#customer_password').val(),
    }).done(function(r) {
        if (r.success) {
            $('#loginSuccess').text(r.message).show();
            setTimeout(() => window.location.href = r.redirect_url, 800);
        } else {
            $('#loginError').text(r.message).show();
            btn.prop('disabled', false).html('Sign In');
        }
    }).fail(function(xhr) {
        $('#loginError').text(xhr.responseJSON?.message || 'Login failed.').show();
        btn.prop('disabled', false).html('Sign In');
    });
});
</script>
</body>
</html>
