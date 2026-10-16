<?php

namespace App\Application\Services;

use App\Infrastructure\Providers\PvaDealsClient;
use App\Jobs\PlaceProviderOrder;
use App\Models\NumberOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderService
{
    public function __construct(
        protected PricingService $pricingService,
        protected WalletService $walletService,
        protected PvaDealsClient $pvaClient
    ) {}

    /**
     * Create a new all-services number order with idempotent double-tap protection,
     * balance checks, atomic wallet debit, and background job dispatch.
     *
     * @throws \Illuminate\Validation\ValidationException on invalid country or duration (422)
     * @throws HttpException on insufficient wallet balance (402) or provider unavailable (503)
     */
    public function create(User $user, string $country, ?int $duration, string $idempotencyKey): NumberOrder
    {
        // 1. Double-tap protection: if an order with this idempotency key already exists for the user, return it
        $existing = NumberOrder::where('user_id', $user->id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing) {
            return $existing;
        }

        // 2. Validate country & duration and snapshot quote (rejects Canada & invalid durations with 422)
        $quote = $this->pricingService->quote($country, $duration);

        // 3. Provider capacity & rate limit check
        if ($this->pvaClient->isPurchasePaused() || !$this->pvaClient->hasSufficientBalance($quote['usd_price'], config('numbers.pricing.provider_balance_buffer_usd', 2.0))) {
            throw new HttpException(503, "Provider capacity temporarily unavailable. Please try again shortly.");
        }

        // 4. Wallet balance verification
        $currentKobo = $this->walletService->getBalanceKobo($user);
        if ($currentKobo < $quote['price_kobo']) {
            throw new HttpException(402, "Insufficient wallet balance. Please top up your wallet to proceed.");
        }

        // 5. Atomic transaction: create order and debit wallet ledger
        return DB::transaction(function () use ($user, $idempotencyKey, $quote) {
            // Check again inside transaction to prevent parallel race conditions
            $existing = NumberOrder::where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $order = NumberOrder::create([
                'user_id' => $user->id,
                'idempotency_key' => $idempotencyKey,
                'country' => $quote['country'],
                'duration_days' => $quote['duration_days'],
                'usd_price' => $quote['usd_price'],
                'fx_rate' => $quote['fx_rate'],
                'margin_pct' => $quote['margin_pct'],
                'price_kobo' => $quote['price_kobo'],
                'status' => 'created',
            ]);

            // Ledger-based debit with immutable order reference
            $this->walletService->debitKobo(
                $user,
                $quote['price_kobo'],
                "order:{$order->id}:debit",
                "All-services rental: {$quote['country_name']} ({$quote['duration_days']} days)",
                ['order_id' => $order->id, 'country' => $quote['country']]
            );

            // Dispatch provider placement job asynchronously after commit
            PlaceProviderOrder::dispatch($order->id)->afterCommit();

            return $order;
        });
    }

    /**
     * Mark an order failed and issue an idempotent refund.
     * Never refunds an active order.
     */
    public function fail(NumberOrder|string $orderTarget, string $reason): NumberOrder
    {
        return DB::transaction(function () use ($orderTarget, $reason) {
            $orderId = $orderTarget instanceof NumberOrder ? $orderTarget->id : $orderTarget;
            $order = NumberOrder::where('id', $orderId)->lockForUpdate()->firstOrFail();

            // Guard: Never refund an order that is already active
            if ($order->status === 'active') {
                Log::warning("OrderService::fail aborted: Attempted to fail/refund an active order #{$order->id}");
                return $order;
            }

            // Guard: Idempotent no-op if already failed
            if ($order->status === 'failed') {
                return $order;
            }

            $order->update([
                'status' => 'failed',
                'failure_reason' => mb_substr($reason, 0, 250),
            ]);

            // Refund wallet using unique reference
            $this->walletService->refundKobo(
                $order->user_id,
                $order->price_kobo,
                "order:{$order->id}:refund",
                "Refund for order #{$order->id}: {$reason}",
                ['order_id' => $order->id, 'reason' => $reason]
            );

            return $order;
        });
    }

    /**
     * Apply provider updates (number provisioned, PENDING status, etc.).
     * Logs CRITICAL if a refunded order is later delivered by provider.
     */
    public function applyProvider(NumberOrder|string $orderTarget, array $providerData): NumberOrder
    {
        return DB::transaction(function () use ($orderTarget, $providerData) {
            $orderId = $orderTarget instanceof NumberOrder ? $orderTarget->id : $orderTarget;
            $order = NumberOrder::where('id', $orderId)->lockForUpdate()->firstOrFail();

            $phone = $providerData['number'] ?? $providerData['phone_number'] ?? null;
            $reqId = $providerData['requestId'] ?? $providerData['_id'] ?? $order->provider_request_id;
            $provStatus = strtoupper($providerData['status'] ?? 'RESERVED');

            // CRITICAL AUDIT: Provider delivered a number on an order that was already refunded
            if ($order->status === 'failed') {
                if (!empty($phone)) {
                    Log::critical("[MONEY SAFETY] Refunded order #{$order->id} was later delivered by PVADeals with phone {$phone} (ReqID: {$reqId}). Manual review required.");
                }
                return $order;
            }

            if (empty($phone) || $provStatus === 'PENDING') {
                $order->update([
                    'status' => 'provider_pending',
                    'provider_request_id' => $reqId,
                    'provider_status' => 'PENDING',
                ]);
            } else {
                if (!str_starts_with($phone, '+')) {
                    $phone = '+' . ltrim($phone, '+');
                }

                $order->update([
                    'status' => 'active',
                    'phone_number' => $phone,
                    'provider_request_id' => $reqId,
                    'provider_status' => $provStatus,
                    'expires_at' => now()->addDays($order->duration_days),
                    'failure_reason' => null,
                ]);
            }

            return $order;
        });
    }
}
