<?php

namespace App\Application\Services;

use App\Domain\Verification\Events\VerificationSessionCancelled;
use App\Domain\Verification\Events\VerificationSessionExpired;
use App\Models\NumberReservation;
use App\Models\PhoneNumberStatusHistory;
use App\Models\VerificationSession;
use App\Models\WalletHold;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificationLifecycleService
{
    public function __construct(
        protected WalletService $walletService,
        protected NumberReservationService $reservationService
    ) {}

    /**
     * Cancel an active or pending verification session, releasing the wallet hold and number.
     */
    public function cancelSession(VerificationSession $session, string $reason = 'user_cancelled'): bool
    {
        if (!in_array($session->status, ['active', 'pending'])) {
            return false;
        }

        return DB::transaction(function () use ($session, $reason) {
            // 1. Release wallet hold so user gets funds restored immediately
            $hold = WalletHold::query()
                ->where('reference', 'HOLD_ITEM_' . $session->order_item_id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if ($hold) {
                $this->walletService->releaseHold($hold, "Session cancelled: {$reason}");
            }

            // 2. Release number reservation and update phone number status
            NumberReservation::query()
                ->where('order_item_id', $session->order_item_id)
                ->where('status', 'reserved')
                ->update([
                    'status' => 'cancelled',
                    'released_at' => now(),
                ]);

            if ($session->phoneNumber) {
                $session->phoneNumber->update(['status' => 'available']);
                PhoneNumberStatusHistory::create([
                    'phone_number_id' => $session->phoneNumber->id,
                    'old_status' => 'reserved',
                    'new_status' => 'available',
                    'reason' => "Session cancelled ({$reason})",
                    'created_at' => now(),
                ]);
            }

            // 3. Mark session and order cancelled
            $session->update([
                'status' => 'cancelled',
            ]);

            if ($session->orderItem && $session->orderItem->order) {
                $session->orderItem->order->update(['status' => 'cancelled']);
            }

            event(new VerificationSessionCancelled($session, $reason));

            return true;
        });
    }

    /**
     * Batch expire all sessions past their expires_at timestamp, auto-refunding held funds.
     */
    public function expireOverdueSessions(): int
    {
        $overdueSessions = VerificationSession::query()
            ->whereIn('status', ['active', 'pending'])
            ->where('expires_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($overdueSessions as $session) {
            DB::transaction(function () use ($session, &$count) {
                // Lock session row
                $lockedSession = VerificationSession::where('id', $session->id)->lockForUpdate()->first();
                if (!$lockedSession || !in_array($lockedSession->status, ['active', 'pending'])) {
                    return;
                }

                // 1. Release wallet hold
                $hold = WalletHold::query()
                    ->where('reference', 'HOLD_ITEM_' . $lockedSession->order_item_id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if ($hold) {
                    $this->walletService->releaseHold($hold, 'Auto-released: session expired with no SMS received');
                }

                // 2. Release number reservation
                NumberReservation::query()
                    ->where('order_item_id', $lockedSession->order_item_id)
                    ->where('status', 'reserved')
                    ->update([
                        'status' => 'expired',
                        'released_at' => now(),
                    ]);

                if ($lockedSession->phoneNumber) {
                    $lockedSession->phoneNumber->update(['status' => 'available']);
                    PhoneNumberStatusHistory::create([
                        'phone_number_id' => $lockedSession->phoneNumber->id,
                        'old_status' => 'reserved',
                        'new_status' => 'available',
                        'reason' => 'Session expired timeout; number returned to pool',
                        'created_at' => now(),
                    ]);
                }

                // 3. Update session and order
                $lockedSession->update(['status' => 'expired']);

                if ($lockedSession->orderItem && $lockedSession->orderItem->order) {
                    $lockedSession->orderItem->order->update(['status' => 'expired']);
                }

                event(new VerificationSessionExpired($lockedSession));
                $count++;
            });
        }

        return $count;
    }
}
