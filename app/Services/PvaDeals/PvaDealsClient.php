<?php

namespace App\Services\PvaDeals;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PvaDealsClient
{
    protected string $apiKey;
    protected string $baseUrl;

    /** Connection timeout in seconds */
    protected int $connectTimeout = 10;

    /** Total request timeout in seconds (catalog is large, allow 30s) */
    protected int $totalTimeout = 30;

    /** Maximum retry attempts for SAFE READS ONLY (GET requests) */
    protected int $maxReadRetries = 2;

    public function __construct(?string $apiKey = null, ?string $baseUrl = null)
    {
        $resolvedKey = $apiKey ?? (string) config('services.pvadeals.key', '');
        if (empty($resolvedKey)) {
            $resolvedKey = (string) env('PVADEALS_API_KEY', '');
        }
        if (empty($resolvedKey)) {
            try {
                $account = \App\Models\ProviderAccount::whereHas('provider', fn($q) => $q->where('code', 'pvadeals'))->first();
                if ($account && !empty($account->credentials['api_key'])) {
                    $resolvedKey = (string) $account->credentials['api_key'];
                }
            } catch (\Throwable $e) {}
        }
        $this->apiKey = trim($resolvedKey);
        $this->baseUrl = rtrim($baseUrl ?? (string) config('services.pvadeals.base_url', 'https://prod-v3.pvadeals.com/v3/api'), '/');
    }

    public function getRawBalance(): array
    {
        $res = $this->request('GET', '/balance');
        return is_array($res) ? $res : [];
    }

    public function flagNumber(string $requestId): bool
    {
        $res = $this->flagRequest($requestId);
        return !empty($res['success']);
    }

    public function isPendingDelivery(array $purchaseResult): bool
    {
        $status = strtoupper($purchaseResult['status'] ?? $purchaseResult['data']['status'] ?? '');
        return in_array($status, ['PENDING', 'PROCESSING', 'WAITING']);
    }

    public function getAvailableNumbersCount(): int
    {
        return Cache::store('file')->remember('pvadeals_total_available_numbers_count', 300, function () {
            $services = $this->getAllServices();
            if (empty($services)) {
                return 10620276;
            }
            $total = 0;
            foreach ($services as $s) {
                $total += (int) ($s['str_stock'] ?? $s['STRstock'] ?? 0);
            }
            return $total > 0 ? $total : 10620276;
        });
    }

    public function getAvailableCountries(): array
    {
        return Cache::store('file')->remember('pvadeals_available_countries_v2', 600, function () {
            $allServices = $this->getAllServices();
            $stockByCountry = [];
            $servicesCountByCountry = [];
            foreach ($allServices as $s) {
                $c = $this->normalizeCountryCode($s['country'] ?? 'US');
                $stockByCountry[$c] = ($stockByCountry[$c] ?? 0) + (int) ($s['str_stock'] ?? $s['STRstock'] ?? 0);
                $servicesCountByCountry[$c] = ($servicesCountByCountry[$c] ?? 0) + 1;
            }

            $allConfig = config('numbers.all_services_countries', []);

            $result = [];
            $countryCodes = array_unique(array_merge(['US', 'UK', 'HK', 'ID', 'AU'], array_keys($stockByCountry)));

            foreach ($countryCodes as $code) {
                $norm = $this->normalizeCountryCode($code);
                $cfg = $allConfig[$norm] ?? null;

                $name = $cfg['name'] ?? ($norm === 'US' ? 'United States' : ($norm === 'UK' ? 'United Kingdom' : $norm));
                $flag = $cfg['flag'] ?? ($norm === 'US' ? '🇺🇸' : ($norm === 'UK' ? '🇬🇧' : ($norm === 'HK' ? '🇭🇰' : ($norm === 'ID' ? '🇮🇩' : ($norm === 'AU' ? '🇦🇺' : '🌐')))));
                $dialCode = $cfg['dial_code'] ?? ($norm === 'US' ? '+1' : ($norm === 'UK' ? '+44' : '+1'));
                $carrier = $cfg['carrier'] ?? ($norm === 'US' ? ['name' => 'T-Mobile', 'badge' => 'T', 'color' => '#E20074'] : ['name' => 'EE', 'badge' => 'EE', 'color' => '#007B85']);

                $strStock = $stockByCountry[$norm] ?? 0;
                $servicesCount = $servicesCountByCountry[$norm] ?? 0;
                $hasStr = $servicesCount > 0;
                $hasLtrAllServices = isset($allConfig[$norm]);

                $result[$norm] = [
                    'code' => $norm,
                    'name' => $name,
                    'flag' => $flag,
                    'dial_code' => $dialCode,
                    'carrier' => $carrier,
                    'services_count' => $servicesCount,
                    'str_stock' => $strStock,
                    'has_str' => $hasStr,
                    'has_ltr_all_services' => $hasLtrAllServices,
                    'allowed_durations' => $cfg['allowed_durations'] ?? ($hasStr ? [20] : [30]),
                    'default_duration' => $cfg['default_duration'] ?? ($norm === 'US' ? 28 : 30),
                ];
            }

            return $result;
        });
    }

    public function hasApiKey(): bool
    {
        return !empty($this->apiKey);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $key): void
    {
        $this->apiKey = trim($key);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Check unauthenticated service health.
     */
    public function checkHealth(): array
    {
        try {
            $response = Http::withoutVerifying()
                ->connectTimeout(5)
                ->timeout(10)
                ->get("{$this->baseUrl}/health");

            $json = $response->json() ?? [];
            return array_merge([
                'status' => $response->successful() ? ($json['status'] ?? 'operational') : 'unavailable',
                'statusCode' => $response->status(),
            ], $json);
        } catch (\Throwable $e) {
            return ['status' => 'unavailable', 'statusCode' => 503, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fast health check (status code 200).
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::withoutVerifying()
                ->connectTimeout(5)
                ->timeout(10)
                ->get("{$this->baseUrl}/health");
            return $response->status() === 200;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check unauthenticated deployed version.
     */
    public function getVersion(): array
    {
        try {
            $response = Http::withoutVerifying()
                ->connectTimeout(5)
                ->timeout(10)
                ->get("{$this->baseUrl}/version");
            return $response->json() ?? ['status' => 'unknown'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Retrieve account balance in USD credits with 30s cache.
     */
    public function getBalance(bool $forceRefresh = false): ?float
    {
        if ($forceRefresh) {
            Cache::forget('pvadeals_carrier_balance');
        }

        return Cache::remember('pvadeals_carrier_balance', 30, function () {
            $res = $this->request('GET', '/balance');
            if ($res && isset($res['data']['credits'])) {
                return (float) $res['data']['credits'];
            }
            if ($res && isset($res['credits'])) {
                return (float) $res['credits'];
            }
            return null;
        });
    }

    public function hasSufficientBalance(float $requiredUsd, float $buffer = 0.5): bool
    {
        $balance = $this->getBalance();
        if ($balance === null) {
            return false;
        }
        return $balance >= ($requiredUsd + $buffer);
    }

    /**
     * Fetch catalog of all services with 10-minute cache and atomic lock.
     * Stale-if-error pattern: if provider returns 429 or fails, serves last cached copy.
     */
    public function getAllServices(bool $forceRefresh = false): array
    {
        $cacheKey = 'pvadeals_all_services_catalog_v2';
        $staleCacheKey = 'pvadeals_all_services_catalog_stale';

        $fileCache = Cache::store('file');

        if (!$forceRefresh && $fileCache->has($cacheKey)) {
            return $fileCache->get($cacheKey, []);
        }

        // Lock refresh so concurrent requests don't hit 429
        $lock = Cache::lock('pvadeals_services_sync_lock', 25);

        try {
            $acquired = $lock->get();
            if (!$acquired && $fileCache->has($staleCacheKey)) {
                return $fileCache->get($staleCacheKey, []);
            }

            $res = $this->request('GET', '/services/all');

            if ($res && !empty($res['success'])) {
                $rawServices = $res['data']['services'] ?? $res['data'] ?? [];
                if (!is_array($rawServices)) {
                    $rawServices = [];
                }

                $normalized = array_map([$this, 'normalizeServiceItem'], $rawServices);

                // Cache fresh copy for 10 minutes (600s) on file store
                $fileCache->put($cacheKey, $normalized, 600);
                // Keep stale fallback for 24 hours
                $fileCache->put($staleCacheKey, $normalized, 86400);

                return $normalized;
            }

            // On provider failure (429, 503, etc), fall back to stale cache if available
            if ($fileCache->has($staleCacheKey)) {
                return $fileCache->get($staleCacheKey, []);
            }

            return [];
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Get single service by 24-character hex ID with 5-minute cache.
     * Note: GET /service/{id} returns price as 'price', while /services/all returns 'STRprice'.
     */
    public function getService(string $serviceId, bool $forceRefresh = false): ?array
    {
        $cacheKey = "pvadeals_service_details_{$serviceId}";

        if (!$forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $res = $this->request('GET', "/service/{$serviceId}");
        if ($res && !empty($res['success']) && isset($res['data'])) {
            $normalized = $this->normalizeServiceItem($res['data']);
            Cache::put($cacheKey, $normalized, 300);
            return $normalized;
        }

        return null;
    }

    /**
     * Query US state inventory for an STR service.
     * Returns 422 without network request if called on a non-US service.
     */
    public function getInventory(string $serviceId, string $type = 'STR', ?string $state = null): array
    {
        $query = [
            'serviceId' => $serviceId,
            'type' => strtoupper($type),
        ];

        if (!empty($state)) {
            $query['state'] = strtoupper($state);
        }

        $res = $this->request('GET', '/inventory', $query);

        if (!$res || empty($res['success'])) {
            return [
                'success' => false,
                'status' => $res['status'] ?? 500,
                'message' => $res['message'] ?? 'Failed to retrieve inventory from provider.',
                'stock' => 0,
                'states' => [],
            ];
        }

        $data = $res['data'] ?? [];
        $countsByState = $data['countsByState'] ?? [];
        $totalStock = 0;
        $stateList = [];

        foreach ($countsByState as $stCode => $stData) {
            $cnt = is_array($stData) ? ($stData['count'] ?? 0) : (int) $stData;
            $name = is_array($stData) ? ($stData['name'] ?? $stCode) : $stCode;
            $totalStock += $cnt;
            $stateList[] = [
                'code' => $stCode,
                'name' => $name,
                'stock' => $cnt,
            ];
        }

        return [
            'success' => true,
            'status' => 200,
            'stock' => $totalStock,
            'states' => $stateList,
            'counts_by_state' => $countsByState,
            'raw' => $data,
        ];
    }

    /**
     * STR - Purchase a single-use verification number (20 minutes).
     * NEVER auto-retried. Strictly classifies definitive vs ambiguous outcomes.
     *
     * @param string      $serviceId  24-character hex ID from services/all
     * @param string|null $areaCode   US only (never send with state)
     * @param string|null $state      US only (never send with areaCode)
     */
    public function purchaseNumber(string $serviceId, ?string $areaCode = null, ?string $state = null): array
    {
        if ($this->isPurchasePaused()) {
            return [
                'success' => false,
                'status' => 429,
                'is_ambiguous' => false,
                'message' => 'Provider is temporarily busy. Please try again shortly.',
            ];
        }

        // Validate mutually exclusive US location targeting
        if (!empty($areaCode) && !empty($state)) {
            return [
                'success' => false,
                'status' => 422,
                'is_ambiguous' => false,
                'message' => 'Cannot specify both areaCode and state. Choose only one.',
            ];
        }

        $serviceItem = ['serviceId' => $serviceId];

        if (!empty($state)) {
            $serviceItem['state'] = strtoupper($state);
        } elseif (!empty($areaCode)) {
            $serviceItem['areaCode'] = (string) $areaCode;
        }

        // Body must wrap services in an array: {"services": [{"serviceId": "..."}]}
        $payload = [
            'services' => [$serviceItem],
        ];

        return $this->executePurchaseRequest('/purchase', $payload);
    }

    /**
     * LTR - Purchase long-term rental line (3, 7, 14, 28, or 30 days).
     */
    public function purchaseLtr(
        string $serviceId,
        ?int $duration = null,
        ?string $country = null,
        ?string $areaCode = null,
        ?string $state = null
    ): array {
        if ($this->isPurchasePaused()) {
            return [
                'success' => false,
                'status' => 429,
                'is_ambiguous' => false,
                'message' => 'Provider is temporarily busy. Please try again shortly.',
            ];
        }

        if ($serviceId !== 'ALL_SERVICES' && $country !== null) {
            return [
                'success' => false,
                'status' => 422,
                'is_ambiguous' => false,
                'message' => "The 'country' parameter is only allowed when serviceId is 'ALL_SERVICES'.",
            ];
        }

        $payload = ['serviceId' => $serviceId];

        if ($serviceId === 'ALL_SERVICES') {
            $allCountries = config('numbers.all_services_countries', []);
            $normCountry = $this->normalizeCountryCode($country ?? 'US');

            if (!isset($allCountries[$normCountry])) {
                return [
                    'success' => false,
                    'status' => 422,
                    'is_ambiguous' => false,
                    'message' => "Country '{$country}' is not supported for all-services rentals.",
                ];
            }

            $countryConfig = $allCountries[$normCountry];
            $allowedDurations = $countryConfig['allowed_durations'] ?? [30];
            $selectedDuration = $duration ?? $countryConfig['default_duration'] ?? 30;

            if (!in_array($selectedDuration, $allowedDurations, true)) {
                return [
                    'success' => false,
                    'status' => 422,
                    'is_ambiguous' => false,
                    'message' => "Duration {$selectedDuration} days is not valid for {$countryConfig['name']}.",
                ];
            }

            $payload['country'] = $normCountry;
            if ($normCountry !== 'US') {
                $payload['duration'] = $selectedDuration;
            }
        } else {
            $payload['duration'] = $duration ?? 30;
            if (!empty($state)) {
                $payload['state'] = strtoupper($state);
            } elseif (!empty($areaCode)) {
                $payload['areaCode'] = $areaCode;
            }
        }

        return $this->executePurchaseRequest('/purchase-ltr', $payload);
    }

    /**
     * Get single request details by ID (safe read, retries allowed).
     */
    public function getRequest(string $requestId): ?array
    {
        $res = $this->request('GET', "/request/{$requestId}");
        return $res['data'] ?? null;
    }

    /**
     * List user requests with cursor pagination.
     * Note: Envelope contains data.data.edges[].node
     */
    public function listRequests(array $params = []): array
    {
        $res = $this->request('GET', '/requests', $params);
        $edges = $res['data']['data']['edges'] ?? $res['data']['edges'] ?? [];

        $requests = [];
        foreach ($edges as $edge) {
            if (isset($edge['node'])) {
                $requests[] = $edge['node'];
            }
        }

        return [
            'success' => !empty($res['success']),
            'requests' => $requests,
            'raw' => $res,
        ];
    }

    /**
     * Flag a request for refund. Only allowed when allowFlag is true.
     */
    public function flagRequest(string $requestId): array
    {
        return $this->request('PUT', "/request/{$requestId}/flag");
    }

    /**
     * Reuse a request. Only allowed when allowReuse is true.
     */
    public function reuseRequest(string $requestId): array
    {
        return $this->request('POST', "/request/{$requestId}/reuse");
    }

    /**
     * Normalize raw provider service array into a stable internal shape.
     */
    public function normalizeServiceItem(array $raw): array
    {
        $id = (string) ($raw['_id'] ?? $raw['id'] ?? '');
        $name = (string) ($raw['name'] ?? '');
        $country = $this->normalizeCountryCode((string) ($raw['country'] ?? 'US'));

        // Handle price naming differences:
        // /services/all returns 'STRprice', /service/{id} returns 'price'
        $strPrice = (float) ($raw['STRprice'] ?? $raw['price'] ?? 0.0);
        $ltr3Price = (float) ($raw['LTR3price'] ?? 0.0);
        $ltr7Price = (float) ($raw['LTR7price'] ?? 0.0);
        $ltr14Price = (float) ($raw['LTR14price'] ?? 0.0);
        $ltr30Price = (float) ($raw['LTR30price'] ?? 0.0);
        $ltr90Price = (float) ($raw['LTR90price'] ?? 0.0);
        $ltr180Price = (float) ($raw['LTR180price'] ?? 0.0);

        $strStock = (int) ($raw['STRstock'] ?? 0);
        $ltrStock = (int) ($raw['LTRstock'] ?? 0);

        // Detect Best Value flag
        $isBestValue = !empty($raw['is_best_value'])
            || !empty($raw['isBestValue'])
            || !empty($raw['best_value'])
            || !empty($raw['bestValue'])
            || (isset($raw['category']) && strtolower($raw['category']) === 'best value')
            || (isset($raw['tags']) && is_array($raw['tags']) && in_array('best_value', $raw['tags']));

        return [
            'id' => $id,
            'name' => $name,
            'country' => $country,
            'str_price_usd' => $strPrice,
            'ltr_prices_usd' => [
                3 => $ltr3Price,
                7 => $ltr7Price,
                14 => $ltr14Price,
                30 => $ltr30Price,
                90 => $ltr90Price,
                180 => $ltr180Price,
            ],
            'str_stock' => $strStock,
            'ltr_stock' => $ltrStock,
            'is_available' => $strStock > 0 || $ltrStock > 0,
            'is_best_value' => $isBestValue,
            'category' => (string) ($raw['category'] ?? ''),
            // Preserved provider fields for backward compatibility
            '_id' => $id,
            'STRprice' => $strPrice,
            'LTR3price' => $ltr3Price,
            'LTR7price' => $ltr7Price,
            'LTR14price' => $ltr14Price,
            'LTR30price' => $ltr30Price,
            'LTR90price' => $ltr90Price,
            'LTR180price' => $ltr180Price,
            'STRstock' => $strStock,
            'LTRstock' => $ltrStock,
        ];
    }

    /**
     * Normalize country codes: USA -> US, GB/GBR -> UK.
     */
    public function normalizeCountryCode(string $code): string
    {
        $code = strtoupper(trim($code));

        if (in_array($code, ['GB', 'GBR', 'UK', 'UNITED KINGDOM'], true)) {
            return 'UK';
        }
        if (in_array($code, ['US', 'USA', 'UNITED STATES'], true)) {
            return 'US';
        }
        if (in_array($code, ['HK', 'HKG', 'HONG KONG'], true)) {
            return 'HK';
        }
        if (in_array($code, ['ID', 'IDN', 'INDONESIA'], true)) {
            return 'ID';
        }
        if (in_array($code, ['AU', 'AUS', 'AUSTRALIA'], true)) {
            return 'AU';
        }

        return $code;
    }

    /**
     * Alias for backward compatibility.
     */
    public function normalizeAllServicesCountry(string $code): string
    {
        return $this->normalizeCountryCode($code);
    }

    public function getAllServicesCountryConfig(string $code): ?array
    {
        $norm = $this->normalizeCountryCode($code);
        return config("numbers.all_services_countries.{$norm}");
    }

    /**
     * Execute purchase POST with strict non-retry semantics and error classification.
     */
    protected function executePurchaseRequest(string $endpoint, array $payload): array
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withoutVerifying()
                ->withHeaders($headers)
                ->connectTimeout($this->connectTimeout)
                ->timeout($this->totalTimeout)
                ->post($url, $payload);

            $status = $response->status();

            // 429 Rate Limit
            if ($status === 429) {
                $retryAfter = (int) $response->header('Retry-After', '60');
                $pauseSeconds = max(60, min($retryAfter, 300));
                $this->pausePurchases($pauseSeconds);

                return [
                    'success' => false,
                    'status' => 429,
                    'is_ambiguous' => false,
                    'message' => 'Provider rate limit reached. Please try again shortly.',
                    'retry_after' => $pauseSeconds,
                ];
            }

            // 4xx Definitive client error
            if ($response->clientError()) {
                $json = $response->json() ?? [];
                $msg = $json['message'] ?? $json['error'] ?? "Request rejected with status {$status}";
                $this->recordPurchaseFailure();

                return [
                    'success' => false,
                    'status' => $status,
                    'is_ambiguous' => false,
                    'message' => $msg,
                    'data' => $json['data'] ?? null,
                ];
            }

            // 503 Maintenance (Definitive)
            if ($status === 503) {
                $this->recordPurchaseFailure();
                return [
                    'success' => false,
                    'status' => 503,
                    'is_ambiguous' => false,
                    'message' => 'Provider is temporarily undergoing maintenance. Please try again shortly.',
                ];
            }

            // 500, 502, 504 -> AMBIGUOUS! The provider might have fulfilled the purchase
            if ($response->serverError()) {
                Log::error("PVADeals ambiguous server error {$status} on purchase {$endpoint}: " . $response->body());
                $this->recordPurchaseFailure();

                return [
                    'success' => false,
                    'status' => $status,
                    'is_ambiguous' => true,
                    'message' => "Ambiguous response ({$status}) from provider gateway. Reconciling order status.",
                ];
            }

            // 200 / 201 Success
            $json = $response->json() ?? [];
            return array_merge([
                'success' => true,
                'status' => $status,
                'is_ambiguous' => false,
            ], $json);

        } catch (ConnectionException $e) {
            // Network timeout / connection drop -> AMBIGUOUS!
            Log::error("PVADeals connection timeout on purchase {$endpoint}: " . $e->getMessage());
            $this->recordPurchaseFailure();

            return [
                'success' => false,
                'status' => 504,
                'is_ambiguous' => true,
                'message' => 'Provider connection timed out while processing order. Reconciling status.',
            ];
        } catch (\Throwable $e) {
            Log::error("PVADeals unexpected exception on purchase {$endpoint}: " . $e->getMessage());
            $this->recordPurchaseFailure();

            return [
                'success' => false,
                'status' => 500,
                'is_ambiguous' => true,
                'message' => 'Unexpected error occurred while contacting provider: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Authenticated safe GET/POST requests with retries for GET only.
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $isGet = strtoupper($method) === 'GET';
        $attempts = $isGet ? ($this->maxReadRetries + 1) : 1;
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $http = Http::withoutVerifying()
                    ->withHeaders($headers)
                    ->connectTimeout($this->connectTimeout)
                    ->timeout($this->totalTimeout);

                $response = match (strtoupper($method)) {
                    'GET' => $http->get($url, $data),
                    'POST' => $http->post($url, $data),
                    'PUT' => $http->put($url, $data),
                    'DELETE' => $http->delete($url, $data),
                    default => $http->get($url, $data),
                };

                if ($response->status() === 429) {
                    $retryAfter = (int) $response->header('Retry-After', '60');
                    $this->pausePurchases($retryAfter);
                    return ['success' => false, 'status' => 429, 'message' => 'Rate limit exceeded.'];
                }

                if ($response->status() === 503 && $attempt < $attempts) {
                    usleep(500000 * $attempt);
                    continue;
                }

                $json = $response->json();
                return $json ?? [
                    'success' => $response->successful(),
                    'status' => $response->status(),
                    'data' => null,
                ];
            } catch (\Throwable $e) {
                $lastException = $e;
                if ($attempt < $attempts) {
                    usleep(500000 * $attempt);
                    continue;
                }
            }
        }

        return [
            'success' => false,
            'status' => 500,
            'message' => $lastException?->getMessage() ?? 'Failed to communicate with provider.',
        ];
    }

    public function isPurchasePaused(): bool
    {
        return Cache::has('pvadeals_purchase_paused');
    }

    public function pausePurchases(int $seconds = 300): void
    {
        Cache::put('pvadeals_purchase_paused', true, now()->addSeconds($seconds));
    }

    protected function recordPurchaseFailure(): void
    {
        $key = 'pvadeals_recent_failures_count';
        $current = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $current, now()->addSeconds(60));

        if ($current >= 15) {
            Log::warning("PVADeals 15 purchase failures detected in 60s. Pausing purchases for 5 minutes.");
            $this->pausePurchases(300);
        }
    }
}
