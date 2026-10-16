<?php

namespace App\Domain\DTOs;

use DateTimeInterface;

readonly class IncomingSmsDTO
{
    public string $messageId;
    public string $sender;
    public string $recipient;
    public string $messageBody;
    public ?string $otpCode;
    public ?DateTimeInterface $receivedAt;
    public ?string $providerCode;
    public ?string $providerNumberId;

    // Direct compatibility properties
    public string $phoneNumber;
    public string $message;
    public ?string $fromNumber;
    public ?string $providerMessageId;

    public function __construct(
        ?string $messageId = null,
        ?string $sender = null,
        ?string $recipient = null,
        ?string $messageBody = null,
        ?string $otpCode = null,
        ?DateTimeInterface $receivedAt = null,
        ?string $phoneNumber = null,
        ?string $message = null,
        ?string $fromNumber = null,
        ?string $providerMessageId = null,
        ?string $providerCode = null,
        ?string $providerNumberId = null
    ) {
        $resolvedMsgId = $messageId ?? $providerMessageId ?? ('MSG_' . uniqid('', true));
        $resolvedSender = $sender ?? $fromNumber ?? 'UNKNOWN';
        $resolvedRecipient = $recipient ?? $phoneNumber ?? '';
        $resolvedBody = $messageBody ?? $message ?? '';

        $this->messageId = $resolvedMsgId;
        $this->providerMessageId = $resolvedMsgId;
        $this->sender = $resolvedSender;
        $this->fromNumber = $resolvedSender;
        $this->recipient = $resolvedRecipient;
        $this->phoneNumber = $resolvedRecipient;
        $this->messageBody = $resolvedBody;
        $this->message = $resolvedBody;
        $this->otpCode = $otpCode;
        $this->receivedAt = $receivedAt;
        $this->providerCode = $providerCode;
        $this->providerNumberId = $providerNumberId;
    }
}
