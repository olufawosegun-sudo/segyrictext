<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletDepositCreditedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Wallet $wallet,
        public WalletTransaction $transaction
    ) {}

    public function envelope(): Envelope
    {
        $symbol = $this->wallet->currency?->symbol ?? '₦';
        $amount = number_format($this->transaction->amount, 2);

        return new Envelope(
            subject: "Deposit Confirmed — {$symbol}{$amount} Added to Your Verixa Wallet",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wallet-credited',
        );
    }
}
