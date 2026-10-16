@extends('web.layouts.app')

@section('title', 'Account Settings')
@section('page_title', 'Account Profile & Security')

@section('content')
<div class="max-w-4xl space-y-8" x-data="{
    showKey: false,
    copyApiKey() {
        navigator.clipboard.writeText(document.getElementById('api-key-input').value);
        alert('API Key copied to clipboard!');
    }
}">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#1E1B4B] via-[#163A75] to-[#0A1C3C] p-6 md:p-8 rounded-2xl border border-white/15 shadow-xl text-white relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-emerald-400/15 pointer-events-none blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-300 bg-emerald-500/20 px-2.5 py-0.5 rounded-full border border-emerald-400/30 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Identity & Security Control
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">
                Account Profile & API Credentials
            </h2>
            <p class="text-xs md:text-sm text-slate-300 mt-1 max-w-xl">
                Manage your profile details, base currency, and programmatic carrier API keys.
            </p>
        </div>
    </div>

    <!-- Profile Information Card -->
    <div class="bg-white dark:bg-[#131E3D] p-6 md:p-8 rounded-card border border-slate-200 dark:border-white/10 shadow-sm space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-white/10">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-[#1E1B4B] dark:text-blue-300 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-[#1E1B4B] dark:text-white">Profile Details</h3>
                <p class="text-xs text-slate-400">Personal credentials associated with this account</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs md:text-sm">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                <input type="text" value="{{ $user->name }}" readonly class="w-full px-4 py-3 bg-slate-50 dark:bg-[#1C2B54] border border-slate-200 dark:border-white/10 rounded-xl text-slate-800 dark:text-white font-medium cursor-not-allowed">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-2">Registered Email Address</label>
                <input type="email" value="{{ $user->email }}" readonly class="w-full px-4 py-3 bg-slate-50 dark:bg-[#1C2B54] border border-slate-200 dark:border-white/10 rounded-xl text-slate-800 dark:text-white font-medium cursor-not-allowed">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-2">Unique User ID</label>
                <input type="text" value="UID-{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}" readonly class="w-full px-4 py-3 bg-slate-50 dark:bg-[#1C2B54] border border-slate-200 dark:border-white/10 rounded-xl text-slate-800 dark:text-white font-mono font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-2">Default Currency</label>
                <input type="text" value="{{ $currencyCode }} ({{ $currencySymbol }})" readonly class="w-full px-4 py-3 bg-slate-50 dark:bg-[#1C2B54] border border-slate-200 dark:border-white/10 rounded-xl text-slate-800 dark:text-white font-bold cursor-not-allowed">
            </div>
        </div>
    </div>

    <!-- API Keys Card -->
    <div class="bg-white dark:bg-[#131E3D] p-6 md:p-8 rounded-card border border-slate-200 dark:border-white/10 shadow-sm space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-white/10">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-[#1E1B4B] dark:text-white">API Integration Key</h3>
                <p class="text-xs text-slate-400">Use this token to automate number lease and SMS retrieval via REST endpoints</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input 
                        :type="showKey ? 'text' : 'password'" 
                        value="st_live_{{ md5($user->id . $user->created_at) }}" 
                        id="api-key-input" 
                        readonly 
                        class="w-full px-4 py-3.5 bg-slate-50 dark:bg-[#1C2B54] border border-slate-200 dark:border-white/10 rounded-xl text-slate-800 dark:text-white font-mono text-sm tracking-wider"
                    >
                </div>
                <button 
                    type="button" 
                    @click="showKey = !showKey"
                    class="px-4 py-3.5 bg-slate-100 dark:bg-[#1C2B54] hover:bg-slate-200 dark:hover:bg-blue-900/50 text-[#1E1B4B] dark:text-white font-bold text-xs rounded-xl border border-slate-200 dark:border-white/10 transition-colors shrink-0"
                >
                    <span x-text="showKey ? 'Hide Secret' : 'Reveal Key'">Reveal Key</span>
                </button>
                <button 
                    type="button" 
                    @click="copyApiKey()"
                    class="px-4 py-3.5 bg-[#1E1B4B] hover:bg-navy-900 dark:bg-[#254B8C] dark:hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-colors shadow-sm shrink-0"
                >
                    Copy API Key
                </button>
            </div>

            <p class="text-xs text-slate-400 leading-relaxed">
                Keep your secret key confidential. All requests signed with this token authenticate directly against your wallet balance.
            </p>
        </div>
    </div>
</div>
@endsection
