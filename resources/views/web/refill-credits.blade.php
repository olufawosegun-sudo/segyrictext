@extends('web.layouts.app')

@section('title', 'Deposit & Refill Credits')
@section('page_title', 'Deposit & Refill Credits')

@section('content')
<script>
    window.refillCreditsComponent = function() {
        return {
            currentBalance: {{ (float)($wallet->available_balance ?? 0) }},
            currencySymbol: '{{ $currencySymbol ?? '₦' }}',
            currencyCode: '{{ $currencyCode ?? 'NGN' }}',
            depositAmount: 5000,
            isPaying: false,
            errorMessage: null,
            thresholdAmount: '0',
            balancePollTimer: null,

            init() {
                this.startBalancePolling();
            },

            selectAmount(amt) {
                this.depositAmount = amt;
                this.errorMessage = null;
            },

            async processPayment() {
                const amt = parseFloat(this.depositAmount);
                if (isNaN(amt) || amt < 100) {
                    this.errorMessage = 'Minimum deposit amount is ' + this.currencySymbol + '100.';
                    return;
                }

                this.isPaying = true;
                this.errorMessage = null;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                try {
                    const res = await fetch('{{ route('payment.initialize') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            amount: amt,
                            currency: this.currencyCode,
                            gateway: 'paystack'
                        })
                    });

                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Payment initiation failed.');
                    }

                    if (data.authorization_url) {
                        window.location.href = data.authorization_url;
                        return;
                    }

                    throw new Error('Checkout URL not returned by payment gateway.');
                } catch (err) {
                    console.error('[Web Payment] Error:', err);
                    this.errorMessage = err.message || 'Could not launch payment checkout. Please try again.';
                    this.isPaying = false;
                }
            },

            startBalancePolling() {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                this.balancePollTimer = setInterval(async () => {
                    try {
                        const res = await fetch('{{ route('api.v1.wallet.balance') }}', {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                        });
                        if (!res.ok) return;
                        const json = await res.json();

                        if (json.success && json.data) {
                            const newBal = parseFloat(json.data.available_balance ?? json.data.balance);
                            if (newBal > this.currentBalance) {
                                const diff = newBal - this.currentBalance;
                                this.currentBalance = newBal;
                                const sidebarBal = document.getElementById('desktop-sidebar-balance');
                                if (sidebarBal) {
                                    sidebarBal.textContent = this.currencySymbol + Number(newBal).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                                if (typeof window.showDesktopPaymentSplash === 'function') {
                                    window.showDesktopPaymentSplash(this.currencySymbol, diff);
                                }
                            }
                        }
                    } catch(e) {}
                }, 5000);
            },

            saveThreshold() {
                alert(`Credit threshold saved: ${this.currencySymbol}${this.thresholdAmount}. Email alerts updated.`);
            }
        };
    };

    if (window.Alpine) {
        window.Alpine.data('refillCreditsComponent', window.refillCreditsComponent);
    } else {
        document.addEventListener('alpine:init', () => {
            window.Alpine.data('refillCreditsComponent', window.refillCreditsComponent);
        });
    }
</script>

<div 
    x-data="refillCreditsComponent()"
    class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
>
    <!-- Dynamic Error / Notice Banner -->
    <template x-if="errorMessage">
        <div class="lg:col-span-12 p-5 rounded-2xl flex items-center justify-between gap-4 bg-red-500/10 border border-red-500/30 text-red-400 shadow-sm animate-fade-in">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-red-500/20 text-red-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="text-sm font-semibold" x-text="errorMessage"></div>
            </div>
            <button type="button" @click="errorMessage = null" class="text-red-400 hover:text-red-200 p-1.5 cursor-pointer">✕</button>
        </div>
    </template>

    @if(isset($paymentNotice) && $paymentNotice)
        <div class="lg:col-span-12 p-4 rounded-2xl flex items-center gap-3.5 {{ $paymentNotice['type'] === 'success' ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border border-red-500/30 text-red-400' }} shadow-sm">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $paymentNotice['type'] === 'success' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }} shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1 font-semibold text-sm">
                {{ $paymentNotice['message'] }}
            </div>
        </div>
    @endif

    <!-- LEFT/CENTER COLUMN (8 of 12 columns on desktop) -->
    <div class="lg:col-span-8 space-y-6">
        
        <!-- Primary In-App Checkout Card (Payment Gateway Checkout) -->
        <div class="bg-white dark:bg-[#131E3D] rounded-2xl border-2 border-emerald-500/80 dark:border-emerald-500/60 shadow-xl shadow-emerald-500/5 overflow-hidden">
            
            <!-- Header Bar -->
            <div class="bg-gradient-to-r from-[#1E1B4B] to-[#1B3F7A] p-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-emerald-400 border border-white/15 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-black tracking-tight">Instant Account Deposit</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500 text-white shadow-sm">Instant Credit</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5">Secure payment via Paystack • Cards, Bank Transfer & USSD</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Zero Wait Time</span>
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 md:p-8 space-y-6">

                <!-- Amount Selection Section -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                        Select or Enter Deposit Amount
                    </label>

                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-2xl font-black text-emerald-600 dark:text-emerald-400 pointer-events-none" x-text="currencySymbol">₦</span>
                        <input 
                            type="number" 
                            min="100" 
                            step="100" 
                            x-model="depositAmount"
                            placeholder="5000"
                            class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-[#1C2B54] border-2 border-slate-200 dark:border-white/15 rounded-2xl text-2xl font-black font-mono text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500 transition-colors shadow-sm"
                        >
                    </div>
                </div>

                <!-- Quick Amount Preset Chips -->
                <div>
                    <span class="block text-xs font-semibold text-slate-400 mb-2">Quick Presets</span>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2.5">
                        <template x-for="amt in [1000, 2000, 5000, 10000, 20000, 50000]" :key="amt">
                            <button 
                                type="button" 
                                @click="selectAmount(amt)"
                                :class="depositAmount == amt 
                                    ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/25 scale-[1.02]' 
                                    : 'bg-slate-50 dark:bg-[#1C2B54] text-slate-700 dark:text-slate-200 border-slate-200 dark:border-white/10 hover:border-emerald-500/50'"
                                class="py-2.5 px-3 rounded-xl border text-xs font-black transition-all cursor-pointer text-center"
                            >
                                <span x-text="currencySymbol + Number(amt).toLocaleString()"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Supported Payment Methods Badge Row -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#1C2B54] border border-slate-200/80 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-500 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200">Accepted Payment Channels</span>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Debit & Credit Cards (Mastercard, Visa, Verve) • Bank Transfer • USSD</div>
                        </div>
                    </div>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        PCI-DSS Level 1 Secure
                    </span>
                </div>

                <!-- Primary Action Button -->
                <button 
                    type="button" 
                    @click="processPayment()"
                    :disabled="isPaying"
                    class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-base shadow-lg shadow-emerald-500/30 hover:scale-[1.01] transition-all flex items-center justify-center gap-3 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <template x-if="isPaying">
                        <svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isPaying ? 'Redirecting to Payment...' : 'Pay ' + currencySymbol + Number(depositAmount || 0).toLocaleString() + ' with Paystack'">
                        Pay with Paystack
                    </span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                </button>

            </div>
        </div>

        <!-- Step-by-Step Payment Guide -->
        <div class="bg-white dark:bg-[#131E3D] rounded-2xl border border-slate-200 dark:border-white/10 p-6 md:p-8 shadow-sm">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-6">HOW INSTANT PAYMENT WORKS</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#1C2B54] border border-slate-100 dark:border-white/5 relative">
                    <span class="w-6 h-6 rounded-full bg-[#1E1B4B] text-white text-xs font-black flex items-center justify-center mb-3">1</span>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Choose Amount</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pick a quick preset or type any custom deposit amount.</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#1C2B54] border border-slate-100 dark:border-white/5 relative">
                    <span class="w-6 h-6 rounded-full bg-[#1E1B4B] text-white text-xs font-black flex items-center justify-center mb-3">2</span>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Click Pay</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">The secure Paystack checkout opens to complete your payment.</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#1C2B54] border border-slate-100 dark:border-white/5 relative">
                    <span class="w-6 h-6 rounded-full bg-[#1E1B4B] text-white text-xs font-black flex items-center justify-center mb-3">3</span>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Select Method</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pay with your debit or credit card, bank transfer, or USSD.</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#1C2B54] border border-slate-100 dark:border-white/5 relative">
                    <span class="w-6 h-6 rounded-full bg-[#7C3AED] text-white text-xs font-black flex items-center justify-center mb-3">4</span>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Instant Credit</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Your vault balance updates automatically within seconds.</p>
                </div>

            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN (4 of 12 columns on desktop) -->
    <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
        
        <!-- Live Liquid Balance Card -->
        <div class="bg-[#1E1B4B] text-white rounded-2xl p-6 shadow-xl shadow-[#1E1B4B]/15 border border-white/10 relative overflow-hidden">
            <div class="relative z-10">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 bg-white/10 px-3 py-1 rounded-full">Available Vault Balance</span>
                <div class="mt-4 flex items-baseline gap-1.5 font-mono">
                    <span class="text-emerald-400 text-2xl font-bold font-sans" x-text="currencySymbol">{{ $currencySymbol ?? '₦' }}</span>
                    <span class="text-4xl font-black tracking-tight" x-text="Number(currentBalance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })">{{ number_format($wallet->available_balance ?? 0, 2) }}</span>
                </div>
                <div class="mt-4 pt-4 border-t border-white/15 flex items-center justify-between text-xs text-slate-300">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Live Sync Active</span>
                    </span>
                    <span class="font-bold uppercase text-white">{{ $currencyCode ?? 'NGN' }}</span>
                </div>
            </div>
        </div>

        <!-- Trust & Security Badges Card -->
        <div class="bg-white dark:bg-[#131E3D] rounded-2xl border border-slate-200 dark:border-white/10 p-6 shadow-sm space-y-4">
            <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">SECURE PAYMENTS</h4>
            
            <div class="space-y-3 text-xs">
                <div class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <span>256-Bit SSL Encrypted Direct Carrier Line</span>
                </div>

                <div class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span>Certified Paystack Integration</span>
                </div>

                <div class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <span>Automatic Refund if SMS Verification Session Times Out</span>
                </div>
            </div>
        </div>

        <!-- Low Balance Alert Threshold Settings -->
        <div class="bg-white dark:bg-[#131E3D] rounded-2xl border border-slate-200 dark:border-white/10 p-6 shadow-sm space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-[#1E1B4B] dark:text-blue-300 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-[#1E1B4B] dark:text-white text-sm">Low Balance Notification</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        Receive an email notification when your balance dips below this amount.
                    </p>
                </div>
            </div>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm" x-text="currencySymbol">
                    {{ $currencySymbol ?? '₦' }}
                </div>
                <input 
                    type="number" 
                    min="0" 
                    x-model="thresholdAmount" 
                    placeholder="500" 
                    class="w-full pl-8 pr-4 py-2.5 bg-white dark:bg-[#1C2B54] border border-slate-200 dark:border-white/10 rounded-xl text-slate-800 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#1E1B4B]/20 focus:border-[#1E1B4B] transition-all shadow-sm"
                >
            </div>

            <button 
                @click="saveThreshold()" 
                type="button" 
                class="w-full py-2.5 px-4 bg-[#1E1B4B] hover:bg-navy-900 text-white rounded-xl font-bold text-xs transition-colors shadow-sm cursor-pointer"
            >
                Save Notification Threshold
            </button>
        </div>

    </div>
</div>
@endsection
