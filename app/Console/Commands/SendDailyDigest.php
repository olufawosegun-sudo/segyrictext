<?php

namespace App\Console\Commands;

use App\Models\ScheduledTaskLog;
use App\Services\NotificationDigestService;
use Illuminate\Console\Command;

class SendDailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches daily activity digests and summaries to users';

    /**
     * Execute the console command.
     */
    public function handle(NotificationDigestService $service): int
    {
        $start = microtime(true);
        $log = ScheduledTaskLog::create([
            'task_name' => 'SendDailyDigest',
            'command' => $this->signature,
            'status' => 'running',
            'started_at' => now(),
        ]);

        $this->info("Dispatching daily digests for active users...");

        try {
            $dispatched = $service->dispatchDailyDigests();
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $output = "Dispatched {$dispatched} daily digest notification jobs in {$durationMs}ms.";
            $this->info($output);

            $log->update([
                'status' => 'completed',
                'output' => $output,
                'duration_ms' => $durationMs,
                'records_affected' => $dispatched,
                'finished_at' => now(),
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $this->error("Failed to dispatch daily digests: {$e->getMessage()}");

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'duration_ms' => $durationMs,
                'finished_at' => now(),
            ]);

            return Command::FAILURE;
        }
    }
}
