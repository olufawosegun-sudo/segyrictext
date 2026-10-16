<?php

namespace App\Application\Services;

use App\Domain\Wallet\Events\WalletCredited;
use App\Domain\Wallet\Events\WalletDebited;
use App\Domain\Wallet\Events\WalletHoldCaptured;
use App\Domain\Wallet\Events\WalletHoldCreated;
use App\Domain\Wallet\Events\WalletHoldReleased;
use App\Domain\Wallet\Exceptions\InsufficientBalanceException;
use App\Domain\Wallet\Exceptions\InvalidHoldStateException;
use App\Domain\Wallet\Exceptions\WalletSuspendedException;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHold;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Retrieve or initialize a user's wallet in the given or signup country currency.
     */
    public function getOrCreateUserWallet(User|int $user, ?Currency $currency = null): Wallet
    {
        $userModel = $user instanceof User ? $user : User::find($user);
        $userId = $userModel ? $userModel->id : (int) $user;

        // If user already has a wallet and no specific currency requested, return existing wallet
        if (!$currency) {
            $existingWallet = Wallet::where('user_id', $userId)->with('currency')->first();
            if ($existingWallet) {
                return $existingWallet;
            }

            // Resolve currency based on user's signup country / currency preference
            $currencyCode = $userModel ? $userModel->resolveSignupCurrencyCode() : 'USD';

            $currency = Currency::where('code', $currencyCode)->first();

            if (!$currency) {
                $currencyDefaults = [
                    'NGN' => ['name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2],
                    'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'decimal_places' => 2],
                    'GHS' => ['name' => 'Ghanaian Cedi', 'symbol' => 'GH₵', 'decimal_places' => 2],
                    'ZAR' => ['name' => 'South African Rand', 'symbol' => 'R', 'decimal_places' => 2],
                    'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'USh', 'decimal_places' => 0],
                    'EGP' => ['name' => 'Egyptian Pound', 'symbol' => 'E£', 'decimal_places' => 2],
                    'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2],
                    'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'decimal_places' => 2],
                    'EUR' => ['name' => 'Euro', 'symbol' => '€', 'decimal_places' => 2],
                ];

                $meta = $currencyDefaults[$currencyCode] ?? ['name' => $currencyCode . ' Currency', 'symbol' => $currencyCode, 'decimal_places' => 2];

                $currency = Currency::create(array_merge(['code' => $currencyCode, 'is_active' => true], $meta));
            }
        }

        return Wallet::firstOrCreate(
            ['user_id' => $userId, 'currency_id' => $currency->id],
            ['balance' => 0.0000, 'status' => 'active']
        );
    }

    /**
     * Get available spendable balance (balance minus active holds).
     */
    public function getBalance(User|int|Wallet $target): float
    {
        $wallet = $this->resolveAndLockWallet($target);
        $activeHolds = (float) $wallet->holds()->where('status', 'active')->sum('amount');
        return round((float) $wallet->balance - $activeHolds, 4);
    }

    /**
     * Atomically credit funds into a wallet using ACID row-level locking.
     *
     * @throws WalletSuspendedException
     */
    public function credit(
        User|int|Wallet $target,
        float $amount,
        ?string $reference = null,
        ?string $description = null,
        ?string $sourceType = null,
        ?int $sourceId = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Credit amount must be greater than zero.");
        }

        return DB::transaction(function () use ($target, $amount, $reference, $description, $sourceType, $sourceId) {
            // Idempotency: repeating the same reference is a safe no-op
            if ($reference !== null) {
                $existing = WalletTransaction::where('reference', $reference)->first();
                if ($existing) {
                    return $existing;
                }
            }

            $wallet = $this->resolveAndLockWallet($target);

            if (!$wallet->isActive()) {
                throw new WalletSuspendedException($wallet->id, $wallet->status);
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = round($balanceBefore + $amount, 4);

            $amountKobo = (int) round($amount * 100);
            $balanceKoboBefore = (int) ($wallet->balance_kobo ?? round($balanceBefore * 100));
            $balanceKoboAfter = $balanceKoboBefore + $amountKobo;

            $wallet->update([
                'balance' => $balanceAfter,
                'balance_kobo' => $balanceKoboAfter,
            ]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'deposit',
                'amount' => $amount,
                'amount_kobo' => $amountKobo,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'balance_after_kobo' => $balanceKoboAfter,
                'reference' => $reference ?? 'TXN_' . Str::ulid(),
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description ?? 'Wallet deposit credit',
                'created_at' => now(),
            ]);

            event(new WalletCredited($wallet, $transaction));

            return $transaction;
        });
    }

    /**
     * Atomically debit funds from a wallet using ACID row-level locking.
     * Checks both the base balance and any active reservation holds.
     *
     * @throws WalletSuspendedException
     * @throws InsufficientBalanceException
     */
    public function debit(
        User|int|Wallet $target,
        float $amount,
        ?string $reference = null,
        ?string $description = null,
        ?string $sourceType = null,
        ?int $sourceId = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Debit amount must be greater than zero.");
        }

        return DB::transaction(function () use ($target, $amount, $reference, $description, $sourceType, $sourceId) {
            // Idempotency: repeating the same reference is a safe no-op
            if ($reference !== null) {
                $existing = WalletTransaction::where('reference', $reference)->first();
                if ($existing) {
                    return $existing;
                }
            }

            $wallet = $this->resolveAndLockWallet($target);

            if (!$wallet->isActive()) {
                throw new WalletSuspendedException($wallet->id, $wallet->status);
            }

            $activeHolds = (float) $wallet->holds()->where('status', 'active')->sum('amount');
            $availableBalance = round((float) $wallet->balance - $activeHolds, 4);

            if ($availableBalance < $amount) {
                throw new InsufficientBalanceException($availableBalance, $amount);
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = round($balanceBefore - $amount, 4);

            $amountKobo = (int) round($amount * 100);
            $balanceKoboBefore = (int) ($wallet->balance_kobo ?? round($balanceBefore * 100));
            $balanceKoboAfter = max(0, $balanceKoboBefore - $amountKobo);

            $wallet->update([
                'balance' => $balanceAfter,
                'balance_kobo' => $balanceKoboAfter,
            ]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'purchase',
                'amount' => $amount,
                'amount_kobo' => -$amountKobo,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'balance_after_kobo' => $balanceKoboAfter,
                'reference' => $reference ?? 'TXN_' . Str::ulid(),
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description ?? 'Wallet purchase debit',
                'created_at' => now(),
            ]);

            event(new WalletDebited($wallet, $transaction));

            return $transaction;
        });
    }

    /**
     * Ledger-based integer kobo balance query.
     */
    public function getBalanceKobo(User|int|Wallet $target): int
    {
        $wallet = $this->resolveAndLockWallet($target);
        if ($wallet->balance_kobo !== null && $wallet->balance_kobo > 0) {
            return (int) $wallet->balance_kobo;
        }
        return (int) round(((float) $wallet->balance) * 100);
    }

    /**
     * Ledger-based integer kobo debit under row lock.
     */
    public function debitKobo(
        User|int|Wallet $target,
        int $amountKobo,
        string $reference,
        ?string $description = null,
        array $meta = []
    ): WalletTransaction {
        if ($amountKobo <= 0) {
            throw new \InvalidArgumentException("Debit kobo amount must be greater than zero.");
        }

        return DB::transaction(function () use ($target, $amountKobo, $reference, $description, $meta) {
            // Idempotency: repeating the same reference is a safe no-op
            $existing = WalletTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            $wallet = $this->resolveAndLockWallet($target);

            if (!$wallet->isActive()) {
                throw new WalletSuspendedException($wallet->id, $wallet->status);
            }

            $currentKobo = (int) ($wallet->balance_kobo ?? round(((float) $wallet->balance) * 100));

            if ($currentKobo < $amountKobo) {
                throw new InsufficientBalanceException(round($currentKobo / 100, 2), round($amountKobo / 100, 2));
            }

            $newKobo = $currentKobo - $amountKobo;
            $newNgn = round($newKobo / 100, 4);
            $amountNgn = round($amountKobo / 100, 4);

            $wallet->update([
                'balance' => $newNgn,
                'balance_kobo' => $newKobo,
            ]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'purchase',
                'amount' => $amountNgn,
                'amount_kobo' => -$amountKobo,
                'balance_before' => round($currentKobo / 100, 4),
                'balance_after' => $newNgn,
                'balance_after_kobo' => $newKobo,
                'reference' => $reference,
                'description' => $description ?? 'Order purchase debit',
                'meta' => $meta,
                'created_at' => now(),
            ]);

            event(new WalletDebited($wallet, $transaction));

            return $transaction;
        });
    }

    /**
     * Ledger-based integer kobo credit under row lock.
     */
    public function creditKobo(
        User|int|Wallet $target,
        int $amountKobo,
        string $reference,
        ?string $description = null,
        array $meta = []
    ): WalletTransaction {
        if ($amountKobo <= 0) {
            throw new \InvalidArgumentException("Credit kobo amount must be greater than zero.");
        }

        return DB::transaction(function () use ($target, $amountKobo, $reference, $description, $meta) {
            // Idempotency: repeating the same reference is a safe no-op
            $existing = WalletTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            $wallet = $this->resolveAndLockWallet($target);

            if (!$wallet->isActive()) {
                throw new WalletSuspendedException($wallet->id, $wallet->status);
            }

            $currentKobo = (int) ($wallet->balance_kobo ?? round(((float) $wallet->balance) * 100));
            $newKobo = $currentKobo + $amountKobo;
            $newNgn = round($newKobo / 100, 4);
            $amountNgn = round($amountKobo / 100, 4);

            $wallet->update([
                'balance' => $newNgn,
                'balance_kobo' => $newKobo,
            ]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'deposit',
                'amount' => $amountNgn,
                'amount_kobo' => $amountKobo,
                'balance_before' => round($currentKobo / 100, 4),
                'balance_after' => $newNgn,
                'balance_after_kobo' => $newKobo,
                'reference' => $reference,
                'description' => $description ?? 'Wallet deposit credit',
                'meta' => $meta,
                'created_at' => now(),
            ]);

            event(new WalletCredited($wallet, $transaction));

            return $transaction;
        });
    }

    /**
     * Ledger-based integer kobo refund under row lock.
     */
    public function refundKobo(
        User|int|Wallet $target,
        int $amountKobo,
        string $reference,
        ?string $description = null,
        array $meta = []
    ): WalletTransaction {
        if ($amountKobo <= 0) {
            throw new \InvalidArgumentException("Refund kobo amount must be greater than zero.");
        }

        return DB::transaction(function () use ($target, $amountKobo, $reference, $description, $meta) {
            // Idempotency: repeating the same reference is a safe no-op
            $existing = WalletTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            $wallet = $this->resolveAndLockWallet($target);

            if (!$wallet->isActive()) {
                throw new WalletSuspendedException($wallet->id, $wallet->status);
            }

            $currentKobo = (int) ($wallet->balance_kobo ?? round(((float) $wallet->balance) * 100));
            $newKobo = $currentKobo + $amountKobo;
            $newNgn = round($newKobo / 100, 4);
            $amountNgn = round($amountKobo / 100, 4);

            $wallet->update([
                'balance' => $newNgn,
                'balance_kobo' => $newKobo,
            ]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'refund',
                'amount' => $amountNgn,
                'amount_kobo' => $amountKobo,
                'balance_before' => round($currentKobo / 100, 4),
                'balance_after' => $newNgn,
                'balance_after_kobo' => $newKobo,
                'reference' => $reference,
                'description' => $description ?? 'Order failure refund',
                'meta' => $meta,
                'created_at' => now(),
            ]);

            event(new WalletCredited($wallet, $transaction));

            return $transaction;
        });
    }

    /**
     * Place a temporary reservation hold on wallet funds.
     * The funds remain in the wallet but cannot be spent until captured or released.
     *
     * @throws WalletSuspendedException
     * @throws InsufficientBalanceException
     */
    public function placeHold(
        User|int|Wallet $target,
        float $amount,
        ?string $reference = null,
        ?int $expiresInSeconds = 1200
    ): WalletHold {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Hold amount must be greater than zero.");
        }

        return DB::transaction(function () use ($target, $amount, $reference, $expiresInSeconds) {
            $wallet = $this->resolveAndLockWallet($target);

            if (!$wallet->isActive()) {
                throw new WalletSuspendedException($wallet->id, $wallet->status);
            }

            $activeHolds = (float) $wallet->holds()->where('status', 'active')->sum('amount');
            $availableBalance = round((float) $wallet->balance - $activeHolds, 4);

            if ($availableBalance < $amount) {
                throw new InsufficientBalanceException($availableBalance, $amount);
            }

            $hold = WalletHold::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'amount' => $amount,
                'reference' => $reference ?? 'HOLD_' . Str::ulid(),
                'status' => 'active',
                'expires_at' => $expiresInSeconds ? now()->addSeconds($expiresInSeconds) : null,
                'released_at' => null,
            ]);

            event(new WalletHoldCreated($wallet, $hold));

            return $hold;
        });
    }

    /**
     * Atomically capture a reservation hold into a finalized debit transaction.
     *
     * @throws InvalidHoldStateException
     * @throws WalletSuspendedException
     */
    public function captureHold(
        WalletHold|int|string $targetHold,
        ?string $description = null
    ): WalletTransaction {
        return DB::transaction(function () use ($targetHold, $description) {
            $hold = $this->resolveAndLockHold($targetHold);

            if ($hold->status !== 'active') {
                throw new InvalidHoldStateException($hold->id, $hold->status, 'capture');
            }

            $wallet = Wallet::where('id', $hold->wallet_id)->lockForUpdate()->firstOrFail();

            if (!$wallet->isActive()) {
                throw new WalletSuspendedException($wallet->id, $wallet->status);
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = round($balanceBefore - (float) $hold->amount, 4);

            $wallet->update(['balance' => $balanceAfter]);

            $hold->update([
                'status' => 'captured',
                'released_at' => now(),
            ]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'type' => 'purchase',
                'amount' => (float) $hold->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference' => 'CAPT_' . Str::ulid(),
                'source_type' => 'wallet_hold',
                'source_id' => $hold->id,
                'description' => $description ?? "Captured hold #{$hold->reference}",
                'created_at' => now(),
            ]);

            event(new WalletHoldCaptured($wallet, $hold, $transaction));

            return $transaction;
        });
    }

    /**
     * Atomically release a hold without deducting any funds.
     *
     * @throws InvalidHoldStateException
     */
    public function releaseHold(
        WalletHold|int|string $targetHold,
        ?string $reason = null
    ): void {
        DB::transaction(function () use ($targetHold, $reason) {
            $hold = $this->resolveAndLockHold($targetHold);

            if ($hold->status !== 'active') {
                throw new InvalidHoldStateException($hold->id, $hold->status, 'release');
            }

            $wallet = Wallet::where('id', $hold->wallet_id)->lockForUpdate()->firstOrFail();

            $hold->update([
                'status' => 'released',
                'released_at' => now(),
            ]);

            event(new WalletHoldReleased($wallet, $hold, $reason));
        });
    }

    /**
     * Sweep and release all expired holds across the system (e.g. from a scheduler cron).
     */
    public function releaseExpiredHolds(): int
    {
        $expiredHolds = WalletHold::expired()->get();
        $count = 0;

        foreach ($expiredHolds as $hold) {
            try {
                $this->releaseHold($hold, 'Hold expired automatically');
                $count++;
            } catch (\Throwable) {
                // Continue with next hold
            }
        }

        return $count;
    }

    /**
     * Resolve target into a locked Wallet row.
     */
    protected function resolveAndLockWallet(User|int|Wallet $target): Wallet
    {
        if ($target instanceof Wallet) {
            return Wallet::where('id', $target->id)->lockForUpdate()->firstOrFail();
        }

        if ($target instanceof User) {
            return $this->getOrCreateUserWallet($target->id);
        }

        // Numeric target: check if wallet ID or user ID
        $wallet = Wallet::where('id', $target)->lockForUpdate()->first();
        if ($wallet) {
            return $wallet;
        }

        return $this->getOrCreateUserWallet($target);
    }

    /**
     * Resolve target into a locked WalletHold row.
     */
    protected function resolveAndLockHold(WalletHold|int|string $target): WalletHold
    {
        if ($target instanceof WalletHold) {
            return WalletHold::where('id', $target->id)->lockForUpdate()->firstOrFail();
        }

        if (is_numeric($target)) {
            return WalletHold::where('id', $target)->lockForUpdate()->firstOrFail();
        }

        return WalletHold::where('reference', $target)->lockForUpdate()->firstOrFail();
    }
}
