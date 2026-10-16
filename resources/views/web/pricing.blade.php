@extends('web.layouts.app')

@section('title', 'Pricing')
@section('page_title', 'Carrier Pricing Catalog')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-[#1E1B4B] via-[#163A75] to-[#0A1C3C] p-6 md:p-8 rounded-2xl border border-white/15 shadow-xl text-white relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-emerald-400/15 pointer-events-none blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-300 bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-400/30 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Carrier Matrix
                </span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-300 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">
                    35+ Countries
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">
                Transparent Carrier Rates & Catalog
            </h2>
            <p class="text-xs md:text-sm text-slate-300 mt-1 max-w-xl">
                Real-time per-verification rates and dedicated monthly lease pricing for all supported apps and countries.
            </p>
        </div>
        <div class="relative z-10 flex items-center gap-3">
            <a href="{{ route('web.refill-credits') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white rounded-xl text-xs md:text-sm font-bold shadow-lg shadow-emerald-500/20 hover:scale-105 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span>Deposit Funds</span>
            </a>
        </div>
    </div>

    <!-- Search & Pricing Table Card -->
    <div class="bg-white dark:bg-[#131E3D] rounded-card border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden">
        <!-- Search bar -->
        <div class="p-4 bg-slate-50 dark:bg-[#1C2B54] border-b border-slate-200 dark:border-white/10">
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <input 
                    type="text" 
                    id="pricing-search" 
                    placeholder="Search service (WhatsApp, Telegram, Google, etc.)..." 
                    oninput="filterServices()" 
                    class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#131E3D] border border-slate-200 dark:border-white/10 rounded-xl text-xs md:text-sm font-medium text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E1B4B]/20 transition-all"
                >
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs md:text-sm" id="pricing-table">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-[#1C2B54]/50 border-b border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-300 font-bold uppercase text-[11px] tracking-wider">
                        <th class="py-3.5 px-5">Service Name</th>
                        <th class="py-3.5 px-5">Service Code</th>
                        <th class="py-3.5 px-5">STR One-Time Rate</th>
                        <th class="py-3.5 px-5">LTR 30-Day Lease</th>
                        <th class="py-3.5 px-5 text-right">Instant Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($services as $svc)
                    <tr class="service-row hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                        <td class="py-4 px-5 font-bold text-slate-900 dark:text-white">
                            {{ $svc->name }}
                        </td>
                        <td class="py-4 px-5 font-mono text-xs text-slate-400">
                            {{ $svc->code ?? strtolower(str_replace(' ', '', $svc->name)) }}
                        </td>
                        <td class="py-4 px-5 font-mono font-black text-[#7C3AED] dark:text-emerald-400">
                            {{ $currencySymbol }}{{ number_format($svc->base_price ?? 200, 2) }}
                        </td>
                        <td class="py-4 px-5 font-mono font-bold text-[#1E1B4B] dark:text-blue-300">
                            {{ $currencySymbol }}{{ number_format(($svc->base_price ?? 200) * 15, 2) }}
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('web.str-numbers') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs rounded-lg transition-colors">
                                <span>Order Line</span>
                                <span>→</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-4 text-center text-slate-400 text-xs">
                            No services catalog found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterServices() {
    const q = document.getElementById('pricing-search').value.toLowerCase();
    document.querySelectorAll('.service-row').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endsection
