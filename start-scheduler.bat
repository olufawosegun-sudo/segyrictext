@echo off
title SegyricText - Laravel Task Scheduler
echo ========================================================
echo Starting Laravel Task Scheduler Worker for SegyricText
echo Running recurring tasks and cron-driven operations...
echo ========================================================
cd /d "%~dp0"
php artisan schedule:work
pause
