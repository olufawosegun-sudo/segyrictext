<?php

namespace App\Jobs;

use App\Application\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentWebhookJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public string $gateway,
        public array $payload,
        public array $headers = []
    ) {}

    public function handle(PaymentService $paymentService): void
    {
        Log::info("ProcessPaymentWebhookJob: Asynchronously processing [{$this->gateway}] payment webhook");

        try {
            $result = $paymentService->processWebhook(
                gateway: $this->gateway,
                payload: $this->payload,
                headers: $this->headers
            );

            Log::info("ProcessPaymentWebhookJob: Payment webhook result for [{$this->gateway}]", [
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'Processed',
            ]);
        } catch (\Throwable $e) {
            Log::error("ProcessPaymentWebhookJob: Exception processing [{$this->gateway}] webhook: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
