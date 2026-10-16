@extends('web.layouts.app')

@section('title', 'Support')
@section('page_title', 'Support & Helpdesk')

@section('content')
<div class="max-w-5xl space-y-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#1E1B4B] via-[#163A75] to-[#0A1C3C] p-6 md:p-8 rounded-2xl border border-white/15 shadow-xl text-white relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-emerald-400/15 pointer-events-none blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-300 bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-400/30 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    24/7 Priority Ops
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">
                Carrier Support & Help Center
            </h2>
            <p class="text-xs md:text-sm text-slate-300 mt-1.5 max-w-2xl">
                Need help with verification routing, unreceived OTP codes, enterprise volume pricing, or API integrations? Our carrier operations team is on standby 24/7.
            </p>
        </div>
    </div>

    <!-- Contact Channels Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Channel 1: Telegram -->
        <div class="bg-white dark:bg-[#131E3D] p-6 md:p-8 rounded-card border border-slate-200 dark:border-white/10 shadow-sm flex flex-col justify-between space-y-6">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-[#0088cc]/10 text-[#0088cc] flex items-center justify-center mb-4 text-3xl">
                    💬
                </div>
                <h3 class="text-lg font-bold text-[#1E1B4B] dark:text-white">Telegram 24/7 Live Agent</h3>
                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Instant agent resolution for live SMS delivery checks, country routing inquiries, and direct API assistance.
                </p>
            </div>
            <div>
                <a href="https://t.me" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 bg-[#0088cc] hover:bg-[#0077b5] text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all w-full justify-center">
                    <span>Open Telegram Support</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- Channel 2: Email -->
        <div class="bg-white dark:bg-[#131E3D] p-6 md:p-8 rounded-card border border-slate-200 dark:border-white/10 shadow-sm flex flex-col justify-between space-y-6">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-[#1E1B4B]/10 dark:bg-blue-900/30 text-[#1E1B4B] dark:text-blue-300 flex items-center justify-center mb-4 text-3xl">
                    📧
                </div>
                <h3 class="text-lg font-bold text-[#1E1B4B] dark:text-white">Email & Billing Desk</h3>
                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    For enterprise accounts, bulk number reservations, manual bank deposit verifications, and partnership inquiries.
                </p>
            </div>
            <div>
                <a href="mailto:support@Verixa.com" class="inline-flex items-center gap-2 px-5 py-3 bg-[#1E1B4B] hover:bg-navy-900 dark:bg-[#254B8C] dark:hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all w-full justify-center">
                    <span>Email Support Team</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>

    <!-- FAQ Accordion -->
    <div class="bg-white dark:bg-[#131E3D] p-6 md:p-8 rounded-card border border-slate-200 dark:border-white/10 shadow-sm space-y-6">
        <h3 class="text-base md:text-lg font-black text-[#1E1B4B] dark:text-white tracking-tight">
            Frequently Asked Questions
        </h3>

        <div class="space-y-4 divide-y divide-slate-100 dark:divide-white/10 text-xs md:text-sm">
            <div class="pt-4 first:pt-0">
                <h4 class="font-bold text-[#1E1B4B] dark:text-white text-sm md:text-base">How fast do verification SMS codes arrive?</h4>
                <p class="text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                    Codes typically land within 5 to 30 seconds of being dispatched by the service. If an SMS does not arrive within the 20-minute allocation window, your wallet balance is automatically released and never charged.
                </p>
            </div>

            <div class="pt-4">
                <h4 class="font-bold text-[#1E1B4B] dark:text-white text-sm md:text-base">Are these real non-VoIP carrier lines?</h4>
                <p class="text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                    Yes. All allocated numbers originate from physical SIM banks and direct interconnect agreements with Tier-1 carriers across USA (AT&T, Verizon, T-Mobile), UK (EE, Vodafone, O2), and 35+ other countries.
                </p>
            </div>

            <div class="pt-4">
                <h4 class="font-bold text-[#1E1B4B] dark:text-white text-sm md:text-base">How does automated credit refunding work?</h4>
                <p class="text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                    When you order a number, credits are temporarily held. If the SMS is received and verified, the transaction completes. If you cancel or the timer runs out without receiving a message, 100% of the funds are instantly restored to your available balance.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
