<?php

namespace Tests\Feature;

use App\Application\Services\NumberAllocationService;
use App\Application\Services\NumberReservationService;
use App\Application\Services\WalletService;
use App\Domain\Number\Exceptions\NumberUnavailableException;
use App\Domain\Wallet\Exceptions\InsufficientBalanceException;
use App\Models\Country;
use App\Models\Currency;
use App\Models\NumberType;
use App\Models\PhoneNumber;
use App\Models\PricingRule;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NumberAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected NumberAllocationService $allocationService;
    protected NumberReservationService $reservationService;
    protected WalletService $walletService;
    protected User $user;
    protected Country $country;
    protected Service $service;
    protected NumberType $numberType;
    protected Provider $provider;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->allocationService = app(NumberAllocationService::class);
        $this->reservationService = app(NumberReservationService::class);
        $this->walletService = app(WalletService::class);

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Customer User', 'password' => Hash::make('password'), 'status' => 'active']
        );

        $this->country = Country::firstOrCreate(
            ['iso2' => 'US'],
            ['name' => 'United States', 'iso3' => 'USA', 'dial_code' => '+1', 'currency_code' => 'USD', 'is_active' => true]
        );

        $this->service = Service::firstOrCreate(
            ['code' => 'whatsapp'],
            ['name' => 'WhatsApp', 'description' => 'WhatsApp registration', 'is_active' => true]
        );

        $this->numberType = NumberType::firstOrCreate(
            ['code' => 'mobile'],
            ['name' => 'Mobile Cellular', 'is_active' => true]
        );

        $this->provider = Provider::firstOrCreate(
            ['code' => 'twilio'],
            ['name' => 'Twilio Telecom Carrier', 'type' => 'aggregator', 'priority' => 1, 'status' => 'active']
        );

        PricingRule::firstOrCreate(
            [
                'country_id' => $this->country->id,
                'service_id' => $this->service->id,
            ],
            [
                'purchase_price' => 0.40,
                'selling_price' => 0.85,
                'currency_code' => 'USD',
                'priority' => 1,
                'is_active' => true,
            ]
        );
    }

    public function test_can_allocate_number_via_provider_with_wallet_hold(): void
    {
        // Credit the customer's wallet with $10
        $this->walletService->credit($this->user, 10.00, 'TEST_DEPOSIT_01');

        $session = $this->allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );

        $this->assertNotNull($session->id);
        $this->assertEquals('active', $session->status);
        $this->assertEquals($this->user->id, $session->user_id);
        $this->assertEquals($this->service->id, $session->service_id);
        $this->assertNotNull($session->phone_number_id);

        // Check wallet hold: 0.85 should be held
        $wallet = $this->walletService->getOrCreateUserWallet($this->user);
        $this->assertEquals(10.00, (float) $wallet->balance);
        $this->assertEquals(9.15, $wallet->available_balance);

        // Verify order created
        $this->assertNotNull($session->orderItem);
        $this->assertNotNull($session->orderItem->order);
        $this->assertEquals(0.85, (float) $session->orderItem->unit_price);
        $this->assertEquals(0.85, (float) $session->orderItem->order->total_amount);

        // Verify phone number created and reserved
        $phoneNumber = $session->phoneNumber;
        $this->assertEquals('reserved', $phoneNumber->status);
    }

    public function test_allocation_fails_when_user_has_insufficient_balance(): void
    {
        // Wallet starts at 0 balance
        $this->expectException(InsufficientBalanceException::class);

        $this->allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );
    }

    public function test_can_allocate_from_existing_local_inventory(): void
    {
        // Add a pre-existing available phone number
        $existingNumber = PhoneNumber::create([
            'provider_id' => $this->provider->id,
            'country_id' => $this->country->id,
            'number_type_id' => $this->numberType->id,
            'phone_number' => '+12025550199',
            'provider_number_id' => 'PN_EXISTING_01',
            'status' => 'available',
        ]);
        $existingNumber->services()->sync([$this->service->id]);

        $this->walletService->credit($this->user, 5.00);

        $session = $this->allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );

        // Should have reused the local number
        $this->assertEquals($existingNumber->id, $session->phone_number_id);

        $existingNumber->refresh();
        $this->assertEquals('reserved', $existingNumber->status);
    }

    public function test_reservation_service_locks_number_and_blocks_concurrent_reservation(): void
    {
        $number = PhoneNumber::create([
            'provider_id' => $this->provider->id,
            'country_id' => $this->country->id,
            'number_type_id' => $this->numberType->id,
            'phone_number' => '+12025550188',
            'provider_number_id' => 'PN_TEST_LOCK_01',
            'status' => 'available',
        ]);

        $reservation = $this->reservationService->reserve($this->user, $number);

        $this->assertEquals('reserved', $reservation->status);
        $number->refresh();
        $this->assertEquals('reserved', $number->status);

        // Second attempt to reserve must throw NumberUnavailableException
        $this->expectException(NumberUnavailableException::class);
        $this->reservationService->reserve($this->user, $number);
    }

    public function test_releasing_reservation_restores_number_to_available_pool(): void
    {
        $number = PhoneNumber::create([
            'provider_id' => $this->provider->id,
            'country_id' => $this->country->id,
            'number_type_id' => $this->numberType->id,
            'phone_number' => '+12025550177',
            'provider_number_id' => 'PN_TEST_RELEASE_01',
            'status' => 'available',
        ]);

        $reservation = $this->reservationService->reserve($this->user, $number);
        $this->reservationService->release($reservation, 'Cancelled by user');

        $reservation->refresh();
        $number->refresh();

        $this->assertEquals('released', $reservation->status);
        $this->assertEquals('available', $number->status);
    }
}
