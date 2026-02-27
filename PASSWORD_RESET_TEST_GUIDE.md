# Password Reset Testing Guide

## Admin Credentials
- **Email**: a.mateen2025@gmail.com
- **Password**: 12345678

## Test Steps

### 1. Test Login
```
URL: http://localhost:8000/admin/login
Email: a.mateen2025@gmail.com
Password: 12345678
```

### 2. Test Forgot Password Flow
1. Go to: http://localhost:8000/admin/login
2. Click "Forgot Password?" link
3. Enter: a.mateen2025@gmail.com
4. Click "Send Reset Link"
5. Check email inbox for reset link
6. Click the link in email
7. Enter new password (min 8 chars)
8. Confirm password
9. Click "Reset Password"
10. Login with new password

### 3. Verify Email Settings
```bash
php artisan config:clear
php artisan tinker
```

Then in tinker:
```php
Mail::raw('Test email from AIPromptHub', function($msg) {
    $msg->to('a.mateen2025@gmail.com')->subject('Test Email');
});
```

## Expected Results

✅ Login works with: a.mateen2025@gmail.com / 12345678
✅ Forgot password form accessible
✅ Email sent successfully
✅ Reset link works (valid for 60 minutes)
✅ Password reset successful
✅ Can login with new password
✅ Old password no longer works

## Troubleshooting

### Email not received:
1. Check spam folder
2. Verify Gmail app password is correct
3. Run: `php artisan config:clear`
4. Check logs: `storage/logs/laravel.log`

### Login fails:
1. Verify email: a.mateen2025@gmail.com
2. Verify password: 12345678
3. Check user is admin in database

### Reset link expired:
- Links expire after 60 minutes
- Request a new reset link

## Database Check

```sql
-- Check admin user
SELECT id, name, email, is_admin FROM users WHERE email = 'a.mateen2025@gmail.com';

-- Check reset tokens
SELECT * FROM password_reset_tokens WHERE email = 'a.mateen2025@gmail.com';
```

## Quick Commands

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Check routes
php artisan route:list | grep password

# Test email
php artisan tinker --execute="Mail::raw('Test', function(\$m) { \$m->to('a.mateen2025@gmail.com')->subject('Test'); });"
```

## Success Indicators

1. ✅ Login page shows new credentials
2. ✅ "Forgot Password?" link visible
3. ✅ Email configuration updated
4. ✅ Admin user updated in database
5. ✅ Config cache cleared
6. ✅ All routes registered

## Ready to Test! 🚀
