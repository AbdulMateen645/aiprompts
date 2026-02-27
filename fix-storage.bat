@echo off
echo Fixing storage symlink...

cd /d "%~dp0"

REM Remove old symlink/junction if exists
if exist "public\storage" (
    rmdir "public\storage" 2>nul
)

REM Create junction (works without admin rights)
mklink /J public\storage storage\app\public

echo.
echo Storage link fixed!
echo Images should now display correctly.
echo.
pause
