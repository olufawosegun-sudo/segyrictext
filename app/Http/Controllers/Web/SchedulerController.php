<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ScheduledTaskLog;
use App\Services\HealthMetricsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchedulerController extends Controller
{
    public function index(HealthMetricsService $healthService): View
    {
        $health = $healthService->getSystemHealth();

        $scheduledTasks = [
            [
                'name' => 'System Health Diagnostics',
                'command' => 'app:system-health-check',
                'schedule' => 'Every 5 minutes (*/5 * * * *)',
                'description' => 'Collects database latency, queue pending counts, storage free space, and memory metrics.',
                'icon' => 'activity',
            ],
            [
                'name' => 'Process Pending & Scheduled Texts',
                'command' => 'app:process-pending-texts',
                'schedule' => 'Every minute (* * * * *)',
                'description' => 'Scans for documents due for background analysis and dispatches queue jobs.',
                'icon' => 'cpu',
            ],
            [
                'name' => 'Daily User Activity Digest',
                'command' => 'app:send-daily-digest',
                'schedule' => 'Daily at 08:00 AM (0 8 * * *)',
                'description' => 'Aggregates 24-hour activity and sends automated user summary digests.',
                'icon' => 'mail',
            ],
            [
                'name' => 'Cleanup Expired API Tokens',
                'command' => 'app:cleanup-expired-tokens',
                'schedule' => 'Daily at midnight (0 0 * * *)',
                'description' => 'Purges expired or abandoned Sanctum mobile tokens older than 30 days.',
                'icon' => 'key',
            ],
            [
                'name' => 'Prune Old Audit Logs & Trashed Records',
                'command' => 'app:prune-old-records',
                'schedule' => 'Weekly on Sundays at 02:00 AM (0 2 * * 0)',
                'description' => 'Permanently cleans soft-deleted records older than 60 days and old task logs.',
                'icon' => 'trash-2',
            ],
            [
                'name' => 'Sweep Pending Payments',
                'command' => 'payments:sweep-pending',
                'schedule' => 'Every 2 minutes (*/2 * * * *)',
                'description' => 'Re-verifies stale pending payments that may have missed their callback webhook.',
                'icon' => 'credit-card',
            ],
        ];

        $taskLogs = ScheduledTaskLog::latest()->paginate(15);
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        return view('web.scheduler.index', compact('scheduledTasks', 'taskLogs', 'health', 'pendingJobs', 'failedJobs'));
    }

    public function trigger(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'command' => ['required', 'string', 'in:app:system-health-check,app:process-pending-texts,app:send-daily-digest,app:cleanup-expired-tokens,app:prune-old-records,payments:sweep-pending'],
        ]);

        $exitCode = Artisan::call($validated['command']);
        $output = trim(Artisan::output());

        if ($exitCode === 0) {
            return back()->with('success', "Task [{$validated['command']}] executed successfully! Output: {$output}");
        }

        return back()->with('error', "Task [{$validated['command']}] failed with code {$exitCode}: {$output}");
    }

    public function clearLogs(): RedirectResponse
    {
        ScheduledTaskLog::truncate();
        return back()->with('success', 'Scheduled task execution logs cleared.');
    }
}
