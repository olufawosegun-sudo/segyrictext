<?php

namespace App\Console\Commands;

use App\Models\ScheduledTaskLog;
use App\Services\TextProcessingService;
use Illuminate\Console\Command;

class ProcessPendingTexts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-pending-texts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches background jobs for all pending or scheduled documents';

    /**
     * Execute the console command.
     */
    public function handle(TextProcessingService $service): int
    {
        $start = microtime(true);
        $log = ScheduledTaskLog::create([
            'task_name' => 'ProcessPendingTexts',
            'command' => $this->signature,
            'status' => 'running',
            'started_at' => now(),
        ]);

        $this->info("Checking for pending and scheduled texts to process...");

        try {
            $processed = $service->processPendingScheduled();
            $durationMs = (int) round((microtime(true) - $start) * 1000);

            $output = "Dispatched {$processed} pending text processing jobs in {$durationMs}ms.";
            $this->info($output);

            $log->update([
                'status' => 'completed',
                'output' => $output,
                'duration_ms' => $durationMs,
                'records_affected' => $processed,
                'finished_at' => now(),
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $this->error("Failed to process pending texts: {$e->getMessage()}");

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
