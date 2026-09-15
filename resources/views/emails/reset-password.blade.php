<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
</head>
<body>
    <h2>Password Reset Request</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>
        <a href="{{ $resetLink }}" style="display:inline-block;padding:10px 20px;background-color:#b71615;color:#fff;text-decoration:none;border-radius:5px;">
            Reset Password
        </a>
    </p>
    <p>If you did not request a password reset, no further action is required.</p>
    <br>
    <p>Regards,<br>Hexagon CRM</p>
</body>
</html>
