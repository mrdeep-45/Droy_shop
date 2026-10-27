<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DPShop — Delivery Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; min-height: 100vh; background: #0f1824; display: flex; align-items: stretch; }
        .login-left {
            flex: 1; background: linear-gradient(145deg, #141e2e 0%, #0f1824 50%, #2a1538 100%);
            display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px 40px;
        }
        .login-brand { text-align: center; margin-bottom: 50px; }
        .login-brand .brand-icon {
            width: 72px; height: 72px; background: linear-gradient(135deg, #fd7e14, #ff5722);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff; margin: 0 auto 16px; box-shadow: 0 10px 30px rgba(253,126,20,0.4);
        }
        .login-brand h1 { font-size: 2rem; font-weight: 800; color: #fff; margin: 0; }
        .login-brand h1 span { color: #fd7e14; }
        .login-brand p { color: #8a97b0; margin: 6px 0 0; font-size: 0.9rem; }
        
        .login-right { width: 460px; background: #fff; display: flex; flex-direction: column; justify-content: center; padding: 50px 44px; }
        .login-right h2 { font-size: 1.6rem; font-weight: 800; color: #1a202c; margin: 0 0 6px; }
        .login-right .subtitle { color: #9aacbf; font-size: 0.87rem; margin-bottom: 32px; }
        
        .form-label-custom { font-size: 0.82rem; font-weight: 600; color: #4a5568; margin-bottom: 5px; display: block; }
        .form-control-custom { border-radius: 10px; border: 1.5px solid #e9ecef; padding: 10px 14px; font-size: 0.875rem; width: 100%; outline: none; }
        .form-control-custom:focus { border-color: #fd7e14; box-shadow: 0 0 0 3px rgba(253,126,20,0.12); }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9aacbf; cursor: pointer; }
        
        .btn-login {
            width: 100%; background: linear-gradient(135deg, #fd7e14, #ff5722); border: none; border-radius: 10px;
            color: #fff; font-weight: 700; font-size: 0.9rem; padding: 12px; cursor: pointer; margin-top: 8px;
        }
        .btn-login:hover { box-shadow: 0 6px 20px rgba(253,126,20,0.35); }
        
        .error-box, .success-box { border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; margin-bottom: 16px; display: none; }
        .error-box { background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; }
        .success-box { background: #f0fff4; border: 1px solid #c6f6d5; color: #276749; }
        @media (max-width: 900px) { .login-left { display: none; } .login-right { width: 100%; } }
    </style>
</head>
<body>

<div class="login-left">
    <div class="login-brand">
        <div class="brand-icon"><i class="bx bx-cycling"></i></div>
        <h1>Hexa<span>Delivery</span></h1>
        <p>Delivery Staff Portal</p>
    </div>
</div>

<div class="login-right">
    <h2>Welcome Rider 👋</h2>
    <p class="subtitle">Sign in to manage your assigned deliveries</p>

    <div class="error-box" id="loginError"></div>
    <div class="success-box" id="loginSuccess"></div>

    <form id="delivery-form">
        <div class="mb-3">
            <label class="form-label-custom">Email Address</label>
            <div class="input-icon-wrap">
                <input type="email" class="form-control-custom" id="delivery_email" placeholder="rider@example.com" required>
                <i class="bx bx-envelope"></i>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Password</label>
            <div class="input-icon-wrap">
                <input type="password" class="form-control-custom" id="delivery_password" placeholder="Enter your password" required>
                <i class="bx bx-hide" onclick="this.classList.toggle('bx-show'); this.classList.toggle('bx-hide'); const i=document.getElementById('delivery_password'); i.type = i.type==='password'?'text':'password';"></i>
            </div>
        </div>
        <button type="submit" class="btn-login" id="deliveryLoginBtn">
            <i class="bx bx-log-in me-1"></i> Sign In to Rider Portal
        </button>
    </form>
    
    <div class="mt-4 pt-4 border-top text-center" style="font-size:0.78rem;">
        <a href="{{ route('shop.login') }}" style="color:#b0bec5;text-decoration:none;">← Back to Admin Login</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

$('#delivery-form').on('submit', function(e) {
    e.preventDefault();
    $('#loginError, #loginSuccess').hide();
    const btn = $('#deliveryLoginBtn');
    btn.prop('disabled', true).html('Signing in...');

    $.post('{{ route("shop.delivery.login.process") }}', {
        email: $('#delivery_email').val(), password: $('#delivery_password').val(),
    }).done(function(r) {
        if (r.success) {
            $('#loginSuccess').text(r.message).show();
            setTimeout(() => window.location.href = r.redirect_url, 800);
        } else {
            $('#loginError').text(r.message).show();
            btn.prop('disabled', false).html('<i class="bx bx-log-in me-1"></i> Sign In to Rider Portal');
        }
    }).fail(function(xhr) {
        $('#loginError').text(xhr.responseJSON?.message || 'Login failed.').show();
        btn.prop('disabled', false).html('<i class="bx bx-log-in me-1"></i> Sign In to Rider Portal');
    });
});
</script>
</body>
</html>
