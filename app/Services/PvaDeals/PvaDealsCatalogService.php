<?php

namespace App\Services\PvaDeals;

use App\Application\Services\PricingEngineService;
use App\Models\Country;
use App\Models\CountryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PvaDealsCatalogService
{
    public function __construct(
        protected PvaDealsClient $client,
        protected ?PricingEngineService $pricingEngine = null
    ) {
        $this->pricingEngine = $pricingEngine ?? app(PricingEngineService::class);
    }

    /**
     * Get active countries with Best Value tags.
     */
    public function getCountries(): Collection
    {
        return Cache::remember('catalog_active_countries_v2', 300, function () {
            return Country::where('is_active', true)
                ->orderByDesc('is_best_value')
                ->orderBy('name')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'code' => $c->iso2 ?? $c->iso3,
                    'dial_code' => $c->dial_code,
                    'is_best_value' => (bool) $c->is_best_value,
                ]);
        });
    }

    /**
     * Retrieve server-driven website list for a given country with Naira pricing and Best Value.
     */
    public function getWebsites(string $country = 'USA', ?string $search = null, string $targetCurrency = 'NGN'): array
    {
        $normCountry = $this->client->normalizeCountryCode($country);
        $searchKey = strtolower(trim((string) $search));
        $cacheKey = "catalog_websites_{$normCountry}_{$targetCurrency}_" . md5($searchKey);

        return Cache::store('file')->remember($cacheKey, 180, function () use ($normCountry, $searchKey, $targetCurrency, $country) {
            $exchangeRate = (float) $this->pricingEngine->convertCurrency(1.0, 'USD', $targetCurrency);
            if ($exchangeRate <= 0) $exchangeRate = 1650.0;
            $currencySymbol = $targetCurrency === 'NGN' ? '₦' : ($targetCurrency === 'USD' ? '$' : $targetCurrency);
            $marginPct = (float) config('numbers.pricing.default_margin_percentage', 15.0);

            // First check if we have database-synced offerings for this country
            $countryRecord = Country::where(function ($q) use ($normCountry, $country) {
                if ($normCountry === 'UK') {
                    $q->whereIn('iso2', ['GB', 'UK'])
                      ->orWhereIn('iso3', ['GBR', 'UK'])
                      ->orWhereIn('name', ['United Kingdom', 'UK', 'Great Britain']);
                } elseif ($normCountry === 'US') {
                    $q->whereIn('iso2', ['US', 'USA'])
                      ->orWhereIn('iso3', ['USA', 'US'])
                      ->orWhereIn('name', ['United States', 'USA', 'US']);
                } elseif ($normCountry === 'HK') {
                    $q->whereIn('iso2', ['HK'])
                      ->orWhereIn('iso3', ['HKG'])
                      ->orWhereIn('name', ['Hong Kong', 'HK']);
                } elseif ($normCountry === 'ID') {
                    $q->whereIn('iso2', ['ID'])
                      ->orWhereIn('iso3', ['IDN'])
                      ->orWhereIn('name', ['Indonesia', 'ID']);
                } elseif ($normCountry === 'AU') {
                    $q->whereIn('iso2', ['AU'])
                      ->orWhereIn('iso3', ['AUS'])
                      ->orWhereIn('name', ['Australia', 'AU']);
                } else {
                    $q->where('iso2', $normCountry)
                      ->orWhere('iso3', $normCountry)
                      ->orWhere('name', $country);
                }
            })->first();

            $dbItems = collect();
            if ($countryRecord) {
                $dbItems = CountryService::with('service')
                    ->where('country_id', $countryRecord->id)
                    ->where('is_active', true)
                    ->when($searchKey, function ($q) use ($searchKey) {
                        $q->whereHas('service', fn($sq) => $sq->where('name', 'like', "%{$searchKey}%"));
                    })
                    ->get();
            }

            if ($dbItems->isNotEmpty()) {
                $websites = $dbItems->map(function (CountryService $cs) use ($exchangeRate, $currencySymbol, $marginPct) {
                    $strPriceUsd = (float) $cs->supplier_str_price;
                    $retailNgn = $cs->customer_str_price_kobo 
                        ? round($cs->customer_str_price_kobo / 100)
                        : ceil(($strPriceUsd * $exchangeRate * (1 + ($marginPct / 100))) / 10) * 10;

                    $ltr3Usd = (float) $cs->supplier_ltr3_price;
                    $ltr7Usd = (float) $cs->supplier_ltr7_price;
                    $ltr14Usd = (float) $cs->supplier_ltr14_price;
                    $ltr30Usd = (float) $cs->supplier_ltr30_price;
                    $ltr90Usd = (float) $cs->supplier_ltr90_price;
                    $ltr180Usd = (float) $cs->supplier_ltr180_price;

                    return [
                        'id' => $cs->pva_service_id ?? (string) $cs->service_id,
                        'name' => $cs->service->name,
                        'category' => $cs->service->category ?? '',
                        'price' => (int) $retailNgn,
                        'price_formatted' => $currencySymbol . number_format($retailNgn),
                        'ltr3' => (int) round($ltr3Usd * $exchangeRate),
                        'ltr3_formatted' => $currencySymbol . number_format(round($ltr3Usd * $exchangeRate)),
                        'ltr7' => (int) round($ltr7Usd * $exchangeRate),
                        'ltr7_formatted' => $currencySymbol . number_format(round($ltr7Usd * $exchangeRate)),
                        'ltr14' => (int) round($ltr14Usd * $exchangeRate),
                        'ltr14_formatted' => $currencySymbol . number_format(round($ltr14Usd * $exchangeRate)),
                        'ltr30' => (int) round($ltr30Usd * $exchangeRate),
                        'ltr30_formatted' => $currencySymbol . number_format(round($ltr30Usd * $exchangeRate)),
                        'ltr90' => (int) round($ltr90Usd * $exchangeRate),
                        'ltr90_formatted' => $currencySymbol . number_format(round($ltr90Usd * $exchangeRate)),
                        'ltr180' => (int) round($ltr180Usd * $exchangeRate),
                        'ltr180_formatted' => $currencySymbol . number_format(round($ltr180Usd * $exchangeRate)),
                        'price_usd' => $strPriceUsd,
                        'stock' => (int) $cs->str_stock,
                        'ltr_stock' => (int) $cs->ltr_stock,
                        'is_best_value' => (bool) ($cs->is_best_value || $cs->service->is_best_value),
                    ];
                });
            } else {
                // Fallback directly to provider cached list if DB is empty before first sync
                $allServices = $this->client->getAllServices();
                $websites = collect($allServices)
                    ->filter(function ($s) use ($normCountry) {
                        return $this->client->normalizeCountryCode($s['country'] ?? '') === $normCountry;
                    })
                    ->when($searchKey, fn($c) => $c->filter(fn($s) => str_contains(strtolower($s['name'] ?? ''), $searchKey)))
                    ->map(function ($s) use ($exchangeRate, $currencySymbol, $marginPct) {
                        $strPriceUsd = (float) ($s['str_price_usd'] ?? $s['STRprice'] ?? 0);
                        $retailNgn = ceil(($strPriceUsd * $exchangeRate * (1 + ($marginPct / 100))) / 10) * 10;

                        $ltr3Usd = (float) ($s['ltr_prices_usd'][3] ?? $s['LTR3price'] ?? 0);
                        $ltr7Usd = (float) ($s['ltr_prices_usd'][7] ?? $s['LTR7price'] ?? 0);
                        $ltr14Usd = (float) ($s['ltr_prices_usd'][14] ?? $s['LTR14price'] ?? 0);
                        $ltr30Usd = (float) ($s['ltr_prices_usd'][30] ?? $s['LTR30price'] ?? 0);
                        $ltr90Usd = (float) ($s['ltr_prices_usd'][90] ?? $s['LTR90price'] ?? 0);
                        $ltr180Usd = (float) ($s['ltr_prices_usd'][180] ?? $s['LTR180price'] ?? 0);

                        return [
                            'id' => $s['id'] ?? $s['_id'],
                            'name' => $s['name'],
                            'category' => $s['category'] ?? '',
                            'price' => (int) $retailNgn,
                            'price_formatted' => $currencySymbol . number_format($retailNgn),
                            'ltr3' => (int) round($ltr3Usd * $exchangeRate),
                            'ltr3_formatted' => $currencySymbol . number_format(round($ltr3Usd * $exchangeRate)),
                            'ltr7' => (int) round($ltr7Usd * $exchangeRate),
                            'ltr7_formatted' => $currencySymbol . number_format(round($ltr7Usd * $exchangeRate)),
                            'ltr14' => (int) round($ltr14Usd * $exchangeRate),
                            'ltr14_formatted' => $currencySymbol . number_format(round($ltr14Usd * $exchangeRate)),
                            'ltr30' => (int) round($ltr30Usd * $exchangeRate),
                            'ltr30_formatted' => $currencySymbol . number_format(round($ltr30Usd * $exchangeRate)),
                            'ltr90' => (int) round($ltr90Usd * $exchangeRate),
                            'ltr90_formatted' => $currencySymbol . number_format(round($ltr90Usd * $exchangeRate)),
                            'ltr180' => (int) round($ltr180Usd * $exchangeRate),
                            'ltr180_formatted' => $currencySymbol . number_format(round($ltr180Usd * $exchangeRate)),
                            'price_usd' => $strPriceUsd,
                            'stock' => (int) ($s['str_stock'] ?? $s['STRstock'] ?? 0),
                            'ltr_stock' => (int) ($s['ltr_stock'] ?? $s['LTRstock'] ?? 0),
                            'is_best_value' => (bool) ($s['is_best_value'] ?? false),
                        ];
                    });
            }

            // Sort: Best Value items first, then alphabetical by name
            $sorted = $websites->sortBy([
                ['is_best_value', 'desc'],
                ['name', 'asc'],
            ])->values();

            return [
                'services' => $sorted->all(),
                'total' => $sorted->count(),
                'best_value_count' => $sorted->where('is_best_value', true)->count(),
                'exchange_rate' => $exchangeRate,
                'currency_symbol' => $currencySymbol,
            ];
        });
    }
}
