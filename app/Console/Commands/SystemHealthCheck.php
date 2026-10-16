<?php

namespace App\Console\Commands;

use App\Models\ScheduledOperation;
use App\Services\HealthMetricsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:system-health-check {--quiet-log : Only log on warnings or errors}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs periodic system diagnostics, queue health checks, and database status';

    /**
     * Execute the console command.
     */
    public function handle(HealthMetricsService $service): int
    {
        $start = microtime(true);
        $health = $service->getSystemHealth();
        $durationMs = (int) round((microtime(true) - $start) * 1000);

        $dbStatus = $health['database']['status'] ?? 'unknown';
        $queueStatus = $health['queue']['status'] ?? 'unknown';
        $cacheStatus = $health['cache']['status'] ?? 'unknown';

        $isHealthy = ($dbStatus === 'healthy') && ($queueStatus !== 'unhealthy') && ($cacheStatus !== 'unhealthy');

        $output = sprintf(
            "Health Check: DB=%s (latency: %sms), Queue=%s (pending: %d, failed: %d), Cache=%s, Users=%d, Docs=%d",
            $dbStatus,
            $health['database']['latency_ms'] ?? 0,
            $queueStatus,
            $health['queue']['pending_jobs'] ?? 0,
            $health['queue']['failed_jobs'] ?? 0,
            $cacheStatus,
            $health['counts']['users'] ?? 0,
            $health['counts']['documents'] ?? 0
        );

        $this->info($output);

        if (!$this->option('quiet-log') || !$isHealthy) {
            Log::channel('single')->info("SystemHealthCheck: {$output}");
        }

        ScheduledOperation::updateOrCreate(
            ['operation_type' => 'SystemHealthCheck'],
            [
                'status' => $isHealthy ? 'completed' : 'failed',
                'scheduled_at' => now(),
                'started_at' => now()->subMilliseconds($durationMs),
                'completed_at' => now(),
                'error_message' => $isHealthy ? null : 'Degraded components detected: ' . substr($output, 0, 500),
            ]
        );

        return $isHealthy ? Command::SUCCESS : Command::FAILURE;
    }
}
