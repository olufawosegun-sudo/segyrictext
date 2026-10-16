<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\PricingEngineService;
use App\Http\Controllers\Controller;
use App\Infrastructure\Providers\PvaDealsClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PvaServiceCatalogController extends Controller
{
    public function __construct(protected ?PricingEngineService $pricingEngine = null)
    {
        $this->pricingEngine = $pricingEngine ?? app(PricingEngineService::class);
    }

    /**
     * Return the full PVADeals service catalog grouped by country.
     * Supports ?country=USA|UK, ?search=whatsapp, ?currency=NGN
     */
    public function index(Request $request): JsonResponse
    {
        $client = new PvaDealsClient();

        if (!$client->hasApiKey()) {
            return response()->json([
                'success' => false,
                'message' => 'PVADeals API key not configured.',
            ], 503);
        }

        $allServices = $client->getAllServices();

        $country = strtoupper($request->query('country', ''));
        $search = strtolower(trim($request->query('search', '')));

        $currency = strtoupper($request->query('currency', 'NGN'));
        $rate = (float) $this->pricingEngine->convertCurrency(1.0, 'USD', $currency);
        if ($rate <= 0) $rate = 1650.0;
        $symbol = $currency === 'NGN' ? '₦' : ($currency === 'USD' ? '$' : $currency);

        $filtered = collect($allServices);

        // Filter by country
        if ($country) {
            $filtered = $filtered->filter(function ($s) use ($country) {
                $sc = strtoupper($s['country'] ?? '');
                if ($country === 'US' || $country === 'USA') {
                    return in_array($sc, ['US', 'USA', 'UNITED STATES']);
                }
                if ($country === 'UK' || $country === 'GB') {
                    return in_array($sc, ['UK', 'GB', 'GBR', 'UNITED KINGDOM']);
                }
                return $sc === $country;
            });
        }

        // Filter by search term
        if ($search) {
            $filtered = $filtered->filter(fn($s) => str_contains(strtolower($s['name'] ?? ''), $search));
        }

        // Sort alphabetically by name
        $filtered = $filtered->sortBy(fn($s) => strtolower($s['name'] ?? ''))->values();

        // Available countries summary
        $countries = collect($allServices)
            ->pluck('country')
            ->unique()
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $filtered->values()->all(),
                'total' => $filtered->count(),
                'countries' => $countries,
            ],
            'currency' => [
                'code' => $currency,
                'symbol' => $symbol,
                'rate' => $rate,
            ],
        ]);
    }

    /**
     * Return website/service list for a given country with automatic Naira (₦) conversion and Best Value.
     */
    public function websites(Request $request): JsonResponse
    {
        $client = new PvaDealsClient();

        if (!$client->hasApiKey()) {
            return response()->json(['success' => false, 'message' => 'API key not configured.'], 503);
        }

        $country = strtoupper($request->query('country', 'USA'));
        $search = strtolower(trim($request->query('search', '')));
        $targetCurrency = strtoupper($request->query('currency', 'NGN'));

        /** @var \App\Services\PvaDeals\PvaDealsCatalogService $catalogService */
        $catalogService = app(\App\Services\PvaDeals\PvaDealsCatalogService::class);
        $catalogData = $catalogService->getWebsites($country, $search, $targetCurrency);
        $websites = $catalogData['services'];
        $exchangeRate = $catalogData['exchange_rate'];
        $currencySymbol = $catalogData['currency_symbol'];

        // Carrier metadata based on selected country
        $isUk = in_array(strtoupper($country), ['UK', 'GB', 'GBR', 'UNITED KINGDOM']);
        $carrierInfo = $isUk ? [
            'name' => 'EE',
            'code' => 'ee',
            'badge' => 'EE',
            'color' => '#007B85',
        ] : [
            'name' => 'T-Mobile',
            'code' => 'tmobile',
            'badge' => 'T',
            'color' => '#E20074',
        ];

        // Official ALL_SERVICES rates from PVADeals API docs — per country pricing
        $allServicesRates = $this->buildAllServicesRates($country, $exchangeRate, $currencySymbol, $carrierInfo);

        return response()->json([
            'success' => true,
            'data' => $websites,
            'total' => count($websites),
            'best_value_count' => $catalogData['best_value_count'] ?? 0,
            'currency' => [
                'code' => $targetCurrency,
                'symbol' => $currencySymbol,
                'exchange_rate' => $exchangeRate,
            ],
            'all_services' => $allServicesRates,
        ]);
    }

    /**
     * Build ALL_SERVICES pricing block based on the selected country using config/numbers.php.
     */
    protected function buildAllServicesRates(
        string $country,
        float $exchangeRate,
        string $currencySymbol,
        array $carrierInfo
    ): array {
        $fmt = fn(float $usd) => $currencySymbol . number_format(round($usd * $exchangeRate));

        $client = new PvaDealsClient();
        $normCountry = $client->normalizeAllServicesCountry($country);
        $countryConfig = config("numbers.all_services_countries.{$normCountry}")
            ?? config('numbers.all_services_countries.US');

        $carrier = $countryConfig['carrier'] ?? $carrierInfo;

        $base = [
            'id' => 'ALL_SERVICES',
            'name' => 'All services',
            'stock' => 9999,
            'carrier' => $carrier,
            'country' => $countryConfig['code'],
            'country_name' => $countryConfig['name'],
            'flag' => $countryConfig['flag'],
            'dial_code' => $countryConfig['dial_code'],
            'durations' => $countryConfig['allowed_durations'] ?? [],
            'default_duration' => $countryConfig['default_duration'] ?? 30,
            'plans' => [],
        ];

        foreach ($countryConfig['plans'] ?? [] as $duration => $plan) {
            $priceUsd = (float) $plan['price_usd'];
            $priceLocal = (float) round($priceUsd * $exchangeRate);

            $base["ltr{$duration}_usd"] = $priceUsd;
            $base["ltr{$duration}"] = $priceLocal;
            $base["ltr{$duration}_formatted"] = $fmt($priceUsd);

            $base['plans'][$duration] = [
                'duration' => (int) $duration,
                'label' => $plan['label'],
                'price_usd' => $priceUsd,
                'price_local' => $priceLocal,
                'price_formatted' => $fmt($priceUsd),
                'description' => $plan['description'] ?? '',
            ];
        }

        // Backward compatibility alias for ltr30 if default is 28 (e.g. US)
        if (isset($base['ltr28']) && !isset($base['ltr30'])) {
            $base['ltr30'] = $base['ltr28'];
            $base['ltr30_usd'] = $base['ltr28_usd'];
            $base['ltr30_formatted'] = $base['ltr28_formatted'];
        }

        return $base;
    }

    /**
     * Return all available all-services countries and their duration plans.
     */
    public function allServicesCountries(Request $request): JsonResponse
    {
        $countries = config('numbers.all_services_countries', []);
        $targetCurrency = strtoupper($request->query('currency', 'NGN'));
        $exchangeRate = 1650.0;
        $currencySymbol = '₦';

        try {
            $rateData = $this->pricingEngine->getExchangeRate('USD', $targetCurrency);
            $exchangeRate = (float) ($rateData['rate'] ?? 1650.0);
            $currencySymbol = (string) ($rateData['symbol'] ?? '₦');
        } catch (\Throwable $e) {}

        $fmt = fn(float $usd) => $currencySymbol . number_format(round($usd * $exchangeRate));

        $formatted = [];
        foreach ($countries as $code => $c) {
            $plans = [];
            foreach ($c['plans'] ?? [] as $dur => $p) {
                $usd = (float) $p['price_usd'];
                $local = (float) round($usd * $exchangeRate);
                $plans[$dur] = [
                    'duration' => (int) $dur,
                    'label' => $p['label'],
                    'price_usd' => $usd,
                    'price_local' => $local,
                    'price_formatted' => $fmt($usd),
                    'description' => $p['description'] ?? '',
                ];
            }

            $formatted[$code] = [
                'code' => $c['code'],
                'name' => $c['name'],
                'flag' => $c['flag'],
                'dial_code' => $c['dial_code'],
                'carrier' => $c['carrier'],
                'default_duration' => $c['default_duration'],
                'allowed_durations' => $c['allowed_durations'],
                'plans' => $plans,
                'supports_per_service' => $c['supports_per_service'] ?? false,
                'supports_auto_renew' => $c['supports_auto_renew'] ?? true,
                'auto_renew_lockout_days' => $c['auto_renew_lockout_days'] ?? 0,
            ];
        }

        return response()->json([
            'success' => true,
            'currency' => [
                'code' => $targetCurrency,
                'symbol' => $currencySymbol,
                'exchange_rate' => $exchangeRate,
            ],
            'countries' => $formatted,
        ]);
    }

    /**
     * Return structured locations / area codes / regions for a given country.
     */
    public function locations(Request $request): JsonResponse
    {
        $country = strtoupper(trim($request->query('country', 'USA')));

        $locations = match ($country) {
            'UK', 'GB', 'GBR', 'UNITED KINGDOM' => [
                ['name' => 'Any Location (Optimal Route)', 'state' => 'UK National', 'area_code' => '', 'badge' => 'Recommended', 'is_default' => true],
                ['name' => 'Greater London', 'state' => 'London', 'area_code' => '020', 'badge' => 'High Capacity'],
                ['name' => 'Manchester', 'state' => 'Greater Manchester', 'area_code' => '0161', 'badge' => 'Popular'],
                ['name' => 'Birmingham', 'state' => 'West Midlands', 'area_code' => '0121', 'badge' => 'Popular'],
                ['name' => 'Leeds', 'state' => 'West Yorkshire', 'area_code' => '0113', 'badge' => 'Fast'],
                ['name' => 'Glasgow', 'state' => 'Scotland', 'area_code' => '0141', 'badge' => 'Fast'],
                ['name' => 'Liverpool', 'state' => 'Merseyside', 'area_code' => '0151', 'badge' => 'Active'],
                ['name' => 'Edinburgh', 'state' => 'Scotland', 'area_code' => '0131', 'badge' => 'Active'],
                ['name' => 'Bristol', 'state' => 'South West', 'area_code' => '0117', 'badge' => 'Active'],
                ['name' => 'Sheffield', 'state' => 'South Yorkshire', 'area_code' => '0114', 'badge' => 'Active'],
                ['name' => 'Newcastle', 'state' => 'Tyne & Wear', 'area_code' => '0191', 'badge' => 'Active'],
                ['name' => 'Nottingham', 'state' => 'East Midlands', 'area_code' => '0115', 'badge' => 'Active'],
                ['name' => 'Belfast', 'state' => 'Northern Ireland', 'area_code' => '028', 'badge' => 'Active'],
                ['name' => 'Cardiff', 'state' => 'Wales', 'area_code' => '029', 'badge' => 'Active'],
            ],
            'CA', 'CAN', 'CANADA' => [
                ['name' => 'Any Location (Optimal Route)', 'state' => 'Canada Wide', 'area_code' => '', 'badge' => 'Recommended', 'is_default' => true],
                ['name' => 'Toronto (Downtown)', 'state' => 'Ontario', 'area_code' => '416', 'badge' => 'Popular'],
                ['name' => 'Toronto (GTA)', 'state' => 'Ontario', 'area_code' => '647', 'badge' => 'High Capacity'],
                ['name' => 'Montreal', 'state' => 'Quebec', 'area_code' => '514', 'badge' => 'Popular'],
                ['name' => 'Vancouver', 'state' => 'British Columbia', 'area_code' => '604', 'badge' => 'Popular'],
                ['name' => 'Calgary', 'state' => 'Alberta', 'area_code' => '403', 'badge' => 'Active'],
                ['name' => 'Ottawa', 'state' => 'Ontario', 'area_code' => '613', 'badge' => 'Active'],
                ['name' => 'Edmonton', 'state' => 'Alberta', 'area_code' => '780', 'badge' => 'Active'],
                ['name' => 'Quebec City', 'state' => 'Quebec', 'area_code' => '418', 'badge' => 'Active'],
                ['name' => 'Winnipeg', 'state' => 'Manitoba', 'area_code' => '204', 'badge' => 'Active'],
            ],
            'HK', 'HONG KONG' => [
                ['name' => 'Any Location (Hong Kong)', 'state' => 'Hong Kong', 'area_code' => '', 'badge' => 'Recommended', 'is_default' => true],
            ],
            'ID', 'INDONESIA' => [
                ['name' => 'Any Location (Indonesia)', 'state' => 'Indonesia', 'area_code' => '', 'badge' => 'Recommended', 'is_default' => true],
            ],
            'AU', 'AUS', 'AUSTRALIA' => [
                ['name' => 'Any Location (Australia)', 'state' => 'Australia', 'area_code' => '', 'badge' => 'Recommended', 'is_default' => true],
            ],
            default => [ // USA
                ['name' => 'Any Location (Optimal Route)', 'state' => 'United States', 'area_code' => '', 'badge' => 'Recommended', 'is_default' => true],
                ['name' => 'California (Los Angeles)', 'state' => 'California', 'area_code' => '213', 'badge' => 'Popular'],
                ['name' => 'California (Beverly Hills / West LA)', 'state' => 'California', 'area_code' => '310', 'badge' => 'High Demand'],
                ['name' => 'California (San Francisco)', 'state' => 'California', 'area_code' => '415', 'badge' => 'Tech Hub'],
                ['name' => 'California (San Diego)', 'state' => 'California', 'area_code' => '619', 'badge' => 'Fast'],
                ['name' => 'California (San Jose / Silicon Valley)', 'state' => 'California', 'area_code' => '408', 'badge' => 'Tech Hub'],
                ['name' => 'California (Sacramento)', 'state' => 'California', 'area_code' => '916', 'badge' => 'Active'],
                ['name' => 'New York (Manhattan)', 'state' => 'New York', 'area_code' => '212', 'badge' => 'High Demand'],
                ['name' => 'New York (NYC Metro)', 'state' => 'New York', 'area_code' => '646', 'badge' => 'Popular'],
                ['name' => 'New York (Brooklyn / Queens)', 'state' => 'New York', 'area_code' => '718', 'badge' => 'Popular'],
                ['name' => 'New York (NYC Mobile)', 'state' => 'New York', 'area_code' => '917', 'badge' => 'High Capacity'],
                ['name' => 'Texas (Houston)', 'state' => 'Texas', 'area_code' => '713', 'badge' => 'Popular'],
                ['name' => 'Texas (Dallas)', 'state' => 'Texas', 'area_code' => '214', 'badge' => 'Popular'],
                ['name' => 'Texas (Austin)', 'state' => 'Texas', 'area_code' => '512', 'badge' => 'Tech Hub'],
                ['name' => 'Texas (San Antonio)', 'state' => 'Texas', 'area_code' => '210', 'badge' => 'Active'],
                ['name' => 'Florida (Miami)', 'state' => 'Florida', 'area_code' => '305', 'badge' => 'High Demand'],
                ['name' => 'Florida (Orlando)', 'state' => 'Florida', 'area_code' => '407', 'badge' => 'Popular'],
                ['name' => 'Florida (Tampa)', 'state' => 'Florida', 'area_code' => '813', 'badge' => 'Active'],
                ['name' => 'Florida (Fort Lauderdale)', 'state' => 'Florida', 'area_code' => '954', 'badge' => 'Active'],
                ['name' => 'Illinois (Chicago Downtown)', 'state' => 'Illinois', 'area_code' => '312', 'badge' => 'Popular'],
                ['name' => 'Illinois (Chicago Metro)', 'state' => 'Illinois', 'area_code' => '773', 'badge' => 'Active'],
                ['name' => 'Washington (Seattle)', 'state' => 'Washington', 'area_code' => '206', 'badge' => 'Tech Hub'],
                ['name' => 'Georgia (Atlanta Downtown)', 'state' => 'Georgia', 'area_code' => '404', 'badge' => 'Popular'],
                ['name' => 'Georgia (Atlanta Metro)', 'state' => 'Georgia', 'area_code' => '678', 'badge' => 'Active'],
                ['name' => 'Massachusetts (Boston)', 'state' => 'Massachusetts', 'area_code' => '617', 'badge' => 'Popular'],
                ['name' => 'Pennsylvania (Philadelphia)', 'state' => 'Pennsylvania', 'area_code' => '215', 'badge' => 'Active'],
                ['name' => 'Pennsylvania (Pittsburgh)', 'state' => 'Pennsylvania', 'area_code' => '412', 'badge' => 'Active'],
                ['name' => 'District of Columbia (Washington DC)', 'state' => 'DC', 'area_code' => '202', 'badge' => 'High Demand'],
                ['name' => 'Nevada (Las Vegas)', 'state' => 'Nevada', 'area_code' => '702', 'badge' => 'Popular'],
                ['name' => 'Arizona (Phoenix)', 'state' => 'Arizona', 'area_code' => '602', 'badge' => 'Active'],
                ['name' => 'Colorado (Denver)', 'state' => 'Colorado', 'area_code' => '303', 'badge' => 'Active'],
                ['name' => 'Michigan (Detroit)', 'state' => 'Michigan', 'area_code' => '313', 'badge' => 'Active'],
                ['name' => 'Ohio (Columbus)', 'state' => 'Ohio', 'area_code' => '614', 'badge' => 'Active'],
                ['name' => 'North Carolina (Charlotte)', 'state' => 'North Carolina', 'area_code' => '704', 'badge' => 'Active'],
                ['name' => 'Virginia (Northern Virginia)', 'state' => 'Virginia', 'area_code' => '703', 'badge' => 'Active'],
                ['name' => 'New Jersey (Newark / Jersey City)', 'state' => 'New Jersey', 'area_code' => '201', 'badge' => 'Active'],
            ],
        };

        $search = strtolower(trim($request->query('search', '')));
        if ($search !== '') {
            $locations = array_values(array_filter($locations, function ($loc) use ($search) {
                return str_contains(strtolower($loc['name']), $search)
                    || str_contains(strtolower($loc['state']), $search)
                    || str_contains(strtolower($loc['area_code']), $search);
            }));
        }

        return response()->json([
            'success' => true,
            'country' => $country,
            'data' => $locations,
            'total' => count($locations),
        ]);
    }

    /**
     * Proxy to PVADeals inventory endpoint — per-state stock counts for US services only.
     */
    public function inventory(Request $request): JsonResponse
    {
        $client = new PvaDealsClient();

        if (!$client->hasApiKey()) {
            return response()->json(['success' => false, 'message' => 'API key not configured.'], 503);
        }

        $serviceId = (string) $request->query('serviceId', '');
        $type = strtoupper((string) $request->query('type', 'STR'));
        $state = $request->query('state');

        if (empty($serviceId)) {
            return response()->json([
                'success' => false,
                'message' => 'serviceId query parameter is required.',
            ], 422);
        }

        // Per docs: inventory is US only. UK services return 422.
        $service = $client->getService($serviceId);
        if ($service && in_array(strtoupper($service['country'] ?? ''), ['UK', 'GB', 'GBR'])) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Inventory is supported for US services only.',
            ], 422);
        }

        $data = $client->getInventory($serviceId, $type, $state);

        return response()->json([
            'success' => !empty($data['success']),
            'data' => $data,
        ], !empty($data['success']) ? 200 : ($data['status'] ?? 422));
    }

    /**
     * Manual / Administrator trigger for PVADeals catalog synchronization.
     */
    public function sync(Request $request, \App\Services\PvaDeals\PvaDealsSyncService $syncService): JsonResponse
    {
        $result = $syncService->sync();

        return response()->json([
            'success' => !empty($result['success']),
            'message' => !empty($result['success']) 
                ? 'Catalog synchronized successfully.' 
                : 'Failed to synchronize catalog: ' . ($result['error'] ?? 'Unknown error'),
            'details' => $result,
        ], !empty($result['success']) ? 200 : 500);
    }

    /**
     * Return best value services for a selected country.
     */
    public function bestValue(Request $request, \App\Services\PvaDeals\PvaDealsCatalogService $catalogService): JsonResponse
    {
        $country = strtoupper((string) $request->query('country', 'US'));
        $targetCurrency = strtoupper((string) $request->query('currency', 'NGN'));

        $catalogData = $catalogService->getWebsites($country, null, $targetCurrency);
        $bestValue = array_values(array_filter($catalogData['services'], fn($s) => !empty($s['is_best_value'])));

        return response()->json([
            'success' => true,
            'country' => $country,
            'total' => count($bestValue),
            'data' => $bestValue,
        ]);
    }

    /**
     * Return live numbers stats and country inventory breakdown from PVADeals.
     */
    public function numbersStats(Request $request): JsonResponse
    {
        $activeCountries = \App\Models\Country::where('is_active', true)->orderBy('name')->get();
        $totalStrStock = (int) \App\Models\CountryService::where('is_active', true)->sum('str_stock');
        $totalLtrStock = (int) \App\Models\CountryService::where('is_active', true)->sum('ltr_stock');

        if ($totalStrStock === 0) {
            $client = new PvaDealsClient();
            $totalStrStock = $client->getAvailableNumbersCount();
        }

        $byCountry = [];
        foreach ($activeCountries as $country) {
            $services = \App\Models\CountryService::where('country_id', $country->id)->where('is_active', true);
            $strStock = (int) $services->sum('str_stock');
            $ltrStock = (int) $services->sum('ltr_stock');
            $count = (int) $services->count();

            $byCountry[] = [
                'id' => $country->id,
                'name' => $country->name,
                'iso2' => $country->iso2,
                'iso3' => $country->iso3,
                'dial_code' => $country->dial_code,
                'flag' => $country->flag_emoji ?? $country->iso2,
                'str_stock' => $strStock,
                'ltr_stock' => $ltrStock,
                'total_numbers' => $strStock + $ltrStock,
                'services_count' => $count,
            ];
        }

        return response()->json([
            'success' => true,
            'total_available_numbers' => $totalStrStock,
            'total_ltr_numbers' => $totalLtrStock,
            'total_numbers_pool' => $totalStrStock + $totalLtrStock,
            'active_countries_count' => $activeCountries->count(),
            'countries' => $byCountry,
        ]);
    }
}
