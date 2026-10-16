@extends('web.layouts.app')

@section('title', 'My LTR Numbers')
@section('page_title', 'My LTR Numbers')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-[#1E1B4B] via-[#163A75] to-[#0A1C3C] p-6 md:p-8 rounded-2xl border border-white/15 shadow-xl text-white relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-emerald-400/15 pointer-events-none blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-300 bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-400/30 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Dedicated Carrier Leases
                </span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-300 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">
                    3 · 7 · 30 Days
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">
                Long-Term Rental Numbers (LTR)
            </h2>
            <p class="text-xs md:text-sm text-slate-300 mt-1 max-w-xl">
                Dedicated non-VoIP carrier lines reserved for continuous 2FA, banking verification, and repeated SMS access.
            </p>
        </div>
        <div class="relative z-10 flex items-center gap-3">
            <a href="{{ route('web.str-numbers') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white rounded-xl text-xs md:text-sm font-bold shadow-lg shadow-emerald-500/20 hover:scale-105 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                <span>Rent Long-Term Line</span>
            </a>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-[#131E3D] rounded-card border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs md:text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#1C2B54] border-b border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-300 font-bold uppercase text-[11px] tracking-wider">
                        <th class="py-3.5 px-5">Country & Carrier</th>
                        <th class="py-3.5 px-5">Dedicated Phone Number</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5">Expires / Renews</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($reservations as $res)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">
                                    @php
                                        $iso = strtoupper($res->phoneNumber?->country?->iso2 ?? 'US');
                                        $flag = '';
                                        foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                        echo $flag;
                                    @endphp
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $res->phoneNumber?->country?->name ?? 'Global Carrier Line' }}</div>
                                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">Private Dedicated Lease</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5 font-mono font-bold text-[#1E1B4B] dark:text-blue-300">
                            {{ $res->phoneNumber?->number ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-5">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                                ACTIVE RENTAL
                            </span>
                        </td>
                        <td class="py-4 px-5 text-xs text-slate-600 dark:text-slate-300 font-mono">
                            {{ $res->expires_at ? $res->expires_at->format('M d, Y H:i') : 'Auto-renewing line' }}
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('web.sms-logs') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-[#1E1B4B] dark:text-blue-300 font-bold text-xs rounded-lg transition-colors">
                                <span>View SMS Logs</span>
                                <span>→</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 px-4 text-center">
                            <div class="w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                🔒
                            </div>
                            <h3 class="text-base font-bold text-[#1E1B4B] dark:text-white">No LTR Numbers Active</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                                Rent private non-VoIP numbers for 3, 7, or 30 days for ongoing verifications, WhatsApp Business, and banking 2FA accounts.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('web.str-numbers') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-bold rounded-xl shadow-sm hover:scale-105 transition-all">
                                    Browse LTR Rental Catalog
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-white/10 bg-slate-50/50 dark:bg-[#1C2B54]/30">
            {{ $reservations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
