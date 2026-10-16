<?php

namespace App\Domain\Verification\Services;

class OtpExtractor implements OtpExtractorInterface
{
    /**
     * Regex patterns for finding OTP codes in SMS text, ordered by specificity.
     */
    protected array $patterns = [
        // 1. Service-prefixed codes: G-123456, FB-12345, VK-123456
        '/(?:[A-Z]{1,4}-)(\d{4,8})/i',

        // 2. Explicit keyword labels (English, Spanish, French, German, Russian)
        '/(?:verification\s*code|security\s*code|login\s*code|auth\s*code|access\s*code|one-time\s*code|passcode|código|code|otp)\s*(?:is|:|\s|=|-)\s*([0-9]{4,8})\b/i',

        // 3. Phrased code indicators: "use 123456 to verify", "enter 123456 to continue"
        '/(?:use|enter|type|input)\s+([0-9]{4,8})\s+(?:to|for|as)\b/i',

        // 4. "is 123456." or "is 123456"
        '/\bis\s+([0-9]{4,8})\b/i',

        // 5. Hyphenated numbers: 123-456 -> extract digits
        '/\b([0-9]{3})-([0-9]{3})\b/',

        // 6. Generic 4 to 8 standalone digits surrounded by word boundaries (excluding common years like 19xx, 20xx if not sole number)
        '/\b([0-9]{4,8})\b/',
    ];

    public function extractCode(string $message): ?string
    {
        $message = trim($message);
        if (empty($message)) {
            return null;
        }

        // Test patterned rules first
        foreach ($this->patterns as $index => $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                // For hyphenated numbers pattern (index 4)
                if ($index === 4 && isset($matches[1], $matches[2])) {
                    return $matches[1] . $matches[2];
                }

                $candidate = $matches[1] ?? null;
                if ($candidate && $this->isValidCodeCandidate($candidate, $message, $index)) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    /**
     * Validate that candidate code is plausible (e.g. not a year or currency if matched loosely).
     */
    protected function isValidCodeCandidate(string $candidate, string $message, int $patternIndex): bool
    {
        $len = strlen($candidate);
        if ($len < 4 || $len > 8) {
            return false;
        }

        // If matched by generic standalone digits (index 5)
        if ($patternIndex === 5) {
            // Avoid interpreting years (2020-2035) as OTP unless string is very short
            if ($len === 4 && (int)$candidate >= 1990 && (int)$candidate <= 2035) {
                // If the message has other words, check if "year" is nearby
                if (stripos($message, 'year') !== false || stripos($message, 'date') !== false) {
                    return false;
                }
            }
        }

        return true;
    }
}
