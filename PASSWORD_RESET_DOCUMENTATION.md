# Password Reset System Documentation

## Overview
Professional password reset system for admin panel with email notifications.

---

## Features Implemented

### 1. **Database Structure**
- Table: `password_reset_tokens` (already exists)
- Columns: email (primary), token (hashed), created_at
- Token expiration: 60 minutes

### 2. **Email Configuration**
- SMTP: Gmail
- Port: 587 (TLS)
- App Password: `cjmd mxvz dmzz xboz`
- **Note**: Update `MAIL_USERNAME` and `MAIL_FROM_ADDRESS` in `.env` with your actual Gmail address

### 3. **Routes Structure**
```
GET  /admin/forgot-password       → Show forgot password form
POST /admin/forgot-password       → Send reset link email
GET  /admin/reset-password/{token} → Show reset password form
POST /admin/reset-password        → Process password reset
```

### 4. **Security Features**
✅ Token hashing (bcrypt)
✅ 60-minute expiration
✅ Admin-only verification
✅ Token single-use (deleted after reset)
✅ Password confirmation required
✅ Minimum 8 characters

---

## File Structure

```
app/
├── Http/Controllers/Admin/
│   └── PasswordResetController.php    # Main controller
├── Mail/
│   └── PasswordResetMail.php          # Email class

resources/views/
├── admin/auth/
│   ├── forgot-password.blade.php      # Request reset form
│   └── reset-password.blade.php       # Reset password form
└── emails/
    └── password-reset.blade.php       # Email template

database/migrations/
└── 2026_02_26_174134_create_password_reset_tokens_table.php
```

---

## Usage Flow

### Admin Forgot Password:
1. Click "Forgot Password?" on login page
2. Enter email address
3. Receive email with reset link
4. Click link (valid for 60 minutes)
5. Enter new password (min 8 chars)
6. Confirm password
7. Redirected to login with success message

---

## Configuration Steps

### 1. Update .env file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-actual-email@gmail.com
MAIL_PASSWORD="cjmd mxvz dmzz xboz"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-actual-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Clear config cache:
```bash
php artisan config:clear
```

### 3. Test email sending:
```bash
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

---

## Controller Methods

### PasswordResetController

#### `showForgotForm()`
- Displays forgot password form
- No authentication required

#### `sendResetLink(Request $request)`
- Validates email
- Checks if admin user exists
- Generates secure token
- Stores hashed token in database
- Sends email with reset link
- Returns success message

#### `showResetForm($token, Request $request)`
- Displays reset password form
- Pre-fills email from query string
- Token passed as hidden field

#### `resetPassword(Request $request)`
- Validates token, email, password
- Checks token validity and expiration
- Updates user password
- Deletes used token
- Redirects to login

---

## Email Template

Professional HTML email with:
- Branded header
- Clear call-to-action button
- Expiration notice
- Security message
- Responsive design

---

## Frontend Integration (Next Steps)

### API Endpoints to Create:
```php
POST /api/forgot-password
POST /api/reset-password
```

### Frontend Flow:
1. Create forgot password page
2. Create reset password page
3. Handle email link redirect
4. Show success/error messages
5. Redirect to login after success

---

## Testing Checklist

### Admin Panel:
- [ ] Click "Forgot Password?" link
- [ ] Submit valid admin email
- [ ] Receive email within 1 minute
- [ ] Click reset link in email
- [ ] Enter new password (8+ chars)
- [ ] Confirm password matches
- [ ] Successfully reset password
- [ ] Login with new password

### Error Cases:
- [ ] Non-existent email shows error
- [ ] Non-admin email shows error
- [ ] Expired token (>60 min) shows error
- [ ] Invalid token shows error
- [ ] Password mismatch shows error
- [ ] Password <8 chars shows error

### Security:
- [ ] Token is hashed in database
- [ ] Token deleted after use
- [ ] Cannot reuse same token
- [ ] Old password no longer works
- [ ] New password works immediately

---

## Maintenance

### Token Cleanup (Optional)
Add scheduled task to clean expired tokens:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHour())
            ->delete();
    })->daily();
}
```

---

## Troubleshooting

### Email not sending:
1. Check Gmail app password is correct
2. Verify MAIL_USERNAME is your Gmail address
3. Enable "Less secure app access" if needed
4. Check spam folder
5. Run `php artisan config:clear`

### Token errors:
1. Check database table exists
2. Verify token hasn't expired
3. Ensure email matches exactly
4. Check token hasn't been used

### Password not updating:
1. Verify user is admin (is_admin = 1)
2. Check password meets requirements
3. Ensure confirmation matches
4. Check database connection

---

## Best Practices Implemented

✅ **Separation of Concerns**: Dedicated controller for password reset
✅ **Security First**: Token hashing, expiration, single-use
✅ **User Experience**: Clear messages, professional emails
✅ **Maintainability**: Clean code structure, well-documented
✅ **Scalability**: Easy to extend to frontend/API
✅ **Error Handling**: Comprehensive validation and error messages

---

## Next Steps for Frontend

1. Create API endpoints (similar to admin routes)
2. Add user verification (not just admin)
3. Create React/Vue components
4. Implement email verification for new users
5. Add 2FA (optional enhancement)

---

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Test email configuration
3. Verify database connection
4. Check route list: `php artisan route:list`
