<!DOCTYPE html>
<html lang="en" 
      class="dark" 
      data-theme="dark"
      x-data="{
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          accountOpen: true,
          darkMode: document.documentElement.classList.contains('dark'),
          announcementDismissed: sessionStorage.getItem('announcement_dismissed') === 'true',
          commandSearchOpen: false,
          searchQuery: '',
          toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
          },
          toggleDarkMode() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('dark_mode', this.darkMode);
              localStorage.setItem('verixa_theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
                  document.documentElement.setAttribute('data-theme', 'dark');
                  document.documentElement.style.backgroundColor = '#0A0718';
              } else {
                  document.documentElement.classList.remove('dark');
                  document.documentElement.setAttribute('data-theme', 'light');
                  document.documentElement.style.backgroundColor = '#F5F3FF';
              }
          },
          dismissAnnouncement() {
              this.announcementDismissed = true;
              sessionStorage.setItem('announcement_dismissed', 'true');
          },
          init() {
              window.addEventListener('keydown', (e) => {
                  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                      e.preventDefault();
                      this.commandSearchOpen = !this.commandSearchOpen;
                  }
                  if (e.key === 'Escape') {
                      this.commandSearchOpen = false;
                  }
              });
          }
      }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark light">
    <meta name="theme-color" content="#0A0718">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> | Verixa</title>
    <link rel="icon" type="image/jpg" href="<?php echo e(asset('images/verixa-logo.jpg')); ?>">

    <!-- 0. Instant Render-Blocking Theme Initializer (Prevents White Flash) -->
    <script>
        (function() {
            var saved = localStorage.getItem('dark_mode');
            var verixaTheme = localStorage.getItem('verixa_theme');
            var isDark = saved === 'true' || verixaTheme === 'dark' || (saved === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) || saved === null;
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.style.backgroundColor = '#0A0718';
                document.documentElement.style.colorScheme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.style.backgroundColor = '#F5F3FF';
                document.documentElement.style.colorScheme = 'light';
            }
        })();
    </script>

    <!-- Instant Pre-Paint Base Styles -->
    <style>
        html, body {
            background-color: #0A0718;
            color: #F8FAFC;
            margin: 0;
            padding: 0;
        }
        html:not(.dark), html:not(.dark) body {
            background-color: #F5F3FF;
            color: #1E1B4B;
        }
        html.dark, html.dark body {
            background-color: #0A0718;
            color: #F8FAFC;
        }
        [x-cloak] { display: none !important; }
        :root {
            --bg: #F5F3FF;
            --bg-card: #FFFFFF;
            --bg-card-elevated: #EDE9FE;
            --border: rgba(30, 27, 75, 0.08);
            --primary: #7C3AED;
            --accent: #06B6D4;
            --text-primary: #1E1B4B;
            --text-secondary: #6B7280;
        }
        [data-theme="dark"], .dark {
            --bg: #0A0718;
            --bg-card: #110D24;
            --bg-card-elevated: #1A1535;
            --border: rgba(255, 255, 255, 0.08);
            --primary: #A78BFA;
            --accent: #06B6D4;
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #C4B5FD;
            border-radius: 999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #312E81;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #A78BFA;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #4C1D95;
        }
    </style>

    <!-- Google Fonts Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        verixa: {
                            950: '#0A0718',
                            900: '#110D24',
                            850: '#1A1535',
                            800: '#231E45',
                            750: '#2E2756',
                            700: '#3B3370',
                            600: '#4C3D9E',
                            500: '#7C3AED',
                            400: '#A78BFA',
                            300: '#C4B5FD',
                            200: '#DDD6FE',
                            100: '#EDE9FE',
                            50: '#F5F3FF'
                        },
                        cyber: {
                            violet: '#7C3AED',
                            violetHover: '#6D28D9',
                            indigo: '#4F46E5',
                            cyan: '#06B6D4',
                            cyanLight: '#22D3EE',
                            fuchsia: '#D946EF',
                            emerald: '#10B981'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    borderRadius: {
                        'pill': '999px',
                        'card': '18px',
                        'sidebar': '22px'
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
    <script>
        // CRITICAL: Synchronously purge any payment iframe on page load / bfcache restore
        (function() {
            function purgePaymentIframes() {
                try {
                    var frames = document.getElementsByTagName('iframe');
                    for (var i = frames.length - 1; i >= 0; i--) {
                        var f = frames[i];
                        if (f.id === 'in-app-payment-iframe' || f.id === 'complete-payment-iframe' ||
                            (f.src && (f.src.indexOf('paystack') !== -1 || f.src.indexOf('stripe') !== -1 || f.src.indexOf('checkout') !== -1))) {
                            f.src = 'about:blank';
                            if (f.parentNode) f.parentNode.removeChild(f);
                        }
                    }
                    var container = document.getElementById('in-app-payment-iframe-slot');
                    if (container) container.innerHTML = '';
                } catch(e) {}
            }
            purgePaymentIframes();
            window.addEventListener('pageshow', purgePaymentIframes);
            window.addEventListener('pagehide', purgePaymentIframes);
            window.addEventListener('beforeunload', purgePaymentIframes);
        })();

        // In-App Embedded Payment Helper (Keeps user 100% inside native application - Jump/NativePHP)
        window.openInAppPayment = function(url, title, reference, onSuccess, onCancel) {
            console.log('[IN-APP-PAYMENT] === Function called ===');
            console.log('[IN-APP-PAYMENT] URL:', url);
            console.log('[IN-APP-PAYMENT] Reference:', reference);
            
            var modal = document.getElementById('in-app-payment-modal');
            var slot = document.getElementById('in-app-payment-iframe-slot');
            var loader = document.getElementById('in-app-payment-loader');
            var subEl = document.getElementById('in-app-payment-subtitle');

            if (!modal || !slot) {
                console.error('[IN-APP-PAYMENT] ❌ Modal elements not found in DOM!');
                alert('In-app payment modal not available. Please contact support.');
                if (typeof onCancel === 'function') onCancel();
                return;
            }

            if (subEl && title) subEl.textContent = title;
            if (loader) loader.style.display = 'flex';
            
            // Store callbacks for later use
            window.__paymentInAppCallbacks = window.__paymentInAppCallbacks || {};
            window.__paymentInAppCallbacks[reference] = { onSuccess: onSuccess, onCancel: onCancel };

            // Dynamically create iframe ONLY when opening payment
            slot.innerHTML = '';
            var iframe = document.createElement('iframe');
            iframe.id = 'in-app-payment-iframe';
            iframe.style.cssText = 'width: 100%; height: 100%; border: none;';
            iframe.setAttribute('allow', 'payment; camera; geolocation');
            iframe.setAttribute('sandbox', 'allow-same-origin allow-scripts allow-forms allow-popups');
            
            iframe.onload = function() {
                if (loader) loader.style.display = 'none';
                console.log('[IN-APP-PAYMENT] Iframe loaded successfully');
            };

            iframe.onerror = function(e) {
                console.error('[IN-APP-PAYMENT] ❌ Iframe failed to load:', e);
                if (loader) loader.style.display = 'none';
                alert('Payment checkout failed to load.');
                window.closeInAppPayment();
                if (typeof onCancel === 'function') onCancel();
            };
            
            iframe.src = url;
            slot.appendChild(iframe);
            
            modal.style.display = 'flex';

            // Timeout fallback if iframe doesn't load
            setTimeout(function() {
                if (loader && loader.style.display !== 'none') {
                    if (loader) loader.style.display = 'none';
                    window.closeInAppPayment();
                    if (typeof onCancel === 'function') onCancel();
                }
            }, 10000);

            // Start global payment polling for this reference
            if (typeof window.startGlobalPaymentPolling === 'function' && reference) {
                window.startGlobalPaymentPolling({ reference: reference });
            }
        };

        // Close in-app payment modal
        window.closeInAppPayment = function() {
            var modal = document.getElementById('in-app-payment-modal');
            var slot = document.getElementById('in-app-payment-iframe-slot');
            if (modal) modal.style.display = 'none';
            if (slot) slot.innerHTML = '';
        };
    </script>

    <?php echo $__env->yieldContent('head_styles'); ?>
</head>
<body class="bg-[#F5F3FF] dark:bg-[#0A0718] text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col selection:bg-violet-500 selection:text-white">

    <!-- 1. Full-Width Announcement Banner -->
    <?php if(isset($activeAnnouncement) && $activeAnnouncement): ?>
    <div x-show="!announcementDismissed" x-transition.opacity.duration.300ms class="relative z-50 bg-gradient-to-r from-verixa-950 via-violet-900/80 to-verixa-950 text-white text-xs md:text-sm font-medium py-2 px-6 flex items-center justify-between border-b border-violet-500/20 shadow-sm w-full">
        <div class="flex-1 text-center flex items-center justify-center gap-2 truncate">
            <span class="inline-block text-base"><?php echo e($activeAnnouncement->flag_emoji ?? '🇬🇧'); ?></span>
            <span class="tracking-wide"><?php echo e($activeAnnouncement->message); ?></span>
            <?php if($activeAnnouncement->link_url): ?>
                <a href="<?php echo e($activeAnnouncement->link_url); ?>" class="underline font-semibold ml-1 text-cyan-400 hover:text-cyan-300 transition-colors">Learn more</a>
            <?php endif; ?>
        </div>
        <button @click="dismissAnnouncement()" type="button" class="ml-4 text-white/70 hover:text-white transition-colors p-1 rounded hover:bg-white/10 focus:outline-none" title="Dismiss announcement">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <?php endif; ?>

    <!-- 2. Full-Width Top Navigation Bar -->
    <header class="w-full h-16 bg-white/90 dark:bg-[#110D24]/90 backdrop-blur-md border-b border-violet-200/60 dark:border-white/10 sticky top-0 z-50 px-4 md:px-8 flex items-center justify-between shadow-xs transition-colors">
        
        <!-- Left: Verixa Brand + Sidebar Toggle + Global Command Search Trigger -->
        <div class="flex items-center gap-3 md:gap-5">
            <!-- 1. Verixa Brand Logo & Name -->
            <a href="<?php echo e(route('web.dashboard')); ?>" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-violet-600 via-purple-600 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-violet-500/25 group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4"/>
                        <path d="M12 3l8 4.5v7c0 4.5-3.5 8.5-8 10-4.5-1.5-8-5.5-8-10v-7L12 3z"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-slate-900 dark:text-white text-base tracking-tight leading-tight flex items-center gap-1.5">
                        Verixa
                        <span class="text-[9px] font-extrabold px-1.5 py-0.2 bg-violet-500/15 text-violet-600 dark:text-violet-400 rounded-md border border-violet-500/20">PRO</span>
                    </span>
                </div>
            </a>

            <!-- 2. Sidebar Toggle Icon -->
            <button 
                @click="toggleSidebar()" 
                type="button" 
                class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-violet-50 dark:hover:bg-white/10 transition-colors focus:outline-none" 
                title="Toggle Sidebar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                    <line x1="9" y1="3" x2="9" y2="21"/>
                </svg>
            </button>

            <!-- 3. Global Command / Quick Number Finder Trigger -->
            <button 
                @click="commandSearchOpen = true"
                type="button"
                class="hidden md:flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-violet-50 dark:bg-white/5 border border-violet-200/60 dark:border-white/10 text-xs text-slate-500 dark:text-slate-400 hover:border-violet-500/50 hover:bg-violet-100/60 dark:hover:bg-white/10 transition-all cursor-pointer"
            >
                <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span>Quick service search...</span>
                <kbd class="px-1.5 py-0.5 rounded bg-white dark:bg-slate-800 text-[10px] font-mono font-bold shadow-xs border border-violet-200 dark:border-white/10">⌘K</kbd>
            </button>
        </div>

        <!-- Right: "+ Deposit" Action + Day/Dark Toggle + Notifications -->
        <div class="flex items-center gap-3 md:gap-4">

            <!-- Sleek "+ Deposit" Micro-Pill -->
            <a href="<?php echo e(route('web.refill-credits')); ?>" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-pill text-xs font-bold shadow-md shadow-violet-600/25 hover:shadow-lg transition-all group"
               title="Deposit credits">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Deposit</span>
            </a>

            <!-- Day / Dark Mode Toggle Pill -->
            <button 
                @click="toggleDarkMode()" 
                type="button" 
                class="flex items-center gap-1.5 px-3 py-1.5 bg-violet-50 dark:bg-white/10 hover:bg-violet-100 dark:hover:bg-white/15 text-slate-700 dark:text-slate-200 rounded-pill text-xs font-bold transition-colors shadow-xs" 
                title="Toggle Theme"
            >
                <span x-show="!darkMode">☀️</span>
                <span x-show="darkMode">🌙</span>
                <span class="hidden sm:inline" x-text="darkMode ? 'Dark' : 'Light'">Light</span>
            </button>

            <!-- Notifications Bell -->
            <button 
                onclick="alert('All verification nodes reporting 100% nominal health.')" 
                type="button" 
                class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-violet-50 dark:hover:bg-white/10 transition-colors" 
                title="System Status"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-violet-500 rounded-full ring-2 ring-white dark:ring-[#110D24]"></span>
            </button>
        </div>
    </header>

    <!-- Global Command Palette Search Modal -->
    <div x-show="commandSearchOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-center pt-20 px-4 bg-black/60 backdrop-blur-sm" @click.self="commandSearchOpen = false">
        <div class="w-full max-w-lg bg-white dark:bg-[#110D24] border border-violet-200 dark:border-white/15 rounded-2xl shadow-2xl overflow-hidden" @keydown.escape.window="commandSearchOpen = false">
            <div class="p-3 border-b border-violet-200 dark:border-white/10 flex items-center gap-3">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input 
                    type="text" 
                    placeholder="Search WhatsApp, Telegram, Google, USA, UK..." 
                    class="w-full bg-transparent border-none outline-none text-sm text-slate-900 dark:text-white placeholder-slate-400"
                    x-model="searchQuery"
                    x-ref="searchInput"
                    x-init="$watch('commandSearchOpen', val => val && setTimeout(() => $refs.searchInput.focus(), 100))"
                >
                <kbd class="px-2 py-0.5 rounded bg-violet-50 dark:bg-white/10 text-[10px] font-mono text-slate-500">ESC</kbd>
            </div>
            <div class="p-3 max-h-72 overflow-y-auto space-y-1 text-xs">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-2 py-1">Quick Navigation</div>
                <a href="<?php echo e(route('web.str-numbers')); ?>" class="flex items-center justify-between p-2 rounded-xl hover:bg-violet-500/10 hover:text-violet-500 dark:hover:bg-violet-500/20 text-slate-700 dark:text-slate-200 transition-colors">
                    <span class="flex items-center gap-2">⚡ Buy Short-Term Number (STR)</span>
                    <span class="font-mono text-[10px] text-violet-500">Instant</span>
                </a>
                <a href="<?php echo e(route('web.ltr-numbers')); ?>" class="flex items-center justify-between p-2 rounded-xl hover:bg-violet-500/10 hover:text-violet-500 dark:hover:bg-violet-500/20 text-slate-700 dark:text-slate-200 transition-colors">
                    <span class="flex items-center gap-2">📅 Rent Long-Term Line (LTR)</span>
                    <span class="font-mono text-[10px] text-amber-500">3-30 Days</span>
                </a>
                <a href="<?php echo e(route('web.sms-logs')); ?>" class="flex items-center justify-between p-2 rounded-xl hover:bg-violet-500/10 hover:text-violet-500 dark:hover:bg-violet-500/20 text-slate-700 dark:text-slate-200 transition-colors">
                    <span class="flex items-center gap-2">💬 SMS Messages & OTP Logs</span>
                    <span class="font-mono text-[10px] text-purple-500">Live</span>
                </a>
                <a href="<?php echo e(route('web.refill-credits')); ?>" class="flex items-center justify-between p-2 rounded-xl hover:bg-violet-500/10 hover:text-violet-500 dark:hover:bg-violet-500/20 text-slate-700 dark:text-slate-200 transition-colors">
                    <span class="flex items-center gap-2">💳 Deposit & Reload Wallet</span>
                    <span class="font-mono text-[10px] text-violet-500">Paystack</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. Body Layout: Sidebar + Content -->
    <?php
        $userWallet = auth()->user()->wallet ?? null;
        $curSymbol = $userWallet->currency->symbol ?? '$';
        $curBalance = $userWallet->available_balance ?? 0.00;
    ?>
    <div class="flex-1 w-full max-w-[1560px] mx-auto p-4 md:p-6 flex gap-6 items-start relative">

        <!-- Verixa Obsidian-Glass Floating Sidebar -->
        <aside 
            :class="sidebarCollapsed ? 'w-[72px]' : 'w-[250px]'" 
            class="bg-gradient-to-b from-[#1A1535]/95 via-[#110D24]/95 to-[#0A0718]/95 dark:from-[#110D24]/95 dark:to-[#0A0718]/95 backdrop-blur-xl transition-all duration-300 ease-in-out flex flex-col shrink-0 sticky top-20 shadow-2xl select-none rounded-sidebar border border-violet-800/40 dark:border-white/10 overflow-hidden text-white"
        >
            <!-- User Status Card -->
            <div class="p-4 border-b border-white/10 bg-white/5 flex items-center gap-3 overflow-hidden">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-violet-500 to-purple-600 text-white flex items-center justify-center font-black text-base shadow-md shadow-violet-500/25 shrink-0">
                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                </div>
                <div x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="truncate flex-1 min-w-0">
                    <div class="font-bold text-sm text-white truncate leading-tight"><?php echo e(auth()->user()->name ?? 'Account'); ?></div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-violet-400 animate-pulse"></span>
                        <span class="text-[11px] text-violet-300 font-semibold tracking-wide">VERIFIED</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="py-3 px-3 space-y-1.5 overflow-y-auto overflow-x-hidden text-sm" id="desktop-sidebar-nav">
                
                <!-- 1. Dashboard -->
                <a href="<?php echo e(route('web.dashboard')); ?>" 
                   title="Dashboard"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.dashboard') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.dashboard')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.dashboard') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="9" rx="2"/>
                            <rect x="14" y="3" width="7" height="5" rx="2"/>
                            <rect x="14" y="12" width="7" height="9" rx="2"/>
                            <rect x="3" y="16" width="7" height="5" rx="2"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Dashboard</span>
                    </div>
                </a>

                <!-- 2. Buy STR Numbers -->
                <a href="<?php echo e(route('web.str-numbers')); ?>" 
                   title="Buy STR Numbers"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.str-numbers') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.str-numbers')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.str-numbers') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="5" y="2" width="14" height="20" rx="3"/>
                            <path d="M12 18h.01"/>
                            <path d="M13 7l-3 4h4l-2 4"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Buy Number (STR)</span>
                    </div>
                    <span x-show="!sidebarCollapsed" class="px-1.5 py-0.5 text-[9px] font-extrabold tracking-wider bg-violet-500/15 text-violet-300 border border-violet-500/25 rounded-md">INSTANT</span>
                </a>

                <!-- 3. Rent LTR Numbers -->
                <a href="<?php echo e(route('web.ltr-numbers')); ?>" 
                   title="Rent LTR Numbers"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.ltr-numbers') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.ltr-numbers')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.ltr-numbers') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="3"/>
                            <path d="M16 2v4M8 2v4M3 10h18"/>
                            <path d="M8 15h3l2 2 3-3"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Rent Line (LTR)</span>
                    </div>
                    <span x-show="!sidebarCollapsed" class="px-1.5 py-0.5 text-[9px] font-extrabold tracking-wider bg-white/10 text-slate-300 border border-white/10 rounded-md">RENTAL</span>
                </a>

                <!-- 4. SMS Logs -->
                <a href="<?php echo e(route('web.sms-logs')); ?>" 
                   title="SMS Logs"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.sms-logs') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.sms-logs')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.sms-logs') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            <path d="M8 12h.01M12 12h.01M16 12h.01"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">SMS Logs</span>
                    </div>
                    <span x-show="!sidebarCollapsed" class="px-1.5 py-0.5 text-[9px] font-extrabold tracking-wider bg-violet-500/15 text-violet-300 border border-violet-500/25 rounded-md">LIVE</span>
                </a>

                <!-- 5. Reload Credits -->
                <a href="<?php echo e(route('web.refill-credits')); ?>" 
                   title="Deposit & Wallet"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.refill-credits') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.refill-credits')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.refill-credits') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect width="20" height="14" x="2" y="5" rx="3"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                            <path d="M6 15h2M10 15h4"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Deposit / Refill</span>
                    </div>
                </a>

                <!-- 6. Pricing -->
                <a href="<?php echo e(route('web.pricing')); ?>" 
                   title="Pricing Catalog"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.pricing') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.pricing')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.pricing') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                            <line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Pricing Catalog</span>
                    </div>
                </a>

                <!-- 7. Support -->
                <a href="<?php echo e(route('web.support')); ?>" 
                   title="Support Helpdesk"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.support') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.support')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.support') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Support Helpdesk</span>
                    </div>
                </a>

                <!-- 8. Account Settings -->
                <a href="<?php echo e(route('web.account')); ?>" 
                   title="Account Settings"
                   class="sidebar-nav-link flex items-center justify-between px-3.5 py-3 rounded-xl font-medium transition-all relative group <?php echo e(request()->routeIs('web.account') ? 'bg-white/15 dark:bg-violet-500/15 border border-violet-500/30 text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'); ?>">
                    <?php if(request()->routeIs('web.account')): ?>
                        <div class="sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50"></div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 truncate">
                        <svg class="w-5 h-5 shrink-0 <?php echo e(request()->routeIs('web.account') ? 'text-violet-400' : 'text-slate-400 group-hover:text-white'); ?>" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <line x1="4" y1="6" x2="20" y2="6"/>
                            <line x1="4" y1="12" x2="20" y2="12"/>
                            <line x1="4" y1="18" x2="20" y2="18"/>
                            <circle cx="8" cy="6" r="2" fill="currentColor"/>
                            <circle cx="16" cy="12" r="2" fill="currentColor"/>
                            <circle cx="10" cy="18" r="2" fill="currentColor"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Account Settings</span>
                    </div>
                </a>
            </nav>

            <!-- Bottom: Wallet & Logout -->
            <div class="p-3 border-t border-white/10 shrink-0 bg-white/5 space-y-2">
                <div x-show="!sidebarCollapsed" class="p-2.5 rounded-xl bg-black/40 border border-white/10 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Balance</span>
                        <span id="desktop-sidebar-balance" class="font-mono font-bold text-violet-400 text-sm"><?php echo e($curSymbol); ?><?php echo e(number_format($curBalance, 2)); ?></span>
                    </div>
                    <a href="<?php echo e(route('web.refill-credits')); ?>" class="px-2.5 py-1 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-lg font-bold text-[11px] shadow-sm transition-colors">
                        + Deposit
                    </a>
                </div>

                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="source" value="desktop">
                    <button type="submit" 
                            title="Logout"
                            class="w-full flex items-center justify-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-400 hover:bg-red-500/20 hover:text-red-300 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate">Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main id="main-content-area" class="flex-1 min-w-0 pb-8 transition-opacity duration-150">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- Global Page Loading Progress Indicator -->
    <div id="global-page-loader" style="position: fixed; top: 0; left: 0; height: 3px; width: 0%; background: linear-gradient(90deg, #7C3AED, #06B6D4, #D946EF); box-shadow: 0 0 12px rgba(124, 58, 237, 0.8), 0 0 6px rgba(6, 182, 212, 0.6); z-index: 999999; pointer-events: none; transition: width 0.2s ease-out, opacity 0.25s ease; opacity: 0;"></div>

    <script>
        (function() {
            const loader = document.getElementById('global-page-loader');
            let progressTimer = null;
            let currentWidth = 0;

            window.startPageLoading = function() {
                if (!loader) return;
                if (progressTimer) clearInterval(progressTimer);
                currentWidth = 25;
                loader.style.opacity = '1';
                loader.style.width = currentWidth + '%';

                progressTimer = setInterval(() => {
                    if (currentWidth < 90) {
                        currentWidth += Math.random() * 15;
                        if (currentWidth > 90) currentWidth = 90;
                        loader.style.width = currentWidth + '%';
                    }
                }, 100);
            };

            window.finishPageLoading = function() {
                if (!loader) return;
                if (progressTimer) clearInterval(progressTimer);
                loader.style.width = '100%';
                setTimeout(() => {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.width = '0%';
                    }, 250);
                }, 120);
            };

            // Update sidebar active highlights instantly
            function updateSidebarState(url) {
                const links = document.querySelectorAll('.sidebar-nav-link');
                const targetPath = new URL(url, window.location.origin).pathname.replace(/\/$/, '');

                links.forEach(link => {
                    const linkHref = link.getAttribute('href');
                    if (!linkHref) return;
                    const linkPath = new URL(linkHref, window.location.origin).pathname.replace(/\/$/, '');
                    const isCurrent = (linkPath === targetPath);
                    const indicator = link.querySelector('.sidebar-active-indicator');

                    if (isCurrent) {
                        link.classList.remove('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
                        link.classList.add('bg-white/15', 'dark:bg-violet-500/15', 'border', 'border-violet-500/30', 'text-white', 'font-bold', 'shadow-sm');
                        if (!indicator) {
                            const newInd = document.createElement('div');
                            newInd.className = 'sidebar-active-indicator absolute left-0 top-2 bottom-2 w-1.5 bg-gradient-to-b from-violet-400 to-purple-500 rounded-r-full shadow-md shadow-violet-500/50';
                            link.prepend(newInd);
                        }
                    } else {
                        link.classList.remove('bg-white/15', 'dark:bg-violet-500/15', 'border', 'border-violet-500/30', 'text-white', 'font-bold', 'shadow-sm');
                        link.classList.add('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
                        if (indicator) {
                            indicator.remove();
                        }
                    }
                });
            }

            // SPA Smooth Page Loader
            async function navigateSpa(url, pushState = true) {
                const mainArea = document.getElementById('main-content-area');
                if (!mainArea) {
                    window.location.href = url;
                    return;
                }

                window.startPageLoading();
                mainArea.style.opacity = '0.5';
                updateSidebarState(url);

                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) {
                        window.location.href = url;
                        return;
                    }

                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newMain = doc.getElementById('main-content-area') || doc.querySelector('main');

                    if (newMain) {
                        if (doc.title) {
                            document.title = doc.title;
                        }

                        // Destroy old Alpine reactivity tree if active
                        if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                            try { window.Alpine.destroyTree(mainArea); } catch(e) {}
                        }

                        // Replace inner content
                        mainArea.innerHTML = newMain.innerHTML;

                        // Re-execute scripts embedded in new main area
                        const scripts = mainArea.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });

                        // Re-initialize Alpine.js on the updated tree if Alpine is present
                        if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                            try { window.Alpine.initTree(mainArea); } catch(e) {}
                        }

                        if (pushState) {
                            window.history.pushState({ spa: true, url }, '', url);
                        }

                        window.scrollTo({ top: 0, behavior: 'instant' });
                    } else {
                        window.location.href = url;
                        return;
                    }
                } catch (err) {
                    console.error('SPA load error, falling back to normal navigation:', err);
                    window.location.href = url;
                    return;
                } finally {
                    mainArea.style.opacity = '1';
                    window.finishPageLoading();
                }
            }

            // Listen to browser Back/Forward navigation
            window.addEventListener('popstate', (e) => {
                navigateSpa(window.location.href, false);
            });

            // Intercept link clicks for seamless instant SPA switching
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                if (link.getAttribute('target') === '_blank') {
                    const h = link.getAttribute('href') || '';
                    if (!h.startsWith('mailto:') && !h.startsWith('tel:') && !h.startsWith('sms:')) {
                        link.removeAttribute('target');
                    }
                }

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');
                const download = link.getAttribute('download');
                const noSpa = link.hasAttribute('data-no-spa');

                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:') || target === '_blank' || download !== null || noSpa) {
                    return;
                }

                // Check same origin
                try {
                    const parsedUrl = new URL(href, window.location.origin);
                    if (parsedUrl.origin !== window.location.origin) return;

                    // Exclude logout or external action paths if any
                    if (parsedUrl.pathname.includes('/logout')) return;

                    e.preventDefault();
                    navigateSpa(parsedUrl.href, true);
                } catch(err) {
                    // fallback to standard navigation
                }
            }, false);

            // Automatically attach loader to form submits
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (form.hasAttribute('data-no-loader')) return;
                window.startPageLoading();

                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.style.opacity = '0.75';
                    submitBtn.style.pointerEvents = 'none';
                }
            }, true);
        })();
    </script>

    <!-- Global Desktop Payment Instant Splash Overlay -->
    <div id="desktop-payment-splash" style="display: none;" class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/75 backdrop-blur-md transition-all duration-300">
        <div class="relative bg-gradient-to-b from-[#1A1535] to-[#0A0718] border border-violet-500/40 rounded-3xl p-8 max-w-sm w-full mx-4 text-center shadow-2xl shadow-violet-500/20 transform transition-transform" style="animation: desktopSplashPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-violet-500/20 border border-violet-500/50 flex items-center justify-center relative shadow-lg shadow-violet-500/30">
                <div class="w-14 h-14 rounded-full bg-violet-500 text-white flex items-center justify-center font-bold text-2xl shadow-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest bg-violet-500/20 text-violet-300 border border-violet-500/30 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-ping"></span>
                Instant Payment Confirmed
            </div>
            <h3 class="text-xl font-black text-white tracking-tight">Deposit Successful!</h3>
            <div class="my-4 p-4 rounded-2xl bg-white/5 border border-white/10">
                <div class="text-xs text-slate-400 uppercase font-semibold">Credited to Wallet</div>
                <div id="desktop-splash-amount" class="text-3xl font-extrabold text-violet-400 font-mono mt-1">+$25.00</div>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed mb-6">
                Your credits are active instantly across all STR & LTR verification lines.
            </p>
            <button type="button" onclick="document.getElementById('desktop-payment-splash').style.display='none'" class="w-full py-3 px-4 bg-violet-500 hover:bg-violet-400 text-white font-bold rounded-xl text-sm transition-all shadow-lg shadow-violet-500/25">
                Awesome, Continue
            </button>
        </div>
    </div>

    <style>
        @keyframes desktopSplashPop {
            0% { transform: scale(0.85); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>

    <script>
        window.showDesktopPaymentSplash = function(symbol, amount) {
            const splash = document.getElementById('desktop-payment-splash');
            const amtEl = document.getElementById('desktop-splash-amount');
            if (amtEl) {
                amtEl.textContent = '+' + (symbol || '$') + Number(amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            if (splash) {
                splash.style.display = 'flex';
                setTimeout(() => {
                    splash.style.display = 'none';
                }, 4500);
            }
        };
    </script>

    <!-- In-App Embedded Checkout Modal -->
    <div id="in-app-payment-modal" style="display: none; position: fixed; inset: 0; z-index: 999998; background: rgba(10, 7, 24, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); flex-direction: column;">
        <div style="background: #1A1535; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 16px; display: flex; align-items: center; justify-content: space-between; color: #fff; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 9px; background: linear-gradient(135deg, #7C3AED, #4F46E5); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(124,58,237,0.35);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div>
                    <div style="font-size: 14.5px; font-weight: 800; color: #fff; letter-spacing: -0.01em;">Secure Checkout</div>
                    <div id="in-app-payment-subtitle" style="font-size: 11px; color: #A78BFA; font-weight: 600;">Verixa In-App Gateway</div>
                </div>
            </div>
            <button type="button" onclick="window.closeInAppPayment()" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; cursor: pointer;" title="Close">✕</button>
        </div>
        <div style="flex: 1; position: relative; background: #FFFFFF; width: 100%; overflow: hidden;">
            <div id="in-app-payment-loader" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0A0718; color: #fff; z-index: 2;">
                <div style="width: 36px; height: 36px; border: 3px solid rgba(124,58,237,0.25); border-top-color: #7C3AED; border-radius: 50%; animation: spin 0.7s linear infinite; margin-bottom: 14px;"></div>
                <div style="font-size: 13.5px; font-weight: 700; color: #A78BFA; letter-spacing: 0.01em;">Opening secure payment gateway...</div>
                <div style="font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 4px;">Zero redirects • Staying inside Verixa</div>
            </div>
            <div id="in-app-payment-iframe-slot" style="width: 100%; height: 100%;">
                <!-- Dynamically injected on checkout only. NEVER present on page load. -->
            </div>
        </div>
    </div>

    <!-- CSS for spinner animation -->
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <!-- Global Real-Time Payment Synchronizer & Background Poller -->
    <script src="<?php echo e(asset('js/payment-sync.js')); ?>"></script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\segyrictext\resources\views/web/layouts/app.blade.php ENDPATH**/ ?>