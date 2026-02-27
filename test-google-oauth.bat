@echo off
echo Testing Google OAuth and Avatar Integration...
echo.

echo 1. Testing Google OAuth Login...
curl -X POST http://localhost:8000/api/auth/google ^
  -H "Content-Type: application/json" ^
  -d "{\"google_id\":\"test123\",\"email\":\"test@gmail.com\",\"name\":\"Test User\",\"avatar\":\"https://lh3.googleusercontent.com/test.jpg\"}"

echo.
echo.
echo 2. Check if user was created in database...
php artisan tinker --execute="echo App\Models\User::where('email', 'test@gmail.com')->first();"

echo.
echo.
echo Test complete! Check the output above.
pause
