<?php

namespace App\Services;

use App\Models\Document;
use App\Models\ScheduledTaskLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class MaintenanceCleanupService
{
    /**
     * Clean up expired Sanctum API tokens. Idempotent.
     */
    public function cleanupExpiredTokens(int $expirationDays = 30): int
    {
        $cutoff = now()->subDays($expirationDays);

        $deleted = PersonalAccessToken::where('created_at', '<', $cutoff)
            ->where(function ($query) {
                $query->whereNull('last_used_at')
                      ->orWhere('last_used_at', '<', now()->subDays(14));
            })
            ->delete();

        Log::info("MaintenanceCleanupService: Cleaned up {$deleted} expired/inactive API tokens.");
        return $deleted;
    }

    /**
     * Prune old scheduled task audit logs older than N days. Idempotent.
     */
    public function pruneScheduledTaskLogs(int $keepDays = 30): int
    {
        $cutoff = now()->subDays($keepDays);
        $deleted = ScheduledTaskLog::where('created_at', '<', $cutoff)->delete();

        Log::info("MaintenanceCleanupService: Pruned {$deleted} old scheduled task logs.");
        return $deleted;
    }

    /**
     * Purge soft-deleted documents older than N days. Idempotent.
     */
    public function purgeDeletedDocuments(int $keepDays = 60): int
    {
        $cutoff = now()->subDays($keepDays);
        $count = Document::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->forceDelete();

        Log::info("MaintenanceCleanupService: Permanently purged {$count} trashed documents.");
        return $count;
    }

    /**
     * Prune stale failed jobs older than N days.
     */
    public function pruneFailedJobs(int $keepDays = 7): int
    {
        $cutoff = now()->subDays($keepDays);
        $count = DB::table('failed_jobs')
            ->where('failed_at', '<', $cutoff)
            ->delete();

        Log::info("MaintenanceCleanupService: Pruned {$count} stale failed jobs.");
        return $count;
    }
}
