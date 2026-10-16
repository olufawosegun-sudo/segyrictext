<?php

namespace App\Jobs;

use App\Application\Services\OrderService;
use App\Infrastructure\Providers\PvaDealsClient;
use App\Models\NumberOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncOrder implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    /**
     * Unique lock for 15 minutes per order id
     */
    public int $uniqueFor = 900;

    public function __construct(public string $orderId) {}

    public function uniqueId(): string
    {
        return $this->orderId;
    }

    public function handle(OrderService $orderService, PvaDealsClient $pvaClient): void
    {
        $order = NumberOrder::find($this->orderId);

        if (!$order || $order->status !== 'provider_pending') {
            return;
        }

        // If order has been pending for more than 15 minutes, fail and refund
        if ($order->created_at->diffInMinutes(now()) >= 15) {
            $orderService->fail($order, 'Number delivery timed out after 15 minutes of PENDING state');
            return;
        }

        if (empty($order->provider_request_id)) {
            // Missing request ID; send to reconciliation
            ReconcileOrder::dispatch($order->id);
            return;
        }

        $requestData = $pvaClient->getRequest($order->provider_request_id);

        if (!$requestData) {
            // Re-poll in 10 seconds
            self::dispatch($order->id)->delay(now()->addSeconds(10));
            return;
        }

        $provStatus = strtoupper($requestData['status'] ?? '');
        $phone = $requestData['number'] ?? $requestData['phone_number'] ?? null;

        if (!empty($phone) && $provStatus !== 'PENDING') {
            // Number has arrived!
            $orderService->applyProvider($order, $requestData);
            return;
        }

        if (in_array($provStatus, ['TIMEOUT', 'CANCELLED', 'FLAGGED', 'FAILED'], true)) {
            $orderService->fail($order, "Provider cancelled reservation with status {$provStatus}");
            return;
        }

        // Still PENDING: schedule next poll in 10 seconds
        if (config('queue.default') !== 'sync' || !app()->environment('testing')) {
            self::dispatch($order->id)->delay(now()->addSeconds(10));
        }
    }
}
