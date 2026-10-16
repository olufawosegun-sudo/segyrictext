<?php

namespace App\Application\Services;

use App\Models\Country;
use App\Models\CountryService;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\PricingRule;
use App\Models\Service;

class PricingEngineService
{
    /**
     * Default exchange rates against USD fallback
     */
    protected array $defaultRates = [
        'USD' => 1.0,
        'NGN' => 1650.0,
        'KES' => 130.0,
        'GHS' => 15.5,
        'ZAR' => 18.2,
        'UGX' => 3700.0,
        'EGP' => 48.5,
        'EUR' => 0.92,
        'GBP' => 0.78,
        'CAD' => 1.36,
    ];

    /**
     * Calculate base selling price (USD) for a country and service combination.
     */
    public function calculatePrice(Country $country, Service $service): float
    {
        // 1. Check for specific active pricing rule override
        $rule = PricingRule::query()
            ->where('is_active', true)
            ->where(function ($q) use ($country) {
                $q->where('country_id', $country->id)->orWhereNull('country_id');
            })
            ->where(function ($q) use ($service) {
                $q->where('service_id', $service->id)->orWhereNull('service_id');
            })
            ->orderByDesc('priority')
            ->first();

        if ($rule) {
            return (float) $rule->selling_price;
        }

        // 2. Check CountryService configured marketplace price
        $countryService = CountryService::query()
            ->where('country_id', $country->id)
            ->where('service_id', $service->id)
            ->where('status', 'active')
            ->first();

        if ($countryService && $countryService->price) {
            return (float) $countryService->price;
        }

        // 3. Sensible global fallback
        return 0.85;
    }

    /**
     * Calculate price localized in a specific target currency.
     */
    public function calculatePriceInCurrency(Country $country, Service $service, string $targetCurrencyCode = 'USD'): array
    {
        $basePriceUsd = $this->calculatePrice($country, $service);
        $targetCurrencyCode = strtoupper(trim($targetCurrencyCode));
        $convertedPrice = $this->convertCurrency($basePriceUsd, 'USD', $targetCurrencyCode);

        $currency = Currency::where('code', $targetCurrencyCode)->first();
        $symbol = $currency?->symbol ?? '$';
        $decimalPlaces = $currency?->decimal_places ?? 2;

        $formatted = in_array($targetCurrencyCode, ['USD', 'EUR', 'GBP', 'CAD'])
            ? $symbol . number_format($convertedPrice, $decimalPlaces)
            : $symbol . number_format(round($convertedPrice));

        return [
            'price_usd' => $basePriceUsd,
            'price' => $convertedPrice,
            'currency_code' => $targetCurrencyCode,
            'currency_symbol' => $symbol,
            'formatted' => $formatted,
        ];
    }

    /**
     * Convert an amount between currencies using ExchangeRate table with fallback.
     */
    public function convertCurrency(float $amount, string $fromCode = 'USD', string $toCode = 'USD'): float
    {
        $fromCode = strtoupper(trim($fromCode));
        $toCode = strtoupper(trim($toCode));

        if ($fromCode === $toCode) {
            return $amount;
        }

        $rates = $this->defaultRates;

        try {
            $usdCurr = Currency::where('code', 'USD')->first();
            if ($usdCurr) {
                $dbRates = ExchangeRate::with('targetCurrency')
                    ->where('base_currency_id', $usdCurr->id)
                    ->get();

                foreach ($dbRates as $er) {
                    if ($er->targetCurrency) {
                        $rates[$er->targetCurrency->code] = (float) $er->rate;
                    }
                }
            }
        } catch (\Throwable $e) {}

        $rateFrom = $rates[$fromCode] ?? 1.0;
        $rateTo = $rates[$toCode] ?? 1.0;

        $amountInUsd = $amount / $rateFrom;
        $converted = $amountInUsd * $rateTo;

        return in_array($toCode, ['USD', 'EUR', 'GBP', 'CAD'])
            ? round($converted, 2)
            : round($converted);
    }

    /**
     * Get rate and symbol for a target currency against USD.
     */
    public function getExchangeRate(string $fromCode = 'USD', string $toCode = 'USD'): array
    {
        $rate = $this->convertCurrency(1.0, $fromCode, $toCode);
        $curr = Currency::where('code', strtoupper($toCode))->first();
        return [
            'rate' => $rate,
            'symbol' => $curr?->symbol ?? ($toCode === 'NGN' ? '₦' : '$'),
        ];
    }
}
