<?php

namespace App\Services;

use App\Jobs\SendDigestNotificationJob;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationDigestService
{
    /**
     * Dispatch daily summary digest jobs for active users.
     */
    public function dispatchDailyDigests(): int
    {
        $users = User::all();
        $dispatched = 0;

        foreach ($users as $user) {
            $recentCount = Document::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $processedCount = Document::where('user_id', $user->id)
                ->where('status', 'processed')
                ->where('processed_at', '>=', now()->subDay())
                ->count();

            // Dispatch async job for user digest
            SendDigestNotificationJob::dispatch($user->id, [
                'recent_documents' => $recentCount,
                'processed_documents' => $processedCount,
                'date' => now()->toDateString(),
            ]);

            $dispatched++;
        }

        Log::info("NotificationDigestService: Dispatched {$dispatched} daily digest jobs.");
        return $dispatched;
    }
}
