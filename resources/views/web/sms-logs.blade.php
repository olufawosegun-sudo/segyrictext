@extends('web.layouts.app')

@section('title', 'SMS Logs')
@section('page_title', 'SMS Logs')

@section('content')
<div class="space-y-6" x-data="{
    copyCode(code) {
        navigator.clipboard.writeText(code);
        alert('OTP code ' + code + ' copied to clipboard!');
    }
}">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-[#1E1B4B] via-[#163A75] to-[#0A1C3C] p-6 md:p-8 rounded-2xl border border-white/15 shadow-xl text-white relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-emerald-400/15 pointer-events-none blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-300 bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-400/30 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Packet Stream
                </span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-300 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">
                    Instant OTP Decrypt
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">
                Incoming SMS & OTP Message Audit
            </h2>
            <p class="text-xs md:text-sm text-slate-300 mt-1 max-w-xl">
                Real-time carrier packet feed, automated OTP token extraction, and full message payload audit logs.
            </p>
        </div>
        <div class="relative z-10 flex items-center gap-3">
            <button onclick="window.location.reload()" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/15 text-white rounded-xl text-xs md:text-sm font-bold border border-white/10 shadow-sm transition-all">
                <svg class="w-4 h-4 text-violet-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
                <span>Refresh Logs</span>
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-[#131E3D] rounded-card border border-slate-200 dark:border-white/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs md:text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#1C2B54] border-b border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-300 font-bold uppercase text-[11px] tracking-wider">
                        <th class="py-3.5 px-5">Destination Number</th>
                        <th class="py-3.5 px-5">Sender Identity</th>
                        <th class="py-3.5 px-5">Message Payload</th>
                        <th class="py-3.5 px-5">Extracted OTP</th>
                        <th class="py-3.5 px-5 text-right">Received Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($logs as $msg)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors">
                        <td class="py-4 px-5 font-mono font-bold text-[#1E1B4B] dark:text-blue-300 whitespace-nowrap">
                            {{ $msg->phoneNumber?->number ?? '—' }}
                        </td>
                        <td class="py-4 px-5 font-bold text-slate-800 dark:text-white whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-[#1E1B4B] dark:text-blue-300 text-xs">
                                {{ $msg->sender ?? 'Carrier Node' }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-slate-600 dark:text-slate-300 max-w-md font-sans leading-relaxed">
                            {{ $msg->message_body ?? $msg->raw_text ?? 'Encrypted message packet' }}
                        </td>
                        <td class="py-4 px-5 whitespace-nowrap">
                            @if(!empty($msg->otp_code))
                                <button @click="copyCode('{{ $msg->otp_code }}')" type="button" class="inline-flex items-center gap-2 font-mono text-base font-black text-[#7C3AED] dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 px-3 py-1 rounded-lg tracking-wider hover:bg-emerald-100 transition-colors" title="Click to copy code">
                                    <span>{{ $msg->otp_code }}</span>
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                </button>
                            @else
                                <span class="text-xs text-slate-400 font-mono">—</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right text-xs text-slate-400 font-mono whitespace-nowrap">
                            {{ $msg->created_at->format('M d, Y H:i:s') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 px-4 text-center">
                            <div class="w-16 h-16 rounded-full bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 flex items-center justify-center mx-auto mb-3 text-2xl">
                                💬
                            </div>
                            <h3 class="text-base font-bold text-[#1E1B4B] dark:text-white">No Inbound SMS Received Yet</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                                When verification codes and SMS packets are delivered to your leased numbers, they will appear here instantaneously.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-white/10 bg-slate-50/50 dark:bg-[#1C2B54]/30">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
