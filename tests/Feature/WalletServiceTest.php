<?php

namespace Tests\Feature;

use App\Application\Services\WalletService;
use App\Domain\Wallet\Events\WalletCredited;
use App\Domain\Wallet\Events\WalletDebited;
use App\Domain\Wallet\Events\WalletHoldCaptured;
use App\Domain\Wallet\Events\WalletHoldCreated;
use App\Domain\Wallet\Events\WalletHoldReleased;
use App\Domain\Wallet\Exceptions\InsufficientBalanceException;
use App\Domain\Wallet\Exceptions\InvalidHoldStateException;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHold;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;
    protected User $user;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletService = app(WalletService::class);

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'testuser@example.com'],
            ['name' => 'Test User', 'password' => Hash::make('secret'), 'status' => 'active']
        );
    }

    public function test_can_credit_wallet_atomically(): void
    {
        Event::fake([WalletCredited::class]);

        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $initialBalance = (float) $wallet->balance;

        $txn = $this->walletService->credit($this->user, 50.00, 'TEST_CREDIT_01', 'Test top-up');

        $wallet->refresh();

        $this->assertEquals($initialBalance + 50.00, (float) $wallet->balance);
        $this->assertEquals($initialBalance, (float) $txn->balance_before);
        $this->assertEquals($initialBalance + 50.00, (float) $txn->balance_after);
        $this->assertEquals('deposit', $txn->type instanceof \App\Domain\Enums\TransactionType ? $txn->type->value : $txn->type);

        Event::assertDispatched(WalletCredited::class);
    }

    public function test_can_debit_wallet_atomically(): void
    {
        Event::fake([WalletDebited::class]);

        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['balance' => 100.00]);

        $txn = $this->walletService->debit($this->user, 35.50, 'TEST_DEBIT_01', 'Number purchase');

        $wallet->refresh();

        $this->assertEquals(64.50, (float) $wallet->balance);
        $this->assertEquals(100.00, (float) $txn->balance_before);
        $this->assertEquals(64.50, (float) $txn->balance_after);

        Event::assertDispatched(WalletDebited::class);
    }

    public function test_debit_throws_exception_on_insufficient_funds(): void
    {
        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['balance' => 10.00]);

        $this->expectException(InsufficientBalanceException::class);

        $this->walletService->debit($this->user, 25.00);
    }

    public function test_holds_block_spending_even_with_base_balance(): void
    {
        Event::fake([WalletHoldCreated::class]);

        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['balance' => 50.00]);

        // Place a hold of 30.00
        $hold = $this->walletService->placeHold($this->user, 30.00, 'HOLD_TEST_01');

        $this->assertEquals('active', $hold->status);
        $this->assertEquals(50.00, (float) $wallet->balance); // base balance unchanged
        $this->assertEquals(20.00, $wallet->available_balance); // available is 50 - 30 = 20

        Event::assertDispatched(WalletHoldCreated::class);

        // Attempting to spend 25.00 must fail because available balance is only 20.00
        $this->expectException(InsufficientBalanceException::class);
        $this->walletService->debit($this->user, 25.00);
    }

    public function test_capturing_hold_deducts_balance_and_closes_hold(): void
    {
        Event::fake([WalletHoldCaptured::class]);

        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['balance' => 50.00]);

        $hold = $this->walletService->placeHold($this->user, 20.00);

        $txn = $this->walletService->captureHold($hold, 'OTP received capture');

        $wallet->refresh();
        $hold->refresh();

        $this->assertEquals('captured', $hold->status);
        $this->assertNotNull($hold->released_at);
        $this->assertEquals(30.00, (float) $wallet->balance);
        $this->assertEquals(30.00, $wallet->available_balance);
        $this->assertEquals(20.00, (float) $txn->amount);

        Event::assertDispatched(WalletHoldCaptured::class);
    }

    public function test_releasing_hold_restores_available_balance(): void
    {
        Event::fake([WalletHoldReleased::class]);

        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['balance' => 50.00]);

        $hold = $this->walletService->placeHold($this->user, 25.00);
        $this->assertEquals(25.00, $wallet->available_balance);

        $this->walletService->releaseHold($hold, 'Verification timed out');

        $wallet->refresh();
        $hold->refresh();

        $this->assertEquals('released', $hold->status);
        $this->assertEquals(50.00, (float) $wallet->balance);
        $this->assertEquals(50.00, $wallet->available_balance);

        Event::assertDispatched(WalletHoldReleased::class);
    }

    public function test_cannot_capture_already_released_hold(): void
    {
        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['balance' => 50.00]);

        $hold = $this->walletService->placeHold($this->user, 10.00);
        $this->walletService->releaseHold($hold);

        $this->expectException(InvalidHoldStateException::class);
        $this->walletService->captureHold($hold);
    }

    public function test_suspended_wallet_blocks_credit_and_debit(): void
    {
        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['status' => 'suspended', 'balance' => 100.00]);

        $this->expectException(\App\Domain\Wallet\Exceptions\WalletSuspendedException::class);
        $this->walletService->debit($this->user, 10.00);
    }

    public function test_release_expired_holds_sweep(): void
    {
        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->currency);
        $wallet->update(['balance' => 100.00]);

        // Create an expired hold (expired 5 minutes ago)
        $expiredHold = WalletHold::create([
            'wallet_id' => $wallet->id,
            'user_id' => $this->user->id,
            'amount' => 15.00,
            'reference' => 'HOLD_EXP_01',
            'status' => 'active',
            'expires_at' => now()->subMinutes(5),
        ]);

        // Create an active future hold
        $activeHold = $this->walletService->placeHold($this->user, 20.00, 'HOLD_ACT_01', 3600);

        $releasedCount = $this->walletService->releaseExpiredHolds();

        $this->assertEquals(1, $releasedCount);

        $expiredHold->refresh();
        $activeHold->refresh();

        $this->assertEquals('released', $expiredHold->status);
        $this->assertEquals('active', $activeHold->status);
    }
}

