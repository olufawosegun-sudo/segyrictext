<?php

namespace App\Infrastructure\Providers;

use App\Domain\DTOs\IncomingSmsDTO;
use App\Domain\DTOs\ProviderNumberDTO;
use App\Domain\Interfaces\NumberProviderInterface;
use App\Models\Country;
use App\Models\ProviderAccount;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PvaDealsAdapter implements NumberProviderInterface
{
    protected PvaDealsClient $client;

    public function __construct(
        protected ?ProviderAccount $account = null
    ) {
        $apiKey = $this->account?->credentials['api_key'] ?? config('services.pvadeals.key');
        $baseUrl = $this->account?->credentials['base_url'] ?? config('services.pvadeals.base_url');
        $this->client = new PvaDealsClient($apiKey, $baseUrl);
    }

    public function getClient(): PvaDealsClient
    {
        return $this->client;
    }

    public function checkAvailability(Country $country, Service $service): bool
    {
        if (!$this->client->hasApiKey()) {
            return true;
        }

        try {
            $pvaService = $this->resolvePvaService($country, $service);
            if ($pvaService) {
                $stock = $pvaService['str_stock'] ?? $pvaService['STRstock'] ?? null;
                if ($stock !== null) {
                    return (int) $stock > 0;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("PVADeals availability check failed: " . $e->getMessage());
        }

        return true;
    }

    /**
     * Reserve a number. Supports optional state targeting for US numbers.
     *
     * @param Country     $country
     * @param Service     $service
     * @param string|null $state    Optional US state code (e.g. "CA") or full name
     * @return ProviderNumberDTO
     */
    public function reserveNumber(Country $country, Service $service, ?string $state = null): ProviderNumberDTO
    {
        if ($this->client->hasApiKey() && !app()->environment('testing')) {
            $pvaService = $this->resolvePvaService($country, $service);

            if ($pvaService && !empty($pvaService['_id'])) {
                $serviceId = $pvaService['_id'];
                $purchaseResult = $this->client->purchaseNumber($serviceId, null, $state);

                $req = $purchaseResult['data']['requests'][0]
                    ?? $purchaseResult['requests'][0]
                    ?? (isset($purchaseResult['data']['number']) ? $purchaseResult['data'] : null);

                if (!empty($purchaseResult['success']) && !empty($req)) {
                    $allocatedPhone = $req['number'] ?? '';
                    if (!empty($allocatedPhone) && !str_starts_with($allocatedPhone, '+')) {
                        $allocatedPhone = '+' . ltrim($allocatedPhone, '+');
                    }

                    // Use the actual amount charged by PVADeals, fall back to catalog price
                    $costPrice = (float) ($req['amount'] ?? $pvaService['STRprice'] ?? $pvaService['str_price_usd'] ?? 0.20);

                    return new ProviderNumberDTO(
                        providerNumberId: (string) ($req['_id'] ?? $req['id'] ?? Str::random(24)),
                        phoneNumber: $allocatedPhone,
                        costPrice: $costPrice,
                        currency: 'USD',
                        capabilities: ['sms', 'otp'],
                        expiresInSeconds: 1200
                    );
                } else {
                    $errorMsg = $purchaseResult['message'] ?? 'Service currently out of stock or unavailable on PVADeals.';
                    Log::warning("PVADeals purchase call failed: " . json_encode($purchaseResult));
                    throw new \App\Domain\Number\Exceptions\NumberUnavailableException("PVADeals: {$errorMsg}");
                }
            } else {
                throw new \App\Domain\Number\Exceptions\NumberUnavailableException("PVADeals: No line currently in stock for '{$service->name}' in {$country->name}. Please try another country or 'Any Other Service'.");
            }
        }

        if (!$this->client->hasApiKey() && !app()->environment('testing')) {
            throw new \App\Domain\Number\Exceptions\NumberUnavailableException("PVADeals API Key is not configured. Please enter your PVADEALS_API_KEY in .env or settings to allocate real numbers.");
        }

        // High-fidelity fallback / test sandbox simulation ONLY in testing environment
        $dial = ltrim($country->dial_code ?? '1', '+');
        $randomSubscriber = mt_rand(2000000000, 9999999999);
        $fullNumber = "+{$dial}" . substr((string) $randomSubscriber, 0, 10);
        $providerId = 'PVA_' . Str::lower(Str::random(24));

        return new ProviderNumberDTO(
            providerNumberId: $providerId,
            phoneNumber: $fullNumber,
            costPrice: 0.20,
            currency: 'USD',
            capabilities: ['sms', 'otp'],
            expiresInSeconds: 1200
        );
    }

    public function getMessages(string $providerNumberId): array
    {
        if (!$this->client->hasApiKey() || str_starts_with($providerNumberId, 'PVA_')) {
            return [];
        }

        try {
            $req = $this->client->getRequest($providerNumberId);
            if ($req && !empty($req['message']) && ($req['status'] === 'COMPLETED' || !empty($req['messageCounter']))) {
                return [
                    new IncomingSmsDTO(
                        messageId: 'PVA_MSG_' . $providerNumberId,
                        sender: $req['serviceName'] ?? 'PVADeals',
                        recipient: $req['number'],
                        messageBody: $req['message'],
                        providerCode: 'pvadeals',
                        providerNumberId: $providerNumberId
                    ),
                ];
            }
        } catch (\Throwable $e) {
            Log::warning("PVADeals getMessages error: " . $e->getMessage());
        }

        return [];
    }

    public function releaseNumber(string $providerNumberId): bool
    {
        if (!$this->client->hasApiKey() || str_starts_with($providerNumberId, 'PVA_')) {
            return true;
        }

        try {
            return $this->client->flagNumber($providerNumberId);
        } catch (\Throwable $e) {
            Log::warning("PVADeals release/flag error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Resolve best matching PVADeals service from catalog based on Country and Service.
     */
    public function resolvePvaService(Country $country, Service $service): ?array
    {
        $services = $this->client->getAllServices();

        // 1. Direct DB lookup if already synced
        $dbCs = \App\Models\CountryService::where('country_id', $country->id)
            ->where('service_id', $service->id)
            ->whereNotNull('pva_service_id')
            ->first();

        if ($dbCs) {
            if (!empty($services)) {
                foreach ($services as $srv) {
                    if (($srv['_id'] ?? '') === $dbCs->pva_service_id) {
                        return $srv;
                    }
                }
            }

            return [
                '_id' => $dbCs->pva_service_id,
                'name' => $service->name,
                'country' => $country->iso2,
                'STRprice' => (float) ($dbCs->supplier_str_price ?? 0.20),
                'LTR3price' => 0.70,
                'LTR7price' => 1.25,
                'LTR14price' => 1.75,
                'LTR30price' => 2.25,
                'STRstock' => (int) ($dbCs->str_stock ?? 0),
                'str_stock' => (int) ($dbCs->str_stock ?? 0),
                'ltr_stock' => (int) ($dbCs->ltr_stock ?? 0),
            ];
        }

        if (empty($services)) {
            return null;
        }

        $iso = strtoupper($country->iso2 ?? 'US');
        $countryName = strtolower(trim($country->name ?? ''));
        $isUk = in_array($iso, ['GB', 'UK']);

        $serviceName = strtolower(trim($service->name));
        $serviceCode = strtolower(trim($service->code ?? ''));

        // Standard service alias dictionary
        $aliases = [
            'chatgpt' => ['openai', 'chatgpt'],
            'openai' => ['openai', 'chatgpt'],
            'google' => ['google', 'gmail', 'youtube'],
            'gmail' => ['google', 'gmail'],
            'twitter' => ['twitter', 'x'],
            'x' => ['twitter', 'x'],
            'facebook' => ['facebook', 'meta'],
            'meta' => ['facebook', 'meta'],
            'apple' => ['apple', 'icloud'],
            'icloud' => ['apple', 'icloud'],
            'uber' => ['uber', 'ubereats'],
            'wise' => ['wise', 'transferwise'],
            'other' => ['other', 'any other', 'universal'],
        ];

        $serviceSearchTerms = [$serviceName, $serviceCode];
        foreach ($aliases as $k => $vals) {
            if ($serviceCode === $k || str_contains($serviceName, $k)) {
                $serviceSearchTerms = array_merge($serviceSearchTerms, $vals);
            }
        }
        $serviceSearchTerms = array_unique(array_filter($serviceSearchTerms));

        // Filter services by country match first
        $matchedByCountry = [];
        foreach ($services as $s) {
            $sCountry = strtoupper($s['country'] ?? '');
            $sCountryLower = strtolower($sCountry);

            $countryMatched = false;
            if ($isUk && in_array($sCountry, ['UK', 'GB', 'GBR', 'UNITED KINGDOM'])) {
                $countryMatched = true;
            } elseif (!$isUk && ($iso === 'US' || $iso === 'USA') && in_array($sCountry, ['USA', 'US', 'UNITED STATES'])) {
                $countryMatched = true;
            } elseif ($sCountry === $iso || $sCountryLower === $countryName) {
                $countryMatched = true;
            }

            if ($countryMatched) {
                $matchedByCountry[] = $s;
            }
        }

        // If no country-specific match, search within all services
        $searchPool = !empty($matchedByCountry) ? $matchedByCountry : $services;

        // 1. Exact name/code match
        foreach ($searchPool as $s) {
            $pvaName = strtolower(trim($s['name'] ?? ''));
            foreach ($serviceSearchTerms as $term) {
                if ($pvaName === $term) {
                    return $s;
                }
            }
        }

        // 2. Substring match
        foreach ($searchPool as $s) {
            $pvaName = strtolower(trim($s['name'] ?? ''));
            foreach ($serviceSearchTerms as $term) {
                if (str_contains($pvaName, $term) || str_contains($term, $pvaName)) {
                    return $s;
                }
            }
        }

        // 3. If user wanted "other" or no specific match, find generic/other service
        foreach ($searchPool as $s) {
            $pvaName = strtolower(trim($s['name'] ?? ''));
            if (str_contains($pvaName, 'other') || str_contains($pvaName, 'any')) {
                return $s;
            }
        }

        // 4. Return first service with stock
        foreach ($searchPool as $s) {
            if (isset($s['STRstock']) && (int)$s['STRstock'] > 0) {
                return $s;
            }
        }

        return $searchPool[0] ?? $services[0] ?? null;
    }
}
