<?php

namespace App\Jobs;

use App\Application\Services\OrderService;
use App\Infrastructure\Providers\PvaDealsClient;
use App\Models\NumberOrder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReconcileOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public string $orderId) {}

    public function handle(OrderService $orderService, PvaDealsClient $pvaClient): void
    {
        $order = NumberOrder::find($this->orderId);

        if (!$order || !in_array($order->status, ['needs_review', 'placing'], true)) {
            return;
        }

        // Fetch recent requests from provider gateway
        $res = $pvaClient->listRequests(['first' => 50, 'orderBy' => 'desc']);
        $rawNodes = [];

        // Handle both cursor format and flat array
        if (!empty($res['data']['edges'])) {
            foreach ($res['data']['edges'] as $edge) {
                if (!empty($edge['node'])) {
                    $rawNodes[] = $edge['node'];
                }
            }
        } elseif (!empty($res['data']['requests'])) {
            $rawNodes = $res['data']['requests'];
        } elseif (!empty($res['data']) && is_array($res['data'])) {
            $rawNodes = $res['data'];
        }

        $orderTime = $order->created_at;
        $orderCountry = strtoupper($order->country);

        // Filter potential matches: country matches, created within 15 minutes of order
        $matches = [];
        foreach ($rawNodes as $node) {
            $nodeCountry = strtoupper($node['country'] ?? '');
            if ($nodeCountry === 'USA') $nodeCountry = 'US';
            if ($nodeCountry === 'GB' || $nodeCountry === 'GBR') $nodeCountry = 'UK';

            if ($nodeCountry !== $orderCountry) {
                continue;
            }

            // Verify timestamp if available
            $nodeTime = !empty($node['createdAt']) ? Carbon::parse($node['createdAt']) : null;
            if ($nodeTime) {
                $diffMinutes = abs($orderTime->diffInMinutes($nodeTime));
                if ($diffMinutes > 15) {
                    continue;
                }
            }

            $matches[] = $node;
        }

        $matchCount = count($matches);

        // Case 1: Exactly one match -> Link it safely!
        if ($matchCount === 1) {
            $matchedNode = $matches[0];
            Log::info("ReconcileOrder: Exactly 1 match found for order #{$order->id} (Request ID {$matchedNode['_id']}). Applying.");
            $orderService->applyProvider($order, $matchedNode);

            $refreshed = $order->fresh();
            if ($refreshed && $refreshed->status === 'provider_pending') {
                SyncOrder::dispatch($order->id)->delay(now()->addSeconds(10));
            }
            return;
        }

        // Case 2: Zero matches
        if ($matchCount === 0) {
            $minutesAgo = $orderTime ? abs(now()->diffInMinutes($orderTime)) : 0;
            if ($minutesAgo >= 10) {
                // After 10 minutes with 0 matches on provider, it is definitive that no line was allocated
                Log::info("ReconcileOrder: 0 matches found after 10 minutes for order #{$order->id}. Issuing refund.");
                $orderService->fail($order, 'No matching line found on provider gateway after 10 minutes of reconciliation');
            } else {
                // Retry in 60s (safety guard for sync queue driver in testing environment)
                if (config('queue.default') !== 'sync' || !app()->environment('testing')) {
                    self::dispatch($order->id)->delay(now()->addSeconds(60));
                }
            }
            return;
        }

        // Case 3: Multiple matches (2 or more) -> Log CRITICAL for human review, never guess!
        Log::critical("[MONEY SAFETY] Ambiguous reconciliation: {$matchCount} potential matches found on provider for order #{$order->id}. Leaving in needs_review for manual administrator review.");
    }
}
