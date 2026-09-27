<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HexaShop Admin — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0; padding: 0;
            min-height: 100vh;
            background: #0f1824;
            display: flex;
            align-items: stretch;
        }

        /* ── Left Panel ─────────────────────────── */
        .login-left {
            flex: 1;
            background: linear-gradient(145deg, #141e2e 0%, #0f1824 50%, #1a2e1a 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: -100px; left: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(13,110,253,0.15) 0%, transparent 70%);
        }
        .login-left::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(32,201,151,0.12) 0%, transparent 70%);
        }
        .login-brand {
            text-align: center;
            margin-bottom: 50px;
            position: relative; z-index: 1;
        }
        .login-brand .brand-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #0d6efd, #20c997);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff;
            margin: 0 auto 16px;
            box-shadow: 0 10px 30px rgba(13,110,253,0.4);
        }
        .login-brand h1 {
            font-size: 2rem; font-weight: 800;
            color: #fff; margin: 0;
            letter-spacing: -1px;
        }
        .login-brand h1 span { color: #20c997; }
        .login-brand p { color: #8a97b0; margin: 6px 0 0; font-size: 0.9rem; }

        .feature-list { position: relative; z-index: 1; width: 100%; max-width: 340px; }
        .feature-item {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .feature-item:last-child { border-bottom: none; }
        .feature-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .feature-icon.blue   { background: rgba(13,110,253,0.15); color: #4e9eff; }
        .feature-icon.green  { background: rgba(32,201,151,0.15); color: #20c997; }
        .feature-icon.orange { background: rgba(253,126,20,0.15);  color: #ffa94d; }
        .feature-icon.purple { background: rgba(111,66,193,0.15);  color: #9b6fe8; }
        .feature-text h6 { color: #e2e8f0; font-size: 0.87rem; font-weight: 600; margin: 0 0 3px; }
        .feature-text p  { color: #8a97b0; font-size: 0.78rem; margin: 0; line-height: 1.4; }

        /* ── Right Panel (Login Form) ────────────── */
        .login-right {
            width: 460px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 44px;
        }
        .login-right h2 {
            font-size: 1.6rem; font-weight: 800;
            color: #1a202c; margin: 0 0 6px;
        }
        .login-right .subtitle {
            color: #9aacbf; font-size: 0.87rem; margin-bottom: 32px;
        }

        .login-tabs {
            display: flex;
            background: #f0f3f8;
            border-radius: 10px;
            padding: 3px;
            margin-bottom: 24px;
            gap: 2px;
        }
        .login-tab {
            flex: 1; border: none; background: none;
            border-radius: 8px; padding: 8px;
            font-size: 0.8rem; font-weight: 600;
            color: #9aacbf; cursor: pointer;
            transition: all 0.2s;
        }
        .login-tab.active {
            background: #fff;
            color: #0d6efd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .form-label-custom {
            font-size: 0.82rem; font-weight: 600;
            color: #4a5568; margin-bottom: 5px; display: block;
        }
        .form-control-custom {
            border-radius: 10px;
            border: 1.5px solid #e9ecef;
            padding: 10px 14px;
            font-size: 0.875rem;
            width: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-control-custom:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.12);
        }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap i {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            color: #9aacbf; cursor: pointer; font-size: 1.1rem;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none; border-radius: 10px;
            color: #fff; font-weight: 700;
            font-size: 0.9rem; padding: 12px;
            cursor: pointer; transition: all 0.2s;
            margin-top: 8px;
        }
        .btn-login:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,110,253,0.35); }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .error-box {
            background: #fff5f5; border: 1px solid #fed7d7;
            border-radius: 8px; padding: 10px 14px;
            font-size: 0.82rem; color: #c53030;
            margin-bottom: 16px; display: none;
        }
        .success-box {
            background: #f0fff4; border: 1px solid #c6f6d5;
            border-radius: 8px; padding: 10px 14px;
            font-size: 0.82rem; color: #276749;
            margin-bottom: 16px; display: none;
        }

        .divider { display: flex; align-items: center; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e9ecef; }
        .divider span { padding: 0 12px; color: #b0bec5; font-size: 0.78rem; }

        .btn-vendor {
            width: 100%;
            border: 2px solid #e9ecef; border-radius: 10px;
            color: #4a5568; font-weight: 600;
            font-size: 0.85rem; padding: 10px;
            background: #fff; cursor: pointer;
            transition: all 0.2s; text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-vendor:hover { border-color: #0d6efd; color: #0d6efd; }

        @media (max-width: 900px) {
            .login-left { display: none; }
            .login-right { width: 100%; }
        }
    </style>
</head>
<body>

{{-- Left Brand Panel --}}
<div class="login-left">
    <div class="login-brand">
        <div class="brand-icon"><i class="bx bx-shopping-bag"></i></div>
        <h1>Hexa<span>Shop</span></h1>
        <p>Complete E-Commerce Management Platform</p>
    </div>

    <div class="feature-list">
        <div class="feature-item">
            <div class="feature-icon blue"><i class="bx bx-bar-chart-alt-2"></i></div>
            <div class="feature-text">
                <h6>Real-time Analytics</h6>
                <p>Track orders, revenue & performance with live dashboards</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon green"><i class="bx bx-store-alt"></i></div>
            <div class="feature-text">
                <h6>Multi-Vendor Marketplace</h6>
                <p>Manage multiple vendors, products & earnings centrally</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon orange"><i class="bx bx-package"></i></div>
            <div class="feature-text">
                <h6>Smart Inventory</h6>
                <p>Stock management, low-stock alerts & purchase orders</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon purple"><i class="bx bx-shield-check"></i></div>
            <div class="feature-text">
                <h6>Role-Based Access</h6>
                <p>Granular permissions for Admin, Vendor & Delivery staff</p>
            </div>
        </div>
    </div>
</div>

{{-- Right Login Panel --}}
<div class="login-right">
    <h2>Welcome back 👋</h2>
    <p class="subtitle">Sign in to your HexaShop admin panel</p>

    {{-- Login Type Tabs --}}
    <div class="login-tabs">
        <button class="login-tab active" data-form="admin-form" onclick="switchTab(this, 'admin-form')">
            <i class="bx bx-user-check me-1"></i> Admin
        </button>
        <button class="login-tab" data-form="vendor-form" onclick="switchTab(this, 'vendor-form')">
            <i class="bx bx-store me-1"></i> Vendor
        </button>
        <button class="login-tab" data-form="delivery-form" onclick="switchTab(this, 'delivery-form')">
            <i class="bx bx-cycling me-1"></i> Delivery
        </button>
    </div>

    {{-- Error / Success Messages --}}
    <div class="error-box" id="loginError"></div>
    <div class="success-box" id="loginSuccess"></div>

    {{-- Admin Login Form --}}
    <form id="admin-form" class="login-form-block">
        <div class="mb-3">
            <label class="form-label-custom">Email Address</label>
            <div class="input-icon-wrap">
                <input type="email" class="form-control-custom" id="admin_email" name="email"
                       placeholder="admin@hexashop.com" required autocomplete="email">
                <i class="bx bx-envelope"></i>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Password</label>
            <div class="input-icon-wrap">
                <input type="password" class="form-control-custom" id="admin_password" name="password"
                       placeholder="Enter your password" required autocomplete="current-password">
                <i class="bx bx-hide" id="toggleAdminPwd" onclick="togglePassword('admin_password', this)"></i>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <label class="d-flex align-items-center gap-2" style="font-size:0.82rem;cursor:pointer;">
                <input type="checkbox" style="accent-color:#0d6efd;"> Remember me
            </label>
            <a href="#" style="font-size:0.82rem;color:#0d6efd;text-decoration:none;">Forgot password?</a>
        </div>
        <button type="submit" class="btn-login" id="adminLoginBtn">
            <i class="bx bx-log-in me-1"></i> Sign In as Admin
        </button>
    </form>

    {{-- Vendor Login Form --}}
    <form id="vendor-form" class="login-form-block" style="display:none;">
        <div class="mb-3">
            <label class="form-label-custom">Vendor Email</label>
            <div class="input-icon-wrap">
                <input type="email" class="form-control-custom" id="vendor_email" name="email"
                       placeholder="vendor@example.com" autocomplete="email">
                <i class="bx bx-envelope"></i>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Password</label>
            <div class="input-icon-wrap">
                <input type="password" class="form-control-custom" id="vendor_password" name="password"
                       placeholder="Enter your password" autocomplete="current-password">
                <i class="bx bx-hide" onclick="togglePassword('vendor_password', this)"></i>
            </div>
        </div>
        <button type="submit" class="btn-login" id="vendorLoginBtn">
            <i class="bx bx-store me-1"></i> Sign In as Vendor
        </button>

        <div class="divider"><span>OR</span></div>
        <a href="{{ route('vendor.register') }}" class="btn-vendor">
            <i class="bx bx-user-plus"></i> Register as a New Vendor
        </a>
    </form>

    {{-- Delivery Login Form --}}
    <form id="delivery-form" class="login-form-block" style="display:none;">
        <div class="mb-3">
            <label class="form-label-custom">Email / Phone</label>
            <div class="input-icon-wrap">
                <input type="email" class="form-control-custom" id="delivery_email" name="email"
                       placeholder="delivery@example.com" autocomplete="email">
                <i class="bx bx-envelope"></i>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label-custom">Password</label>
            <div class="input-icon-wrap">
                <input type="password" class="form-control-custom" id="delivery_password" name="password"
                       placeholder="Enter your password" autocomplete="current-password">
                <i class="bx bx-hide" onclick="togglePassword('delivery_password', this)"></i>
            </div>
        </div>
        <button type="submit" class="btn-login" id="deliveryLoginBtn">
            <i class="bx bx-cycling me-1"></i> Sign In as Delivery Staff
        </button>
    </form>

    <div class="mt-4 pt-4 border-top text-center" style="font-size:0.78rem; color:#b0bec5;">
        <a href="{{ url('/') }}" style="color:#b0bec5;text-decoration:none;">
            ← Back to CRM Login
        </a>
        &nbsp;·&nbsp;
        <a href="{{ route('store.login') }}" style="color:#0d6efd;text-decoration:none;">
            Customer Login
        </a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

// ── Tab switcher ─────────────────────────────────────────────────────
function switchTab(btn, formId) {
    document.querySelectorAll('.login-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.login-form-block').forEach(f => f.style.display = 'none');
    document.getElementById(formId).style.display = 'block';
    clearMessages();
}

// ── Password toggle ──────────────────────────────────────────────────
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bx-hide', 'bx-show');
    } else {
        input.type = 'password';
        icon.classList.replace('bx-show', 'bx-hide');
    }
}

function showError(msg)   { $('#loginError').text(msg).show(); $('#loginSuccess').hide(); }
function showSuccess(msg) { $('#loginSuccess').text(msg).show(); $('#loginError').hide(); }
function clearMessages()  { $('#loginError, #loginSuccess').hide(); }

// ── Admin Login ──────────────────────────────────────────────────────
$('#admin-form').on('submit', function(e) {
    e.preventDefault();
    clearMessages();
    const btn = $('#adminLoginBtn');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Signing in...');

    $.post('{{ route("shop.admin.login.process") }}', {
        email:    $('#admin_email').val(),
        password: $('#admin_password').val(),
    }).done(function(r) {
        if (r.success) {
            showSuccess(r.message);
            setTimeout(() => window.location.href = r.redirect_url, 800);
        } else {
            showError(r.message);
            btn.prop('disabled', false).html('<i class="bx bx-log-in me-1"></i> Sign In as Admin');
        }
    }).fail(function(xhr) {
        showError(xhr.responseJSON?.message || 'Login failed. Please try again.');
        btn.prop('disabled', false).html('<i class="bx bx-log-in me-1"></i> Sign In as Admin');
    });
});

// ── Vendor Login ─────────────────────────────────────────────────────
$('#vendor-form').on('submit', function(e) {
    e.preventDefault();
    clearMessages();
    const btn = $('#vendorLoginBtn');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Signing in...');

    $.post('{{ route("shop.vendor.login.process") }}', {
        email:    $('#vendor_email').val(),
        password: $('#vendor_password').val(),
    }).done(function(r) {
        if (r.success) {
            showSuccess(r.message);
            setTimeout(() => window.location.href = r.redirect_url, 800);
        } else {
            showError(r.message);
            btn.prop('disabled', false).html('<i class="bx bx-store me-1"></i> Sign In as Vendor');
        }
    }).fail(function(xhr) {
        showError(xhr.responseJSON?.message || 'Login failed.');
        btn.prop('disabled', false).html('<i class="bx bx-store me-1"></i> Sign In as Vendor');
    });
});

// ── Delivery Login ───────────────────────────────────────────────────
$('#delivery-form').on('submit', function(e) {
    e.preventDefault();
    clearMessages();
    const btn = $('#deliveryLoginBtn');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Signing in...');

    $.post('{{ route("shop.delivery.login.process") }}', {
        email:    $('#delivery_email').val(),
        password: $('#delivery_password').val(),
    }).done(function(r) {
        if (r.success) {
            showSuccess(r.message);
            setTimeout(() => window.location.href = r.redirect_url, 800);
        } else {
            showError(r.message);
            btn.prop('disabled', false).html('<i class="bx bx-cycling me-1"></i> Sign In as Delivery Staff');
        }
    }).fail(function(xhr) {
        showError(xhr.responseJSON?.message || 'Login failed.');
        btn.prop('disabled', false).html('<i class="bx bx-cycling me-1"></i> Sign In as Delivery Staff');
    });
});
</script>
</body>
</html>
