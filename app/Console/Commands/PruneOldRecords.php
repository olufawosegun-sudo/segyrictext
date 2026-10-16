<?php

namespace App\Console\Commands;

use App\Models\ScheduledTaskLog;
use App\Services\MaintenanceCleanupService;
use Illuminate\Console\Command;

class PruneOldRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-old-records {--keep-days=30 : Days of audit logs to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prunes old task logs, trashed documents, and stale failed jobs';

    /**
     * Execute the console command.
     */
    public function handle(MaintenanceCleanupService $service): int
    {
        $start = microtime(true);
        $keepDays = (int) $this->option('keep-days');

        $log = ScheduledTaskLog::create([
            'task_name' => 'PruneOldRecords',
            'command' => $this->signature,
            'status' => 'running',
            'started_at' => now(),
        ]);

        $this->info("Pruning old records older than {$keepDays} days...");

        try {
            $logsPruned = $service->pruneScheduledTaskLogs($keepDays);
            $trashedPurged = $service->purgeDeletedDocuments(60);
            $failedPruned = $service->pruneFailedJobs(14);

            $total = $logsPruned + $trashedPurged + $failedPruned;
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $output = "Pruned {$logsPruned} logs, {$trashedPurged} trashed documents, {$failedPruned} failed jobs (Total: {$total}) in {$durationMs}ms.";
            $this->info($output);

            $log->update([
                'status' => 'completed',
                'output' => $output,
                'duration_ms' => $durationMs,
                'records_affected' => $total,
                'finished_at' => now(),
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $this->error("Failed to prune old records: {$e->getMessage()}");

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
