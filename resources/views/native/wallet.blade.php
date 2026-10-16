@extends('native.layouts.mobile')

@section('title', 'Wallet')
@section('header_title', 'Wallet')
@section('back_url', route('mobile.home'))

@section('header_actions')
    <button type="button" onclick="openDepositModal()" class="btn btn-primary btn-sm blitz-btn" style="padding: 8px 14px; font-size: 12px; background: #7C3AED; border: none; border-radius: 10px; font-weight: 700; color: #fff;">
        + Deposit
    </button>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 10px;">

    <!-- Balance Hero -->
    <div class="balance-hero fade-in">
        <div class="balance-label">Available Balance ({{ $wallet->currency->code ?? 'NGN' }})</div>
        <div class="balance-amount">{{ $wallet->currency->symbol ?? '₦' }}<span id="native-wallet-balance-num">{{ number_format($wallet->available_balance, $wallet->currency->decimal_places ?? 2) }}</span> <span style="font-size: 14px; font-weight: 600; color: #94a3b8;">{{ $wallet->currency->code ?? 'NGN' }}</span></div>
        <div class="balance-sub">
            @if($wallet->active_holds_total > 0)
                <span style="color: #fbbf24;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align: -1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->active_holds_total, $wallet->currency->decimal_places ?? 2) }} held
                </span>
                <span>•</span>
            @endif
            <span>Total: {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->balance, $wallet->currency->decimal_places ?? 2) }}</span>
        </div>

        <!-- Quick deposit CTA -->
        <div style="margin-top: 14px; position: relative; z-index: 2;">
            <button type="button" onclick="openDepositModal()" class="btn btn-sm blitz-btn" style="width: 100%; background: rgba(16, 185, 129, 0.15); border: 1.5px solid rgba(16, 185, 129, 0.35); color: #7C3AED; font-weight: 800; font-size: 13px; padding: 12px 14px; border-radius: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span>+ Fund Wallet Instantly</span>
            </button>
        </div>
    </div>

    <!-- Quick Funding Card (Paystack In-App Payment) -->
    <div class="card fade-in" style="margin-top: 2px; padding: 16px 18px; border-radius: 18px; background: var(--bg-card, #FFFFFF); border: 1px solid var(--border, rgba(15, 23, 42, 0.08));">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 9px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); color: #6366F1; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 800; color: var(--text-primary, #1E1B4B);">Paystack In-App Payment</div>
                    <div style="font-size: 11px; color: var(--text-secondary, #64748B);">Instant Cards • Bank Transfer • USSD</div>
                </div>
            </div>
            <span style="font-size: 10px; font-weight: 800; background: rgba(99, 102, 241, 0.12); color: #6366F1; border: 1px solid rgba(99, 102, 241, 0.25); padding: 3px 8px; border-radius: 9999px;">Instant Credit</span>
        </div>

        <!-- Quick Amount Presets -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-bottom: 12px;">
            <button type="button" class="blitz-btn" onclick="openDepositModal(1000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦1,000</button>
            <button type="button" class="blitz-btn" onclick="openDepositModal(2000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦2,000</button>
            <button type="button" class="blitz-btn" onclick="openDepositModal(5000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦5,000</button>
            <button type="button" class="blitz-btn" onclick="openDepositModal(10000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦10,000</button>
        </div>

        <button type="button" onclick="openDepositModal()" class="blitz-btn" style="width: 100%; background: linear-gradient(135deg, #7C3AED, #059669); color: #FFFFFF; border: none; border-radius: 12px; padding: 11px 14px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
            <span>Choose Amount & Pay</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>

    <!-- Transaction Stats -->
    <div class="metric-row fade-in" style="animation-delay: 0.05s;">
        <div class="metric-pill">
            <div class="metric-pill-label">Deposits</div>
            <div class="metric-pill-value" style="color: #34d399;">{{ $transactions->where('type', 'credit')->count() }}</div>
        </div>
        <div class="metric-pill">
            <div class="metric-pill-label">Charges</div>
            <div class="metric-pill-value" style="color: #f87171;">{{ $transactions->where('type', 'debit')->count() }}</div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="fade-in" style="animation-delay: 0.1s;">
        <div class="section-label">Transaction Ledger</div>

        <div class="card">
            <div style="padding: 4px 0;">
                @forelse($transactions as $tx)
                    @php
                        $txTypeStr = is_object($tx->type) ? ($tx->type->value ?? '') : (string)$tx->type;
                        $isCredit = in_array($txTypeStr, ['credit', 'deposit', 'refund', 'adjustment']) || ($tx->amount ?? 0) >= 0;
                    @endphp
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid var(--border); last-child:border-bottom: none;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ $isCredit ? 'rgba(34, 197, 94, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; display: flex; align-items: center; justify-content: center; color: {{ $isCredit ? '#22c55e' : '#ef4444' }};">
                                @if($isCredit)
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                @else
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                @endif
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);">
                                    {{ $tx->description ?? ($isCredit ? 'Deposit' : 'Usage Charge') }}
                                </div>
                                <div style="font-size: 11px; color: var(--text-tertiary);">
                                    {{ $tx->created_at->format('M j, Y • g:i A') }}
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: {{ $isCredit ? '#22c55e' : '#ef4444' }};">
                                {{ $isCredit ? '+' : '-' }}{{ $wallet->currency->symbol ?? '₦' }}{{ number_format(abs($tx->amount), $wallet->currency->decimal_places ?? 2) }}
                            </div>
                            <div style="font-size: 10px; color: var(--text-tertiary);">
                                {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($tx->balance_after ?? 0, $wallet->currency->decimal_places ?? 2) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>
                        </div>
                        <div class="empty-state-title">No transactions yet</div>
                        <div class="empty-state-text">Deposit funds to start using SMS verification services.</div>
                        <button type="button" onclick="openDepositModal()" class="btn btn-primary btn-sm blitz-btn" style="margin-top: 12px; background: #7C3AED; border: none; border-radius: 10px; padding: 9px 18px; font-weight: 700;">
                            + Fund Wallet
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        @if($transactions->hasPages())
            <div style="display: flex; justify-content: center; gap: 8px; margin-top: 16px;">
                @if($transactions->onFirstPage())
                    <span class="btn btn-ghost btn-sm" style="opacity: 0.4;">← Prev</span>
                @else
                    <a href="{{ $transactions->previousPageUrl() }}" class="btn btn-ghost btn-sm">← Prev</a>
                @endif
                <span style="font-size: 12px; color: var(--text-tertiary); display: flex; align-items: center;">
                    {{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}
                </span>
                @if($transactions->hasMorePages())
                    <a href="{{ $transactions->nextPageUrl() }}" class="btn btn-ghost btn-sm">Next →</a>
                @else
                    <span class="btn btn-ghost btn-sm" style="opacity: 0.4;">Next →</span>
                @endif
            </div>
        @endif
    </div>

    <div style="height: 20px;"></div>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        function updateBalanceDisplay(newBalance) {
            if (newBalance === undefined || newBalance === null) return;
            const el = document.getElementById('native-wallet-balance-num');
            if (el) {
                el.textContent = Number(newBalance).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        // Listen for real-time balance updates
        window.addEventListener('balance-updated', function(e) {
            if (e.detail && e.detail.balance !== undefined) {
                updateBalanceDisplay(e.detail.balance);
            }
        });

        // Check if there is an updated balance stored
        const storedBalance = localStorage.getItem('user_wallet_balance');
        if (storedBalance) {
            updateBalanceDisplay(storedBalance);
        }

        // On pageshow (e.g. back navigation or app resume), fetch latest balance in background
        window.addEventListener('pageshow', function() {
            fetch("{{ url('/wallet/balance') }}", {
                headers: { 'Accept': 'application/json', 'Cache-Control': 'no-cache' },
                credentials: 'same-origin'
            }).then(r => r.json()).then(data => {
                const bal = data?.available_balance ?? data?.balance ?? data?.data?.available_balance;
                if (bal !== undefined && bal !== null) {
                    updateBalanceDisplay(bal);
                    localStorage.setItem('user_wallet_balance', bal);
                }
            }).catch(function() {});
        });
    })();
</script>
@endsection
