<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DPShop — Vendor Portal Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif; margin: 0; padding: 0;
            min-height: 100vh; background: #0f1824;
            display: flex; align-items: stretch;
        }
        .login-left {
            flex: 1; background: linear-gradient(145deg, #141e2e 0%, #0f1824 50%, #1a2e1a 100%);
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 60px 40px; position: relative; overflow: hidden;
        }
        .login-brand { text-align: center; margin-bottom: 50px; position: relative; z-index: 1; }
        .login-brand .brand-icon {
            width: 72px; height: 72px; background: linear-gradient(135deg, #20c997, #0d6efd);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff; margin: 0 auto 16px; box-shadow: 0 10px 30px rgba(32,201,151,0.4);
        }
        .login-brand h1 { font-size: 2rem; font-weight: 800; color: #fff; margin: 0; letter-spacing: -1px; }
        .login-brand h1 span { color: #20c997; }
        .login-brand p { color: #8a97b0; margin: 6px 0 0; font-size: 0.9rem; }
        
        .login-right {
            width: 460px; background: #fff; display: flex; flex-direction: column;
            justify-content: center; padding: 50px 44px;
        }
        .login-right h2 { font-size: 1.6rem; font-weight: 800; color: #1a202c; margin: 0 0 6px; }
        .login-right .subtitle { color: #9aacbf; font-size: 0.87rem; margin-bottom: 32px; }
        
        .form-label-custom { font-size: 0.82rem; font-weight: 600; color: #4a5568; margin-bottom: 5px; display: block; }
        .form-control-custom {
            border-radius: 10px; border: 1.5px solid #e9ecef; padding: 10px 14px;
            font-size: 0.875rem; width: 100%; outline: none;
        }
        .form-control-custom:focus { border-color: #20c997; box-shadow: 0 0 0 3px rgba(32,201,151,0.12); }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9aacbf; cursor: pointer; }
        
        .btn-login {
            width: 100%; background: linear-gradient(135deg, #20c997, #0fcf97); border: none; border-radius: 10px;
            color: #fff; font-weight: 700; font-size: 0.9rem; padding: 12px; cursor: pointer; margin-top: 8px;
        }
        .btn-login:hover { box-shadow: 0 6px 20px rgba(32,201,151,0.35); }
        
        .btn-outline {
            width: 100%; border: 2px solid #e9ecef; border-radius: 10px; color: #4a5568; font-weight: 600;
            font-size: 0.85rem; padding: 10px; background: #fff; cursor: pointer; text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 15px;
        }
        .btn-outline:hover { border-color: #20c997; color: #20c997; }
        
        .error-box, .success-box { border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; margin-bottom: 16px; display: none; }
        .error-box { background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; }
        .success-box { background: #f0fff4; border: 1px solid #c6f6d5; color: #276749; }
        
        @media (max-width: 900px) { .login-left { display: none; } .login-right { width: 100%; } }
    </style>
</head>
<body>

<div class="login-left">
    <div class="login-brand">
        <div class="brand-icon"><i class="bx bx-store"></i></div>
        <h1>Hexa<span>Vendor</span></h1>
        <p>Manage your products, orders, and earnings</p>
    </div>
</div>

<div class="login-right">
    <h2>Welcome Vendor 👋</h2>
    <p class="subtitle">Sign in to your DPShop seller account</p>

    <div class="error-box" id="loginError"></div>
    <div class="success-box" id="loginSuccess"></div>

    <form id="vendor-form">
        <div class="mb-3">
            <label class="form-label-custom">Email Address</label>
            <div class="input-icon-wrap">
                <input type="email" class="form-control-custom" id="vendor_email" placeholder="vendor@example.com" required>
                <i class="bx bx-envelope"></i>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Password</label>
            <div class="input-icon-wrap">
                <input type="password" class="form-control-custom" id="vendor_password" placeholder="Enter your password" required>
                <i class="bx bx-hide" onclick="this.classList.toggle('bx-show'); this.classList.toggle('bx-hide'); const i=document.getElementById('vendor_password'); i.type = i.type==='password'?'text':'password';"></i>
            </div>
        </div>
        <button type="submit" class="btn-login" id="vendorLoginBtn">
            <i class="bx bx-log-in me-1"></i> Sign In to Seller Panel
        </button>
    </form>

    <div class="d-flex align-items-center my-4">
        <hr class="flex-grow-1 m-0"><span class="px-3 text-muted" style="font-size:0.8rem;">OR</span><hr class="flex-grow-1 m-0">
    </div>

    <a href="{{ route('vendor.register') }}" class="btn-outline">
        <i class="bx bx-user-plus"></i> Become a Seller
    </a>
    
    <div class="mt-4 pt-4 border-top text-center" style="font-size:0.78rem;">
        <a href="{{ route('shop.login') }}" style="color:#b0bec5;text-decoration:none;">← Back to Admin Login</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

$('#vendor-form').on('submit', function(e) {
    e.preventDefault();
    $('#loginError, #loginSuccess').hide();
    const btn = $('#vendorLoginBtn');
    btn.prop('disabled', true).html('Signing in...');

    $.post('{{ route("shop.vendor.login.process") }}', {
        email: $('#vendor_email').val(), password: $('#vendor_password').val(),
    }).done(function(r) {
        if (r.success) {
            $('#loginSuccess').text(r.message).show();
            setTimeout(() => window.location.href = r.redirect_url, 800);
        } else {
            $('#loginError').text(r.message).show();
            btn.prop('disabled', false).html('<i class="bx bx-log-in me-1"></i> Sign In to Seller Panel');
        }
    }).fail(function(xhr) {
        $('#loginError').text(xhr.responseJSON?.message || 'Login failed.').show();
        btn.prop('disabled', false).html('<i class="bx bx-log-in me-1"></i> Sign In to Seller Panel');
    });
});
</script>
</body>
</html>
