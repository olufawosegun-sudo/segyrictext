<?php

namespace App\Services\PvaDeals;

use App\Application\Services\PricingEngineService;
use App\Models\Country;
use App\Models\CountryService;
use App\Models\PvaDealsSyncLog;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PvaDealsSyncService
{
    public function __construct(
        protected PvaDealsClient $client,
        protected ?PricingEngineService $pricingEngine = null
    ) {
        $this->pricingEngine = $pricingEngine ?? app(PricingEngineService::class);
    }

    /**
     * Run full catalog synchronization.
     * Can accept an explicit catalog payload (for tests/custom sources) or fetches from PVADeals API.
     *
     * @param array|null $overrideCatalog
     * @return array Sync summary
     */
    public function sync(?array $overrideCatalog = null): array
    {
        $log = PvaDealsSyncLog::create([
            'status' => 'running',
            'synced_at' => now(),
        ]);

        try {
            // 1. Fetch provider data
            $catalog = $overrideCatalog ?? $this->client->getAllServices(forceRefresh: true);

            if (empty($catalog)) {
                $log->update([
                    'status' => 'failed',
                    'error_message' => 'Empty or unparseable catalog received from provider.',
                ]);
                return [
                    'success' => false,
                    'message' => 'No services received from provider.',
                ];
            }

            // 2. Fetch current FX rate (USD to NGN) and margin
            $rateData = $this->pricingEngine->getExchangeRate('USD', 'NGN');
            $fxRate = (float) ($rateData['rate'] ?? 1650.0);
            if ($fxRate <= 0) $fxRate = 1650.0;
            $marginPct = (float) config('numbers.pricing.default_margin_percentage', 15.0);

            $syncedCountryIds = [];
            $syncedCountryServiceIds = [];
            $createdCount = 0;
            $updatedCount = 0;
            $syncTimestamp = Carbon::now();

            DB::transaction(function () use (
                $catalog,
                $fxRate,
                $marginPct,
                $syncTimestamp,
                &$syncedCountryIds,
                &$syncedCountryServiceIds,
                &$createdCount,
                &$updatedCount
            ) {
                // Pre-fetch all countries and services for quick lookups with aliases
                $existingCountries = collect();
                foreach (Country::all() as $c) {
                    if ($c->iso2) $existingCountries->put(strtoupper($c->iso2), $c);
                    if ($c->iso3) $existingCountries->put(strtoupper($c->iso3), $c);
                    if ($c->name) $existingCountries->put(strtoupper($c->name), $c);
                    if (strtoupper($c->iso2) === 'GB' || strtoupper($c->iso2) === 'UK') {
                        $existingCountries->put('UK', $c);
                        $existingCountries->put('GB', $c);
                        $existingCountries->put('GBR', $c);
                        $existingCountries->put('UNITED KINGDOM', $c);
                    }
                    if (strtoupper($c->iso2) === 'US') {
                        $existingCountries->put('US', $c);
                        $existingCountries->put('USA', $c);
                        $existingCountries->put('UNITED STATES', $c);
                    }
                }

                // 1. Ensure all 5 supported PVADeals countries exist and are active
                $allConfig = config('numbers.all_services_countries', []);
                $pvaCountryCodes = ['US', 'UK', 'HK', 'ID', 'AU'];
                foreach ($pvaCountryCodes as $pvaCode) {
                    $cConfig = $allConfig[$pvaCode] ?? [];
                    $cName = $cConfig['name'] ?? $this->resolveCountryName($pvaCode, $pvaCode);
                    $dialCode = $cConfig['dial_code'] ?? $this->resolveDialCode($pvaCode);
                    $iso2 = ($pvaCode === 'UK') ? 'GB' : $pvaCode;
                    $iso3 = ($pvaCode === 'UK') ? 'GBR' : ($pvaCode === 'US' ? 'USA' : ($cConfig['aliases'][0] ?? $pvaCode));

                    $country = $existingCountries->get($pvaCode) ?? $existingCountries->get($iso2);
                    if (!$country) {
                        $country = Country::create([
                            'name' => $cName,
                            'iso2' => $iso2,
                            'iso3' => $iso3,
                            'dial_code' => $dialCode,
                            'currency_code' => 'USD',
                            'is_active' => true,
                            'is_best_value' => true,
                            'last_synced_at' => $syncTimestamp,
                        ]);
                        $createdCount++;
                    } else {
                        $country->update([
                            'name' => $cName,
                            'dial_code' => $dialCode,
                            'is_active' => true,
                            'last_synced_at' => $syncTimestamp,
                        ]);
                    }
                    $existingCountries->put($pvaCode, $country);
                    $existingCountries->put($iso2, $country);
                    $syncedCountryIds[] = $country->id;
                }

                // Pre-fetch all services and country-services for O(1) in-memory lookups
                $existingServices = Service::all()->keyBy('code');
                $existingCountryServices = CountryService::all()->keyBy(fn($cs) => $cs->country_id . '_' . $cs->service_id);

                // 2. Ensure "All services" service exists
                $allServicesModel = $existingServices->get('all-services') ?? $existingServices->get('all_services');
                if (!$allServicesModel) {
                    $allServicesModel = Service::create([
                        'name' => 'All services',
                        'code' => 'all-services',
                        'category' => 'All Services',
                        'is_active' => true,
                        'is_best_value' => true,
                        'last_synced_at' => $syncTimestamp,
                    ]);
                    $existingServices->put('all-services', $allServicesModel);
                    $createdCount++;
                }

                // 3. Link "All services" to all 5 supported PVADeals countries
                foreach ($pvaCountryCodes as $pvaCode) {
                    $country = $existingCountries->get($pvaCode);
                    if (!$country) continue;

                    $cConfig = $allConfig[$pvaCode] ?? [];
                    $plans = $cConfig['plans'] ?? [];
                    $defaultDur = $cConfig['default_duration'] ?? 30;
                    $defaultPlan = $plans[$defaultDur] ?? (reset($plans) ?: []);
                    $baseUsd = (float) ($defaultPlan['price_usd'] ?? 12.99);

                    $retailNgn = ceil(($baseUsd * $fxRate * (1 + ($marginPct / 100))) / 10) * 10;
                    $kobo = (int) round($retailNgn * 100);

                    $csKey = $country->id . '_' . $allServicesModel->id;
                    $cs = $existingCountryServices->get($csKey);

                    $csPayload = [
                        'country_id' => $country->id,
                        'service_id' => $allServicesModel->id,
                        'pva_service_id' => 'ALL_SERVICES',
                        'supplier_str_price' => 0.0,
                        'supplier_ltr3_price' => 0.0,
                        'supplier_ltr7_price' => 0.0,
                        'supplier_ltr14_price' => 0.0,
                        'supplier_ltr30_price' => (float) ($plans[30]['price_usd'] ?? ($pvaCode === 'US' ? ($plans[28]['price_usd'] ?? 12.99) : 0)),
                        'supplier_ltr90_price' => (float) ($plans[90]['price_usd'] ?? 0),
                        'supplier_ltr180_price' => (float) ($plans[180]['price_usd'] ?? 0),
                        'customer_str_price_kobo' => $kobo,
                        'str_stock' => 0,
                        'ltr_stock' => 9999,
                        'is_active' => true,
                        'is_best_value' => true,
                        'last_synced_at' => $syncTimestamp,
                        'metadata' => [
                            'carrier' => $cConfig['carrier'] ?? null,
                            'plans' => $plans,
                            'allowed_durations' => $cConfig['allowed_durations'] ?? [$defaultDur],
                        ],
                    ];

                    if (!$cs) {
                        $cs = CountryService::create($csPayload);
                        $createdCount++;
                    } else {
                        $cs->update($csPayload);
                        $updatedCount++;
                    }

                    $existingCountryServices->put($csKey, $cs);
                    $syncedCountryServiceIds[] = $cs->id;
                }

                // 4. Sync per-service catalog from PVADeals /services/all
                foreach ($catalog as $item) {
                    $item = is_array($item) ? $item : (array) $item;
                    $normalized = $this->client->normalizeServiceItem($item);

                    $rawCountry = trim((string) ($item['country'] ?? 'US'));
                    $countryCode = $this->client->normalizeCountryCode($rawCountry);
                    $serviceName = trim((string) ($item['name'] ?? 'Unknown Service'));
                    $serviceCode = Str::slug($serviceName);
                    if (empty($serviceCode)) {
                        $serviceCode = 'srv_' . substr(md5($serviceName), 0, 10);
                    }

                    // Best Value detection (service or country level)
                    $isItemBestValue = !empty($normalized['is_best_value'])
                        || !empty($item['best_value'])
                        || !empty($item['is_best_value'])
                        || !empty($item['bestValue'])
                        || !empty($item['isBestValue'])
                        || (isset($item['category']) && strtolower($item['category']) === 'best value');

                    $isCountryBestValue = !empty($item['country_best_value'])
                        || !empty($item['countryBestValue'])
                        || (isset($item['country_status']) && strtolower($item['country_status']) === 'best_value')
                        || $isItemBestValue;

                    // A. Ensure Country exists
                    $country = $existingCountries->get(strtoupper($countryCode))
                        ?? $existingCountries->get(strtoupper($rawCountry));

                    if (!$country) {
                        $countryName = $this->resolveCountryName($countryCode, $rawCountry);
                        $country = Country::create([
                            'name' => $countryName,
                            'iso2' => strlen($countryCode) === 2 ? $countryCode : substr($countryCode, 0, 2),
                            'iso3' => strlen($countryCode) === 3 ? $countryCode : strtoupper(substr($countryName, 0, 3)),
                            'dial_code' => $this->resolveDialCode($countryCode),
                            'currency_code' => 'USD',
                            'is_active' => true,
                            'is_best_value' => $isCountryBestValue,
                            'last_synced_at' => $syncTimestamp,
                        ]);
                        $existingCountries->put(strtoupper($countryCode), $country);
                        $createdCount++;
                    }

                    $syncedCountryIds[] = $country->id;

                    // B. Ensure Service exists
                    $service = $existingServices->get($serviceCode);
                    if (!$service) {
                        $service = Service::create([
                            'name' => $serviceName,
                            'code' => $serviceCode,
                            'category' => $item['category'] ?? null,
                            'is_active' => true,
                            'is_best_value' => $isItemBestValue,
                            'last_synced_at' => $syncTimestamp,
                        ]);
                        $existingServices->put($serviceCode, $service);
                        $createdCount++;
                    } else {
                        $service->update([
                            'is_active' => true,
                            'is_best_value' => $isItemBestValue,
                            'last_synced_at' => $syncTimestamp,
                        ]);
                    }

                    // C. Calculate retail price in kobo (USD x FX x (1 + margin), rounded up to N10)
                    $usdStrPrice = $normalized['str_price_usd'];
                    $customerStrKobo = 0;
                    if ($usdStrPrice > 0) {
                        $retailNgn = $usdStrPrice * $fxRate * (1 + ($marginPct / 100));
                        $roundedNgn = ceil($retailNgn / 10) * 10;
                        $customerStrKobo = (int) round($roundedNgn * 100);
                    }

                    // D. Upsert CountryService link via fast in-memory key
                    $csKey = $country->id . '_' . $service->id;
                    $countryService = $existingCountryServices->get($csKey);

                    $pvaId = $normalized['id'];
                    $strStock = $normalized['str_stock'];
                    $ltrStock = $normalized['ltr_stock'];
                    $isAvailable = $strStock > 0 || $ltrStock > 0;

                    $payload = [
                        'country_id' => $country->id,
                        'service_id' => $service->id,
                        'pva_service_id' => $pvaId,
                        'supplier_str_price' => $usdStrPrice,
                        'supplier_ltr3_price' => $normalized['ltr_prices_usd'][3],
                        'supplier_ltr7_price' => $normalized['ltr_prices_usd'][7],
                        'supplier_ltr14_price' => $normalized['ltr_prices_usd'][14],
                        'supplier_ltr30_price' => $normalized['ltr_prices_usd'][30],
                        'supplier_ltr90_price' => $normalized['ltr_prices_usd'][90],
                        'supplier_ltr180_price' => $normalized['ltr_prices_usd'][180],
                        'customer_str_price_kobo' => $customerStrKobo,
                        'str_stock' => $strStock,
                        'ltr_stock' => $ltrStock,
                        'is_active' => $isAvailable,
                        'is_best_value' => $isItemBestValue,
                        'last_synced_at' => $syncTimestamp,
                        'metadata' => [
                            'raw_country' => $rawCountry,
                            'raw_id' => $pvaId,
                            'category' => $item['category'] ?? null,
                        ],
                    ];

                    if (!$countryService) {
                        $countryService = CountryService::create($payload);
                        $createdCount++;
                    } else {
                        $countryService->update($payload);
                        $updatedCount++;
                    }

                    $existingCountryServices->put($csKey, $countryService);
                    $syncedCountryServiceIds[] = $countryService->id;
                }

                // Deactivate offerings NOT in the current sync batch (preserve historical records)
                if (!empty($syncedCountryServiceIds)) {
                    CountryService::whereNotIn('id', $syncedCountryServiceIds)
                        ->update([
                            'is_active' => false,
                            'str_stock' => 0,
                            'ltr_stock' => 0,
                            'is_best_value' => false,
                        ]);
                }

                // Deactivate countries NOT offered by PVADeals
                if (!empty($syncedCountryIds)) {
                    Country::whereNotIn('id', $syncedCountryIds)
                        ->update([
                            'is_active' => false,
                            'is_best_value' => false,
                        ]);
                }
            });

            // Invalidate caches
            $this->invalidateCaches();

            $log->update([
                'status' => 'success',
                'services_count' => count($syncedCountryServiceIds),
                'countries_count' => count(array_unique($syncedCountryIds)),
                'created_count' => $createdCount,
                'updated_count' => $updatedCount,
            ]);

            return [
                'success' => true,
                'services_count' => count($syncedCountryServiceIds),
                'countries_count' => count(array_unique($syncedCountryIds)),
                'created_count' => $createdCount,
                'updated_count' => $updatedCount,
                'synced_at' => $syncTimestamp->toIso8601String(),
            ];

        } catch (\Throwable $e) {
            Log::error("PVADeals catalog sync failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Invalidate catalog caches so frontend immediately sees updated data.
     */
    public function invalidateCaches(): void
    {
        Cache::forget('pvadeals_all_services_catalog_v2');
        Cache::forget('mobile_available_numbers_count');
        Cache::forget('catalog_all_services_json');
        Cache::forget('pvadeals_websites_us_ngn');
        Cache::forget('pvadeals_websites_uk_ngn');
    }

    protected function resolveCountryName(string $code, string $fallback): string
    {
        $map = [
            'US' => 'United States',
            'USA' => 'United States',
            'UK' => 'United Kingdom',
            'GB' => 'United Kingdom',
            'HK' => 'Hong Kong',
            'ID' => 'Indonesia',
            'AU' => 'Australia',
            'CA' => 'Canada',
        ];
        return $map[strtoupper($code)] ?? $fallback;
    }

    protected function resolveDialCode(string $code): string
    {
        $map = [
            'US' => '+1',
            'USA' => '+1',
            'UK' => '+44',
            'GB' => '+44',
            'HK' => '+852',
            'ID' => '+62',
            'AU' => '+61',
            'CA' => '+1',
        ];
        return $map[strtoupper($code)] ?? '+1';
    }
}
