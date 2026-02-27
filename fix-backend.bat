@echo off
echo Fixing Laravel Backend Issues...

echo.
echo 1. Clearing cache and config...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Checking migration status...
php artisan migrate:status

echo.
echo 3. Starting Laravel server...
php artisan serve --host=0.0.0.0 --port=8000

pause