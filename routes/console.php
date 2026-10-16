<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Application Task Scheduler
|--------------------------------------------------------------------------
|
| The following schedule defines the recurring background tasks.
| All tasks use idempotent service methods with failure logging,
| without overlapping prevention, and graceful timeout handling.
|
*/

// 1. Periodic System Health Diagnostics (Every 5 minutes)
Schedule::command('app:system-health-check')
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 2. Process Pending and Scheduled Texts/Documents (Every minute)
Schedule::command('app:process-pending-texts')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 3. Expire Overdue Virtual Number Verification Sessions & Refund Holds (Every minute)
Schedule::command('verification:expire')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 3. Send Daily Activity Digests to Users (Daily at 08:00 AM)
Schedule::command('app:send-daily-digest')
    ->dailyAt('08:00')
    ->withoutOverlapping(30)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 4. Clean up Expired Personal Access Tokens (Daily at midnight)
Schedule::command('app:cleanup-expired-tokens --days=30')
    ->daily()
    ->withoutOverlapping(15)
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 5. Prune Old Audit Logs, Trashed Records, and Stale Failed Jobs (Weekly on Sundays at 02:00 AM)
Schedule::command('app:prune-old-records --keep-days=30')
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping(30)
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 6. Prune Failed Queue Jobs older than 48 hours
Schedule::command('queue:prune-failed --hours=48')
    ->daily()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 7. Prune models implementing Prunable interface
Schedule::command('model:prune')
    ->daily()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 8. Sweep Pending Payments — retry verification for payments stuck in 'pending' (every minute)
Schedule::command('payments:sweep-pending')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 9. Restart Queue Workers after code deploys (daily at 03:00 AM)
Schedule::command('queue:restart')
    ->dailyAt('03:00')
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 10. Safety Net: Reconcile stuck placing/needs_review orders & sync pending orders (Every 2 minutes)
Schedule::command('orders:reconcile-stuck')
    ->everyTwoMinutes()
    ->withoutOverlapping(5)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 11. Sync PVADeals Catalog, Prices & Best Value (Every 30 minutes)
Artisan::command('pvadeals:sync', function (\App\Services\PvaDeals\PvaDealsSyncService $syncService) {
    $this->info('Starting PVADeals catalog synchronization...');
    $res = $syncService->sync();
    if (!empty($res['success'])) {
        $this->info("Catalog synchronized successfully. Services: {$res['services_count']}, Countries: {$res['countries_count']}");
    } else {
        $this->error("Catalog sync failed: " . ($res['error'] ?? 'Unknown error'));
    }
})->purpose('Synchronize PVADeals catalog, supplier prices, availability, and Best Value classifications');

Schedule::command('pvadeals:sync')
    ->everyThirtyMinutes()
    ->withoutOverlapping(15)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

