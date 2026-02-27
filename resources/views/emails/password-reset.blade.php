<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button { display: inline-block; padding: 12px 30px; background: #3B82F6; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <p>You requested to reset your password. Click the button below to reset it:</p>
        <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        <p>Or copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #3B82F6;">{{ $resetUrl }}</p>
        <p>This link will expire in 60 minutes.</p>
        <p>If you didn't request this, please ignore this email.</p>
        <div class="footer">
            <p>© {{ date('Y') }} AIPromptHub. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
