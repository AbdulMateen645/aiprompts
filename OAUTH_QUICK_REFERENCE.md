# Google OAuth - Quick Reference Card

## ⚡ Quick Start

```bash
# 1. Verify setup
php verify-oauth-setup.php

# 2. Run migrations (if needed)
php artisan migrate

# 3. Test Google login
# Visit: http://localhost:8000/auth/google
```

## 🔧 Environment Variables

```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000
```

## 📊 Database Schema

```sql
-- Users table must have:
google_id VARCHAR(191) NULL UNIQUE
avatar TEXT NULL
password VARCHAR(255) NULL
```

## 🚨 Common Errors & Fixes

| Error | Fix |
|-------|-----|
| `Data too long for column 'avatar'` | Run: `php artisan migrate` (avatar is now TEXT) |
| `Column 'google_id' doesn't exist` | Run: `php artisan migrate` |
| `SSL certificate problem` | Already fixed in GoogleAuthController |
| `Specified key was too long` | google_id limited to 191 chars ✅ |

## 🧪 Testing

```bash
# Test database structure
php artisan tinker --execute="DB::select('DESCRIBE users');"

# Test user creation
php artisan tinker --execute="App\Models\User::first();"

# Check routes
php artisan route:list --path=auth
```

## 📁 Key Files

- `app/Http/Controllers/Auth/GoogleAuthController.php` - OAuth logic
- `app/Models/User.php` - User model (fillable: google_id, avatar)
- `config/services.php` - Google OAuth config
- `database/migrations/*_add_oauth_fields_to_users_table.php` - Schema

## ✅ Verification Checklist

- [ ] Environment variables set
- [ ] Migrations run successfully
- [ ] Avatar column is TEXT type
- [ ] Google OAuth routes registered
- [ ] User model has google_id & avatar in fillable
- [ ] Test login works with real Google account

## 🆘 Need Help?

1. Run: `php verify-oauth-setup.php`
2. Check: `storage/logs/laravel.log`
3. Read: `GOOGLE_OAUTH_SETUP.md`
4. Review: `OAUTH_FIX_SUMMARY.md`

---
**Status:** ✅ Production Ready
**Last Updated:** 2026-02-24
