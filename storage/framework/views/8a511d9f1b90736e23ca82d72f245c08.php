<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $currencySymbol = $currencySymbol ?? ($wallet->currency->symbol ?? '$');
    $currencyCode = $currencyCode ?? ($wallet->currency->code ?? 'USD');
?>
<div class="space-y-8" x-data="{
    activeTab: 'sessions',
    copyOtp(otp, id) {
        navigator.clipboard.writeText(otp);
        alert('OTP code ' + otp + ' copied to clipboard!');
    },
    copyNumber(num) {
        navigator.clipboard.writeText(num);
        alert('Phone number ' + num + ' copied to clipboard!');
    }
}">

    <!-- Post-Payment Status Notification Banner -->
    <?php if(isset($paymentNotice) && $paymentNotice): ?>
        <div class="p-4 rounded-2xl flex items-center gap-3.5 <?php echo e($paymentNotice['type'] === 'success' ? 'bg-violet-500/10 border border-violet-500/30 text-violet-400' : 'bg-red-500/10 border border-red-500/30 text-red-400'); ?> shadow-sm">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center <?php echo e($paymentNotice['type'] === 'success' ? 'bg-violet-500/20 text-violet-400' : 'bg-red-500/20 text-red-400'); ?> shrink-0">
                <?php if($paymentNotice['type'] === 'success'): ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <?php else: ?>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <?php endif; ?>
            </div>
            <div class="flex-1 font-semibold text-sm">
                <?php echo e($paymentNotice['message']); ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- 1. Top Welcome & Telecom Telemetry Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white/80 dark:bg-[#110D24]/80 backdrop-blur-md p-5 md:p-6 rounded-card border border-slate-200/80 dark:border-white/10 shadow-xs">
        <div class="flex items-center gap-4">
            <!-- User Holographic Avatar -->
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-violet-500 via-teal-500 to-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-violet-500/20 border-2 border-white/20 shrink-0">
                <?php echo e(strtoupper(substr($user->name ?? 'User', 0, 1))); ?>

            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        Welcome, <?php echo e($user->name ?? 'User'); ?>

                    </h2>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-500/25">
                        <span class="w-1.5 h-1.5 rounded-full bg-violet-500 animate-pulse"></span>
                        ENTERPRISE TIER
                    </span>
                </div>
                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    <?php echo e($user->email); ?>

                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('web.refill-credits')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-xl text-sm font-bold shadow-md shadow-violet-600/20 hover:shadow-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span>Deposit Funds</span>
            </a>
            <a href="<?php echo e(route('web.str-numbers')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white/10 dark:hover:bg-white/15 text-white rounded-xl text-sm font-bold shadow-md transition-all">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Get Numbers</span>
            </a>
        </div>
    </div>

    <!-- 2. Wallet & Balance Hero Hub -->
    <div class="bg-gradient-to-br from-[#1A1535] via-[#231E55] to-[#0F0A28] rounded-2xl p-6 md:p-8 text-white border border-white/15 shadow-xl relative overflow-hidden">
        <!-- Cyber ambient glow orbs -->
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-violet-400/15 pointer-events-none blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-teal-400/15 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-violet-300 flex items-center gap-1.5 bg-violet-500/20 px-2.5 py-0.5 rounded-full border border-violet-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Verixa Wallet
                    </span>
                </div>

                <div class="mt-3">
                    <div class="text-xs font-semibold text-slate-300">Available Liquid Balance</div>
                    <div class="text-3xl md:text-5xl font-black font-mono text-white tracking-tight mt-1 flex items-baseline gap-1.5">
                        <span class="text-violet-400 font-sans"><?php echo e($currencySymbol); ?></span>
                        <span><?php echo e(number_format($wallet->available_balance ?? 0, 2)); ?></span>
                        <span class="text-xs font-bold text-slate-300 tracking-normal font-sans ml-1 uppercase bg-white/10 px-2.5 py-1 rounded-md"><?php echo e($currencyCode); ?></span>
                    </div>
                </div>

                <?php if(($wallet->active_holds_total ?? 0) > 0): ?>
                <div class="mt-3 pt-3 border-t border-white/10 text-xs font-semibold text-amber-300 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span><?php echo e($currencySymbol); ?><?php echo e(number_format($wallet->active_holds_total, 2)); ?> active verification hold (auto-refund on completion)</span>
                </div>
                <?php endif; ?>

            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <a href="<?php echo e(route('web.refill-credits')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-400 hover:to-indigo-500 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-violet-500/25 hover:scale-105 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <span>+ Deposit Funds</span>
                </a>
                <a href="<?php echo e(route('web.str-numbers')); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-white/10 hover:bg-white/15 text-white font-bold text-sm rounded-xl border border-white/10 shadow-sm transition-all">
                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="3"/><path d="M12 18h.01"/><path d="M13 7l-3 4h4l-2 4"/></svg>
                    <span>Get Numbers</span>
                </a>
            </div>
        </div>

        <!-- Security Badges Bar -->
        <div class="relative z-10 mt-6 pt-4 border-t border-white/10 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-2.5 rounded-xl bg-white/5 border border-white/5 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-semibold uppercase">Security</span>
                    <strong class="font-bold text-white text-xs">256-Bit SSL Encrypted</strong>
                </div>
            </div>
            <div class="p-2.5 rounded-xl bg-white/5 border border-white/5 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-semibold uppercase">Refund Guarantee</span>
                    <strong class="font-bold text-white text-xs">100% Auto-Refund If No SMS</strong>
                </div>
            </div>
            <div class="p-2.5 rounded-xl bg-white/5 border border-white/5 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 12h5l3-9 4 18 3-9h5"/></svg>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-semibold uppercase">Carrier Tier</span>
                    <strong class="font-bold text-white text-xs">Tier-1 Real Physical SIMs</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!--  3-CONTAINER COMMAND CENTER GRID                                  -->
    <!--  Container 1: Platform Stats   |  Container 2: Live Monitor       -->
    <!--  Container 3: Quick Services (full-width below)                   -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ╔═══ CONTAINER 1 ▸ Platform Intelligence ═══╗ -->
        <div class="bg-white dark:bg-[#110D24] rounded-2xl border border-slate-200 dark:border-white/10 shadow-xs overflow-hidden flex flex-col">
            <!-- Container Header -->
            <div class="px-5 py-4 border-b border-slate-100 dark:border-white/8 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 12h5l3-9 4 18 3-9h5"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Platform Stats</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Real-time metrics</p>
                </div>
            </div>

            <!-- Metric Rows -->
            <div class="flex-1 divide-y divide-slate-100 dark:divide-white/5">
                <!-- Total Verifications -->
                <div class="px-5 py-4 flex items-center justify-between hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block">Total Verifications</span>
                            <span class="text-[10px] text-slate-400 font-medium">Lifetime processed</span>
                        </div>
                    </div>
                    <div class="text-2xl font-black font-mono text-slate-900 dark:text-white">
                        <?php echo e(number_format($stats['total_verifications'] ?? 0)); ?>

                    </div>
                </div>

                <!-- Active Sessions -->
                <div class="px-5 py-4 flex items-center justify-between hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block">Active Sessions</span>
                            <span class="text-[10px] text-violet-600 dark:text-violet-400 font-semibold">Live waiting OTP</span>
                        </div>
                    </div>
                    <div class="text-2xl font-black font-mono text-violet-600 dark:text-violet-400">
                        <?php echo e(number_format($stats['active_verifications'] ?? 0)); ?>

                    </div>
                </div>

                <!-- Available Pool -->
                <div class="px-5 py-4 flex items-center justify-between hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block">Available Pool</span>
                            <span class="text-[10px] text-slate-400 font-medium">Instant lease capacity</span>
                        </div>
                    </div>
                    <div class="text-2xl font-black font-mono text-slate-900 dark:text-white">
                        <?php echo e(number_format($stats['available_numbers'] ?? 1420)); ?>

                    </div>
                </div>

                <!-- Global Coverage -->
                <div class="px-5 py-4 flex items-center justify-between hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block">Global Coverage</span>
                            <span class="text-[10px] text-slate-400 font-medium"><?php echo e($stats['supported_services'] ?? 80); ?>+ services enabled</span>
                        </div>
                    </div>
                    <div class="text-2xl font-black font-mono text-slate-900 dark:text-white">
                        <?php echo e($stats['active_countries'] ?? 35); ?>+
                    </div>
                </div>
            </div>
        </div>

        <!-- ╔═══ CONTAINER 2 ▸ Live Verification Monitor ═══╗ -->
        <div class="lg:col-span-2 bg-white dark:bg-[#110D24] rounded-2xl border border-slate-200 dark:border-white/10 shadow-xs overflow-hidden flex flex-col">
            <!-- Container Header -->
            <div class="px-5 py-4 border-b border-slate-100 dark:border-white/8 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-3 h-3 rounded-full bg-violet-500 animate-pulse"></div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Live Verification Monitor</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-500/20">
                        <?php echo e($activeSessions->count()); ?> active
                    </span>
                </div>
                <a href="<?php echo e(route('web.str-numbers')); ?>" class="text-xs font-bold text-violet-600 dark:text-violet-400 hover:underline">
                    View All →
                </a>
            </div>

            <!-- Monitor Body -->
            <div class="flex-1 p-5">
                <?php if($activeSessions->count() > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php $__currentLoopData = $activeSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/70 dark:bg-white/[0.03] hover:border-violet-500/30 hover:shadow-md transition-all flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xl">
                                    <?php
                                        $iso = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                                        $flag = '';
                                        foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                        echo $flag;
                                    ?>
                                </span>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900 dark:text-white"><?php echo e($session->service?->name ?? 'SMS Verification'); ?></h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400"><?php echo e($session->phoneNumber?->country?->name ?? 'United States'); ?></p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                WAITING
                            </span>
                        </div>

                        <!-- Number -->
                        <div class="bg-white dark:bg-[#131E3D] p-2.5 rounded-lg border border-slate-200 dark:border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold uppercase">Assigned</span>
                                <span class="font-mono font-bold text-sm text-slate-900 dark:text-white tracking-wider">
                                    <?php echo e($session->phoneNumber?->number ?? '+1 (000) 000-0000'); ?>

                                </span>
                            </div>
                            <button @click="copyNumber('<?php echo e($session->phoneNumber?->number); ?>')" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-violet-600 dark:hover:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-950/30 transition-colors" title="Copy">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </button>
                        </div>

                        <!-- OTP -->
                        <?php $latestOtp = $session->latestOtp?->otp_code ?? null; ?>
                        <?php if($latestOtp): ?>
                        <div class="bg-violet-50 dark:bg-violet-950/40 border border-emerald-200 dark:border-emerald-800/60 p-3 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-emerald-800 dark:text-violet-300 block uppercase">OTP Received:</span>
                                <span class="font-mono font-black text-lg text-violet-600 dark:text-violet-400 tracking-widest"><?php echo e($latestOtp); ?></span>
                            </div>
                            <button @click="copyOtp('<?php echo e($latestOtp); ?>')" type="button" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition-colors">
                                Copy
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="bg-[#0F2A5A]/5 dark:bg-[#0F2A5A]/30 border border-[#0F2A5A]/10 dark:border-white/5 p-2.5 rounded-xl flex items-center justify-center gap-2 text-xs font-semibold text-[#0F2A5A] dark:text-slate-300">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Listening on carrier...</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="text-center py-12 px-4 flex flex-col items-center justify-center h-full">
                    <div class="w-14 h-14 rounded-2xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">No Active Verifications</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto mt-1.5 leading-relaxed">
                        Your verification channels are standing by. Get numbers whenever you need them.
                    </p>
                    <div class="mt-5 flex items-center gap-3">
                        <a href="<?php echo e(route('web.str-numbers')); ?>" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                            Order STR Number
                        </a>
                        <a href="<?php echo e(route('web.ltr-numbers')); ?>" class="px-4 py-2 bg-white dark:bg-white/10 hover:bg-slate-50 dark:hover:bg-white/15 text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold transition-all shadow-sm">
                            Rent LTR Number
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ╔═══ CONTAINER 3 ▸ Quick Services ═══╗ -->
    <div class="bg-white dark:bg-[#110D24] rounded-2xl border border-slate-200 dark:border-white/10 shadow-xs overflow-hidden">
        <!-- Container Header -->
        <div class="px-5 py-4 border-b border-slate-100 dark:border-white/8 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Quick Services</h3>
                <p class="text-[10px] text-slate-400 font-medium">Core platform services</p>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 dark:divide-white/5">
            <!-- STR Numbers -->
            <a href="<?php echo e(route('web.str-numbers')); ?>" class="group p-5 flex items-start gap-4 hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                <div class="w-11 h-11 rounded-xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">Short-Term Numbers</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Instant 20-min disposable numbers for single SMS verifications.</p>
                    <span class="inline-flex items-center gap-1 mt-2.5 text-[11px] font-bold text-violet-600 dark:text-violet-400">
                        Manage STR <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                    </span>
                </div>
            </a>

            <!-- LTR Rentals -->
            <a href="<?php echo e(route('web.ltr-numbers')); ?>" class="group p-5 flex items-start gap-4 hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                <div class="w-11 h-11 rounded-xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">Long-Term Rentals</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Dedicated lines for 3–30 days with recurring SMS and auto-renewal.</p>
                    <span class="inline-flex items-center gap-1 mt-2.5 text-[11px] font-bold text-violet-600 dark:text-violet-400">
                        Manage LTR <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                    </span>
                </div>
            </a>

            <!-- SMS Logs -->
            <a href="<?php echo e(route('web.sms-logs')); ?>" class="group p-5 flex items-start gap-4 hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors">
                <div class="w-11 h-11 rounded-xl bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">SMS Carrier Logs</h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Real-time logs, OTP tokens, and full message audit trails.</p>
                    <span class="inline-flex items-center gap-1 mt-2.5 text-[11px] font-bold text-violet-600 dark:text-violet-400">
                        Audit Logs <span class="group-hover:translate-x-0.5 transition-transform">→</span>
                    </span>
                </div>
            </a>
        </div>
    </div>

    <!-- 6. Recent Activity & Transaction History Tabs -->
    <div class="bg-white dark:bg-[#110D24] rounded-card border border-slate-200 dark:border-white/10 shadow-xs p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-slate-100 dark:border-white/10 gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Recent Activity Feed</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Real-time audit log of your verifications and balance ledger</p>
            </div>

            <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 p-1 rounded-xl">
                <button 
                    @click="activeTab = 'sessions'" 
                    :class="activeTab === 'sessions' ? 'bg-white dark:bg-violet-500 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-white'"
                    type="button" 
                    class="px-3.5 py-1.5 rounded-lg text-xs transition-all"
                >
                    Past Verifications
                </button>
                <button 
                    @click="activeTab = 'transactions'" 
                    :class="activeTab === 'transactions' ? 'bg-white dark:bg-violet-500 text-slate-900 dark:text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-white'"
                    type="button" 
                    class="px-3.5 py-1.5 rounded-lg text-xs transition-all"
                >
                    Wallet Transactions
                </button>
            </div>
        </div>

        <!-- Tab 1: Past Verifications -->
        <div x-show="activeTab === 'sessions'" class="mt-4 overflow-x-auto">
            <?php if(isset($recentSessions) && $recentSessions->count() > 0): ?>
            <table class="w-full text-left border-collapse text-xs md:text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/10 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-3">Country & Service</th>
                        <th class="py-3 px-3">Phone Number</th>
                        <th class="py-3 px-3">OTP Code</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Date / Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <?php $__currentLoopData = $recentSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                <span class="text-base">
                                    <?php
                                        $iso = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                                        $flag = '';
                                        foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                        echo $flag;
                                    ?>
                                </span>
                                <div>
                                    <span class="block"><?php echo e($session->service?->name ?? 'SMS Service'); ?></span>
                                    <span class="text-[11px] text-slate-400 font-normal"><?php echo e($session->phoneNumber?->country?->name ?? 'United States'); ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 font-mono font-bold text-slate-700 dark:text-slate-200">
                            <?php echo e($session->phoneNumber?->number ?? '—'); ?>

                        </td>
                        <td class="py-3 px-3 font-mono font-extrabold text-[#2E8B3F] dark:text-violet-400">
                            <?php echo e($session->latestOtp?->otp_code ?? '—'); ?>

                        </td>
                        <td class="py-3 px-3">
                            <?php if($session->status === 'completed'): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 dark:bg-violet-950/60 text-emerald-800 dark:text-violet-300">
                                    COMPLETED
                                </span>
                            <?php elseif($session->status === 'cancelled'): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                    CANCELLED
                                </span>
                            <?php elseif($session->status === 'expired'): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">
                                    EXPIRED
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300">
                                    <?php echo e(strtoupper($session->status)); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 text-right text-slate-400 text-xs font-mono">
                            <?php echo e($session->created_at->format('M d, H:i')); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-xs text-slate-400 text-center py-6">No previous verification sessions found.</p>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Wallet Transactions -->
        <div x-show="activeTab === 'transactions'" class="mt-4 overflow-x-auto" style="display: none;">
            <?php if(isset($recentTransactions) && $recentTransactions->count() > 0): ?>
            <table class="w-full text-left border-collapse text-xs md:text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/10 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-3">Type</th>
                        <th class="py-3 px-3">Description</th>
                        <th class="py-3 px-3">Amount</th>
                        <th class="py-3 px-3">Balance After</th>
                        <th class="py-3 px-3 text-right">Date / Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <?php $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $txTypeStr = $tx->type instanceof \BackedEnum ? $tx->type->value : (is_string($tx->type) ? $tx->type : 'transaction');
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="py-3 px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase <?php echo e($tx->amount >= 0 ? 'bg-emerald-100 dark:bg-violet-950/60 text-emerald-800 dark:text-violet-300' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300'); ?>">
                                <?php echo e(strtoupper($txTypeStr)); ?>

                            </span>
                        </td>
                        <td class="py-3 px-3 font-medium text-slate-800 dark:text-slate-200">
                            <?php echo e($tx->description ?? 'Wallet activity'); ?>

                        </td>
                        <td class="py-3 px-3 font-mono font-bold <?php echo e($tx->amount >= 0 ? 'text-[#2E8B3F] dark:text-violet-400' : 'text-slate-800 dark:text-slate-300'); ?>">
                            <?php echo e($tx->amount >= 0 ? '+' : ''); ?><?php echo e($currencySymbol); ?><?php echo e(number_format($tx->amount, 2)); ?>

                        </td>
                        <td class="py-3 px-3 font-mono text-slate-500 dark:text-slate-400">
                            <?php echo e($currencySymbol); ?><?php echo e(number_format($tx->balance_after ?? 0, 2)); ?>

                        </td>
                        <td class="py-3 px-3 text-right text-slate-400 text-xs font-mono">
                            <?php echo e($tx->created_at->format('M d, H:i')); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-xs text-slate-400 text-center py-6">No recent wallet transactions recorded.</p>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\segyrictext\resources\views/web/dashboard.blade.php ENDPATH**/ ?>