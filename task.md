# Implementation Tasks

## Phase 1: Laravel Project Scaffolding
- `[x]` Create Laravel 11 project via composer
- `[x]` Configure `.env` for XAMPP MariaDB (`segyrictext`)
- `[x]` Install Laravel Breeze / Session auth & Sanctum API tokens
- `[x]` Verify base project serves correctly

## Phase 2: Database & Shared Backend
- `[x]` Create database `segyrictext` in MariaDB
- `[x]` Create migrations (categories, documents, task logs, notifications, queue tables)
- `[x]` Create Models (User, Category, Document, ScheduledTaskLog)
- `[x]` Create Services (TextProcessingService, NotificationDigestService, MaintenanceCleanupService, HealthMetricsService)
- `[x]` Create Form Requests (StoreDocumentRequest, UpdateDocumentRequest, AuthLoginRequest)
- `[x]` Create Web Controllers (AuthController, DashboardController, DocumentController, SchedulerController)
- `[x]` Create API Controllers (AuthController, DashboardController, DocumentController, SystemController)
- `[x]` Run migrations
- `[x]` Create DatabaseSeeder with demo user (`admin@segyrictext.com` / `password`)

## Phase 3: Scheduler, Cron Jobs & Queue Configuration
- `[x]` Configure queue connection (`database` driver with MariaDB)
- `[x]` Create Artisan Commands (`app:process-pending-texts`, `app:send-daily-digest`, `app:cleanup-expired-tokens`, `app:prune-old-records`, `app:system-health-check`)
- `[x]` Create Queued Jobs (`ProcessTextJob`, `SendDigestNotificationJob`)
- `[x]` Configure scheduled tasks in `routes/console.php` with `withoutOverlapping()`, logging, and error handling
- `[x]` Verify with `php artisan schedule:list`

## Phase 4: Web UI (Blade + CSS + JS)
- `[x]` Create master web layout with glassmorphism, responsive sidebar, and header
- `[x]` Create dashboard view with real-time stats cards, diagnostics widget, and recent texts
- `[x]` Create documents CRUD views (index, create, edit, show) with live word/char counters and analysis preview
- `[x]` Create Scheduler & Cron Control Center with cron expression guide, manual execution triggers, and live audit trail
- `[x]` Style with modern typography (Plus Jakarta Sans, JetBrains Mono) and dark theme

## Phase 5: NativePHP Mobile Setup
- `[x]` Install `nativephp/mobile` package via Composer
- `[x]` Set `NATIVEPHP_APP_ID=com.segyrictext.app` in `.env`
- `[x]` Run `php artisan native:install --with-icu`
- `[x]` Let NativePHP scaffold and manage `nativephp/` (Android project + iOS config)
- `[x]` Create dedicated Native Mobile layout (`native/layouts/mobile.blade.php`) with safe-area support and bottom navigation bar
- `[x]` Create Native mobile screens (`home`, `library`, `editor`, `show`, `edit`, `activity`, `settings`)
- `[x]` Create API Sanctum endpoints for mobile in `routes/api.php`

## Phase 6: Middleware & Platform Detection
- `[x]` Create `DetectPlatform` middleware (`X-Platform`, `User-Agent`, native runtime detection)
- `[x]` Register middleware globally in `bootstrap/app.php`
- `[x]` Connect smart routing in `routes/web.php` and `routes/mobile.php`

## Phase 7: Queue Worker & Scheduler Scripts
- `[x]` Create `start-worker.bat` (Windows queue worker loop)
- `[x]` Create `start-scheduler.bat` (Windows scheduler worker loop)
- `[x]` Create `run-android.bat` (Windows Android runner)
- `[x]` Document Linux/cPanel crontab configuration for production

## Verification
- `[x]` Automated PHPUnit/Pest test suite passes 100% (10 tests, 30 assertions)
- `[x]` Auth (web session + mobile API token) tested and verified
- `[x]` Background queues process text analysis jobs idempotently
- `[x]` Scheduled commands execute and log metrics into database
- `[x]` Web and Mobile share identical backend models, services, and validation
