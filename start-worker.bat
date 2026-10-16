@echo off
title SegyricText - Background Queue Worker
echo ========================================================
echo Starting Laravel Background Queue Worker for SegyricText
echo Driver: Database (MariaDB) / Queue: default
echo ========================================================
cd /d "%~dp0"
php artisan queue:work database --sleep=3 --tries=3 --max-time=3600
pause
