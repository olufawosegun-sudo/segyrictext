<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthMetricsService
{
    /**
     * Gather system health metrics.
     */
    public function getSystemHealth(): array
    {
        $metrics = [
            'timestamp' => now()->toIso8601String(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'database' => $this->checkDatabase(),
            'queue' => $this->checkQueue(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'counts' => [
                'users' => User::count(),
                'orders' => \App\Models\Order::count(),
                'sessions' => \App\Models\VerificationSession::count(),
                'phone_numbers' => \App\Models\PhoneNumber::count(),
            ],
        ];

        return $metrics;
    }

    protected function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'connection' => config('database.default'),
                'latency_ms' => $latency,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function checkQueue(): array
    {
        try {
            $pendingCount = DB::table('jobs')->count();
            $failedCount = DB::table('failed_jobs')->count();

            return [
                'status' => 'healthy',
                'driver' => config('queue.default'),
                'pending_jobs' => $pendingCount,
                'failed_jobs' => $failedCount,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'warning',
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function checkCache(): array
    {
        try {
            $key = 'health_check_ping_'.time();
            Cache::put($key, true, 10);
            $working = Cache::get($key) === true;
            Cache::forget($key);

            return [
                'status' => $working ? 'healthy' : 'degraded',
                'driver' => config('cache.default'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function checkStorage(): array
    {
        $storagePath = storage_path();
        $freeSpaceMb = @disk_free_space($storagePath) ? round(disk_free_space($storagePath) / 1024 / 1024, 2) : 'N/A';
        $writable = is_writable(storage_path('logs'));

        return [
            'status' => $writable ? 'healthy' : 'unhealthy',
            'writable' => $writable,
            'free_space_mb' => $freeSpaceMb,
        ];
    }
}
