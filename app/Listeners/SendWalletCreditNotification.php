<?php

namespace App\Listeners;

use App\Domain\Wallet\Events\WalletCredited;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class SendWalletCreditNotification
{
    /**
     * Handle the event.
     */
    public function handle(WalletCredited $event): void
    {
        // Never trigger native toasts or email alerts during test suite execution
        if (app()->environment('testing')) {
            return;
        }

        try {
            $wallet = $event->wallet;
            $transaction = $event->transaction;

            // Idempotency: Prevent duplicate notifications / toasts for the same transaction
            $alreadyNotified = Notification::where('user_id', $wallet->user_id)
                ->where('type', 'wallet_credit')
                ->where('data->transaction_id', $transaction->id)
                ->exists();

            if ($alreadyNotified) {
                return;
            }

            $currencySymbol = $wallet->currency?->symbol ?? '₦';
            $formattedAmount = number_format($transaction->amount, 2);
            $formattedBalance = number_format($wallet->balance, 2);

            Notification::create([
                'user_id' => $wallet->user_id,
                'type' => 'wallet_credit',
                'title' => 'Deposit Credited',
                'message' => "Your wallet was credited with {$currencySymbol}{$formattedAmount}. New balance: {$currencySymbol}{$formattedBalance}.",
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'amount' => $transaction->amount,
                    'balance_after' => $wallet->balance,
                ],
                'read_at' => null,
            ]);

            // 1. Send Email Alert to User
            $user = $wallet->user;
            if ($user && !empty($user->email)) {
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)
                        ->send(new \App\Mail\WalletDepositCreditedMail($user, $wallet, $transaction));
                    Log::info("SendWalletCreditNotification: Deposit email alert sent to {$user->email} for TXN #{$transaction->id}");
                } catch (\Throwable $me) {
                    Log::warning("SendWalletCreditNotification: Failed sending email alert to {$user->email}: " . $me->getMessage());
                }
            }

            // 2. Native Mobile Toast Notification (iOS & Android NativePHP)
            if (class_exists(\Native\Mobile\Facades\Dialog::class)) {
                try {
                    \Native\Mobile\Facades\Dialog::toast("Deposit of {$currencySymbol}{$formattedAmount} credited successfully!");
                } catch (\Throwable $ne) {
                    // Ignore if bridge call unavailable
                }
            }

            Log::info("SendWalletCreditNotification: Notification recorded for User #{$wallet->user_id}, TXN #{$transaction->id}");
        } catch (\Throwable $e) {
            Log::error("SendWalletCreditNotification failed: " . $e->getMessage());
        }
    }
}
