<?php

namespace App\Application\Services;

use App\Domain\DTOs\IncomingSmsDTO;
use App\Domain\Verification\Events\SmsReceived;
use App\Domain\Verification\Events\VerificationSessionCompleted;
use App\Domain\Verification\Services\OtpExtractorInterface;
use App\Models\NumberReservation;
use App\Models\OtpCode;
use App\Models\PhoneNumber;
use App\Models\PhoneNumberStatusHistory;
use App\Models\Provider;
use App\Models\SmsMessage;
use App\Models\VerificationSession;
use App\Models\WalletHold;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsIngestionService
{
    public function __construct(
        protected OtpExtractorInterface $otpExtractor,
        protected WalletService $walletService
    ) {}

    /**
     * Ingest an incoming SMS from a provider webhook or mock, extract OTP,
     * capture the wallet hold, and complete the verification session.
     */
    public function ingest(IncomingSmsDTO $smsDto): ?SmsMessage
    {
        // 1. Normalize phone number (strip spaces, symbols, ensure leading + or matching formats)
        $cleanNumber = preg_replace('/[^0-9+]/', '', $smsDto->phoneNumber);
        $numberWithoutPlus = ltrim($cleanNumber, '+');

        // Locate phone number record
        $phoneNumber = PhoneNumber::query()
            ->where(function ($query) use ($cleanNumber, $numberWithoutPlus, $smsDto) {
                $query->where('phone_number', $cleanNumber)
                    ->orWhere('phone_number', '+' . $numberWithoutPlus)
                    ->orWhere('phone_number', $numberWithoutPlus);
                if ($smsDto->providerNumberId) {
                    $query->orWhere('provider_number_id', $smsDto->providerNumberId);
                }
            })
            ->first();

        if (!$phoneNumber) {
            Log::warning("Incoming SMS for unknown phone number: {$smsDto->phoneNumber}");
            return null;
        }

        // Provider resolution
        $provider = null;
        if ($smsDto->providerCode) {
            $provider = Provider::where('code', $smsDto->providerCode)->first();
        }
        $provider ??= $phoneNumber->provider;

        // 2. Find active or pending verification session for this phone number
        $session = VerificationSession::query()
            ->where('phone_number_id', $phoneNumber->id)
            ->whereIn('status', ['active', 'pending', 'sms_received'])
            ->latest()
            ->first();

        // 3. Extract OTP from message body
        $extractedOtp = $this->otpExtractor->extractCode($smsDto->message);

        return DB::transaction(function () use ($smsDto, $phoneNumber, $session, $provider, $extractedOtp) {
            // Idempotency check: don't duplicate provider message ID if already saved
            if ($smsDto->providerMessageId) {
                $existingMessage = SmsMessage::where('provider_message_id', $smsDto->providerMessageId)->first();
                if ($existingMessage) {
                    return $existingMessage;
                }
            }

            // Persist SMS message record
            $smsMessage = SmsMessage::create([
                'phone_number_id' => $phoneNumber->id,
                'verification_session_id' => $session?->id,
                'provider_id' => $provider?->id ?? $phoneNumber->provider_id,
                'from_number' => $smsDto->fromNumber ?? 'UNKNOWN',
                'message' => $smsDto->message,
                'provider_message_id' => $smsDto->providerMessageId ?? ('MSG_' . uniqid('', true)),
                'received_at' => $smsDto->receivedAt ?? now(),
            ]);

            // If an active session exists and OTP was found
            if ($session && $extractedOtp) {
                // Record OTP code
                $otpCode = OtpCode::create([
                    'verification_session_id' => $session->id,
                    'code' => $extractedOtp,
                    'status' => 'received',
                    'received_at' => now(),
                    'expires_at' => now()->addMinutes(10),
                    'used_at' => null,
                ]);

                // Update session state to completed
                $session->update([
                    'verification_code' => $extractedOtp,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Atomically capture the wallet hold tied to this session's order item
                $hold = WalletHold::query()
                    ->where('reference', 'HOLD_ITEM_' . $session->order_item_id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if ($hold) {
                    $serviceName = $session->service?->name ?? 'SMS Verification';
                    $this->walletService->captureHold(
                        $hold,
                        "Completed {$serviceName} verification ({$phoneNumber->phone_number})"
                    );
                }

                // Update Order status
                if ($session->orderItem && $session->orderItem->order) {
                    $session->orderItem->order->update(['status' => 'completed']);
                }

                // Mark number reservation as completed
                NumberReservation::query()
                    ->where('order_item_id', $session->order_item_id)
                    ->where('status', 'reserved')
                    ->update([
                        'status' => 'completed',
                        'released_at' => now(),
                    ]);

                // Update phone number status history
                $phoneNumber->update(['status' => 'used']);
                PhoneNumberStatusHistory::create([
                    'phone_number_id' => $phoneNumber->id,
                    'old_status' => 'reserved',
                    'new_status' => 'used',
                    'reason' => "Verification completed with OTP: {$extractedOtp}",
                    'created_at' => now(),
                ]);

                // Fire event
                event(new VerificationSessionCompleted($session, $otpCode));
            }

            // Fire SMS received event
            event(new SmsReceived($smsMessage));

            return $smsMessage;
        });
    }
}
