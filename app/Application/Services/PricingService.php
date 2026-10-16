<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class PricingService
{
    /**
     * Generate an immutable pricing quote for an all-services rental order.
     * Formula: USD price x FX rate x (1 + margin), rounded up to nearest N10,
     * stored as integer kobo.
     *
     * @throws ValidationException if country or duration is not allowed
     */
    public function quote(string $country, ?int $duration = null): array
    {
        $allCountries = config('numbers.all_services_countries', []);
        $normCountry = $this->normalizeCountry($country);

        if (!isset($allCountries[$normCountry])) {
            throw ValidationException::withMessages([
                'country' => ["Country '{$country}' is not supported for all-services rentals. Canada and unlisted regions are not offered."],
            ]);
        }

        $cConfig = $allCountries[$normCountry];
        $allowedDurations = $cConfig['allowed_durations'] ?? [30];
        $selectedDuration = $duration ?? $cConfig['default_duration'] ?? 30;

        // Enforce strict duration validation
        if (!in_array($selectedDuration, $allowedDurations, true)) {
            throw ValidationException::withMessages([
                'duration' => ["Duration {$selectedDuration} days is not valid for {$cConfig['name']}. Offered plans: " . implode(', ', $allowedDurations) . " days."],
            ]);
        }

        $plan = $cConfig['plans'][$selectedDuration] ?? null;
        if (!$plan) {
            throw ValidationException::withMessages([
                'duration' => ["Pricing plan not configured for {$selectedDuration} days in {$cConfig['name']}."],
            ]);
        }

        $usdPrice = (float) $plan['price_usd'];
        $fxRate = (float) config('numbers.pricing.fx_rate_usd_to_ngn', 1650.0);
        $marginPct = (float) config('numbers.pricing.margin_percentage', 15.0);

        // Formula: USD price x FX rate x (1 + margin)
        $rawNgn = $usdPrice * $fxRate * (1 + ($marginPct / 100));

        // Round up to nearest N10 (e.g., 24651 -> 24660)
        $ngnPrice = (int) (ceil($rawNgn / 10.0) * 10);

        // Stored as integer kobo (1 NGN = 100 kobo)
        $priceKobo = (int) ($ngnPrice * 100);

        return [
            'country' => $normCountry,
            'country_name' => $cConfig['name'],
            'flag' => $cConfig['flag'] ?? '',
            'duration_days' => $selectedDuration,
            'label' => $plan['label'] ?? "{$selectedDuration} days",
            'usd_price' => $usdPrice,
            'fx_rate' => $fxRate,
            'margin_pct' => $marginPct,
            'price_ngn' => $ngnPrice,
            'price_kobo' => $priceKobo,
            'carrier' => $cConfig['carrier'] ?? [],
            'is_new' => (bool) ($cConfig['new'] ?? false),
        ];
    }

    /**
     * Get the full catalog of all-services countries, plans, and NGN prices for UI.
     * Cached 5 min; cache key includes config hash so FX/margin edits bust it.
     */
    public function catalog(): array
    {
        $configSnapshot = config('numbers.all_services_countries', []);
        $pricingConfig = config('numbers.pricing', []);
        $cacheKey = 'all_services_catalog_' . md5(json_encode([$configSnapshot, $pricingConfig]));
        $ttl = now()->addMinutes(config('numbers.pricing.cache_duration_minutes', 5));

        return Cache::remember($cacheKey, $ttl, function () use ($configSnapshot) {
            $catalog = [];

            foreach ($configSnapshot as $code => $cConfig) {
                $plans = [];
                foreach ($cConfig['plans'] ?? [] as $dur => $p) {
                    $quote = $this->quote($code, (int) $dur);
                    $plans[] = [
                        'duration' => (int) $dur,
                        'label' => $p['label'] ?? "{$dur} days",
                        'price_usd' => (float) $p['price_usd'],
                        'price_ngn' => $quote['price_ngn'],
                        'price_kobo' => $quote['price_kobo'],
                        'price_formatted' => '₦' . number_format($quote['price_ngn']),
                        'description' => $p['description'] ?? '',
                    ];
                }

                $catalog[] = [
                    'code' => $code,
                    'name' => $cConfig['name'],
                    'flag' => $cConfig['flag'],
                    'dial_code' => $cConfig['dial_code'],
                    'is_new' => (bool) ($cConfig['new'] ?? false),
                    'carrier' => $cConfig['carrier'],
                    'default_duration' => $cConfig['default_duration'],
                    'plans' => $plans,
                ];
            }

            return $catalog;
        });
    }

    /**
     * Normalize country code with support for aliases.
     */
    public function normalizeCountry(string $code): string
    {
        $code = strtoupper(trim($code));

        if (in_array($code, ['GB', 'GBR', 'UK', 'UNITED KINGDOM'], true)) {
            return 'UK';
        }

        if (in_array($code, ['US', 'USA', 'UNITED STATES'], true)) {
            return 'US';
        }

        if (in_array($code, ['HKG', 'HONG KONG', 'HK'], true)) {
            return 'HK';
        }

        if (in_array($code, ['IDN', 'INDONESIA', 'ID'], true)) {
            return 'ID';
        }

        if (in_array($code, ['AUS', 'AUSTRALIA', 'AU'], true)) {
            return 'AU';
        }

        return $code;
    }
}
