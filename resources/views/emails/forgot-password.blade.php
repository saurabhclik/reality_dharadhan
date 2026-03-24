<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <p>Hello,</p>
    
    <p>You are receiving this email because we received a password reset request for your account.</p>
    
    <p>Click the button below to reset your password:</p>
    
    <a href="{{ $resetLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
        Reset Password
    </a>
    
    <p>If you did not request a password reset, no further action is required.</p>
    
    <p>Thank you,<br>{{ $fromName }}</p> 
    
    <p style="color: #888; font-size: 12px;">
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
        <br>{{ $resetLink }}
    </p>
</body>
</html>
