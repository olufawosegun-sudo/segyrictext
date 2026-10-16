<?php

namespace App\Application\Services;

use App\Domain\Number\Exceptions\NumberUnavailableException;
use App\Models\NumberReservation;
use App\Models\PhoneNumber;
use App\Models\PhoneNumberStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NumberReservationService
{
    /**
     * Atomically reserve a phone number for a user with pessimistic locking.
     *
     * @throws NumberUnavailableException
     */
    public function reserve(
        User $user,
        PhoneNumber|int $phoneNumberTarget,
        ?int $expiresInSeconds = 1200,
        ?int $orderItemId = null
    ): NumberReservation {
        return DB::transaction(function () use ($user, $phoneNumberTarget, $expiresInSeconds, $orderItemId) {
            $phoneNumberId = $phoneNumberTarget instanceof PhoneNumber ? $phoneNumberTarget->id : $phoneNumberTarget;

            $phoneNumber = PhoneNumber::where('id', $phoneNumberId)->lockForUpdate()->firstOrFail();

            if ($phoneNumber->status !== 'available') {
                throw new NumberUnavailableException($phoneNumber->id, $phoneNumber->status);
            }

            $oldStatus = $phoneNumber->status;
            $phoneNumber->update(['status' => 'reserved']);

            // Create reservation record
            $reservation = NumberReservation::create([
                'phone_number_id' => $phoneNumber->id,
                'user_id' => $user->id,
                'order_item_id' => $orderItemId,
                'status' => 'reserved',
                'reserved_at' => now(),
                'expires_at' => now()->addSeconds($expiresInSeconds ?? 1200),
                'released_at' => null,
            ]);

            // Track status history
            PhoneNumberStatusHistory::create([
                'phone_number_id' => $phoneNumber->id,
                'old_status' => $oldStatus,
                'new_status' => 'reserved',
                'reason' => 'User reservation initialized',
                'created_at' => now(),
            ]);

            return $reservation;
        });
    }

    /**
     * Atomically release a reservation and return the phone number back to the available pool.
     */
    public function release(
        NumberReservation|int $reservationTarget,
        string $reason = 'Reservation released'
    ): void {
        DB::transaction(function () use ($reservationTarget, $reason) {
            $reservationId = $reservationTarget instanceof NumberReservation ? $reservationTarget->id : $reservationTarget;

            $reservation = NumberReservation::where('id', $reservationId)->lockForUpdate()->firstOrFail();

            if ($reservation->status === 'released') {
                return;
            }

            $phoneNumber = PhoneNumber::where('id', $reservation->phone_number_id)->lockForUpdate()->firstOrFail();

            $reservation->update([
                'status' => 'released',
                'released_at' => now(),
            ]);

            $oldStatus = $phoneNumber->status;
            $phoneNumber->update(['status' => 'available']);

            PhoneNumberStatusHistory::create([
                'phone_number_id' => $phoneNumber->id,
                'old_status' => $oldStatus,
                'new_status' => 'available',
                'reason' => $reason,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Sweep expired reservations and release numbers back to pool.
     */
    public function releaseExpiredReservations(): int
    {
        $expired = NumberReservation::expired()->get();
        $count = 0;

        foreach ($expired as $reservation) {
            try {
                $this->release($reservation, 'Reservation expired automatically');
                $count++;
            } catch (\Throwable) {
                // Continue
            }
        }

        return $count;
    }
}
