<?php

namespace App\Jobs;

use App\Http\Controllers\WalletDepositController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyPendingDepositJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $reference
    ) {}

    /**
     * Execute the job - verify payment status in background.
     */
    public function handle(): void
    {
        try {
            $depositController = app(WalletDepositController::class);
            $result = $depositController->processVerification($this->reference);

            if ($result['verified'] ?? false) {
                Log::info('[PAYMENT-JOB] Successfully verified deposit in background', [
                    'reference' => $this->reference,
                    'amount' => $result['amount'] ?? null
                ]);
            } else {
                Log::debug('[PAYMENT-JOB] Payment not yet confirmed', [
                    'reference' => $this->reference,
                    'pending' => $result['pending'] ?? false
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[PAYMENT-JOB] Background deposit verification failed', [
                'reference' => $this->reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[PAYMENT-JOB] Deposit verification job failed after all retries', [
            'reference' => $this->reference,
            'error' => $exception->getMessage()
        ]);
    }
}
