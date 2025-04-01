<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset Request</h1>
    <p>Hi,</p>
    <p>You requested a password reset for your account. Click the link below to reset your password:</p>
    <a href="{{ env('APP_URL')}}/reset-password?token={{$token}}&email={{urlencode($email)}}">Reset Password</a>
    <p>If you did not request a password reset, please ignore this email.</p>
    <p>Thank you,</p>
    <p>{{ config('app.name') }}</p>
</body>
</html>