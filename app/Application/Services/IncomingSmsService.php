<?php

namespace App\Application\Services;

use App\Domain\Enums\SmsProcessingStatus;
use App\Domain\Enums\VerificationStatus;
use App\Models\OtpCode;
use App\Models\PhoneNumber;
use App\Models\SmsMessage;
use App\Models\VerificationSession;
use Illuminate\Support\Facades\DB;

class IncomingSmsService
{
    /**
     * Ingest an incoming SMS, deduplicate, extract OTP, and update verification session.
     */
    public function processIncomingMessage(
        string $recipient,
        string $messageBody,
        ?string $sender = null,
        ?string $providerMessageId = null
    ): ?SmsMessage {
        // 1. Generate deduplication hash
        $cleanRecipient = preg_replace('/[^\d+]/', '', $recipient);
        $messageHash = hash('sha256', "{$cleanRecipient}|{$sender}|{$messageBody}");

        // Idempotency check: Ignore duplicate delivery
        $existing = SmsMessage::where('message_hash', $messageHash)->first();
        if ($existing) {
            return $existing;
        }

        // 2. Resolve matching phone number
        $numberHash = hash('sha256', $cleanRecipient);
        $phoneNumber = PhoneNumber::where('phone_number_hash', $numberHash)
            ->orWhere('phone_number', $cleanRecipient)
            ->first();

        if (!$phoneNumber) {
            return null;
        }

        // 3. Extract OTP code using smart patterns
        $extractedOtp = $this->extractOtpCode($messageBody);

        return DB::transaction(function () use ($phoneNumber, $cleanRecipient, $messageBody, $sender, $providerMessageId, $messageHash, $extractedOtp) {
            // 4. Find active verification session for this number
            $session = VerificationSession::query()
                ->where('phone_number_id', $phoneNumber->id)
                ->whereIn('status', [
                    VerificationStatus::WaitingForSms,
                    VerificationStatus::Assigned,
                    VerificationStatus::Searching,
                ])
                ->latest()
                ->first();

            // 5. Create immutable SMS record
            $sms = SmsMessage::create([
                'verification_session_id' => $session?->id,
                'phone_number_id' => $phoneNumber->id,
                'provider_id' => $phoneNumber->provider_id,
                'message_id' => $providerMessageId,
                'sender' => $sender ?? 'SMS Gateway',
                'recipient' => $cleanRecipient,
                'message_body' => $messageBody,
                'message_hash' => $messageHash,
                'received_at' => now(),
                'processing_status' => SmsProcessingStatus::Processed,
            ]);

            // 6. If active session exists, update state & store OTP code
            if ($session) {
                if ($extractedOtp) {
                    OtpCode::create([
                        'verification_session_id' => $session->id,
                        'code_hash' => hash('sha256', $extractedOtp),
                        'code_length' => strlen($extractedOtp),
                        'status' => 'received',
                        'attempts' => 0,
                        'max_attempts' => 5,
                        'expires_at' => now()->addMinutes(10),
                    ]);
                }

                $session->update([
                    'status' => VerificationStatus::SmsReceived,
                    'first_sms_at' => $session->first_sms_at ?? now(),
                ]);
            }

            return $sms;
        });
    }

    /**
     * Smart Regex extraction of 4 to 8 digit verification codes.
     */
    public function extractOtpCode(string $text): ?string
    {
        // Pattern 1: Keywords like "code is 123456" or "pin: 1234"
        if (preg_match('/(?:code|pin|verification|otp|is|passcode)[:\s-]+(\b\d{4,8}\b)/i', $text, $matches)) {
            return $matches[1];
        }

        // Pattern 2: Standalone 4 to 8 digit numbers in message
        if (preg_match('/\b(\d{4,8})\b/', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
