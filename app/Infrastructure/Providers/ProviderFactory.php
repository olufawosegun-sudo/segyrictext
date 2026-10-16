<?php

namespace App\Infrastructure\Providers;

use App\Domain\Interfaces\NumberProviderInterface;
use App\Models\Provider;
use InvalidArgumentException;

class ProviderFactory
{
    /**
     * Resolve provider adapter for a given Provider entity or code.
     */
    public function make(Provider|string $provider): NumberProviderInterface
    {
        $code = $provider instanceof Provider ? $provider->code : $provider;
        $account = $provider instanceof Provider ? $provider->accounts()->where('status', 'active')->first() : null;

        return match ($code) {
            'pvadeals', 'pva-deals' => new PvaDealsAdapter($account),
            'twilio' => new TwilioAdapter($account),
            '5sim' => new FiveSimAdapter($account),
            'sms-activate' => new SmsActivateAdapter($account),
            'mock' => new MockTelecomAdapter(),
            default => new MockTelecomAdapter(),
        };
    }
}
