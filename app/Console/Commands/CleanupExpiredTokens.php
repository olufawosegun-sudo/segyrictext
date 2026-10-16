<?php

namespace App\Console\Commands;

use App\Models\ScheduledTaskLog;
use App\Services\MaintenanceCleanupService;
use Illuminate\Console\Command;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-expired-tokens {--days=30 : Age in days for expired tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up and purges expired Sanctum API tokens';

    /**
     * Execute the console command.
     */
    public function handle(MaintenanceCleanupService $service): int
    {
        $start = microtime(true);
        $days = (int) $this->option('days');

        $log = ScheduledTaskLog::create([
            'task_name' => 'CleanupExpiredTokens',
            'command' => $this->signature,
            'status' => 'running',
            'started_at' => now(),
        ]);

        $this->info("Purging API tokens older than {$days} days...");

        try {
            $deleted = $service->cleanupExpiredTokens($days);
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $output = "Cleaned up {$deleted} expired personal access tokens in {$durationMs}ms.";
            $this->info($output);

            $log->update([
                'status' => 'completed',
                'output' => $output,
                'duration_ms' => $durationMs,
                'records_affected' => $deleted,
                'finished_at' => now(),
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $this->error("Failed to clean up tokens: {$e->getMessage()}");

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
