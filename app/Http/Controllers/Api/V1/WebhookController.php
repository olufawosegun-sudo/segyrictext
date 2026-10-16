<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\PaymentService;
use App\Application\Services\SmsIngestionService;
use App\Domain\DTOs\IncomingSmsDTO;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessCarrierSmsJob;
use App\Jobs\ProcessPaymentWebhookJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected SmsIngestionService $smsIngestionService,
        protected PaymentService $paymentService
    ) {}

    /**
     * Inbound SMS webhook receiver for carrier integrations.
     */
    public function handleCarrierWebhook(Request $request, string $provider): Response|JsonResponse
    {
        Log::info("Carrier inbound webhook received [{$provider}]", [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        $lowerProvider = strtolower($provider);

        // Handle PVADeals event notifications (acknowledge non-sms events like number_purchased, number_flagged, number_renewed, number_reused, sms_blocked)
        if (in_array($lowerProvider, ['pvadeals', 'pva-deals'], true)) {
            $event = $request->input('event');
            if ($event && $event !== 'sms_received') {
                $logContext = [
                    'event' => $event,
                    'requestId' => $request->input('requestId'),
                    'serviceId' => $request->input('serviceId'),
                    'number' => $request->input('number'),
                ];

                // number_renewed gives a NEW requestId and an oldRequestId. Update stored order/number to new ID.
                if ($event === 'number_renewed') {
                    $oldRequestId = (string) $request->input('oldRequestId');
                    $newRequestId = (string) $request->input('requestId');
                    $newExpiresAt = $request->input('newExpiresAt');

                    $logContext['oldRequestId'] = $oldRequestId;
                    $logContext['serviceName'] = $request->input('serviceName');
                    $logContext['amount'] = $request->input('amount');
                    $logContext['newExpiresAt'] = $newExpiresAt;

                    if (!empty($oldRequestId) && !empty($newRequestId)) {
                        // Idempotent lookup: find phone number with oldRequestId (or already updated to newRequestId)
                        $phone = \App\Models\PhoneNumber::where('provider_number_id', $oldRequestId)->first();
                        if ($phone) {
                            $phone->provider_number_id = $newRequestId;
                            $phone->last_synced_at = now();
                            $phone->save();

                            // Update corresponding reservation expiry if provided
                            if ($newExpiresAt) {
                                try {
                                    $expDate = \Illuminate\Support\Carbon::parse($newExpiresAt);
                                    \App\Models\NumberReservation::where('phone_number_id', $phone->id)
                                        ->where('status', 'reserved')
                                        ->update(['expires_at' => $expDate]);

                                    \App\Models\VerificationSession::where('phone_number_id', $phone->id)
                                        ->whereIn('status', ['active', 'pending'])
                                        ->update(['expires_at' => $expDate]);
                                } catch (\Throwable $e) {}
                            }

                            Log::info("PVADeals number_renewed updated stored phone #{$phone->id} provider ID from {$oldRequestId} to {$newRequestId}");
                        }
                    }
                }

                // number_purchased resolves asynchronous PENDING delivery
                if ($event === 'number_purchased') {
                    $reqId = (string) $request->input('requestId');
                    $allocatedNum = (string) $request->input('number');

                    if (!empty($reqId) && !empty($allocatedNum)) {
                        $phone = \App\Models\PhoneNumber::where('provider_number_id', $reqId)->first();
                        if ($phone) {
                            if (empty($phone->phone_number) || str_contains($phone->phone_number, 'PENDING')) {
                                $phone->phone_number = $allocatedNum;
                                $phone->status = 'reserved';
                                $phone->last_synced_at = now();
                                $phone->save();

                                \App\Models\VerificationSession::where('phone_number_id', $phone->id)
                                    ->where('status', 'pending')
                                    ->update(['status' => 'active']);

                                Log::info("PVADeals number_purchased async delivery fulfilled for phone #{$phone->id}: {$allocatedNum}");
                            }
                        }
                    }
                }

                // sms_blocked tells us an inbound SMS was rejected because it didn't match the rented service
                if ($event === 'sms_blocked') {
                    $logContext['blockedService'] = $request->input('blockedService');
                    $logContext['matchType'] = $request->input('matchType');
                    $logContext['matchedValue'] = $request->input('matchedValue');
                }

                Log::info("PVADeals lifecycle webhook event acknowledged: [{$event}]", $logContext);

                return response()->json([
                    'success' => true,
                    'event' => $event,
                    'message' => 'Event acknowledged successfully.',
                ], 200);
            }
        }

        $smsDto = match ($lowerProvider) {
            'pvadeals', 'pva-deals' => $this->parsePvaDealsWebhook($request),
            'twilio' => $this->parseTwilioWebhook($request),
            '5sim', 'fivesim' => $this->parseFiveSimWebhook($request),
            'sms-activate', 'smsactivate' => $this->parseSmsActivateWebhook($request),
            default => $this->parseGenericWebhook($request, $provider),
        };

        if (!$smsDto || empty($smsDto->phoneNumber) || empty($smsDto->message)) {
            return response()->json([
                'success' => false,
                'error' => 'INVALID_PAYLOAD',
                'message' => 'Unable to parse incoming SMS payload.',
            ], 400);
        }

        // Dispatch async job for non-blocking SMS processing
        ProcessCarrierSmsJob::dispatch($smsDto)->onQueue('sms');

        // Twilio expects TwiML XML response
        if (strtolower($provider) === 'twilio') {
            return response('<Response></Response>', 200, ['Content-Type' => 'text/xml']);
        }

        return response()->json([
            'success' => true,
            'status' => 'queued',
            'message' => 'SMS accepted for asynchronous processing.',
        ]);
    }

    /**
     * Parse PVADeals v3 webhook payload.
     * Webhook payload: {"event": "sms_received", "timestamp": "...", "requestId": "...", "serviceId": "...", "number": "+13130001234", "message": "Your code is 1234"}
     */
    protected function parsePvaDealsWebhook(Request $request): ?IncomingSmsDTO
    {
        $phone = $request->input('number') ?? $request->input('phone');
        $message = $request->input('message') ?? $request->input('text') ?? '';
        $requestId = $request->input('requestId') ?? $request->input('id');
        $timestamp = $request->input('timestamp');
        $receivedAt = $timestamp ? \Carbon\Carbon::parse($timestamp) : now();

        if (!$phone || !$message) {
            return null;
        }

        return new IncomingSmsDTO(
            messageId: 'PVA_MSG_' . ($requestId ?? uniqid()),
            sender: 'PVADeals',
            recipient: $phone,
            messageBody: $message,
            receivedAt: $receivedAt,
            providerCode: 'pvadeals',
            providerNumberId: $requestId
        );
    }

    /**
     * Parse Twilio standard form-encoded webhook.
     */
    protected function parseTwilioWebhook(Request $request): ?IncomingSmsDTO
    {
        $to = $request->input('To');
        $body = $request->input('Body');
        $from = $request->input('From', 'UNKNOWN');
        $sid = $request->input('MessageSid', 'TW_' . uniqid());

        if (!$to || !$body) {
            return null;
        }

        return new IncomingSmsDTO(
            messageId: $sid,
            sender: $from,
            recipient: $to,
            messageBody: $body,
            providerCode: 'twilio'
        );
    }

    /**
     * Parse 5sim JSON push notification webhook.
     */
    protected function parseFiveSimWebhook(Request $request): ?IncomingSmsDTO
    {
        $phone = $request->input('phone');
        $smsData = $request->input('sms', []);
        $text = $smsData['text'] ?? $request->input('text') ?? '';
        $sender = $smsData['sender'] ?? '5sim';
        $id = (string) ($smsData['id'] ?? $request->input('id') ?? uniqid());

        if (!$phone || !$text) {
            return null;
        }

        return new IncomingSmsDTO(
            messageId: '5SIM_' . $id,
            sender: $sender,
            recipient: $phone,
            messageBody: $text,
            providerCode: '5sim'
        );
    }

    /**
     * Parse SMS-Activate push notification webhook.
     */
    protected function parseSmsActivateWebhook(Request $request): ?IncomingSmsDTO
    {
        $phone = $request->input('phone') ?? $request->input('number');
        $text = $request->input('text') ?? $request->input('sms') ?? '';
        $sender = $request->input('sender') ?? 'SmsActivate';
        $id = (string) ($request->input('id') ?? $request->input('activation_id') ?? uniqid());

        if (!$phone || !$text) {
            return null;
        }

        return new IncomingSmsDTO(
            messageId: 'ACTIVATE_' . $id,
            sender: $sender,
            recipient: $phone,
            messageBody: $text,
            providerCode: 'sms-activate'
        );
    }

    /**
     * Parse generic JSON/Form webhook.
     */
    protected function parseGenericWebhook(Request $request, string $provider): ?IncomingSmsDTO
    {
        $phone = $request->input('phone') ?? $request->input('to') ?? $request->input('phone_number') ?? $request->input('recipient');
        $body = $request->input('message') ?? $request->input('body') ?? $request->input('text') ?? $request->input('content');
        $sender = $request->input('from') ?? $request->input('sender') ?? 'CARRIER';
        $id = (string) ($request->input('message_id') ?? $request->input('id') ?? uniqid());

        if (!$phone || !$body) {
            return null;
        }

        return new IncomingSmsDTO(
            messageId: $id,
            sender: $sender,
            recipient: $phone,
            messageBody: $body,
            providerCode: $provider
        );
    }

    /**
     * Inbound payment gateway webhook receiver (Stripe, Flutterwave, Mock).
     * Returns instant HTTP 200 and processes asynchronously via queue.
     */
    public function handlePaymentWebhook(Request $request, string $gateway): JsonResponse
    {
        Log::info("Payment webhook received [{$gateway}] — dispatching async job", [
            'payload_keys' => array_keys($request->all()),
        ]);

        // Dispatch async job for non-blocking payment fulfillment
        ProcessPaymentWebhookJob::dispatch(
            gateway: $gateway,
            payload: $request->all(),
            headers: $request->headers->all()
        )->onQueue('payments');

        // Always return 200 instantly so the gateway doesn't retry
        return response()->json([
            'success' => true,
            'message' => 'Webhook accepted for asynchronous processing.',
        ], 200);
    }

    /**
     * Dedicated PVADeals webhook receiver with secret authentication, event deduplication,
     * SMS storage, and 503 retry semantics for race conditions.
     * Never moves money from a webhook payload.
     */
    public function handlePvaDealsWebhook(Request $request, string $secret): JsonResponse
    {
        // 1. Authenticate secret using timing-safe comparison
        $configuredSecret = (string) config('numbers.webhook.secret', env('PVADEALS_WEBHOOK_SECRET', 'pvadeals_wh_sec_99348128340192830192849182301923'));
        if (!hash_equals($configuredSecret, $secret)) {
            Log::warning("PVADeals webhook rejected: Invalid secret token");
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payload = $request->all();
        $event = (string) ($payload['event'] ?? $payload['type'] ?? 'unknown');
        $eventKey = hash('sha256', json_encode($payload));

        // 2. Deduplicate webhook event by content hash
        $existingEvent = \App\Models\WebhookEvent::where('event_key', $eventKey)->first();
        if ($existingEvent) {
            return response()->json(['message' => 'Event already processed.'], 200);
        }

        $requestId = (string) ($payload['requestId'] ?? $payload['_id'] ?? '');
        $number = (string) ($payload['number'] ?? $payload['phoneNumber'] ?? '');

        // 3. Process Specific Events
        if ($event === 'sms_received') {
            $body = (string) ($payload['message'] ?? $payload['body'] ?? $payload['text'] ?? '');
            $from = (string) ($payload['from'] ?? $payload['sender'] ?? 'UNKNOWN');

            // Find matching number order by provider_request_id or phone number
            $order = null;
            if (!empty($requestId)) {
                $order = \App\Models\NumberOrder::where('provider_request_id', $requestId)->first();
            }
            if (!$order && !empty($number)) {
                $normNumber = '+' . ltrim($number, '+');
                $order = \App\Models\NumberOrder::where('phone_number', $normNumber)->latest()->first();
            }

            // Race Condition Guard: If the order is not found yet, return 503 so PVADeals retries (5x, 5s apart)
            if (!$order) {
                Log::warning("PVADeals webhook sms_received: Order not found yet for request {$requestId} / number {$number}. Returning 503 for retry.");
                return response()->json(['message' => 'Order not found yet; awaiting commitment.'], 503);
            }

            // Deduplicate SMS body by hash
            $dedupeHash = hash('sha256', $requestId . '|' . $body . '|' . ($payload['receivedAt'] ?? time()));
            $existingSms = \App\Models\SmsMessage::where('dedupe_hash', $dedupeHash)->first();

            if (!$existingSms) {
                \App\Models\SmsMessage::create([
                    'order_id' => $order->id,
                    'message' => $body,
                    'body' => $body,
                    'from_number' => $from,
                    'provider_message_id' => (string) ($payload['messageId'] ?? uniqid()),
                    'dedupe_hash' => $dedupeHash,
                    'received_at' => now(),
                ]);
            }
        } elseif ($event === 'number_purchased') {
            $order = null;
            if (!empty($requestId)) {
                $order = \App\Models\NumberOrder::where('provider_request_id', $requestId)->first();
            }

            // Race Condition Guard: Return 503 so provider retries if order row is still committing
            if (!$order) {
                Log::warning("PVADeals webhook number_purchased: Order not found yet for request {$requestId}. Returning 503 for retry.");
                return response()->json(['message' => 'Order not found yet; awaiting commitment.'], 503);
            }

            // Update order status if in pending/placing state
            if (in_array($order->status, ['provider_pending', 'placing', 'created'], true)) {
                app(\App\Application\Services\OrderService::class)->applyProvider($order, $payload);
            }
        } elseif ($event === 'number_renewed') {
            // Update order request ID (auto-renew billing is out of scope)
            $oldRequestId = (string) ($payload['oldRequestId'] ?? '');
            $newRequestId = (string) ($payload['requestId'] ?? '');

            if (!empty($oldRequestId) && !empty($newRequestId)) {
                \App\Models\NumberOrder::where('provider_request_id', $oldRequestId)
                    ->update(['provider_request_id' => $newRequestId]);
            }
        }

        // 4. Record event for immutable audit trail and deduplication
        \App\Models\WebhookEvent::create([
            'source' => 'pvadeals',
            'event_key' => $eventKey,
            'event_type' => $event,
            'payload' => $payload,
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'event' => $event,
            'message' => 'Webhook received and processed successfully.',
        ], 200);
    }
}
