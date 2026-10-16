@echo off
title SegyricText - NativePHP Android Runner
echo ========================================================
echo Building and Running NativePHP Android Application
echo Target Platform: Android / Package ID: com.segyrictext.app
echo ========================================================
cd /d "%~dp0"
php artisan native:run android
pause
