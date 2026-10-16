<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendDigestNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $userId, public array $digestData)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            return;
        }

        // Insert notification using Notification model
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'daily_digest',
            'title' => 'Daily Verixa Digest',
            'message' => "You have {$this->digestData['recent_documents']} recent documents and {$this->digestData['processed_documents']} processed items.",
            'data' => [
                'title' => 'Daily Verixa Digest',
                'date' => $this->digestData['date'] ?? now()->toDateString(),
                'metrics' => $this->digestData,
            ],
            'read_at' => null,
        ]);

        Log::info("SendDigestNotificationJob: Saved digest notification for User #{$user->id}");
    }
}
