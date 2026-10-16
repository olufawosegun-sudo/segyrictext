@extends('web.layouts.app')

@section('title', 'Get STR Numbers • Verixa')
@section('page_title', 'Virtual Number Provisioning & STR Console')

@section('content')
<div class="space-y-8">
    <!-- Header Banner -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 bg-gradient-to-r from-[#1E1B4B] via-[#2E1065] to-[#0A0718] p-6 md:p-8 rounded-2xl border border-white/15 shadow-xl relative overflow-hidden text-white">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-violet-500/20 pointer-events-none blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-violet-300 bg-violet-500/20 px-2.5 py-0.5 rounded-full border border-violet-400/30 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
                    Instant Carrier Allocation
                </span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-300 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">
                    20-Min Disposable
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                Short-Term Virtual Numbers (STR)
            </h2>
            <p class="text-xs md:text-sm text-slate-400 mt-1 max-w-xl leading-relaxed">
                Select your country and service below to allocate a high-reputation non-VoIP carrier line. Includes 100% auto-refund protection if no SMS arrives within 20 minutes.
            </p>
        </div>

        <div class="relative z-10 flex flex-wrap items-center gap-3">
            <div class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl flex items-center gap-3">
                <span class="text-xs text-slate-400">Balance:</span>
                <span class="text-base font-black font-mono text-violet-300">{{ $currencySymbol }}{{ number_format($wallet->available_balance ?? 0, 2) }}</span>
            </div>
            <a href="{{ route('web.refill-credits') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-violet-500/20 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span>Deposit Funds</span>
            </a>
            <button type="button" onclick="document.getElementById('order-provision-section')?.scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white rounded-xl text-xs font-bold border border-white/10 shadow-sm transition-all">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                <span>Provision Number</span>
            </button>
        </div>
    </div>

    <!-- Main 12-Column Provisioning & Active Monitor Suite -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" id="order-provision-section">
        
        <!-- LEFT: Interactive Number Ordering Console (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white dark:bg-[#0B0F19] rounded-2xl border border-slate-200 dark:border-white/10 shadow-md p-1 overflow-hidden">
                <x-purchase-number-section 
                    :wallet="$wallet" 
                    :countries="$countries ?? collect()" 
                    :services="$services ?? collect()" 
                    :activeSessions="$activeSessions ?? collect()" 
                />
            </div>
        </div>

        <!-- RIGHT: Active Verifications & Historical Sessions Table (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Active Live Sessions Monitor (if any) -->
            @if(isset($activeSessions) && $activeSessions->count() > 0)
            <div class="bg-white dark:bg-[#0B0F19] rounded-2xl border border-emerald-500/30 dark:border-emerald-500/30 shadow-md p-6 relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">Active Verification Stream</h3>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20">
                        {{ $activeSessions->count() }} Live
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($activeSessions as $active)
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $active->service?->name ?? 'SMS Verification' }}</span>
                                <span class="text-xs text-slate-400">({{ $active->phoneNumber?->country?->name ?? 'Global' }})</span>
                            </div>
                            <div class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-base mt-0.5 flex items-center gap-2">
                                <span>{{ $active->phoneNumber?->number ?? 'Allocating...' }}</span>
                                @if($active->phoneNumber?->number)
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $active->phoneNumber->number }}'); alert('Phone number copied!');" class="text-xs text-slate-400 hover:text-white" title="Copy Number">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                </button>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($active->latestOtp)
                                <div class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-500/40 rounded-lg text-emerald-800 dark:text-emerald-300 font-mono font-black text-lg tracking-widest flex items-center gap-2">
                                    <span>{{ $active->latestOtp->otp_code }}</span>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $active->latestOtp->otp_code }}'); alert('OTP copied!');" class="text-emerald-600 hover:text-emerald-400" title="Copy Code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                    </button>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 px-3 py-1.5 rounded-lg border border-amber-500/20">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                    <span>Waiting for SMS packet...</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Complete STR History Table Card -->
            <div class="bg-white dark:bg-[#0B0F19] rounded-2xl border border-slate-200 dark:border-white/10 shadow-md overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">STR Verification History</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Audit log of your allocated one-time verification lines</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400 font-mono">
                        {{ $sessions->total() ?? $sessions->count() }} Total
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs md:text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                <th class="py-3 px-4">Service</th>
                                <th class="py-3 px-4">Number</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">OTP Code</th>
                                <th class="py-3 px-4 text-right">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse($sessions as $session)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $session->service?->name ?? 'SMS Verification' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $session->phoneNumber?->country?->name ?? 'Global' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800 dark:text-slate-200">
                                    {{ $session->phoneNumber?->number ?? 'Pending' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($session->status === 'completed')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                                            COMPLETED
                                        </span>
                                    @elseif($session->status === 'active' || $session->status === 'pending')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-cyan-100 dark:bg-cyan-950/60 text-cyan-800 dark:text-cyan-300 animate-pulse">
                                            ACTIVE
                                        </span>
                                    @elseif($session->status === 'expired')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">
                                            EXPIRED
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                            {{ strtoupper($session->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($session->latestOtp)
                                        <span class="font-mono font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 px-2 py-0.5 rounded text-xs">
                                            {{ $session->latestOtp->otp_code }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right text-[11px] text-slate-400 font-mono">
                                    {{ $session->created_at->format('M d, H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 px-4 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                        📱
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">No Historical Numbers Yet</h4>
                                    <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">
                                        Use the provisioning console on the left to configure your service and allocate your first virtual number.
                                    </p>
                                    <div class="mt-3">
                                        <button type="button" onclick="document.getElementById('order-provision-section')?.scrollIntoView({behavior: 'smooth'})" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                                            Configure Order Above ↑
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sessions->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-white/10 bg-slate-50/50 dark:bg-white/5">
                    {{ $sessions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
