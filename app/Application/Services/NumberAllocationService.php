<?php

namespace App\Application\Services;

use App\Domain\Number\Exceptions\NumberUnavailableException;
use App\Domain\Wallet\Exceptions\InsufficientBalanceException;
use App\Infrastructure\Providers\PvaDealsClient;
use App\Infrastructure\Providers\ProviderFactory;
use App\Models\Country;
use App\Models\Currency;
use App\Models\NumberReservation;
use App\Models\NumberType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PhoneNumber;
use App\Models\PhoneNumberStatusHistory;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use App\Models\VerificationSession;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NumberAllocationService
{
    public function __construct(
        protected PricingEngineService $pricingEngine,
        protected ProviderFactory $providerFactory,
        protected WalletService $walletService,
        protected NumberReservationService $reservationService
    ) {}

    /**
     * Atomically reserve a phone number, hold wallet balance, and initiate a VerificationSession.
     * Enforces money safety: checks PVADeals carrier balance before purchase, rolls back on failure.
     *
     * @throws Exception
     */
    public function allocateVerificationNumber(
        User $user,
        Country $country,
        Service $service,
        ?NumberType $numberType = null,
        ?string $currencyCode = 'USD',
        ?int $duration = null,
        ?string $state = null,
        ?string $areaCode = null
    ): VerificationSession {
        $userWallet = $this->walletService->getOrCreateUserWallet($user);
        $currency = $userWallet->currency ?? Currency::where('code', 'USD')->first()
            ?? Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]);

        $numberType ??= NumberType::where('code', 'mobile')->first()
            ?? NumberType::firstOrCreate(
                ['code' => 'mobile'],
                ['name' => 'Mobile Cellular', 'is_active' => true]
            );

        $isAllServices = Str::contains(strtolower($service->name), 'all services')
            || in_array(strtolower($service->code), ['all_services', 'all-services'], true);

        $pvaClient = new PvaDealsClient();

        // 1. Calculate price & wholesale cost
        $price = 0.0;
        $costUsd = 0.20;
        $expiresInMinutes = 20;

        if ($isAllServices) {
            $normCountry = $pvaClient->normalizeAllServicesCountry($country->iso2 ?? $country->iso3 ?? 'US');
            $countryConfig = $pvaClient->getAllServicesCountryConfig($normCountry);

            if (!$countryConfig) {
                throw new NumberUnavailableException("Country '{$country->name}' does not support all-services numbers.");
            }

            $allowedDurations = $countryConfig['allowed_durations'] ?? [30];
            $selectedDuration = $duration ?? $countryConfig['default_duration'] ?? 30;

            if (!in_array($selectedDuration, $allowedDurations, true)) {
                throw new NumberUnavailableException("Invalid duration {$selectedDuration} days for {$countryConfig['name']}. Allowed: " . implode(', ', $allowedDurations) . " days.");
            }

            $plan = $countryConfig['plans'][$selectedDuration] ?? null;
            if (!$plan) {
                throw new NumberUnavailableException("Plan not configured for {$selectedDuration} days in {$countryConfig['name']}.");
            }

            $costUsd = (float) $plan['price_usd'];
            $rateData = $this->pricingEngine->getExchangeRate('USD', $currency->code);
            $exchangeRate = (float) ($rateData['rate'] ?? 1650.0);
            $marginPct = (float) config('numbers.pricing.default_margin_percentage', 15.0);
            $retailUsd = $costUsd * (1 + ($marginPct / 100));
            $price = round($retailUsd * $exchangeRate, 2);
            $expiresInMinutes = $selectedDuration * 24 * 60;
        } elseif ($duration && $duration > 0) {
            // Per-service Long-Term Rental (LTR) (3, 7, 14, or 30 days)
            $pvaAdapter = new \App\Infrastructure\Providers\PvaDealsAdapter();
            $pvaService = $pvaAdapter->resolvePvaService($country, $service);
            $costKey = "LTR{$duration}price";
            $costUsd = (float) ($pvaService[$costKey] ?? $pvaService['LTR30price'] ?? 2.25);
            $rateData = $this->pricingEngine->getExchangeRate('USD', $currency->code);
            $exchangeRate = (float) ($rateData['rate'] ?? 1650.0);
            $marginPct = (float) config('numbers.pricing.default_margin_percentage', 15.0);
            $retailUsd = $costUsd * (1 + ($marginPct / 100));
            $price = round($retailUsd * $exchangeRate, 2);
            $expiresInMinutes = $duration * 24 * 60;
        } else {
            $localized = $this->pricingEngine->calculatePriceInCurrency($country, $service, $currency->code);
            $price = $localized['price'];
            $costUsd = 0.20;
            $expiresInMinutes = 20;
        }

        // 2. Money safety: verify user wallet balance before doing anything
        $availableBalance = (float) $this->walletService->getBalance($user);
        if ($availableBalance < $price) {
            throw new InsufficientBalanceException((float) $availableBalance, (float) $price);
        }

        // 3. Money safety: verify PVADeals carrier balance so we don't sell numbers we can't pay for
        if ($pvaClient->hasApiKey() && !app()->environment('testing')) {
            if (!$pvaClient->hasSufficientBalance($costUsd)) {
                throw new NumberUnavailableException("Provider capacity temporarily unavailable. Please try again shortly.");
            }
        }

        return DB::transaction(function () use (
            $user,
            $country,
            $service,
            $numberType,
            $price,
            $currency,
            $isAllServices,
            $pvaClient,
            $duration,
            $state,
            $areaCode,
            $expiresInMinutes
        ) {
            // Local inventory reuse (STR per-service only)
            $phoneNumber = null;
            $provider = null;
            $isPending = false;

            if (!$isAllServices) {
                $phoneNumber = PhoneNumber::query()
                    ->where('country_id', $country->id)
                    ->where('number_type_id', $numberType->id)
                    ->where('status', 'available')
                    ->whereHas('services', fn($q) => $q->where('services.id', $service->id))
                    ->lockForUpdate()
                    ->first();
            }

            if ($phoneNumber) {
                $provider = $phoneNumber->provider;
                $phoneNumber->update(['status' => 'reserved']);

                PhoneNumberStatusHistory::create([
                    'phone_number_id' => $phoneNumber->id,
                    'old_status' => 'available',
                    'new_status' => 'reserved',
                    'reason' => 'Allocated from local inventory for verification session',
                    'created_at' => now(),
                ]);
            } else {
                // Provider allocation via PVADeals
                $defaultCode = config('services.default_provider', 'pvadeals');
                $provider = Provider::where('code', $defaultCode)->where('status', 'active')->first()
                    ?? Provider::where('status', 'active')->orderBy('priority')->first()
                    ?? Provider::firstOrCreate(
                        ['code' => 'pvadeals'],
                        ['name' => 'PVADeals v3 API Gateway', 'type' => 'aggregator', 'priority' => 1, 'status' => 'active']
                    );

                if ($isAllServices) {
                    $normCountry = $pvaClient->normalizeAllServicesCountry($country->iso2 ?? $country->iso3 ?? 'US');
                    $selectedDuration = $duration ?? config("numbers.all_services_countries.{$normCountry}.default_duration", 30);

                    if ($pvaClient->hasApiKey() && !app()->environment('testing')) {
                        $purchaseResult = $pvaClient->purchaseLtr('ALL_SERVICES', $selectedDuration, $normCountry);

                        if (!($purchaseResult['success'] ?? false)) {
                            $msg = $purchaseResult['message'] ?? 'Unable to allocate all-services number from provider.';
                            throw new NumberUnavailableException("PVADeals: {$msg}");
                        }

                        $reqData = $purchaseResult['data'] ?? $purchaseResult;
                        $reqId = $reqData['_id'] ?? $reqData['requestId'] ?? ('LTR_' . Str::ulid());
                        $allocatedPhone = $reqData['number'] ?? null;

                        if (empty($allocatedPhone) || $pvaClient->isPendingDelivery($purchaseResult)) {
                            // Asynchronous PENDING delivery
                            $allocatedPhone = 'PENDING_' . $reqId;
                            $isPending = true;
                        }

                        $phoneNumber = PhoneNumber::create([
                            'provider_id' => $provider->id,
                            'country_id' => $country->id,
                            'number_type_id' => $numberType->id,
                            'phone_number' => $allocatedPhone,
                            'provider_number_id' => (string) $reqId,
                            'status' => $isPending ? 'pending' : 'reserved',
                            'capabilities' => ['sms', 'otp', 'ltr'],
                            'last_synced_at' => now(),
                        ]);
                    } else {
                        // Testing fallback
                        $phoneNumber = PhoneNumber::create([
                            'provider_id' => $provider->id,
                            'country_id' => $country->id,
                            'number_type_id' => $numberType->id,
                            'phone_number' => '+1' . mt_rand(2000000000, 9999999999),
                            'provider_number_id' => 'LTR_TEST_' . Str::random(16),
                            'status' => 'reserved',
                            'capabilities' => ['sms', 'otp', 'ltr'],
                            'last_synced_at' => now(),
                        ]);
                    }
                } elseif ($duration && $duration > 0 && $pvaClient->hasApiKey() && !app()->environment('testing')) {
                    // Provision LTR line via PVADeals /purchase-ltr
                    $pvaAdapter = new \App\Infrastructure\Providers\PvaDealsAdapter();
                    $pvaService = $pvaAdapter->resolvePvaService($country, $service);
                    $serviceId = $pvaService['_id'] ?? null;
                    if (!$serviceId) {
                        throw new NumberUnavailableException("PVADeals: '{$service->name}' is currently unavailable for rental in {$country->name}.");
                    }

                    $purchaseResult = $pvaClient->purchaseLtr($serviceId, $duration, null, $areaCode, $state);
                    if (!($purchaseResult['success'] ?? false)) {
                        $msg = $purchaseResult['message'] ?? 'Unable to allocate rental number from provider.';
                        throw new NumberUnavailableException("PVADeals: {$msg}");
                    }

                    $reqData = $purchaseResult['data'] ?? $purchaseResult;
                    $reqId = $reqData['_id'] ?? $reqData['requestId'] ?? ('LTR_' . Str::ulid());
                    $allocatedPhone = $reqData['number'] ?? ($reqData['phoneNumber'] ?? '');
                    if (!empty($allocatedPhone) && !str_starts_with($allocatedPhone, '+')) {
                        $allocatedPhone = '+' . ltrim($allocatedPhone, '+');
                    }

                    $phoneNumber = PhoneNumber::create([
                        'provider_id' => $provider->id,
                        'country_id' => $country->id,
                        'number_type_id' => $numberType->id,
                        'phone_number' => $allocatedPhone ?: '+1' . mt_rand(2000000000, 9999999999),
                        'provider_number_id' => (string) $reqId,
                        'status' => 'reserved',
                        'capabilities' => ['sms', 'otp', 'ltr'],
                        'last_synced_at' => now(),
                    ]);
                } else {
                    $adapter = $this->providerFactory->make($provider);
                    $providerDto = $adapter->reserveNumber($country, $service, $state);

                    $phoneNumber = PhoneNumber::create([
                        'provider_id' => $provider->id,
                        'country_id' => $country->id,
                        'number_type_id' => $numberType->id,
                        'phone_number' => $providerDto->phoneNumber,
                        'provider_number_id' => $providerDto->providerNumberId,
                        'status' => 'reserved',
                        'capabilities' => $providerDto->capabilities,
                        'last_synced_at' => now(),
                    ]);
                }

                $phoneNumber->services()->syncWithoutDetaching([$service->id]);

                PhoneNumberStatusHistory::create([
                    'phone_number_id' => $phoneNumber->id,
                    'old_status' => 'none',
                    'new_status' => $isPending ? 'pending' : 'reserved',
                    'reason' => "Provisioned via provider {$provider->name}",
                    'created_at' => now(),
                ]);
            }

            // 4. Create Order & OrderItem
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD_' . Str::ulid(),
                'status' => $isPending ? 'pending' : 'active',
                'subtotal' => $price,
                'discount_amount' => 0.0000,
                'tax_amount' => 0.0000,
                'total_amount' => $price,
                'currency_code' => $currency->code,
                'idempotency_key' => 'IDEM_' . Str::ulid(),
            ]);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'phone_number_id' => $phoneNumber->id,
                'service_id' => $service->id,
                'country_id' => $country->id,
                'number_type_id' => $numberType->id,
                'quantity' => 1,
                'unit_price' => $price,
                'total_price' => $price,
            ]);

            // 5. Hold wallet funds atomically (funds are never debited on failure)
            $this->walletService->placeHold(
                $user,
                $price,
                'HOLD_ITEM_' . $orderItem->id,
                $expiresInMinutes * 60
            );

            // 6. Create NumberReservation
            NumberReservation::create([
                'phone_number_id' => $phoneNumber->id,
                'user_id' => $user->id,
                'order_item_id' => $orderItem->id,
                'status' => $isPending ? 'pending' : 'reserved',
                'reserved_at' => now(),
                'expires_at' => now()->addMinutes($expiresInMinutes),
                'released_at' => null,
            ]);

            // 7. Create VerificationSession
            return VerificationSession::create([
                'user_id' => $user->id,
                'order_item_id' => $orderItem->id,
                'phone_number_id' => $phoneNumber->id,
                'service_id' => $service->id,
                'provider_id' => $provider?->id,
                'status' => $isPending ? 'pending' : 'active',
                'verification_code' => null,
                'expires_at' => now()->addMinutes($expiresInMinutes),
                'completed_at' => null,
            ]);
        });
    }
}
