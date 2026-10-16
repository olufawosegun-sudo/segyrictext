<?php

namespace Tests\Feature;

use App\Application\Services\PaymentService;
use App\Application\Services\WalletService;
use App\Infrastructure\Payments\FlutterwaveAdapter;
use App\Infrastructure\Payments\MockPaymentAdapter;
use App\Infrastructure\Payments\PaymentGatewayFactory;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaymentArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentGatewayFactory $factory;
    protected PaymentService $paymentService;
    protected WalletService $walletService;
    protected User $user;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = app(PaymentGatewayFactory::class);
        $this->paymentService = app(PaymentService::class);
        $this->walletService = app(WalletService::class);

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'paymentuser@example.com'],
            ['name' => 'Payment Test User', 'password' => Hash::make('password'), 'status' => 'active']
        );
    }

    public function test_payment_gateway_factory_resolves_all_adapters(): void
    {
        $this->assertInstanceOf(MockPaymentAdapter::class, $this->factory->make('mock'));
        $this->assertInstanceOf(FlutterwaveAdapter::class, $this->factory->make('flutterwave'));

        $this->assertEquals('flutterwave', $this->factory->suggestGatewayForCurrency('USD'));
        $this->assertEquals('flutterwave', $this->factory->suggestGatewayForCurrency('NGN'));

        $available = $this->factory->getAvailableGateways();
        $this->assertIsArray($available);
        $this->assertNotEmpty($available);
    }

    public function test_can_initiate_payment_and_record_pending_transaction(): void
    {
        $result = $this->paymentService->initiatePayment(
            user: $this->user,
            amount: 25.50,
            currency: 'USD',
            gateway: 'mock'
        );

        $this->assertArrayHasKey('payment_id', $result);
        $this->assertArrayHasKey('reference', $result);
        $this->assertArrayHasKey('authorization_url', $result);
        $this->assertEquals('pending', $result['status']);
        $this->assertEquals(25.50, $result['amount']);

        $this->assertDatabaseHas('payments', [
            'id' => $result['payment_id'],
            'user_id' => $this->user->id,
            'amount' => 25.50,
            'currency_code' => 'USD',
            'provider' => 'mock',
            'status' => 'pending',
        ]);
    }

    public function test_payment_verification_credits_wallet_atomically_with_idempotency(): void
    {
        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $initialBalance = (float) $wallet->balance;

        // 1. Initiate payment
        $initiated = $this->paymentService->initiatePayment(
            user: $this->user,
            amount: 30.00,
            currency: 'USD',
            gateway: 'mock'
        );

        // 2. Verify payment
        $verifyResult = $this->paymentService->verifyPayment($initiated['reference']);

        $this->assertTrue($verifyResult['success']);
        $this->assertEquals('paid', $verifyResult['status']);

        // Check database
        $this->assertDatabaseHas('payments', [
            'id' => $initiated['payment_id'],
            'status' => 'paid',
        ]);

        $wallet->refresh();
        $this->assertEquals($initialBalance + 30.00, (float) $wallet->balance);

        // 3. Idempotency test: verify again, should not double-credit
        $secondVerify = $this->paymentService->verifyPayment($initiated['reference']);
        $this->assertTrue($secondVerify['success']);
        $this->assertEquals('already_paid', $secondVerify['status']);

        $wallet->refresh();
        $this->assertEquals($initialBalance + 30.00, (float) $wallet->balance);
    }

    public function test_payment_webhook_processes_and_credits_wallet(): void
    {
        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $initialBalance = (float) $wallet->balance;

        $initiated = $this->paymentService->initiatePayment(
            user: $this->user,
            amount: 15.00,
            currency: 'USD',
            gateway: 'mock'
        );

        $response = $this->postJson("/api/v1/webhooks/payments/mock", [
            'reference' => $initiated['reference'],
            'amount' => 15.00,
            'currency' => 'USD',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $wallet->refresh();
        $this->assertEquals($initialBalance + 15.00, (float) $wallet->balance);
    }

    public function test_authenticated_api_endpoints(): void
    {
        // 1. Payment Methods
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/payments/methods');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'user_currency',
            'suggested_gateway',
            'gateways',
        ]);

        // 2. Initiate Payment
        $initResponse = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/payments/initiate', [
            'amount' => 50.00,
            'gateway' => 'mock',
        ]);
        $initResponse->assertStatus(200);
        $initResponse->assertJson(['success' => true]);
        $ref = $initResponse->json('data.reference');

        // 3. Verify Payment
        $verifyResponse = $this->actingAs($this->user, 'sanctum')->getJson("/api/v1/payments/verify/{$ref}");
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertJson(['success' => true, 'status' => 'paid']);
    }
}
