<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hexagon CRM | Secure Access</title>
    <meta name="title" content="Hexagon CRM | Secure Access">
    <meta name="description" content="Secure administrative login portal for Hexagon CRM.">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== LEFT PANEL — matches dashboard sidebar teal gradient ===== */
        .left-panel {
            width: 52%;
            height: 100%;
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, #004f5a 0%, #003840 55%, #002830 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (max-width: 900px) {
            .left-panel {
                display: none;
            }

            .right-panel {
                width: 100% !important;
            }
        }

        /* Decorative circles in background */
        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            left: -80px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 180, 180, 0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -60px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 123, 138, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Top logo area in left panel */
        .left-logo-area {
            position: relative;
            z-index: 2;
            padding: 36px 40px 0;
        }

        .left-logo-box {
            background: #ffffff;
            border-radius: 14px;
            padding: 12px 20px;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.18);
        }

        .left-logo-box img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        /* Illustration in the center */
        .left-illustration {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .left-illustration img {
            width: 88%;
            height: 78%;
            object-fit: contain;
            opacity: 0.65;
            filter: brightness(0.95);
        }

        /* Bottom tagline */
        .left-tagline {
            position: relative;
            z-index: 2;
            padding: 0 40px 40px;
        }

        .left-tagline h2 {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .left-tagline p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.60);
            line-height: 1.6;
        }

        /* Teal accent dots row */
        .dot-row {
            display: flex;
            gap: 8px;
            margin-top: 18px;
        }

        .dot-row span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(0, 180, 180, 0.35);
        }

        .dot-row span:first-child {
            background: #00b4b4;
            width: 22px;
            border-radius: 4px;
        }

        /* ===== RIGHT PANEL ===== */
        .right-panel {
            width: 48%;
            height: 100%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            padding: 40px 20px;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
        }

        /* Mobile logo */
        .mobile-logo {
            display: none;
            justify-content: center;
            margin-bottom: 32px;
        }

        @media (max-width: 900px) {
            .mobile-logo {
                display: flex;
            }
        }

        .mobile-logo-box {
            background: #f4fbfb;
            border: 1.5px solid #c8ecee;
            border-radius: 14px;
            padding: 12px 24px;
        }

        .mobile-logo-box img {
            height: 36px;
            width: auto;
            object-fit: contain;
        }

        /* Form header */
        .form-header {
            margin-bottom: 32px;
        }

        /* Form logo box above Welcome Back */
        .form-logo-box {
            display: inline-flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #cce8ea;
            border-radius: 14px;
            padding: 10px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0, 90, 100, 0.08);
        }
        .form-logo-box img {
            height: 36px;
            width: auto;
            object-fit: contain;
        }



        .form-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #003840;
            line-height: 1.25;
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 13.5px;
            color: #607d80;
            line-height: 1.6;
        }

        /* Input group */
        .input-group-custom {
            margin-bottom: 20px;
        }

        .input-group-custom label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: #4a6a6d;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #7fb8bc;
            font-size: 15px;
            pointer-events: none;
            z-index: 1;
        }

        .form-input {
            width: 100%;
            height: 50px;
            border: 1.5px solid #c8e8ea;
            border-radius: 10px;
            background: #f7fcfc;
            color: #003840;
            font-size: 14px;
            font-family: inherit;
            padding: 0 44px 0 44px;
            outline: none;
            transition: all 0.25s ease;
        }

        .form-input::placeholder {
            color: #a8c8cc;
            font-size: 13px;
        }

        .form-input:focus {
            border-color: #00b4b4;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 180, 180, 0.10);
        }

        .form-input.is-error {
            border-color: #ef4444;
            background: #fff8f8;
        }

        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #7fb8bc;
            cursor: pointer;
            font-size: 15px;
            padding: 4px;
            transition: color 0.2s;
        }

        .toggle-pass:hover {
            color: #007b8a;
        }

        .error-msg {
            font-size: 12px;
            color: #ef4444;
            font-weight: 600;
            margin-top: 6px;
            display: none;
        }

        /* Submit button — matches sidebar active gradient */
        .btn-submit {
            width: 100%;
            height: 52px;
            background: linear-gradient(90deg, #007b8a 0%, #009aaa 100%);
            color: #ffffff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.4px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            box-shadow: 0 4px 18px rgba(0, 154, 170, 0.30);
            transition: all 0.25s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 8px 24px rgba(0, 154, 170, 0.38);
            background: linear-gradient(90deg, #006a78 0%, #00889a 100%);
        }

        .btn-submit:active {
            transform: translateY(0.5px);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 22px;
            height: 22px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Divider */
        .form-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0 6px;
        }

        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #d8eef0;
        }

        .form-divider span {
            font-size: 12px;
            color: #8ab4b8;
            font-weight: 600;
        }

        /* Footer note */
        .form-footer-note {
            text-align: center;
            margin-top: 28px;
            font-size: 12px;
            color: #8ab4b8;
        }

        .form-footer-note strong {
            color: #007b8a;
        }
    </style>
</head>

<body>

    @php
        try {
            $company_data = get_company_details(1);
            $logoFile = $company_data && $company_data->logo ? $company_data->logo : null;
            $logoUrl = $logoFile
                ? asset('assets/uploads/company/' . $logoFile)
                : asset('assets/admin-assets/images/brand-logos/desktop-logo.png');
        } catch (\Exception $e) {
            $logoUrl = asset('assets/admin-assets/images/brand-logos/desktop-logo.png');
        }
    @endphp

    <!-- LEFT PANEL -->
    <div class="left-panel">



        <!-- Center illustration -->
        <div class="left-illustration">
            <img src="{{ asset('assets/login_banner.png') }}" alt="CRM Illustration">
        </div>

       

    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="form-card">

            <!-- Mobile logo -->
            <div class="mobile-logo">
                <div class="mobile-logo-box">
                    <img src="{{ $logoUrl }}" alt="Hexagon CRM">
                </div>
            </div>

            <!-- Form header -->
            <div class="form-header">
                <!-- Logo above Welcome Back -->
                <div class="form-logo-box">
                    <img src="{{ $logoUrl }}" alt="Hexagon CRM">
                </div>
                <h1>Welcome Back 👋</h1>
                <p>Sign in to your workspace to manage leads,<br>clients, and team performance.</p>
            </div>

            <!-- Login Form -->
            <form id="loginForm" novalidate>
                @csrf

                <!-- Email -->
                <div class="input-group-custom">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <i class="fa fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input" placeholder="you@hexagoncrm.com"
                            required autocomplete="email" tabindex="1">
                        <div class="error-msg" id="error-email"></div>
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group-custom">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••"
                            required autocomplete="current-password" tabindex="2">
                        <button type="button" id="togglePassword" class="toggle-pass" tabindex="-1">
                            <i class="fa fa-eye" id="eyeIcon"></i>
                        </button>
                        <div class="error-msg" id="error-password"></div>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="submitBtn" class="btn-submit" tabindex="3">
                    <span id="btnText">
                        <i class="fa fa-right-to-bracket"></i>
                        Access Dashboard
                    </span>
                    <div class="spinner" id="loadingSpinner"></div>
                </button>

            </form>

            <div class="form-divider"><span>Hexagon CRM</span></div>

           

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script>
        // iziToast settings
        iziToast.settings({
            timeout: 3000,
            resetOnHover: true,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOut',
            position: 'topRight',
            titleColor: '#ffffff',
            messageColor: '#ffffff',
            iconColor: '#ffffff',
            titleSize: '14px',
            messageSize: '13px',
        });

        @if(session('success'))
            iziToast.success({
                title: 'Success',
                message: "{{ session('success') }}",
                backgroundColor: '#10b981',
                progressBarColor: '#ffffff'
            });
        @endif

        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa fa-eye';
            }
        });

        // AJAX Login
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('#loginForm').on('submit', function (e) {
                e.preventDefault();

                const $btn = $('#submitBtn');
                const $spinner = $('#loadingSpinner');
                const $btnText = $('#btnText');
                const $errEmail = $('#error-email');
                const $errPass = $('#error-password');

                // Clear errors
                $errEmail.hide().text('');
                $errPass.hide().text('');
                $('#email, #password').removeClass('is-error');

                // Loading state
                $btn.prop('disabled', true);
                $btnText.hide();
                $spinner.css('display', 'block');

                $.ajax({
                    url: "{{ route('login.process') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            iziToast.success({
                                title: 'Welcome',
                                message: res.message || 'Authentication successful. Opening portal...',
                                backgroundColor: '#10b981',
                                progressBarColor: '#ffffff'
                            });
                            if (res.redirect_url) {
                                setTimeout(() => window.location.href = res.redirect_url, 800);
                            }
                        } else {
                            $btn.prop('disabled', false);
                            $btnText.show();
                            $spinner.hide();
                            iziToast.error({
                                title: 'Access Denied',
                                message: 'Please check your credentials.',
                                backgroundColor: '#ef4444',
                                progressBarColor: '#ffffff'
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false);
                        $btnText.show();
                        $spinner.hide();

                        if (xhr.status === 422) {
                            const errs = xhr.responseJSON.errors;
                            iziToast.warning({
                                title: 'Validation',
                                message: 'Please correct the highlighted fields.',
                                backgroundColor: '#f59e0b',
                                progressBarColor: '#ffffff'
                            });
                            if (errs.email) {
                                $('#email').addClass('is-error');
                                $errEmail.text(errs.email[0]).show();
                            }
                            if (errs.password) {
                                $('#password').addClass('is-error');
                                $errPass.text(errs.password[0]).show();
                            }
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: 'Server error. Please try again.',
                                backgroundColor: '#ef4444',
                                progressBarColor: '#ffffff'
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>