<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DPShop — Vendor Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0f1824; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        
        .reg-wrapper { width: 100%; max-width: 700px; background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; display: flex; flex-direction: column; }
        
        .reg-header { background: linear-gradient(135deg, #20c997, #0d6efd); padding: 30px; text-align: center; color: #fff; }
        .reg-header h2 { font-weight: 800; margin: 0 0 5px; font-size: 1.8rem; }
        .reg-header p { margin: 0; opacity: 0.9; font-size: 0.95rem; }
        
        .reg-body { padding: 40px; }
        
        .form-label-custom { font-size: 0.82rem; font-weight: 600; color: #4a5568; margin-bottom: 5px; display: block; }
        .form-control-custom { border-radius: 10px; border: 1.5px solid #e9ecef; padding: 10px 14px; font-size: 0.875rem; width: 100%; outline: none; transition: all 0.2s; }
        .form-control-custom:focus { border-color: #20c997; box-shadow: 0 0 0 3px rgba(32,201,151,0.12); }
        
        .btn-register {
            width: 100%; background: linear-gradient(135deg, #20c997, #0fcf97); border: none; border-radius: 10px;
            color: #fff; font-weight: 700; font-size: 1rem; padding: 14px; cursor: pointer; margin-top: 15px; transition: all 0.2s;
        }
        .btn-register:hover { box-shadow: 0 6px 20px rgba(32,201,151,0.35); transform: translateY(-1px); }
        
        .error-box, .success-box { border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; margin-bottom: 24px; display: none; }
        .error-box { background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; }
        .success-box { background: #f0fff4; border: 1px solid #c6f6d5; color: #276749; }
        
        .back-link { text-align: center; margin-top: 24px; font-size: 0.9rem; }
        .back-link a { color: #6c757d; text-decoration: none; font-weight: 600; }
        .back-link a:hover { color: #20c997; }
    </style>
</head>
<body>

<div class="reg-wrapper">
    <div class="reg-header">
        <h2>Become a DPShop Vendor</h2>
        <p>Register your business and start selling today.</p>
    </div>

    <div class="reg-body">
        <div class="error-box" id="regError"></div>
        <div class="success-box" id="regSuccess"></div>

        <form id="vendor-reg-form">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-custom">Business Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control-custom" name="business_name" placeholder="e.g. Acme Corp" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Owner Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control-custom" name="owner_name" placeholder="Full Name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control-custom" name="email" placeholder="vendor@example.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control-custom" name="phone" placeholder="Phone number" required>
                </div>
                <div class="col-12">
                    <label class="form-label-custom">GST Number</label>
                    <input type="text" class="form-control-custom" name="gst_number" placeholder="Optional for now">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control-custom" name="password" placeholder="Min. 6 characters" required minlength="6">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control-custom" name="password_confirmation" placeholder="Retype password" required minlength="6">
                </div>
            </div>

            <button type="submit" class="btn-register" id="regBtn">Submit Application</button>
        </form>

        <div class="back-link">
            Already a vendor? <a href="{{ route('vendor.login') }}">Sign In</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

$('#vendor-reg-form').on('submit', function(e) {
    e.preventDefault();
    $('#regError, #regSuccess').hide();
    const btn = $('#regBtn');
    btn.prop('disabled', true).html('Submitting...');

    $.post('{{ route("shop.vendor.register.process") }}', $(this).serialize())
    .done(function(r) {
        if (r.success) {
            $('#vendor-reg-form')[0].reset();
            $('#regSuccess').text(r.message).show();
            // Don't redirect immediately so they can read the approval message
            setTimeout(() => window.location.href = '{{ route("vendor.login") }}', 4000);
        } else {
            $('#regError').text(r.message).show();
            btn.prop('disabled', false).html('Submit Application');
        }
    }).fail(function(xhr) {
        let msg = xhr.responseJSON?.message || 'Registration failed.';
        if (xhr.status === 422) {
            const errs = xhr.responseJSON?.errors;
            if (errs) {
                msg = Object.values(errs)[0][0]; 
            }
        }
        $('#regError').text(msg).show();
        btn.prop('disabled', false).html('Submit Application');
    });
});
</script>
</body>
</html>
