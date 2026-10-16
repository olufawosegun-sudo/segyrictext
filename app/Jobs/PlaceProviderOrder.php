<?php

namespace App\Jobs;

use App\Application\Services\OrderService;
use App\Infrastructure\Providers\PvaDealsClient;
use App\Models\NumberOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlaceProviderOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Never auto-retry provider order placement (prevents double charging provider balance)
     */
    public int $tries = 1;

    public function __construct(public string $orderId) {}

    public function handle(OrderService $orderService, PvaDealsClient $pvaClient): void
    {
        // 1. Atomic claim: transition from 'created' to 'placing' under row lock
        $order = DB::transaction(function () {
            $ord = NumberOrder::where('id', $this->orderId)->lockForUpdate()->first();
            if (!$ord || $ord->status !== 'created') {
                return null;
            }

            $ord->update(['status' => 'placing']);
            return $ord;
        });

        if (!$order) {
            // Already claimed or already processed by another worker
            return;
        }

        // 2. Call PVADeals API v3
        $result = $pvaClient->purchaseLtr(
            'ALL_SERVICES',
            $order->duration_days,
            $order->country
        );

        // 3. Handle Ambiguous Outcome (Timeout / 500 / 502 / 504)
        if (!empty($result['is_ambiguous'])) {
            $order->update([
                'status' => 'needs_review',
                'failure_reason' => 'Ambiguous provider gateway response; reconciliation scheduled',
            ]);

            Log::warning("Order #{$order->id} placed into needs_review due to ambiguous outcome. Scheduling ReconcileOrder.");
            ReconcileOrder::dispatch($order->id)->delay(now()->addSeconds(30));
            return;
        }

        // 4. Handle Definitive Failure
        if (!($result['success'] ?? false)) {
            $reason = $result['message'] ?? 'Unable to allocate line from provider gateway';
            $orderService->fail($order, $reason);
            return;
        }

        // 5. Handle Successful Purchase (Immediate Delivery or PENDING)
        $reqData = $result['data']['requests'][0] ?? $result['data'] ?? $result;
        $orderService->applyProvider($order, $reqData);

        // If PENDING, queue polling sync job
        $refreshed = $order->fresh();
        if ($refreshed && $refreshed->status === 'provider_pending') {
            SyncOrder::dispatch($order->id)->delay(now()->addSeconds(10));
        }
    }
}
