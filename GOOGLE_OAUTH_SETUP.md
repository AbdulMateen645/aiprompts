# Google OAuth Setup Guide

## Database Schema

The Google OAuth integration requires the following columns in the `users` table:

- `google_id` (VARCHAR 191, nullable, unique) - Stores Google user ID
- `avatar` (TEXT, nullable) - Stores Google profile picture URL (TEXT type to handle long URLs)
- `password` (VARCHAR 255, nullable) - Made nullable to allow Google-only users

## Migration Files

### 1. Add OAuth Fields Migration
**File:** `database/migrations/2026_02_19_105456_add_oauth_fields_to_users_table.php`

This migration adds:
- `google_id` column with 191 character limit (MySQL index constraint)
- `avatar` column as TEXT type (handles URLs up to 65,535 characters)
- Makes `password` nullable for Google-only authentication

### 2. Modify Avatar Column Migration
**File:** `database/migrations/2026_02_24_095225_modify_avatar_column_in_users_table.php`

This migration ensures the `avatar` column is TEXT type to handle long Google profile picture URLs.

## Environment Configuration

Add these variables to your `.env` file:

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URL=http://localhost:8000/auth/google/callback
FRONTEND_URL=http://localhost:3000
```

## Common Issues & Solutions

### Issue 1: SQL Error - Column Too Long
**Error:** `Specified key was too long; max key length is 1000 bytes`
**Solution:** Use `string('google_id', 191)` instead of `string('google_id')`

### Issue 2: Avatar URL Too Long
**Error:** `Data too long for column 'avatar'`
**Solution:** Use `text('avatar')` instead of `string('avatar')`

### Issue 3: SSL Certificate Error (Windows/WAMP)
**Error:** `cURL error 60: SSL certificate problem`
**Solution:** Disable SSL verification in development (already implemented in GoogleAuthController)

### Issue 4: Password Required Error
**Error:** `Field 'password' doesn't have a default value`
**Solution:** Make password nullable: `$table->string('password')->nullable()->change()`

## Testing

After setup, test the following:

1. **Google Sign In** - Click "Continue with Google" button
2. **User Creation** - Verify new user is created with google_id and avatar
3. **User Update** - Sign in again with same Google account, verify user is updated not duplicated
4. **Avatar Display** - Check if avatar displays correctly in navbar
5. **Logout** - Verify logout functionality works

## Maintenance

- The avatar URL optimization in GoogleAuthController ensures smaller, consistent URLs
- Error handling logs all OAuth errors to Laravel log file
- User-friendly error messages are shown to frontend users
