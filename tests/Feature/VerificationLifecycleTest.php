<?php

namespace Tests\Feature;

use App\Application\Services\NumberAllocationService;
use App\Application\Services\SmsIngestionService;
use App\Application\Services\VerificationLifecycleService;
use App\Application\Services\WalletService;
use App\Domain\DTOs\IncomingSmsDTO;
use App\Domain\Verification\Services\OtpExtractorInterface;
use App\Models\Country;
use App\Models\Currency;
use App\Models\NumberType;
use App\Models\PhoneNumber;
use App\Models\PricingRule;
use App\Models\Provider;
use App\Models\Service;
use App\Models\SmsMessage;
use App\Models\User;
use App\Models\VerificationSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerificationLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected NumberAllocationService $allocationService;
    protected SmsIngestionService $smsIngestionService;
    protected VerificationLifecycleService $lifecycleService;
    protected WalletService $walletService;
    protected OtpExtractorInterface $otpExtractor;

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
        $this->smsIngestionService = app(SmsIngestionService::class);
        $this->lifecycleService = app(VerificationLifecycleService::class);
        $this->walletService = app(WalletService::class);
        $this->otpExtractor = app(OtpExtractorInterface::class);

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'verif_user@example.com'],
            ['name' => 'Verify Test User', 'password' => Hash::make('password'), 'status' => 'active']
        );

        $this->country = Country::firstOrCreate(
            ['iso2' => 'US'],
            ['name' => 'United States', 'iso3' => 'USA', 'dial_code' => '+1', 'currency_code' => 'USD', 'is_active' => true]
        );

        $this->service = Service::firstOrCreate(
            ['code' => 'telegram'],
            ['name' => 'Telegram Messenger', 'description' => 'Telegram auth', 'is_active' => true]
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

    public function test_extracts_otp_from_diverse_carrier_sms_templates(): void
    {
        $templates = [
            'G-482910 is your Google verification code.' => '482910',
            'Your WhatsApp code is: 394-102. Do not share it.' => '394102',
            'Telegram code: 82014. You can also tap on this link to log in:' => '82014',
            'Your OpenAI verification code is 619284.' => '619284',
            'Uber: 7391 is your security code. Never share this code.' => '7391',
            'Tu codigo de verificacion es 849201' => '849201',
            'Votre code de verification est 503928' => '503928',
            'Use 928374 to verify your Steam account.' => '928374',
        ];

        foreach ($templates as $message => $expectedOtp) {
            $extracted = $this->otpExtractor->extractCode($message);
            $this->assertEquals(
                $expectedOtp,
                $extracted,
                "Failed extracting expected OTP [{$expectedOtp}] from template: [{$message}]"
            );
        }
    }

    public function test_end_to_end_verification_success_captures_wallet_hold(): void
    {
        // 1. Credit wallet with $10.00
        $this->walletService->credit($this->user, 10.00, 'TEST_CREDIT_E2E');

        // 2. Allocate number ($0.85 held)
        $session = $this->allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );

        $wallet = $this->walletService->getOrCreateUserWallet($this->user);
        $this->assertEquals(10.00, (float) $wallet->balance);
        $this->assertEquals(9.15, $wallet->available_balance);
        $this->assertEquals('active', $session->status);

        $allocatedPhone = $session->phoneNumber;
        $this->assertEquals('reserved', $allocatedPhone->status);

        // 3. Ingest SMS from carrier
        $smsDto = new IncomingSmsDTO(
            phoneNumber: $allocatedPhone->phone_number,
            message: 'Telegram code: 58291. Do not give this code to anyone.',
            fromNumber: 'Telegram',
            providerCode: 'twilio',
            providerMessageId: 'TW_MSG_TEST_998877'
        );

        $messageRecord = $this->smsIngestionService->ingest($smsDto);

        // 4. Assert SMS and OTP record
        $this->assertNotNull($messageRecord);
        $this->assertEquals($allocatedPhone->id, $messageRecord->phone_number_id);
        $this->assertEquals($session->id, $messageRecord->verification_session_id);

        // Session must now be completed with the extracted OTP
        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals('58291', $session->verification_code);
        $this->assertNotNull($session->completed_at);

        // Wallet hold must be captured into a debit of 0.85
        $wallet->refresh();
        $this->assertEquals(9.15, (float) $wallet->balance);
        $this->assertEquals(9.15, $wallet->available_balance);

        // Phone number status updated to used
        $allocatedPhone->refresh();
        $this->assertEquals('used', $allocatedPhone->status);

        // Order marked completed
        $this->assertEquals('completed', $session->orderItem->order->fresh()->status);
    }

    public function test_user_can_cancel_active_session_and_release_wallet_hold(): void
    {
        $this->walletService->credit($this->user, 10.00, 'TEST_CREDIT_CANCEL');

        $session = $this->allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );

        $this->assertEquals(9.15, $this->walletService->getOrCreateUserWallet($this->user)->available_balance);

        // Cancel session
        $cancelled = $this->lifecycleService->cancelSession($session, 'Changed mind');
        $this->assertTrue($cancelled);

        $session->refresh();
        $this->assertEquals('cancelled', $session->status);

        // Wallet balance restored to full $10.00 available
        $wallet = $this->walletService->getOrCreateUserWallet($this->user);
        $this->assertEquals(10.00, (float) $wallet->balance);
        $this->assertEquals(10.00, $wallet->available_balance);

        // Phone number returned to available
        $this->assertEquals('available', $session->phoneNumber->fresh()->status);

        // Order marked cancelled
        $this->assertEquals('cancelled', $session->orderItem->order->fresh()->status);
    }

    public function test_expired_sessions_are_auto_refunded_by_cron_command(): void
    {
        $this->walletService->credit($this->user, 10.00, 'TEST_CREDIT_EXPIRE');

        $session = $this->allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );

        // Simulate 20 minutes expiration
        $session->update(['expires_at' => now()->subMinutes(5)]);

        // Run artisan verification:expire command
        $exitCode = Artisan::call('verification:expire');
        $this->assertEquals(0, $exitCode);

        $session->refresh();
        $this->assertEquals('expired', $session->status);

        // Available balance restored
        $wallet = $this->walletService->getOrCreateUserWallet($this->user);
        $this->assertEquals(10.00, (float) $wallet->balance);
        $this->assertEquals(10.00, $wallet->available_balance);

        // Phone number back to available
        $this->assertEquals('available', $session->phoneNumber->fresh()->status);
    }

    public function test_sms_idempotency_prevents_duplicate_processing(): void
    {
        $this->walletService->credit($this->user, 10.00, 'TEST_CREDIT_IDEM');

        $session = $this->allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );

        $smsDto = new IncomingSmsDTO(
            phoneNumber: $session->phoneNumber->phone_number,
            message: 'Your verification code is 112233',
            fromNumber: 'Telegram',
            providerCode: 'twilio',
            providerMessageId: 'UNIQUE_ID_1001'
        );

        // First delivery
        $msg1 = $this->smsIngestionService->ingest($smsDto);
        $this->assertNotNull($msg1);

        // Duplicate delivery with same providerMessageId
        $msg2 = $this->smsIngestionService->ingest($smsDto);
        $this->assertEquals($msg1->id, $msg2->id);

        // Assert only 1 SMS message in database
        $this->assertEquals(1, SmsMessage::where('provider_message_id', 'UNIQUE_ID_1001')->count());

        // Balance should have been deducted only once ($9.15, not $8.30)
        $wallet = $this->walletService->getOrCreateUserWallet($this->user);
        $this->assertEquals(9.15, (float) $wallet->balance);
    }
}
