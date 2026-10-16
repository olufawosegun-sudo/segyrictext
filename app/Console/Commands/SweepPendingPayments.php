<?php

namespace App\Console\Commands;

use App\Application\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SweepPendingPayments extends Command
{
    protected $signature = 'payments:sweep-pending {--minutes=15 : How many minutes a payment can stay pending before re-verification}';

    protected $description = 'Re-verifies payments stuck in "pending" status beyond the timeout threshold';

    public function handle(PaymentService $paymentService): int
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = now()->subMinutes($minutes);

        $stalePayments = Payment::where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->where('created_at', '>=', now()->subHours(24)) // Don't sweep ancient payments
            ->limit(50)
            ->get();

        if ($stalePayments->isEmpty()) {
            $this->info('No stale pending payments found.');
            return self::SUCCESS;
        }

        $this->info("Found {$stalePayments->count()} stale pending payment(s). Sweeping...");

        $verified = 0;
        $failed = 0;

        foreach ($stalePayments as $payment) {
            try {
                $result = $paymentService->verifyPayment($payment->provider_reference);

                if ($result['success'] ?? false) {
                    $verified++;
                    $this->info("  ✓ Payment #{$payment->id} [{$payment->provider_reference}]: verified and credited.");
                } else {
                    $failed++;
                    // Mark as expired if gateway says it's no longer valid
                    $status = $result['status'] ?? 'failed';
                    if (in_array($status, ['failed', 'abandoned', 'expired'])) {
                        $payment->update(['status' => 'expired']);
                        $this->warn("  ✗ Payment #{$payment->id}: {$result['message']} — marked expired.");
                    } else {
                        $this->warn("  ~ Payment #{$payment->id}: {$result['message']} — still pending.");
                    }
                }
            } catch (\Throwable $e) {
                $failed++;
                Log::error("SweepPendingPayments: Exception verifying payment #{$payment->id}: {$e->getMessage()}");
                $this->error("  ✗ Payment #{$payment->id}: Exception - {$e->getMessage()}");
            }
        }

        $this->info("Sweep complete: {$verified} verified, {$failed} failed/pending out of {$stalePayments->count()} total.");
        return self::SUCCESS;
    }
}
