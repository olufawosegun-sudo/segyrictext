<?php

namespace App\Domain\Verification\Services;

interface OtpExtractorInterface
{
    /**
     * Extract the one-time password (OTP) or verification code from SMS message text.
     *
     * @param string $message
     * @return string|null The extracted code, or null if no code could be identified.
     */
    public function extractCode(string $message): ?string;
}
