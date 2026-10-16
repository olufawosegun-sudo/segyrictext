@php
    $b64File = public_path('images/Verixa-b64.txt');
    $logoSrc = file_exists($b64File) ? trim(file_get_contents($b64File)) : '/images/verixa-thumb.png';
@endphp
@extends('native.layouts.mobile')

@section('title', 'Verixa')

@section('head_styles')
<style>
    :root { --header-bg: #0B0F19; }

    /* Mobile-First Responsive Utilities */
    .pva-responsive-grid-2 {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    @media (min-width: 480px) {
        .pva-responsive-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
    }

    .pva-scroll-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .pva-scroll-pills::-webkit-scrollbar {
        display: none;
    }

    .pva-card-hover {
        transition: transform 0.16s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.16s ease, border-color 0.16s ease;
    }
    @media (min-width: 640px) {
        .pva-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30, 27, 75, 0.08);
            border-color: rgba(30, 27, 75, 0.16);
        }
    }
</style>
@endsection

@section('custom_top_bar')
<div style="background: #0B0F19; display: flex; align-items: center; justify-content: space-between; padding: 10px 18px 10px; flex-shrink: 0; border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; z-index: 1000;">
    <!-- Left: Verixa Logo / Branding with Signal Wave -->
    <div style="display: flex; align-items: center; gap: 8px;">
        <div style="width: 28px; height: 28px; border-radius: 8px; background: linear-gradient(135deg, #7C3AED, #06B6D4, #6366F1); display: flex; align-items: center; justify-content: center; color: #FFFFFF; box-shadow: 0 2px 10px rgba(16,185,129,0.3); overflow: hidden;">
            <img src="{{ $logoSrc }}" alt="Logo" style="width: 28px; height: 28px; object-fit: contain; border-radius: 8px; display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                <path d="M2 12h5l3-9 4 18 3-9h5"/>
            </svg>
        </div>
        <div style="display: flex; align-items: center; gap: 5px;">
            <span style="font-size: 16.5px; font-weight: 900; color: #FFFFFF; letter-spacing: -0.02em;">Verixa</span>
            <span style="font-size: 9px; font-weight: 800; background: rgba(16, 185, 129, 0.15); color: #7C3AED; padding: 1px 5px; border-radius: 4px; border: 1px solid rgba(16, 185, 129, 0.3);">PRO</span>
        </div>
    </div>

    <!-- Right Controls Row -->
    <div style="display: flex; align-items: center; gap: 7px;">
        <!-- Theme Toggle -->
        <button type="button" id="theme-toggle-btn" onclick="toggleTheme()" class="blitz-btn" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: #FFFFFF; cursor: pointer; padding: 0;" title="Toggle Theme">
            <svg id="theme-toggle-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/>
                <line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
        </button>

        <!-- Map / Country / Region Picker -->
        <button type="button" onclick="openCountryPicker(event)" class="blitz-btn" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 9999px; height: 30px; display: inline-flex; align-items: center; justify-content: center; gap: 5px; color: #FFFFFF; cursor: pointer; padding: 0 10px;" title="Select Region / Location">
            <span id="header-country-flag" style="font-size: 14px; line-height: 1;">🇺🇸</span>
            <span id="header-country-code" style="font-size: 11.5px; font-weight: 800; letter-spacing: 0.02em;">USA</span>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" style="opacity: 0.85;"><polyline points="6 9 12 15 18 9"/></svg>
        </button>

        <!-- Notification Bell -->
        <button type="button" onclick="openNotificationsModal()" class="blitz-btn" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: #FFFFFF; cursor: pointer; position: relative; padding: 0;" title="Notifications">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span style="position: absolute; top: 5px; right: 5px; width: 6px; height: 6px; border-radius: 50%; background: #7C3AED; border: 1.5px solid #0B0F19;"></span>
        </button>
    </div>
</div>

<!-- Scrolling Promo Ticker (Cyber Telecom Obsidian Style) -->
<div style="background: #070B14; overflow: hidden; height: 28px; display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; position: relative;">
    <div style="display: flex; white-space: nowrap; animation: promoTicker 24s linear infinite; will-change: transform;">
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: #34D399;">
            ⚡ Instant Provisioning — 35+ Global Gateways Active
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,0.9);">
            🇺🇸 United States Physical Non-VoIP
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,0.9);">
            🇬🇧 United Kingdom All Services numbers
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: #38BDF8;">
            🛡️ 256-Bit Encrypted Carrier Routing • 100% Auto-Refund
        </span>
        <!-- Duplicated track for seamless infinite loop -->
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: #34D399;">
            ⚡ Instant Provisioning — 35+ Global Gateways Active
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,0.9);">
            🇺🇸 United States Physical Non-VoIP
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,0.9);">
            🇬🇧 United Kingdom All Services numbers
        </span>
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0 24px; font-size: 11.5px; font-weight: 700; color: #38BDF8;">
            🛡️ 256-Bit Encrypted Carrier Routing • 100% Auto-Refund
        </span>
    </div>
</div>

@if(isset($paymentNotice) && $paymentNotice)
<div style="margin: 12px 16px 0; padding: 12px 16px; border-radius: 12px; display: flex; align-items: center; gap: 10px; background: {{ $paymentNotice['type'] === 'success' ? 'rgba(34, 197, 94, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; border: 1px solid {{ $paymentNotice['type'] === 'success' ? 'rgba(34, 197, 94, 0.4)' : 'rgba(239, 68, 68, 0.4)' }}; color: {{ $paymentNotice['type'] === 'success' ? '#A78BFA' : '#f87171' }}; font-size: 13px; font-weight: 600;">
    <span style="font-size: 16px;">{{ $paymentNotice['type'] === 'success' ? '✓' : '✕' }}</span>
    <span style="flex: 1;">{{ $paymentNotice['message'] }}</span>
</div>
@endif
@endsection

@section('content')
<div id="blitz-panel-dashboard" class="blitz-tab-panel {{ ($requestedTab ?? 'dashboard') === 'dashboard' ? 'blitz-tab-active' : '' }}">
<div style="display: flex; flex-direction: column; gap: 14px; padding-top: 4px;">

    <!-- Vault Balance Hero Card (PVADeals Navy) -->
    <div style="background: #1E1B4B; border-radius: 20px; padding: 20px 22px; color: #FFFFFF; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18); position: relative; overflow: hidden;">
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1;">
            <div>
                <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.15); color: #FFFFFF; padding: 4px 10px; border-radius: 9999px;">Vault Liquid Balance</span>
                <div style="font-size: 30px; font-weight: 900; color: #FFFFFF; font-family: 'JetBrains Mono', monospace; letter-spacing: -0.02em; line-height: 1; margin-top: 10px;">
                    <span id="dash-wallet-symbol" style="color: #A78BFA;">{{ $wallet->currency->symbol ?? '₦' }}</span><span id="dash-wallet-balance">{{ number_format($wallet->available_balance, $wallet->currency->decimal_places ?? 2) }}</span>
                </div>
            </div>
            <button type="button" class="blitz-btn" onclick="openDepositModal()" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 12px; padding: 10px 18px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35); flex-shrink: 0;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span>Deposit</span>
            </button>
        </div>

        @if($wallet->active_holds_total > 0)
            <div style="font-size: 11.5px; color: #FDE68A; margin-top: 12px; display: flex; align-items: center; gap: 6px; position: relative; z-index: 1;">
                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #FDE68A;"></span>
                <span>{{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->active_holds_total, $wallet->currency->decimal_places ?? 2) }} held in active sessions</span>
            </div>
        @endif

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.12); font-size: 11px; color: rgba(255,255,255,0.75); position: relative; z-index: 1;">
            <span style="display: flex; align-items: center; gap: 5px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                256-Bit Encrypted Carrier Line
            </span>
            <span style="color: #67E8F9; font-weight: 600;">Auto-Refund on Cancel</span>
        </div>
    </div>

    <!-- Active Verification Monitor (Shown when waiting for SMS/OTP) -->
    @if(isset($activeSessions) && $activeSessions->count() > 0)
        <div>
            <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span class="status-dot status-dot-pulse" style="background: #7C3AED; width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
                    <span>ACTIVE VERIFICATIONS</span>
                </div>
                <span style="font-size: 11px; background: rgba(124, 58, 237, 0.12); color: #7C3AED; border: 1px solid rgba(124, 58, 237, 0.25); padding: 3px 10px; border-radius: 9999px; font-weight: 800;">
                    {{ $activeSessions->count() }} active
                </span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($activeSessions as $session)
                    <div style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.12); border-radius: 18px; padding: 18px; box-shadow: 0 4px 16px rgba(30, 27, 75, 0.04); display: flex; flex-direction: column; gap: 12px;" id="session-card-{{ $session->id }}">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 18px;">
                                    @php
                                        $iso = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                                        $flag = '';
                                        foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                        echo $flag;
                                    @endphp
                                </span>
                                <span style="font-size: 14px; font-weight: 800; color: #1E1B4B; text-transform: uppercase;">{{ $session->service?->name ?? 'SMS Service' }}</span>
                                <span style="background: #EEF1F8; color: #1E1B4B; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $session->phoneNumber?->country?->name ?? 'US' }}</span>
                            </div>
                            <span style="font-size: 13px; color: #b45309; font-family: 'JetBrains Mono', monospace; font-weight: 800;" id="timer-{{ $session->id }}" data-expires="{{ $session->expires_at?->toIso8601String() }}">--:--</span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="font-size: 16px; font-weight: 800; color: #1E1B4B; font-family: 'JetBrains Mono', monospace; background: #EEF1F8; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 12px; padding: 10px 14px; flex: 1;" id="active-phone-number-{{ $session->id }}">
                                {{ $session->phoneNumber?->phone_number }}
                            </div>
                            <button type="button" onclick="copyToClipboard('{{ $session->phoneNumber?->phone_number }}', this)" style="background: #1E1B4B; color: #FFFFFF; border: none; border-radius: 12px; padding: 10px 16px; font-size: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.15);">
                                Copy
                            </button>
                        </div>

                        @if($session->verification_code)
                            <div style="background: rgba(124, 58, 237, 0.08); border: 1.5px solid #7C3AED; border-radius: 14px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 10.5px; font-weight: 800; color: #7C3AED; text-transform: uppercase;">✓ Code Received</div>
                                    <div style="font-size: 22px; font-weight: 900; color: #1E1B4B; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">{{ $session->verification_code }}</div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $session->verification_code }}', this)" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 10px; padding: 8px 16px; font-size: 12.5px; font-weight: 800; cursor: pointer; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);">
                                    Copy Code
                                </button>
                            </div>
                        @else
                            <div id="otp-container-{{ $session->id }}" style="display: none;">
                                <div style="background: rgba(124, 58, 237, 0.08); border: 1.5px solid #7C3AED; border-radius: 14px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <div style="font-size: 10.5px; font-weight: 800; color: #7C3AED; text-transform: uppercase;">✓ Code Received</div>
                                        <div style="font-size: 22px; font-weight: 900; color: #1E1B4B; font-family: 'JetBrains Mono', monospace; margin-top: 2px;" id="otp-text-{{ $session->id }}"></div>
                                    </div>
                                    <button type="button" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 10px; padding: 8px 16px; font-size: 12.5px; font-weight: 800; cursor: pointer; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);" id="btn-copy-otp-{{ $session->id }}">
                                        Copy Code
                                    </button>
                                </div>
                            </div>
                            <div id="waiting-{{ $session->id }}" style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #6B7A99; padding: 2px 0;">
                                <span style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #7C3AED; display: inline-block;"></span>
                                    Waiting for SMS arrival...
                                </span>
                                <button type="button" onclick="cancelSession({{ $session->id }})" style="background: #FFF1F2; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; color: #dc2626; padding: 6px 12px; font-size: 11.5px; font-weight: 700; cursor: pointer;" id="btn-cancel-{{ $session->id }}">
                                    Cancel & Refund
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Stat Cards: Purchased Numbers + Rented Numbers (PVADeals Style) -->
    <div>
        <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px;">
            ACCOUNT OVERVIEW
        </div>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <!-- Purchased Numbers Card -->
            <div onclick="switchTab('numbers', document.querySelector('[data-tab=numbers]'))" class="blitz-btn" style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 18px; padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 16px rgba(30, 27, 75, 0.04); cursor: pointer;">
                <div>
                    <div style="color: #6B7A99; font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Purchased Numbers</div>
                    <div style="color: #1E1B4B; font-size: 28px; font-weight: 900; margin-top: 4px; font-family: 'JetBrains Mono', monospace;">
                        {{ $stats['total_verifications'] ?? 0 }}
                    </div>
                </div>
                <button type="button" class="blitz-btn" onpointerdown="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" onclick="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" style="width: 44px; height: 44px; border-radius: 14px; background: #1E1B4B; color: #FFFFFF; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
            </div>

            <!-- Rented Numbers Card -->
            <div onclick="switchTab('rent', document.querySelector('[data-tab=rent]'))" class="blitz-btn" style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 18px; padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 16px rgba(30, 27, 75, 0.04); cursor: pointer;">
                <div>
                    <div style="color: #6B7A99; font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Rented Numbers (LTR)</div>
                    <div style="color: #1E1B4B; font-size: 28px; font-weight: 900; margin-top: 4px; font-family: 'JetBrains Mono', monospace;">
                        {{ $stats['rented_numbers'] ?? 0 }}
                    </div>
                </div>
                <button type="button" class="blitz-btn" onpointerdown="switchTab('rent', document.querySelector('[data-tab=rent]'), event)" onclick="switchTab('rent', document.querySelector('[data-tab=rent]'), event)" style="width: 44px; height: 44px; border-radius: 14px; background: #1E1B4B; color: #FFFFFF; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- SMS Services Tutorial Banner (PVADeals Theme) -->
    <div style="background: #FFFFFF; border-radius: 18px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; border: 1.5px solid rgba(30, 27, 75, 0.08); box-shadow: 0 4px 16px rgba(30, 27, 75, 0.04);">
        <div style="flex-shrink: 0; width: 46px; height: 46px; border-radius: 14px; background: #EEF1F8; color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </div>

        <div style="flex: 1;">
            <div style="font-size: 14px; font-weight: 800; color: #1E1B4B; margin-bottom: 2px;">SMS Services Tutorial</div>
            <div style="font-size: 12px; color: #6B7A99; line-height: 1.35; margin-bottom: 4px;">Learn how to use our SMS verification services effectively</div>
            <a href="javascript:void(0)" onclick="openTutorialModal()" style="font-size: 12px; font-weight: 800; color: #7C3AED; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                <span>Watch Tutorial</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Recent Activities Section (Dynamic Real Data from Database) -->
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase;">
                RECENT ACTIVITIES
            </div>
            <a href="javascript:void(0)" onclick="switchTab('account', document.querySelector('[data-tab=account]'))" style="font-size: 12px; font-weight: 800; color: #7C3AED; text-decoration: none;">See more</a>
        </div>

        @if(isset($recentActivities) && $recentActivities->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($recentActivities as $activity)
                    <div style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 16px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 12px; background: {{ $activity['type'] === 'sms' ? '#EEF1F8' : 'rgba(124, 58, 237, 0.10)' }}; display: flex; align-items: center; justify-content: center; color: {{ $activity['type'] === 'sms' ? '#1E1B4B' : '#7C3AED' }}; flex-shrink: 0;">
                                @if($activity['type'] === 'sms')
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                                        <line x1="2" y1="10" x2="22" y2="10"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div style="font-size: 13.5px; font-weight: 800; color: #1E1B4B;">{{ $activity['title'] }}</div>
                                <div style="font-size: 11.5px; color: #6B7A99; font-weight: 600; margin-top: 2px;">{{ $activity['date']->format('M d, Y · h:i A') }}</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 900; color: #1E1B4B; font-family: 'JetBrains Mono', monospace;">
                                {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($activity['amount'], 2) }}
                            </div>
                            <div style="font-size: 11px; font-weight: 800; color: {{ $activity['status_color'] }}; margin-top: 2px; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $activity['status_color'] }};"></span>
                                <span>{{ $activity['status'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div style="background: #FFFFFF; border: 1.5px dashed rgba(30, 27, 75, 0.15); border-radius: 18px; padding: 32px 20px; text-align: center; box-shadow: 0 4px 16px rgba(30, 27, 75, 0.02);">
                <div style="width: 46px; height: 46px; border-radius: 14px; background: #EEF1F8; color: #1E1B4B; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div style="font-size: 14px; font-weight: 800; color: #1E1B4B; margin-bottom: 2px;">No recent activities yet</div>
                <div style="font-size: 12px; color: #6B7A99; margin-bottom: 14px;">Your phone verification and wallet activities will appear here.</div>
                <button type="button" class="blitz-btn" onpointerdown="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" onclick="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" style="background: #7C3AED; color: #ffffff; border: none; border-radius: 12px; padding: 10px 18px; font-size: 13px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);">
                    Get First Number
                </button>
            </div>
        @endif
    </div>

    <div style="height: 16px;"></div>
</div>
</div><!-- END blitz-panel-home -->


<!-- ═══════════════════════════════════════════════════════════════════
     BLITZ TAB PANEL 2: BUY NUMBER (PVADeals Instant Provisioning)
═══════════════════════════════════════════════════════════════════ -->
<div id="blitz-panel-buy" class="blitz-tab-panel {{ ($requestedTab ?? '') === 'buy' ? 'blitz-tab-active' : '' }}">
    <div style="display: flex; flex-direction: column; gap: 14px;">

        <!-- Mature Top Segmented View Switcher (STR Numbers vs Order Number) -->
        <div style="display: flex; background: #EEF1F8; border-radius: 14px; padding: 4px; border: 1px solid rgba(30, 27, 75, 0.08);">
            <button type="button" id="str-tab-btn-list" onclick="switchStrSubView('list')" class="blitz-btn" style="flex: 1; border: none; background: #1E1B4B; color: #FFFFFF; font-size: 13px; font-weight: 800; padding: 10px 14px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.2); transition: all 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                <span>STR Numbers</span>
            </button>
            <button type="button" id="str-tab-btn-order" onclick="switchStrSubView('order')" class="blitz-btn" style="flex: 1; border: none; background: transparent; color: #6B7A99; font-size: 13px; font-weight: 700; padding: 10px 14px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                <span>+ Order Number</span>
            </button>
        </div>

        <!-- ═════════════════════════════════════════════════════════════
             SUB-VIEW 1: STR NUMBERS MANAGEMENT VIEW (PVADeals Styled)
        ═════════════════════════════════════════════════════════════ -->
        <div id="str-view-list" style="display: flex; flex-direction: column; gap: 14px;">

            <!-- Header Card (PVADeals Deep Navy) -->
            <div style="background: #1E1B4B; border-radius: 20px; padding: 20px; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18); position: relative; overflow: hidden; color: #FFFFFF;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.15); color: #FFFFFF; padding: 4px 10px; border-radius: 9999px;">20 Mins Disposable · Auto-Refund</span>
                        <div style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; margin-top: 8px;">STR Numbers</div>
                        <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 4px;">Short-term temporary non-VoIP carrier verification lines</div>
                    </div>
                    <button type="button" class="blitz-btn" onclick="switchStrSubView('order')" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 14px; padding: 10px 16px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35); flex-shrink: 0;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>New Number</span>
                    </button>
                </div>
            </div>

            <!-- Collapsible Filters Card (PVADeals Exact Arrangement) -->
            <div style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 18px; overflow: hidden; box-shadow: 0 2px 10px rgba(30, 27, 75, 0.04);">
                <!-- Filter Accordion Header -->
                <button type="button" class="blitz-btn" onclick="toggleStrFilters()" style="width: 100%; background: none; border: none; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 28px; height: 28px; border-radius: 8px; background: #EEF1F8; display: flex; align-items: center; justify-content: center; color: #1E1B4B;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        </div>
                        <span style="font-size: 14px; font-weight: 800; color: #1E1B4B;">Filters</span>
                        <span id="str-filter-count-badge" style="background: #EEF1F8; color: #1E1B4B; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 9999px;">0</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; color: #6B7A99; font-size: 12px; font-weight: 700;">
                        <span id="str-filters-toggle-text">Toggle</span>
                        <svg id="str-filters-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition: transform 0.2s ease;"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>

                <!-- Filter Body (Collapsible) -->
                <div id="str-filters-drawer" style="display: none; padding: 0 18px 18px; border-top: 1px solid rgba(30, 27, 75, 0.06); flex-direction: column; gap: 14px;">
                    <!-- 1. Date Range Picker (From — To) -->
                    <div style="margin-top: 14px;">
                        <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Date Range</label>
                        <div class="pva-responsive-grid-2">
                            <div>
                                <input type="date" id="str-filter-date-from" onchange="applyStrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                            </div>
                            <div>
                                <input type="date" id="str-filter-date-to" onchange="applyStrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Website & Number Search Grid -->
                    <div class="pva-responsive-grid-2">
                        <div>
                            <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Website / App</label>
                            <input type="text" id="str-filter-website" placeholder="Search service (e.g. WhatsApp)..." oninput="applyStrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Number</label>
                            <input type="text" id="str-filter-number" placeholder="Phone number..." oninput="applyStrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                        </div>
                    </div>

                    <!-- 3. Country Filter Pills -->
                    <div>
                        <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Country</label>
                        <div class="pva-scroll-pills">
                            <button type="button" class="blitz-btn str-country-filter-pill active" onclick="selectStrCountryFilter('all', this)" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 800; cursor: pointer; white-space: nowrap;">All</button>
                            <button type="button" class="blitz-btn str-country-filter-pill" onclick="selectStrCountryFilter('US', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">🇺🇸 US</button>
                            <button type="button" class="blitz-btn str-country-filter-pill" onclick="selectStrCountryFilter('GB', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">🇬🇧 UK</button>
                            <button type="button" class="blitz-btn str-country-filter-pill" onclick="selectStrCountryFilter('CA', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">🇨🇦 CA</button>
                            <button type="button" class="blitz-btn str-country-filter-pill" onclick="selectStrCountryFilter('ID', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">🇮🇩 ID</button>
                        </div>
                    </div>

                    <!-- 4. Action Buttons (Apply / Clear / Export) -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 4px; padding-top: 10px; border-top: 1px solid rgba(30, 27, 75, 0.06);">
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="blitz-btn" onclick="applyStrFilters()" style="background: #1E1B4B; color: #FFFFFF; border: none; border-radius: 10px; padding: 8px 16px; font-size: 12.5px; font-weight: 800; cursor: pointer;">
                                Apply
                            </button>
                            <button type="button" class="blitz-btn" onclick="clearStrFilters()" style="background: #EEF1F8; color: #6B7A99; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 10px; padding: 8px 14px; font-size: 12.5px; font-weight: 700; cursor: pointer;">
                                Clear
                            </button>
                        </div>
                        <button type="button" class="blitz-btn" onclick="exportStrCsv()" style="background: none; border: none; color: #7C3AED; font-size: 12.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; padding: 0;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            <span>Export</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Horizontal Status Filter Pills Dock -->
            <div>
                <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;" class="no-scrollbar pva-scroll-pills" id="str-status-filter-dock">
                    <button type="button" class="blitz-btn str-status-pill active" onclick="filterStrStatus('all', this)" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 800; white-space: nowrap; cursor: pointer; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.2); transition: all 0.15s ease;">
                        All ({{ ($recentSessions ?? collect([]))->count() }})
                    </button>
                    <button type="button" class="blitz-btn str-status-pill" onclick="filterStrStatus('reserved', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Reserved ({{ ($activeSessions ?? collect([]))->count() }})
                    </button>
                    <button type="button" class="blitz-btn str-status-pill" onclick="filterStrStatus('completed', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Completed ({{ ($recentSessions ?? collect([]))->where('status', 'completed')->count() }})
                    </button>
                    <button type="button" class="blitz-btn str-status-pill" onclick="filterStrStatus('flagged', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Flagged (0)
                    </button>
                    <button type="button" class="blitz-btn str-status-pill" onclick="filterStrStatus('timeout', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Timeout ({{ ($recentSessions ?? collect([]))->whereIn('status', ['expired', 'cancelled'])->count() }})
                    </button>
                </div>
            </div>

            <!-- STR Orders List Container -->
            <div id="str-orders-list-container" style="display: flex; flex-direction: column; gap: 10px;">
                @forelse(($recentSessions ?? collect([])) as $session)
                    @php
                        $isReserved = in_array($session->status, ['active', 'pending']);
                        $isCompleted = $session->status === 'completed';
                        $isTimeout = in_array($session->status, ['expired', 'cancelled']);
                        
                        $statusClass = $isReserved ? 'reserved' : ($isCompleted ? 'completed' : ($isTimeout ? 'timeout' : 'other'));
                        $badgeBg = $isCompleted ? 'rgba(16, 185, 129, 0.12)' : ($isReserved ? 'rgba(59, 130, 246, 0.12)' : 'rgba(239, 68, 68, 0.12)');
                        $badgeColor = $isCompleted ? '#059669' : ($isReserved ? '#2563EB' : '#DC2626');
                        $badgeLabel = $isReserved ? 'RESERVED' : ($isCompleted ? 'COMPLETED' : ($session->status === 'cancelled' ? 'CANCELLED' : 'TIMEOUT'));

                        $countryCode = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                        $flagEmoji = '';
                        foreach (str_split($countryCode) as $char) {
                            $flagEmoji .= mb_chr(127397 + ord($char), 'UTF-8');
                        }
                    @endphp
                    <div class="str-order-item-card" 
                         data-status="{{ $statusClass }}"
                         data-country="{{ $countryCode }}"
                         data-service="{{ strtolower($session->service?->name ?? 'sms') }}"
                         data-number="{{ $session->phoneNumber?->phone_number }}"
                         data-date="{{ $session->created_at->format('Y-m-d') }}"
                         style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 18px; padding: 16px; box-shadow: 0 2px 10px rgba(30, 27, 75, 0.03); display: flex; flex-direction: column; gap: 12px;">
                        
                        <!-- Top Row: Service + Status Badge -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: #EEF1F8; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                    {{ $flagEmoji }}
                                </div>
                                <div>
                                    <div style="font-size: 14.5px; font-weight: 800; color: #1E1B4B; display: flex; align-items: center; gap: 6px;">
                                        <span>{{ $session->service?->name ?? 'SMS Verification' }}</span>
                                        <span style="font-size: 11px; background: #EEF1F8; color: #1E1B4B; padding: 2px 6px; border-radius: 5px; font-weight: 700;">{{ $countryCode }}</span>
                                    </div>
                                    <div style="font-size: 11.5px; color: #6B7A99; font-weight: 600; margin-top: 1px;">
                                        {{ $session->created_at->format('M d, Y · h:i A') }}
                                    </div>
                                </div>
                            </div>

                            <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 10.5px; font-weight: 800; padding: 4px 10px; border-radius: 9999px; letter-spacing: 0.04em;">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        <!-- Phone Number Monospace Row with 1-Tap Copy -->
                        <div style="display: flex; align-items: center; justify-content: space-between; background: #F8FAFC; border: 1px solid rgba(30, 27, 75, 0.06); border-radius: 12px; padding: 10px 14px;">
                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 15px; font-weight: 800; color: #1E1B4B; letter-spacing: 0.02em;">
                                {{ $session->phoneNumber?->phone_number }}
                            </span>
                            <button type="button" class="blitz-btn" onclick="copyToClipboard('{{ $session->phoneNumber?->phone_number }}', this)" style="background: #1E1B4B; color: #FFFFFF; border: none; border-radius: 8px; padding: 6px 12px; font-size: 11.5px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                <span>Copy</span>
                            </button>
                        </div>

                        <!-- Verification Code Received Box -->
                        @if($session->verification_code)
                            <div style="background: rgba(124, 58, 237, 0.08); border: 1.5px solid #7C3AED; border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 10.5px; font-weight: 800; color: #7C3AED; text-transform: uppercase;">✓ Code Received</div>
                                    <div style="font-size: 20px; font-weight: 900; color: #1E1B4B; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">{{ $session->verification_code }}</div>
                                </div>
                                <button type="button" class="blitz-btn" onclick="copyToClipboard('{{ $session->verification_code }}', this)" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 8px; padding: 7px 14px; font-size: 12px; font-weight: 800; cursor: pointer;">Copy Code</button>
                            </div>
                        @elseif($isReserved)
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #b45309; font-weight: 700;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span id="str-timer-{{ $session->id }}" data-expires="{{ $session->expires_at?->toIso8601String() }}">--:--</span>
                                </div>
                                <button type="button" class="blitz-btn" onclick="cancelSession({{ $session->id }})" style="background: #FFF1F2; border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 8px; color: #dc2626; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer;">Cancel & Refund</button>
                            </div>
                        @endif

                    </div>
                @empty
                    <div id="str-empty-state" style="background: #FFFFFF; border: 1.5px dashed rgba(30, 27, 75, 0.15); border-radius: 20px; padding: 40px 20px; text-align: center;">
                        <div style="width: 52px; height: 52px; border-radius: 16px; background: #EEF1F8; color: #1E1B4B; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        </div>
                        <div style="font-size: 16px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">No STR numbers found</div>
                        <div style="font-size: 13px; color: #6B7A99; margin-bottom: 20px;">You haven't ordered any short-term temporary lines yet.</div>
                        <button type="button" class="blitz-btn" onclick="switchStrSubView('order')" style="background: #7C3AED; color: #ffffff; border: none; border-radius: 12px; padding: 12px 24px; font-size: 13.5px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);">
                            + Order New STR Number
                        </button>
                    </div>
                @endforelse
            </div>

        </div><!-- END str-view-list -->

        <!-- ═════════════════════════════════════════════════════════════
             SUB-VIEW 2: ORDER NEW NUMBER (Embedded Full Provisioning)
        ═════════════════════════════════════════════════════════════ -->
        <div id="str-view-order" style="display: none; flex-direction: column; gap: 14px;">
            <!-- Back to STR Numbers button -->
            <button type="button" class="blitz-btn" onclick="switchStrSubView('list')" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #1E1B4B; font-size: 13px; font-weight: 800; cursor: pointer; padding: 4px 0; align-self: flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Back to STR Numbers</span>
            </button>

            <!-- Embedded Provisioning Section -->
            <x-purchase-number-section 
                :wallet="$wallet" 
                :countries="$countries" 
                :services="$services" 
                :activeSessions="$activeSessions" 
            />
        </div><!-- END str-view-order -->

        <div style="height: 16px;"></div>
    </div>
</div><!-- END blitz-panel-buy -->

<!-- ═══════════════════════════════════════════════════════════════════
     BLITZ TAB PANEL 3: MY NUMBERS (Canonical Verification Monitor & History)
═══════════════════════════════════════════════════════════════════ -->
<div id="blitz-panel-numbers" class="blitz-tab-panel {{ in_array($requestedTab ?? '', ['numbers', 'sms', 'my-numbers']) ? 'blitz-tab-active' : '' }}">
    <div style="display: flex; flex-direction: column; gap: 14px;">

        <!-- Hero Header Card (PVADeals Navy) -->
        <div style="background: #1E1B4B; border-radius: 20px; padding: 20px; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18); position: relative; overflow: hidden; color: #FFFFFF;">
            @php
                $_safeRecentSessions = $recentSessions ?? collect([]);
                $_safeActiveSessions = $activeSessions ?? collect([]);
            @endphp
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.15); color: #FFFFFF; padding: 4px 10px; border-radius: 9999px;">Carrier Lines & Inboxes</span>
                    <div style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; margin-top: 8px;">My Numbers & SMS</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 4px;">Active & historical verification sessions</div>
                </div>
                <button type="button" class="blitz-btn" onpointerdown="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" onclick="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 14px; padding: 10px 16px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35); flex-shrink: 0;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>New Number</span>
                </button>
            </div>
        </div>

        <!-- FILTER STATUS SECTION -->
        <div>
            <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px;">
                FILTER BY STATUS
            </div>
            <!-- Filter Pills Dock (All, Active, Completed, Cancelled, Expired) -->
            <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; -webkit-overflow-scrolling: touch;" class="no-scrollbar" id="numbers-filter-bar">
                <button type="button" class="blitz-btn numbers-filter-pill active" onclick="filterNumberSessions('all', this)" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 800; white-space: nowrap; cursor: pointer; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25); transition: all 0.15s ease;">
                    All ({{ $_safeRecentSessions->count() }})
                </button>
                <button type="button" class="blitz-btn numbers-filter-pill" onclick="filterNumberSessions('active', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                    Active ({{ $_safeActiveSessions->count() }})
                </button>
                <button type="button" class="blitz-btn numbers-filter-pill" onclick="filterNumberSessions('completed', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                    Completed ({{ $_safeRecentSessions->where('status', 'completed')->count() }})
                </button>
                <button type="button" class="blitz-btn numbers-filter-pill" onclick="filterNumberSessions('cancelled', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                    Cancelled ({{ $_safeRecentSessions->where('status', 'cancelled')->count() }})
                </button>
                <button type="button" class="blitz-btn numbers-filter-pill" onclick="filterNumberSessions('expired', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                    Expired ({{ $_safeRecentSessions->where('status', 'expired')->count() }})
                </button>
            </div>
        </div>

        <!-- Active Sessions Section -->
        @if($_safeActiveSessions->count() > 0)
            <div id="numbers-active-container">
                <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span class="status-dot status-dot-pulse" style="background: #7C3AED; width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
                        <span>ACTIVE VERIFICATIONS</span>
                    </div>
                    <span style="font-size: 11px; background: rgba(124, 58, 237, 0.12); color: #7C3AED; border: 1px solid rgba(124, 58, 237, 0.25); padding: 3px 10px; border-radius: 9999px; font-weight: 800;">
                        {{ $_safeActiveSessions->count() }} live
                    </span>
                </div>

                @foreach($activeSessions as $session)
                    <div class="session-item-row" data-status="active" style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.12); border-radius: 18px; padding: 18px; margin-bottom: 12px; box-shadow: 0 4px 16px rgba(30, 27, 75, 0.04); display: flex; flex-direction: column; gap: 12px;" id="numbers-session-card-{{ $session->id }}">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 18px;">
                                    @php
                                        $iso = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                                        $flag = '';
                                        foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                        echo $flag;
                                    @endphp
                                </span>
                                <span style="font-size: 14px; font-weight: 800; color: #1E1B4B; text-transform: uppercase;">{{ $session->service?->name ?? 'SMS' }}</span>
                                <span style="background: #EEF1F8; color: #1E1B4B; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $session->phoneNumber?->country?->name ?? 'US' }}</span>
                            </div>
                            <span style="background: rgba(124, 58, 237, 0.12); color: #7C3AED; border: 1px solid rgba(124, 58, 237, 0.25); padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800;">
                                ● LIVE ACTIVE
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="font-size: 16px; font-weight: 800; color: #1E1B4B; font-family: 'JetBrains Mono', monospace; background: #EEF1F8; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 12px; padding: 10px 14px; flex: 1;">
                                {{ $session->phoneNumber?->phone_number }}
                            </div>
                            <button type="button" onclick="copyToClipboard('{{ $session->phoneNumber?->phone_number }}', this)" style="background: #1E1B4B; color: #FFFFFF; border: none; border-radius: 12px; padding: 10px 16px; font-size: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.15);">Copy</button>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 2px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span style="font-size: 14px; font-weight: 800; color: #b45309; font-family: 'JetBrains Mono', monospace;" id="numbers-timer-{{ $session->id }}" data-expires="{{ $session->expires_at?->toIso8601String() }}">--:--</span>
                            </div>
                            <button type="button" onclick="cancelSession({{ $session->id }})" style="background: #FFF1F2; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; color: #dc2626; padding: 6px 12px; font-size: 11.5px; font-weight: 700; cursor: pointer;">Cancel & Refund</button>
                        </div>

                        @if($session->verification_code)
                            <div style="background: rgba(124, 58, 237, 0.08); border: 1.5px solid #7C3AED; border-radius: 14px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 10.5px; font-weight: 800; color: #7C3AED; text-transform: uppercase;">✓ Code Received</div>
                                    <div style="font-size: 22px; font-weight: 900; color: #1E1B4B; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">{{ $session->verification_code }}</div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $session->verification_code }}', this)" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 10px; padding: 8px 16px; font-size: 12.5px; font-weight: 800; cursor: pointer; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);">Copy Code</button>
                            </div>
                        @else
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6B7A99; font-weight: 600; padding: 4px 2px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #7C3AED; display: inline-block;"></span>
                                Waiting for incoming carrier SMS code...
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Session History List -->
        <div id="numbers-history-container">
            <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; margin-top: 4px;">
                SESSION HISTORY
            </div>

            <div id="numbers-history-list" style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($recentSessions as $session)
                    @php
                        $badgeBg = match($session->status) {
                            'completed' => 'rgba(124, 58, 237, 0.12)',
                            'cancelled' => 'rgba(245, 158, 11, 0.12)',
                            'expired' => 'rgba(239, 68, 68, 0.12)',
                            'active', 'pending' => 'rgba(30, 27, 75, 0.10)',
                            default => '#EEF1F8',
                        };
                        $badgeColor = match($session->status) {
                            'completed' => '#7C3AED',
                            'cancelled' => '#b45309',
                            'expired' => '#dc2626',
                            'active', 'pending' => '#1E1B4B',
                            default => '#6B7A99',
                        };
                    @endphp
                    <div class="session-item-row" data-status="{{ $session->status }}" style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 16px; padding: 14px 16px; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 17px;">
                                    @php
                                        $iso = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                                        $flag = '';
                                        foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                        echo $flag;
                                    @endphp
                                </span>
                                <span style="font-size: 13.5px; font-weight: 800; color: #1E1B4B;">{{ $session->service?->name ?? 'SMS' }}</span>
                                <span style="font-size: 10.5px; background: #EEF1F8; color: #1E1B4B; padding: 2px 7px; border-radius: 6px; font-weight: 700;">{{ $session->phoneNumber?->country?->name ?? 'US' }}</span>
                            </div>
                            <span style="font-size: 10.5px; background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 3px 9px; border-radius: 9999px; font-weight: 800; text-transform: uppercase;">
                                {{ $session->status }}
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 14px; font-weight: 800; color: #1E1B4B; font-family: 'JetBrains Mono', monospace;">{{ $session->phoneNumber?->phone_number }}</span>
                                    <button type="button" onclick="copyToClipboard('{{ $session->phoneNumber?->phone_number }}', this)" style="background: none; border: none; color: #6B7A99; cursor: pointer; padding: 0; display: inline-flex; align-items: center;" title="Copy">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </button>
                                </div>
                                <div style="font-size: 11.5px; color: #6B7A99; font-weight: 600; margin-top: 3px;">{{ $session->created_at->format('M d, Y · H:i') }}</div>
                            </div>

                            @if($session->verification_code)
                                <div style="text-align: right; background: rgba(124, 58, 237, 0.08); border: 1px solid rgba(124, 58, 237, 0.25); border-radius: 10px; padding: 6px 12px;">
                                    <div style="font-size: 9.5px; font-weight: 800; color: #7C3AED; text-transform: uppercase;">Code</div>
                                    <div style="font-size: 15px; font-weight: 900; color: #1E1B4B; font-family: 'JetBrains Mono', monospace;">{{ $session->verification_code }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div id="numbers-empty-state" style="background: #FFFFFF; border: 1.5px dashed rgba(30, 27, 75, 0.15); border-radius: 18px; padding: 36px 20px; text-align: center; box-shadow: 0 4px 16px rgba(30, 27, 75, 0.02);">
                        <div style="width: 50px; height: 50px; border-radius: 14px; background: #EEF1F8; color: #1E1B4B; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div style="font-size: 15px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">No verification sessions yet</div>
                        <div style="font-size: 12.5px; color: #6B7A99; margin-bottom: 16px;">Select any service and get your first carrier number instantly.</div>
                        <button type="button" class="blitz-btn" onpointerdown="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" onclick="switchTab('buy', document.querySelector('[data-tab=buy]'), event)" style="background: #7C3AED; color: #ffffff; border: none; border-radius: 12px; padding: 12px 22px; font-size: 13.5px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);">
                            Buy Virtual Number
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <div style="height: 16px;"></div>
    </div>
</div><!-- END blitz-panel-numbers -->

<!-- ═══════════════════════════════════════════════════════════════════
     BLITZ TAB PANEL 4: WALLET & TRANSACTIONS (Canonical Tab 4)
═══════════════════════════════════════════════════════════════════ -->
<div id="blitz-panel-wallet" class="blitz-tab-panel {{ in_array($requestedTab ?? '', ['wallet', 'transactions']) ? 'blitz-tab-active' : '' }}">
    <div style="display: flex; flex-direction: column; gap: 12px;">

        <!-- Back to Dashboard Row -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 2px 4px 6px;">
            <button type="button" class="blitz-btn" onclick="switchTab('dashboard')" style="background: none; border: none; color: #1E1B4B; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; padding: 0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Back to Dashboard</span>
            </button>
            <span style="font-size: 11.5px; font-weight: 700; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em;">Wallet Ledger</span>
        </div>

        <!-- Balance Hero (PVADeals Navy Card with Green Amount & Add Funds) -->
        <div style="background: #1E1B4B; border-radius: 20px; padding: 22px 20px; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18); position: relative; overflow: hidden;">
            <div style="color: rgba(255, 255, 255, 0.75); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Available Balance</div>
            <div style="display: inline-block; background: #FFFFFF; border-radius: 14px; padding: 4px 16px; margin: 4px 0 10px;">
                <div style="font-size: 32px; font-weight: 800; color: #7C3AED; font-family: 'JetBrains Mono', monospace; letter-spacing: -0.02em;">
                    <span id="wallet-tab-symbol">{{ $wallet->currency->symbol ?? '₦' }}</span><span id="wallet-tab-balance">{{ number_format($wallet->available_balance, $wallet->currency->decimal_places ?? 2) }}</span>
                    <span style="font-size: 14px; font-weight: 700; color: #1E1B4B; margin-left: 2px;">{{ $wallet->currency->code ?? 'NGN' }}</span>
                </div>
            </div>

            @if($wallet->active_holds_total > 0)
                <div style="font-size: 12px; color: rgba(255,255,255,0.85); margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #F59E0B;"></span>
                    <span>{{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->active_holds_total, $wallet->currency->decimal_places ?? 2) }} held in active sessions</span>
                    <span style="opacity: 0.7;">• Total: {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->balance, $wallet->currency->decimal_places ?? 2) }}</span>
                </div>
            @endif

            <!-- Fund Wallet CTA -->
            <div style="display: flex; gap: 10px; margin-top: 14px; position: relative; z-index: 2;">
                <button type="button" class="blitz-btn" onclick="openDepositModal()" style="flex: 1; background: #7C3AED; border: none; border-radius: 12px; color: #ffffff; font-size: 13.5px; font-weight: 800; padding: 11px 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <span>+ Fund Wallet Instantly</span>
                </button>
            </div>
        </div>

        <!-- Quick Funding Card (Stripe In-App Payment) -->
        <div class="card" style="margin-top: 2px; padding: 14px 16px; border-radius: 16px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 9px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); color: #6366F1; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 800; color: var(--text-primary, #1E1B4B);">Stripe In-App Payment</div>
                        <div style="font-size: 11px; color: var(--text-secondary, #64748B);">Instant Cards • Apple Pay • Google Pay</div>
                    </div>
                </div>
                <span style="font-size: 10px; font-weight: 800; background: rgba(99, 102, 241, 0.12); color: #6366F1; border: 1px solid rgba(99, 102, 241, 0.25); padding: 3px 8px; border-radius: 9999px;">Instant</span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-bottom: 10px;">
                <button type="button" class="blitz-btn" onclick="openDepositModal(1000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦1,000</button>
                <button type="button" class="blitz-btn" onclick="openDepositModal(2000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦2,000</button>
                <button type="button" class="blitz-btn" onclick="openDepositModal(5000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦5,000</button>
                <button type="button" class="blitz-btn" onclick="openDepositModal(10000)" style="padding: 8px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.06); color: #7C3AED; cursor: pointer; text-align: center;">₦10K</button>
            </div>
            <button type="button" onclick="openDepositModal()" class="blitz-btn" style="width: 100%; background: linear-gradient(135deg, #7C3AED, #059669); color: #FFFFFF; border: none; border-radius: 12px; padding: 10px 14px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <span>Choose Amount & Pay</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

        <!-- Financial Summary Counters -->
        @php
            $depositTxs = $recentTransactions->filter(fn($tx) => in_array($tx->type, ['credit', 'deposit', 'refund', \App\Domain\Enums\TransactionType::Deposit, \App\Domain\Enums\TransactionType::Refund, \App\Domain\Enums\TransactionType::Adjustment]));
            $chargeTxs = $recentTransactions->filter(fn($tx) => in_array($tx->type, ['debit', 'charge', 'withdrawal', 'purchase', \App\Domain\Enums\TransactionType::Purchase, \App\Domain\Enums\TransactionType::Withdrawal, \App\Domain\Enums\TransactionType::Fee]));
        @endphp
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 14px; padding: 12px 14px; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
                <div style="font-size: 11px; font-weight: 600; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.04em;">Deposits</div>
                <div style="font-size: 20px; font-weight: 800; color: #7C3AED; margin-top: 3px;">{{ $depositTxs->count() }}</div>
                <div style="font-size: 10.5px; color: #6B7A99; margin-top: 1px;">+{{ $wallet->currency->symbol ?? '₦' }}{{ number_format($depositTxs->sum('amount'), 2) }}</div>
            </div>
            <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 14px; padding: 12px 14px; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
                <div style="font-size: 11px; font-weight: 600; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.04em;">Charges</div>
                <div style="font-size: 20px; font-weight: 800; color: #EF4444; margin-top: 3px;">{{ $chargeTxs->count() }}</div>
                <div style="font-size: 10.5px; color: #6B7A99; margin-top: 1px;">-{{ $wallet->currency->symbol ?? '₦' }}{{ number_format($chargeTxs->sum('amount'), 2) }}</div>
            </div>
        </div>

        <!-- Ledger Filter Pills (All, Deposits, Charges, Refunds) -->
        <div style="display: flex; gap: 6px; overflow-x: auto; padding: 2px 2px 6px; -webkit-overflow-scrolling: touch;" class="no-scrollbar" id="wallet-filter-bar">
            <button type="button" class="blitz-btn wallet-filter-pill active" onclick="filterWalletLedger('all', this)" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; white-space: nowrap; cursor: pointer;">
                All ({{ $recentTransactions->count() }})
            </button>
            <button type="button" class="blitz-btn wallet-filter-pill" onclick="filterWalletLedger('deposit', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; white-space: nowrap; cursor: pointer;">
                Deposits ({{ $depositTxs->count() }})
            </button>
            <button type="button" class="blitz-btn wallet-filter-pill" onclick="filterWalletLedger('charge', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; white-space: nowrap; cursor: pointer;">
                Charges ({{ $chargeTxs->count() }})
            </button>
        </div>

        <!-- Transaction Ledger Cards -->
        <div style="margin-top: 2px;">
            <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 18px; overflow: hidden; box-shadow: 0 2px 10px rgba(30, 27, 75, 0.03);">
                <div id="wallet-transactions-list" style="padding: 2px 0;">
                    @forelse($recentTransactions as $tx)
                        @php
                            $isCredit = in_array($tx->type, ['credit', 'deposit', 'refund', \App\Domain\Enums\TransactionType::Deposit, \App\Domain\Enums\TransactionType::Refund, \App\Domain\Enums\TransactionType::Adjustment]);
                            $txCategory = $isCredit ? 'deposit' : 'charge';
                        @endphp
                        <div class="wallet-tx-row" data-category="{{ $txCategory }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-bottom: 1px solid rgba(30, 27, 75, 0.05);">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $isCredit ? 'rgba(124, 58, 237, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                @if($isCredit)
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2.5"><path d="m6 9 6-6 6 6"/><path d="M12 3v18"/></svg>
                                @else
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><path d="m6 15 6 6 6-6"/><path d="M12 21V3"/></svg>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($tx->description ?? ucfirst(is_string($tx->type) ? $tx->type : $tx->type->value), 32) }}</div>
                                <div style="font-size: 11px; color: #6B7A99; margin-top: 2px;">{{ $tx->created_at->format('M d, Y · H:i') }}</div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0;">
                                <div style="font-size: 15px; font-weight: 800; color: {{ $isCredit ? '#7C3AED' : '#ef4444' }}; font-family: 'JetBrains Mono', monospace;">
                                    {{ $isCredit ? '+' : '-' }}{{ $wallet->currency->symbol ?? '₦' }}{{ number_format($tx->amount, $wallet->currency->decimal_places ?? 2) }}
                                </div>
                                <div style="font-size: 10.5px; color: #94A3B8; margin-top: 2px; font-family: 'JetBrains Mono', monospace;">
                                    bal: {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($tx->balance_after, $wallet->currency->decimal_places ?? 2) }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 40px 20px; text-align: center;">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/></svg>
                            </div>
                            <div style="font-size: 14px; font-weight: 700; color: #1E1B4B; margin-bottom: 4px;">No transactions yet</div>
                            <div style="font-size: 12px; color: #6B7A99;">Deposit funds to start using instant SMS verifications.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div style="height: 16px;"></div>
    </div>
</div><!-- END blitz-panel-wallet -->

<!-- ═══════════════════════════════════════════════════════════════════
     BLITZ TAB PANEL 5: ACCOUNT & SETTINGS (Canonical Tab 5)
═══════════════════════════════════════════════════════════════════ -->
<div id="blitz-panel-account" class="blitz-tab-panel {{ in_array($requestedTab ?? '', ['account', 'settings', 'more']) ? 'blitz-tab-active' : '' }}">
    <div style="display: flex; flex-direction: column; gap: 14px;">

        <!-- User Profile Hero Card -->
        <div style="background: #1E1B4B; border-radius: 20px; padding: 22px 20px; color: #FFFFFF; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18);">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #7C3AED; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0;">
                    {{ strtoupper(substr($user->name ?? 'User', 0, 1)) }}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 17px; font-weight: 800; color: #FFFFFF; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->name }}</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.75); margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->email }}</div>
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 6px;">
                        <span style="font-size: 10px; background: rgba(124, 58, 237, 0.25); color: #86efac; border: 1px solid rgba(124, 58, 237, 0.4); padding: 2px 8px; border-radius: 9999px; font-weight: 700; text-transform: uppercase;">
                            Verified Member
                        </span>
                        <span style="font-size: 10px; background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.9); padding: 2px 8px; border-radius: 9999px; font-weight: 600;">
                            ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats in Card -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.12);">
                <div style="text-align: center;">
                    <div style="font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.65); text-transform: uppercase;">Balance</div>
                    <div id="stats-balance" style="font-size: 14px; font-weight: 800; color: #86efac; margin-top: 2px; font-family: monospace;">{{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->available_balance, 0) }}</div>
                </div>
                <div style="text-align: center; border-left: 1px solid rgba(255,255,255,0.12); border-right: 1px solid rgba(255,255,255,0.12);">
                    <div style="font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.65); text-transform: uppercase;">Orders</div>
                    <div style="font-size: 14px; font-weight: 800; color: #FFFFFF; margin-top: 2px;">{{ $stats['total_verifications'] ?? 0 }}</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.65); text-transform: uppercase;">Success</div>
                    <div style="font-size: 14px; font-weight: 800; color: #FFFFFF; margin-top: 2px;">{{ ($stats['total_verifications'] ?? 0) > 0 ? round((($stats['successful_verifications'] ?? 0) / $stats['total_verifications']) * 100) : 100 }}%</div>
                </div>
            </div>
        </div>

        <!-- Section: Wallet & Billing (Integrated More Tab) -->
        <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 18px; overflow: hidden; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
            <div style="padding: 12px 16px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6B7A99;">
                Wallet & Billing
            </div>

            <!-- Wallet & Transactions Row -->
            <div onclick="switchTab('wallet')" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(124, 58, 237, 0.1); color: #7C3AED; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Wallet & Transactions</div>
                        <div style="font-size: 11px; color: #6B7A99;">Ledger, top-up history & auto-refunds</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span id="settings-wallet-balance" style="font-size: 13px; font-weight: 800; color: #7C3AED; font-family: 'JetBrains Mono', monospace;">{{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->available_balance, 2) }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            <!-- Add Funds Quick Action Row -->
            <div onpointerdown="openDepositTab(event)" onclick="openDepositTab(event)" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 21h18M3 10h18M5 10v11M9 10v11M15 10v11M19 10v11M12 2l10 5H2l10-5z"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Deposit / Add Funds</div>
                        <div style="font-size: 11px; color: #6B7A99;">Instant Dedicated Bank Transfer (NUBAN)</div>
                    </div>
                </div>
                <button type="button" class="blitz-btn" onpointerdown="openDepositTab(event)" onclick="openDepositTab(event)" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 8px; padding: 5px 12px; font-size: 11px; font-weight: 700; cursor: pointer;">
                    Transfer
                </button>
            </div>
        </div>

        <!-- Section 1: Security & Protection -->
        <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 18px; overflow: hidden; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
            <div style="padding: 12px 16px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6B7A99;">
                Security & Protection
            </div>

            <!-- Transaction PIN Setting -->
            <div onclick="openSecurityPinModal()" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Security & Transaction PIN</div>
                        <div style="font-size: 11px; color: #6B7A99;">Protect balance and high-value orders</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 11px; color: #7C3AED; font-weight: 700;">Active</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            <!-- Biometric Authentication Switch -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(124, 58, 237, 0.1); color: #7C3AED; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a10 10 0 0 0-10 10c0 5.52 4.48 10 10 10s10-4.48 10-10A10 10 0 0 0 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/><path d="M12 6a6 6 0 0 0-6 6c0 1.66.67 3.16 1.76 4.24l1.42-1.42A4 4 0 0 1 8 12a4 4 0 0 1 4-4 4 4 0 0 1 4 4c0 1.1-.45 2.1-1.18 2.82l1.42 1.42A6 6 0 0 0 18 12a6 6 0 0 0-6-6z"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Biometric Login</div>
                        <div style="font-size: 11px; color: #6B7A99;">FaceID & Fingerprint lock</div>
                    </div>
                </div>
                <label style="position: relative; display: inline-block; width: 44px; height: 24px;">
                    <input type="checkbox" id="biometrics-toggle" onchange="toggleBiometricsSetting(this.checked)" style="opacity: 0; width: 0; height: 0;">
                    <span id="biometrics-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #CBD5E1; transition: .2s; border-radius: 24px;"></span>
                </label>
            </div>
        </div>

        <!-- Section 2: Preferences & Settings -->
        <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 18px; overflow: hidden; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
            <div style="padding: 12px 16px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6B7A99;">
                Preferences
            </div>

            <!-- Appearance Theme Row -->
            <div onclick="toggleTheme()" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Appearance & Dark Mode</div>
                        <div style="font-size: 11px; color: #6B7A99;">Tap to toggle Light / Dark</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span id="account-theme-label" style="font-size: 11px; color: #1E1B4B; font-weight: 700;">Light</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            <!-- Notifications Center -->
            <div onclick="openNotificationsModal()" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Notifications & Alerts</div>
                        <div style="font-size: 11px; color: #6B7A99;">Low balance and SMS arrival alerts</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 11px; color: #6B7A99; font-weight: 600;">Manage</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            <!-- Currency Setting -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(124, 58, 237, 0.1); color: #7C3AED; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">
                        {{ $wallet->currency->symbol ?? '₦' }}
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Operating Currency</div>
                        <div style="font-size: 11px; color: #6B7A99;">Primary billing and pricing currency</div>
                    </div>
                </div>
                <span style="font-size: 12px; font-weight: 800; color: #1E1B4B; background: #EEF1F8; padding: 4px 10px; border-radius: 8px;">
                    {{ $wallet->currency->code ?? 'NGN' }} ({{ $wallet->currency->symbol ?? '₦' }})
                </span>
            </div>
        </div>

        <!-- Section 3: Support & Information -->
        <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 18px; overflow: hidden; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);">
            <div style="padding: 12px 16px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6B7A99;">
                Support & Help Desk
            </div>

            <!-- Help Desk -->
            <div onclick="openSupportModal()" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">24/7 Customer Support</div>
                        <div style="font-size: 11px; color: #6B7A99;">WhatsApp, Telegram & Email assistance</div>
                    </div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <!-- SMS Tutorial -->
            <div onclick="openTutorialModal()" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">How to Use Verixa</div>
                        <div style="font-size: 11px; color: #6B7A99;">Tutorial on allocating numbers & OTPs</div>
                    </div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <!-- Terms & Privacy -->
            <div onclick="openTermsModal()" class="blitz-btn" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid rgba(30, 27, 75, 0.05); cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">Terms of Service & Privacy</div>
                        <div style="font-size: 11px; color: #6B7A99;">Auto-refund guarantee & user compliance</div>
                    </div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- Section 4: Sign Out -->
        <div style="margin-top: 6px;">
            <form action="{{ route('logout') }}" method="POST" id="logout-form" onsubmit="blitzLogout(event)">
                @csrf
                <button type="submit" id="logout-btn" class="blitz-btn blitz-logout-btn" style="
                    width: 100%;
                    background: linear-gradient(135deg, rgba(239, 68, 68, 0.10), rgba(220, 38, 38, 0.06));
                    border: 1.5px solid rgba(239, 68, 68, 0.20);
                    border-radius: 16px;
                    padding: 15px 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 9px;
                    color: #EF4444;
                    font-size: 14px;
                    font-weight: 800;
                    letter-spacing: -0.01em;
                    cursor: pointer;
                    transition: all 0.05s ease;
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    box-shadow: 0 2px 12px rgba(239, 68, 68, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.04);
                    touch-action: manipulation;
                ">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Sign Out of Verixa</span>
                </button>
            </form>
            <div style="text-align: center; margin-top: 10px; font-size: 11px; color: #94A3B8;">
                Verixa Mobile v2.0.4 · NativePHP + Laravel 12
            </div>
        </div>


        <div style="height: 16px;"></div>
    </div>
</div><!-- END blitz-panel-account -->


<!-- ═══════════════════════════════════════════════════════════════════
     BLITZ TAB PANEL 4: RENT (PVADeals Duration Selectors & Workspaces)
═══════════════════════════════════════════════════════════════════ -->
<div id="blitz-panel-rent" class="blitz-tab-panel {{ in_array($requestedTab ?? '', ['rent', 'ltr', 'esim']) ? 'blitz-tab-active' : '' }}">
    <div style="display: flex; flex-direction: column; gap: 14px;">

        <!-- Mature Top Segmented View Switcher (LTR Numbers vs Rent Line) -->
        <div style="display: flex; background: #EEF1F8; border-radius: 14px; padding: 4px; border: 1px solid rgba(30, 27, 75, 0.08);">
            <button type="button" id="ltr-tab-btn-list" onclick="switchLtrSubView('list')" class="blitz-btn" style="flex: 1; border: none; background: #1E1B4B; color: #FFFFFF; font-size: 13px; font-weight: 800; padding: 10px 14px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 8px rgba(30, 27, 75, 0.2); transition: all 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                <span>LTR Numbers</span>
            </button>
            <button type="button" id="ltr-tab-btn-rent" onclick="switchLtrSubView('rent')" class="blitz-btn" style="flex: 1; border: none; background: transparent; color: #6B7A99; font-size: 13px; font-weight: 700; padding: 10px 14px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                <span>+ Rent Line</span>
            </button>
        </div>

        <!-- ═════════════════════════════════════════════════════════════
             SUB-VIEW 1: LTR NUMBERS MANAGEMENT VIEW (PVADeals Styled)
        ═════════════════════════════════════════════════════════════ -->
        <div id="ltr-view-list" style="display: flex; flex-direction: column; gap: 14px;">

            <!-- Header Card (PVADeals Deep Navy) -->
            <div style="background: #1E1B4B; border-radius: 20px; padding: 20px; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18); position: relative; overflow: hidden; color: #FFFFFF;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.15); color: #FFFFFF; padding: 4px 10px; border-radius: 9999px;">Dedicated Physical Lines · Private</span>
                        <div style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; margin-top: 8px;">LTR Numbers</div>
                        <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 4px;">Exclusive long-term carrier lines reserved just for you</div>
                    </div>
                    <button type="button" class="blitz-btn" onclick="switchLtrSubView('rent')" style="background: #7C3AED; color: #FFFFFF; border: none; border-radius: 14px; padding: 10px 16px; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35); flex-shrink: 0;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Rent Line</span>
                    </button>
                </div>
            </div>

            <!-- Collapsible Filters Card (PVADeals Exact Arrangement) -->
            <div style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 18px; overflow: hidden; box-shadow: 0 2px 10px rgba(30, 27, 75, 0.04);">
                <!-- Filter Accordion Header -->
                <button type="button" class="blitz-btn" onclick="toggleLtrFilters()" style="width: 100%; background: none; border: none; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 28px; height: 28px; border-radius: 8px; background: #EEF1F8; display: flex; align-items: center; justify-content: center; color: #1E1B4B;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        </div>
                        <span style="font-size: 14px; font-weight: 800; color: #1E1B4B;">Filters</span>
                        <span id="ltr-filter-count-badge" style="background: #EEF1F8; color: #1E1B4B; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 9999px;">0</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; color: #6B7A99; font-size: 12px; font-weight: 700;">
                        <span id="ltr-filters-toggle-text">Toggle</span>
                        <svg id="ltr-filters-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition: transform 0.2s ease;"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>

                <!-- Filter Body (Collapsible) -->
                <div id="ltr-filters-drawer" style="display: none; padding: 0 18px 18px; border-top: 1px solid rgba(30, 27, 75, 0.06); flex-direction: column; gap: 14px;">
                    <!-- 1. Date Range Picker (From — To) -->
                    <div style="margin-top: 14px;">
                        <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Date Range</label>
                        <div class="pva-responsive-grid-2">
                            <div>
                                <input type="date" id="ltr-filter-date-from" onchange="applyLtrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                            </div>
                            <div>
                                <input type="date" id="ltr-filter-date-to" onchange="applyLtrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Website & Number Search Grid -->
                    <div class="pva-responsive-grid-2">
                        <div>
                            <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Website / App</label>
                            <input type="text" id="ltr-filter-website" placeholder="Search service..." oninput="applyLtrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Number</label>
                            <input type="text" id="ltr-filter-number" placeholder="Phone number..." oninput="applyLtrFilters()" style="width: 100%; box-sizing: border-box; background: #F8FAFC; border: 1.5px solid rgba(30, 27, 75, 0.1); border-radius: 12px; padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1E1B4B; outline: none;">
                        </div>
                    </div>

                    <!-- 3. Country Filter Pills -->
                    <div>
                        <label style="font-size: 11px; font-weight: 800; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">Country</label>
                        <div class="pva-scroll-pills">
                            <button type="button" class="blitz-btn ltr-country-filter-pill active" onclick="selectLtrCountryFilter('all', this)" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 800; cursor: pointer; white-space: nowrap;">All</button>
                            <button type="button" class="blitz-btn ltr-country-filter-pill" onclick="selectLtrCountryFilter('US', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">🇺🇸 US</button>
                            <button type="button" class="blitz-btn ltr-country-filter-pill" onclick="selectLtrCountryFilter('GB', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">🇬🇧 UK</button>
                            <button type="button" class="blitz-btn ltr-country-filter-pill" onclick="selectLtrCountryFilter('CA', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">🇨🇦 CA</button>
                            <button type="button" class="blitz-btn ltr-country-filter-pill" onclick="selectLtrCountryFilter('more', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap;">More</button>
                        </div>
                    </div>

                    <!-- 4. Action Buttons (Apply / Clear / Export) -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 4px; padding-top: 10px; border-top: 1px solid rgba(30, 27, 75, 0.06);">
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="blitz-btn" onclick="applyLtrFilters()" style="background: #1E1B4B; color: #FFFFFF; border: none; border-radius: 10px; padding: 8px 16px; font-size: 12.5px; font-weight: 800; cursor: pointer;">
                                Apply
                            </button>
                            <button type="button" class="blitz-btn" onclick="clearLtrFilters()" style="background: #EEF1F8; color: #6B7A99; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 10px; padding: 8px 14px; font-size: 12.5px; font-weight: 700; cursor: pointer;">
                                Clear
                            </button>
                        </div>
                        <button type="button" class="blitz-btn" onclick="exportLtrCsv()" style="background: none; border: none; color: #7C3AED; font-size: 12.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; padding: 0;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            <span>Export</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Horizontal Status Filter Pills Dock (PVADeals Arrangement: All, Reserved, Completed, Flagged, Favorites) -->
            <div>
                <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;" class="no-scrollbar pva-scroll-pills" id="ltr-status-filter-dock">
                    <button type="button" class="blitz-btn ltr-status-pill active" onclick="filterLtrStatus('all', this)" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 800; white-space: nowrap; cursor: pointer; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.2); transition: all 0.15s ease;">
                        All
                    </button>
                    <button type="button" class="blitz-btn ltr-status-pill" onclick="filterLtrStatus('reserved', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Reserved
                    </button>
                    <button type="button" class="blitz-btn ltr-status-pill" onclick="filterLtrStatus('completed', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Completed
                    </button>
                    <button type="button" class="blitz-btn ltr-status-pill" onclick="filterLtrStatus('flagged', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Flagged
                    </button>
                    <button type="button" class="blitz-btn ltr-status-pill" onclick="filterLtrStatus('favorites', this)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; white-space: nowrap; cursor: pointer; transition: all 0.15s ease;">
                        Favorites
                    </button>
                </div>
            </div>

            <!-- LTR Orders List / Empty State -->
            <div id="ltr-orders-list-container" style="display: flex; flex-direction: column; gap: 10px;">
                <div id="ltr-empty-state" style="background: #FFFFFF; border: 1.5px dashed rgba(30, 27, 75, 0.15); border-radius: 20px; padding: 40px 20px; text-align: center;">
                    <div style="width: 52px; height: 52px; border-radius: 16px; background: #EEF1F8; color: #1E1B4B; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                    </div>
                    <div style="font-size: 16px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">No LTR numbers found</div>
                    <div style="font-size: 13px; color: #6B7A99; margin-bottom: 20px;">You haven't rented any dedicated long-term lines yet.</div>
                    <button type="button" class="blitz-btn" onclick="switchLtrSubView('rent')" style="background: #7C3AED; color: #ffffff; border: none; border-radius: 12px; padding: 12px 24px; font-size: 13.5px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);">
                        + Rent Dedicated Long-Term Line
                    </button>
                </div>
            </div>

        </div><!-- END ltr-view-list -->

        <!-- ═════════════════════════════════════════════════════════════
             SUB-VIEW 2: RENT DEDICATED LINE (Provisioning Console)
        ═════════════════════════════════════════════════════════════ -->
        <div id="ltr-view-rent" style="display: none; flex-direction: column; gap: 14px;">
            <!-- Back to LTR Numbers button -->
            <button type="button" class="blitz-btn" onclick="switchLtrSubView('list')" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #1E1B4B; font-size: 13px; font-weight: 800; cursor: pointer; padding: 4px 0; align-self: flex-start;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Back to LTR Numbers</span>
            </button>

            <!-- 1. DURATION SELECTION DOCK -->
            <div>
                <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px;">
                    SELECT RENTAL DURATION
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;" id="rent-ltr-pills">
                    <button type="button" id="rent-pill-3d" class="blitz-btn rent-duration-pill" onclick="rentSelectPlan('3d', 3, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '3.00' : '5000' }}, '3 Days Rental')" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
                        3 days
                    </button>

                    <button type="button" id="rent-pill-7d" class="blitz-btn rent-duration-pill" onclick="rentSelectPlan('7d', 7, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '5.00' : '8000' }}, '7 Days Rental')" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
                        7 days
                    </button>

                    <button type="button" id="rent-pill-14d" class="blitz-btn rent-duration-pill" onclick="rentSelectPlan('14d', 14, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '9.00' : '14500' }}, '14 Days Rental')" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
                        14 days
                    </button>

                    <button type="button" id="rent-pill-30d" class="blitz-btn rent-duration-pill active" onclick="rentSelectPlan('30d', 30, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '15.00' : '24000' }}, '30 Days Monthly Line')" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 800; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25);">
                        30 days
                    </button>

                    <!-- All Services Pill -->
                    <button type="button" id="rent-pill-allservices" class="blitz-btn rent-duration-pill" onclick="rentSelectPlan('allservices', 30, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '15.00' : '24000' }}, '★ All Services (Best Value)')" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 16px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: none;">
                        <span>★ All services</span>
                        <span style="background: #7C3AED; color: #FFFFFF; font-size: 10px; padding: 2px 6px; border-radius: 6px;">BEST VALUE</span>
                    </button>
                </div>
            </div>

            <!-- 2. ALL SERVICES EXPANSION LOADOUT CARD -->
            <div id="rent-allservices-card" style="background: #FFFFFF; border: 1.5px solid rgba(30, 27, 75, 0.12); border-radius: 18px; padding: 18px; display: flex; flex-direction: column; gap: 12px; box-shadow: 0 4px 16px rgba(30, 27, 75, 0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 13px; font-weight: 800; color: #1E1B4B; text-transform: uppercase;">All Services</span>
                        <!-- Carrier Badge -->
                        <span id="rent-carrier-badge-wrap" style="background: #EEF1F8; color: #1E1B4B; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                            <span id="rent-carrier-badge" style="background: #E20074; color: #ffffff; width: 14px; height: 14px; border-radius: 3px; display: inline-flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">T</span>
                            <span id="rent-carrier-name">T-Mobile</span>
                        </span>
                    </div>
                    <!-- Auto Renewal Pill -->
                    <span id="rent-auto-renewal-badge" style="background: rgba(124, 58, 237, 0.12); color: #7C3AED; border: 1px solid rgba(124, 58, 237, 0.25); padding: 3px 10px; border-radius: 9999px; font-size: 11.5px; font-weight: 700;">
                        ✓ Auto-renewal supported
                    </span>
                </div>

                <!-- Price Row -->
                <div style="display: flex; align-items: baseline; gap: 6px;">
                    <div style="font-size: 28px; font-weight: 900; color: #1E1B4B; font-family: 'JetBrains Mono', monospace;" id="rent-allservices-price">
                        {{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '15.00' : '24,000' }}
                    </div>
                    <div style="font-size: 14px; color: #6B7A99; font-weight: 600;" id="rent-allservices-duration">/ 30 days</div>
                </div>

                <p id="rent-allservices-desc" style="font-size: 13px; color: #6B7A99; line-height: 1.5; margin: 0;">
                    Includes unlimited SMS verifications across all supported apps and services for the entire rental duration.
                </p>

                <!-- Line Country Selector (All 5 PVADeals Regions) -->
                <div style="margin-top: 4px;">
                    <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 6px;">Select Region</div>
                    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;" id="rent-country-pills-row">
                        <button type="button" id="rent-country-usa" class="blitz-btn rent-country-pill active" onclick="rentSelectCountry('USA', '🇺🇸', 'United States', 1)" style="background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 12px; padding: 9px 14px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
                            <span style="font-size: 16px;">🇺🇸</span>
                            <span>United States</span>
                        </button>
                        <button type="button" id="rent-country-uk" class="blitz-btn rent-country-pill" onclick="rentSelectCountry('UK', '🇬🇧', 'United Kingdom', 2)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 12px; padding: 9px 14px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
                            <span style="font-size: 16px;">🇬🇧</span>
                            <span>United Kingdom</span>
                        </button>
                        <button type="button" id="rent-country-hk" class="blitz-btn rent-country-pill" onclick="rentSelectCountry('HK', '🇭🇰', 'Hong Kong', 31)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 12px; padding: 9px 14px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
                            <span style="font-size: 16px;">🇭🇰</span>
                            <span>Hong Kong</span>
                        </button>
                        <button type="button" id="rent-country-id" class="blitz-btn rent-country-pill" onclick="rentSelectCountry('ID', '🇮🇩', 'Indonesia', 17)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 12px; padding: 9px 14px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
                            <span style="font-size: 16px;">🇮🇩</span>
                            <span>Indonesia</span>
                        </button>
                        <button type="button" id="rent-country-au" class="blitz-btn rent-country-pill" onclick="rentSelectCountry('AU', '🇦🇺', 'Australia', 13)" style="background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 12px; padding: 9px 14px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
                            <span style="font-size: 16px;">🇦🇺</span>
                            <span>Australia</span>
                        </button>
                    </div>
                </div>

                <!-- Green Action CTA Button -->
                <button type="button" id="rent-action-btn" class="blitz-btn" onclick="handleRentActionClick()" style="width: 100%; background: #7C3AED; color: #ffffff; border: none; border-radius: 14px; padding: 15px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3); display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 6px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    <span id="rent-action-btn-text">Rent 30 Days Dedicated · {{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '15.00' : '24,000' }}</span>
                </button>
            </div>

            <!-- 3. RECENTLY USED CARDS -->
            <div>
                <div style="font-size: 11.5px; font-weight: 800; color: #6B7A99; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px;">
                    RECENTLY USED
                </div>
                <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
                    <button type="button" onclick="rentSelectPlan('30d', 30, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '15.00' : '24000' }}, '30 Days Monthly Line')" class="blitz-btn" style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 14px; padding: 10px 16px; font-size: 12px; font-weight: 700; color: #1E1B4B; white-space: nowrap; cursor: pointer; box-shadow: 0 2px 6px rgba(30, 27, 75, 0.03);">
                        🇺🇸 30 Days · Unlimited
                    </button>
                    <button type="button" onclick="rentSelectPlan('7d', 7, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '5.00' : '8000' }}, '7 Days Rental')" class="blitz-btn" style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 14px; padding: 10px 16px; font-size: 12px; font-weight: 700; color: #1E1B4B; white-space: nowrap; cursor: pointer; box-shadow: 0 2px 6px rgba(30, 27, 75, 0.03);">
                        🇺🇸 7 Days · Most Popular
                    </button>
                    <button type="button" onclick="rentSelectPlan('20m', 0.014, {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '0.85' : '1500' }}, '20 Minutes (STR)')" class="blitz-btn" style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 14px; padding: 10px 16px; font-size: 12px; font-weight: 700; color: #1E1B4B; white-space: nowrap; cursor: pointer; box-shadow: 0 2px 6px rgba(30, 27, 75, 0.03);">
                        ⚡ 20 Minutes (STR)
                    </button>
                </div>
            </div>
        </div><!-- END ltr-view-rent -->

        <div style="height: 16px;"></div>
    </div>
</div><!-- END blitz-panel-rent -->

<!-- ═══════════════════════════════════════════════════════════════════
     BLITZ TAB PANEL 5: ESIM (PVADeals Data Packages & Global SIM)
═══════════════════════════════════════════════════════════════════ -->
<div id="blitz-panel-esim" class="blitz-tab-panel {{ ($requestedTab ?? '') === 'esim' ? 'blitz-tab-active' : '' }}">
    <div style="display: flex; flex-direction: column; gap: 14px;">

        <!-- Hero Header Card (PVADeals Navy) -->
        <div style="background: #1E1B4B; border-radius: 20px; padding: 20px; box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18); position: relative; overflow: hidden; color: #FFFFFF;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.15); color: #FFFFFF; padding: 4px 10px; border-radius: 9999px;">Global Roaming & Virtual SIM</span>
                    <div style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; margin-top: 8px;">eSIM Digital Profiles</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 4px;">Instant QR activation for 150+ countries with 5G data & SMS</div>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="2" width="16" height="20" rx="3"/>
                        <circle cx="8" cy="7" r="0.8" fill="currentColor"/>
                        <circle cx="12" cy="7" r="0.8" fill="currentColor"/>
                        <circle cx="16" cy="7" r="0.8" fill="currentColor"/>
                        <line x1="8" y1="17" x2="16" y2="17"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Top Destination Countries -->
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 13px; font-weight: 700; color: #1E1B4B; letter-spacing: 0.02em;">Choose Destination</div>
            <span id="esim-active-country-label" style="font-size: 11px; font-weight: 700; color: #1E1B4B; background: #EEF1F8; padding: 3px 8px; border-radius: 6px;">United States 🇺🇸</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;" id="esim-country-list">
            <div id="esim-dest-USA" class="blitz-btn esim-country-card active" onclick="esimSelectCountry('USA', '🇺🇸', 'United States', '5G LTE Ready')" style="background: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 14px; padding: 12px 10px; text-align: center; cursor: pointer; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.08); transition: all 0.15s ease;">
                <div style="font-size: 22px;">🇺🇸</div>
                <div style="font-size: 13px; font-weight: 800; color: #1E1B4B; margin-top: 4px;">USA</div>
                <div style="font-size: 10px; color: #7C3AED; font-weight: 700; margin-top: 2px;">5G Ready</div>
            </div>
            <div id="esim-dest-UK" class="blitz-btn esim-country-card" onclick="esimSelectCountry('UK', '🇬🇧', 'United Kingdom', 'O2 / Vodafone')" style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 14px; padding: 12px 10px; text-align: center; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 2px 6px rgba(30, 27, 75, 0.02);">
                <div style="font-size: 22px;">🇬🇧</div>
                <div style="font-size: 13px; font-weight: 700; color: #1E1B4B; margin-top: 4px;">UK</div>
                <div style="font-size: 10px; color: #6B7A99; font-weight: 600; margin-top: 2px;">Vodafone</div>
            </div>
            <div id="esim-dest-EU" class="blitz-btn esim-country-card" onclick="esimSelectCountry('EU', '🇪🇺', 'Europe 33+ Countries', 'Cross-Border')" style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 14px; padding: 12px 10px; text-align: center; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 2px 6px rgba(30, 27, 75, 0.02);">
                <div style="font-size: 22px;">🇪🇺</div>
                <div style="font-size: 13px; font-weight: 700; color: #1E1B4B; margin-top: 4px;">Europe 33+</div>
                <div style="font-size: 10px; color: #6B7A99; font-weight: 600; margin-top: 2px;">Roaming</div>
            </div>
        </div>

        <!-- Data Packages -->
        <div style="font-size: 13px; font-weight: 700; color: #1E1B4B; letter-spacing: 0.02em; margin-top: 4px;">Select Data Package</div>
        <div style="display: flex; flex-direction: column; gap: 10px;" id="esim-package-list">
            <!-- 1GB -->
            <div id="esim-pkg-1gb" class="blitz-btn esim-package-card" onclick="esimSelectPackage('1gb', '1 GB High-Speed Data', {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '3.50' : '5500' }}, '7 Days Validity')" style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 16px; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 2px 6px rgba(30, 27, 75, 0.03);">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #EEF1F8; color: #1E1B4B; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 14px;">1GB</div>
                    <div>
                        <div style="font-size: 14.5px; font-weight: 700; color: #1E1B4B;">1 GB High-Speed Data</div>
                        <div style="font-size: 11.5px; color: #6B7A99; margin-top: 2px;">7 Days Validity · Instant QR Delivery</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 16px; font-weight: 800; color: #1E1B4B; font-family: 'JetBrains Mono', monospace;">{{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '3.50' : '5,500' }}</div>
                    <div style="font-size: 10px; color: #6B7A99;">One-time</div>
                </div>
            </div>

            <!-- 3GB (Best Value, Default Active) -->
            <div id="esim-pkg-3gb" class="blitz-btn esim-package-card active" onclick="esimSelectPackage('3gb', '3 GB Best Value', {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '8.00' : '12500' }}, '30 Days Validity')" style="background: #FFFFFF; border: 2px solid #1E1B4B; border-radius: 16px; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; box-shadow: 0 4px 14px rgba(30, 27, 75, 0.12); transition: all 0.15s ease;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #1E1B4B; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 14px;">3GB</div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 14.5px; font-weight: 800; color: #1E1B4B;">3 GB Best Value</span>
                            <span style="font-size: 9.5px; font-weight: 800; background: #7C3AED; color: #FFFFFF; padding: 2px 6px; border-radius: 9999px; text-transform: uppercase;">Recommended</span>
                        </div>
                        <div style="font-size: 11.5px; color: #6B7A99; margin-top: 2px;">30 Days Validity · 5G Roaming + SMS</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 16px; font-weight: 800; color: #7C3AED; font-family: 'JetBrains Mono', monospace;">{{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '8.00' : '12,500' }}</div>
                    <div style="font-size: 10px; color: #6B7A99;">One-time</div>
                </div>
            </div>

            <!-- 10GB -->
            <div id="esim-pkg-10gb" class="blitz-btn esim-package-card" onclick="esimSelectPackage('10gb', '10 GB Power Traveler', {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '20.00' : '31000' }}, '30 Days Validity')" style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 16px; padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 2px 6px rgba(30, 27, 75, 0.03);">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #EEF1F8; color: #1E1B4B; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 14px;">10GB</div>
                    <div>
                        <div style="font-size: 14.5px; font-weight: 700; color: #1E1B4B;">10 GB Power Traveler</div>
                        <div style="font-size: 11.5px; color: #6B7A99; margin-top: 2px;">30 Days Validity · High Speed Hotspot</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 16px; font-weight: 800; color: #1E1B4B; font-family: 'JetBrains Mono', monospace;">{{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '20.00' : '31,000' }}</div>
                    <div style="font-size: 10px; color: #6B7A99;">One-time</div>
                </div>
            </div>
        </div>

        <!-- Action Button (Opens Dedicated eSIM Order Modal Right on eSIM Tab) -->
        <button type="button" id="esim-action-btn" class="blitz-btn" onclick="openEsimModal()" style="width: 100%; background: #7C3AED; color: #ffffff; border: none; border-radius: 14px; padding: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 18px rgba(124, 58, 237, 0.3); display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 4px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="4" y="2" width="16" height="20" rx="3"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            <span id="esim-action-btn-text">Order eSIM Profile · {{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '8.00' : '12,500' }}</span>
        </button>

        <div style="height: 16px;"></div>
    </div>
</div><!-- END blitz-panel-esim -->

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 2: RENT NUMBER CHECKOUT & CONFIRMATION
══════════════════════════════════════════════════════════════════ -->
<div id="rent-number-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeRentModal()" onclick="closeRentModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">Rent Dedicated Line</span>
        <button type="button" class="blitz-btn" onpointerdown="closeRentModal()" onclick="closeRentModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 22px 20px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box; background: #FFFFFF;">
        <div style="font-size: 20px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">Dedicated Private Line</div>
        <p style="font-size: 13px; color: #6B7A99; margin-bottom: 22px;">Confirm your dedicated line order. Your number is 100% private to your account.</p>

        <!-- Order Summary Card -->
        <div style="background: #EEF1F8; border: 1.5px solid rgba(30, 27, 75, 0.12); border-radius: 18px; padding: 18px; margin-bottom: 20px; box-shadow: 0 4px 14px rgba(30, 27, 75, 0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid rgba(30, 27, 75, 0.08);">
                <span style="font-size: 12.5px; color: #6B7A99; font-weight: 600;">Selected Plan</span>
                <span id="rent-modal-plan-name" style="font-size: 13.5px; font-weight: 800; color: #1E1B4B;">30 Days Dedicated</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(30, 27, 75, 0.08);">
                <span style="font-size: 12.5px; color: #6B7A99; font-weight: 600;">Country</span>
                <span id="rent-modal-country-name" style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">🇺🇸 United States</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(30, 27, 75, 0.08);">
                <span style="font-size: 12.5px; color: #6B7A99; font-weight: 600;">Service Type</span>
                <span style="font-size: 13.5px; font-weight: 700; color: #7C3AED;">All Services (Unrestricted)</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px;">
                <span style="font-size: 14px; color: #1E1B4B; font-weight: 700;">Total Investment</span>
                <span id="rent-modal-price" style="font-size: 22px; font-weight: 900; color: #7C3AED; font-family: 'JetBrains Mono', monospace;">{{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '15.00' : '24,000' }}</span>
            </div>
        </div>

        <div id="rent-order-status" style="display: none; padding: 12px 14px; border-radius: 12px; margin-bottom: 14px; font-size: 13px; font-weight: 600;"></div>

        <button type="button" id="btn-confirm-rent" class="blitz-btn" onclick="executeRentOrder()" style="width: 100%; background: #7C3AED; color: #ffffff; border: none; border-radius: 14px; padding: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3); margin-bottom: 10px;">
            Confirm & Reserve Dedicated Line
        </button>
        <!-- Explicit Cancel Button that cleanly stays on Rent Tab -->
        <button type="button" class="blitz-btn" onpointerdown="closeRentModal()" onclick="closeRentModal()" style="width: 100%; background: transparent; color: #6B7A99; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 14px; padding: 12px; font-size: 13px; font-weight: 600; cursor: pointer;">
            Cancel
        </button>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 3: ESIM ACTIVATION & QR PROFILE DELIVERY
══════════════════════════════════════════════════════════════════ -->
<div id="esim-order-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeEsimModal()" onclick="closeEsimModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">eSIM Profile Activation</span>
        <button type="button" class="blitz-btn" onpointerdown="closeEsimModal()" onclick="closeEsimModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 22px 20px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box; background: #FFFFFF;">
        <div style="font-size: 20px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">Global Data & Virtual SIM</div>
        <p style="font-size: 13px; color: #6B7A99; margin-bottom: 22px;">Instant digital profile generation with QR code and cellular activation parameters.</p>

        <!-- Order Summary Card -->
        <div style="background: #EEF1F8; border: 1.5px solid rgba(30, 27, 75, 0.12); border-radius: 18px; padding: 18px; margin-bottom: 20px; box-shadow: 0 4px 14px rgba(30, 27, 75, 0.04);">
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid rgba(30, 27, 75, 0.08);">
                <span style="font-size: 12.5px; color: #6B7A99; font-weight: 600;">Destination Country</span>
                <span id="esim-modal-country" style="font-size: 13.5px; font-weight: 800; color: #1E1B4B;">🇺🇸 United States</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(30, 27, 75, 0.08);">
                <span style="font-size: 12.5px; color: #6B7A99; font-weight: 600;">Data Allowance</span>
                <span id="esim-modal-package" style="font-size: 13.5px; font-weight: 700; color: #1E1B4B;">3 GB Best Value (30 Days)</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(30, 27, 75, 0.08);">
                <span style="font-size: 12.5px; color: #6B7A99; font-weight: 600;">Network Carrier</span>
                <span id="esim-modal-network" style="font-size: 13.5px; font-weight: 700; color: #7C3AED;">5G LTE Fast Roaming</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px;">
                <span style="font-size: 14px; color: #1E1B4B; font-weight: 700;">Total Investment</span>
                <span id="esim-modal-price" style="font-size: 22px; font-weight: 900; color: #7C3AED; font-family: 'JetBrains Mono', monospace;">{{ $wallet->currency->symbol ?? '₦' }}{{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '8.00' : '12,500' }}</span>
            </div>
        </div>

        <div id="esim-order-status" style="display: none; padding: 12px 14px; border-radius: 12px; margin-bottom: 14px; font-size: 13px; font-weight: 600;"></div>

        <button type="button" id="btn-confirm-esim" class="blitz-btn" onclick="executeEsimOrder()" style="width: 100%; background: #7C3AED; color: #ffffff; border: none; border-radius: 14px; padding: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3); margin-bottom: 10px;">
            Confirm & Activate eSIM
        </button>
        <!-- Explicit Cancel Button that cleanly stays on eSIM Tab -->
        <button type="button" class="blitz-btn" onpointerdown="closeEsimModal()" onclick="closeEsimModal()" style="width: 100%; background: transparent; color: #6B7A99; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 14px; padding: 12px; font-size: 13px; font-weight: 600; cursor: pointer;">
            Cancel
        </button>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 4: SMS SERVICES TUTORIAL
══════════════════════════════════════════════════════════════════ -->
<div id="tutorial-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeTutorialModal()" onclick="closeTutorialModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">How It Works</span>
        <button type="button" class="blitz-btn" onpointerdown="closeTutorialModal()" onclick="closeTutorialModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 22px 20px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; background: #FFFFFF;">
        <div style="font-size: 20px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">SMS Services Tutorial</div>
        <p style="font-size: 13px; color: #6B7A99; margin-bottom: 24px;">How to verify any app or website with Verixa:</p>

        <div style="display: flex; flex-direction: column; gap: 18px; margin-bottom: 28px;">
            <div style="display: flex; gap: 14px;">
                <div style="width: 34px; height: 34px; border-radius: 50%; background: #1E1B4B; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0;">1</div>
                <div>
                    <div style="font-size: 15px; font-weight: 800; color: #1E1B4B;">Pick Country & Service</div>
                    <div style="font-size: 13px; color: #6B7A99; margin-top: 3px; line-height: 1.4;">Tap the '+' button next to Purchased Numbers and choose or search the app you want to verify.</div>
                </div>
            </div>

            <div style="display: flex; gap: 14px;">
                <div style="width: 34px; height: 34px; border-radius: 50%; background: #1E1B4B; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0;">2</div>
                <div>
                    <div style="font-size: 15px; font-weight: 800; color: #1E1B4B;">Paste the Phone Number</div>
                    <div style="font-size: 13px; color: #6B7A99; margin-top: 3px; line-height: 1.4;">Copy the allocated virtual number and enter it into the target app.</div>
                </div>
            </div>

            <div style="display: flex; gap: 14px;">
                <div style="width: 34px; height: 34px; border-radius: 50%; background: #7C3AED; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0;">3</div>
                <div>
                    <div style="font-size: 15px; font-weight: 800; color: #1E1B4B;">Instant OTP Delivery</div>
                    <div style="font-size: 13px; color: #6B7A99; margin-top: 3px; line-height: 1.4;">Your code appears automatically in real time. If no code arrives, your wallet is 100% refunded automatically!</div>
                </div>
            </div>
        </div>

        <button type="button" class="blitz-btn" onpointerdown="closeTutorialModal()" onclick="closeTutorialModal()" style="width: 100%; background: #7C3AED; color: #ffffff; border: none; border-radius: 14px; padding: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3);">
            Got It!
        </button>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 5: CARRIER SIMULATOR
══════════════════════════════════════════════════════════════════ -->
<div id="simulator-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeSimModal()" onclick="closeSimModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">Telecom Simulator</span>
        <button type="button" class="blitz-btn" onpointerdown="closeSimModal()" onclick="closeSimModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 22px 20px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; background: #FFFFFF;">
        <div style="font-size: 20px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">Carrier Webhook Simulator</div>
        <p style="font-size: 13px; color: #6B7A99; margin-bottom: 20px;">Simulate inbound SMS carrier webhooks to test automatic OTP capture.</p>

        <div style="margin-bottom: 14px;">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #1E1B4B; margin-bottom: 6px; text-transform: uppercase;">Provider</label>
            <select id="sim-provider" style="width: 100%; background: #EEF1F8; border: 1px solid rgba(30, 27, 75, 0.12); border-radius: 12px; padding: 12px 14px; font-size: 14px; color: #1E1B4B; outline: none;">
                <option value="pvadeals" selected>Verixa Direct Carrier Network (Default)</option>
                <option value="twilio">Twilio Adapter</option>
                <option value="five_sim">5sim Adapter</option>
                <option value="sms_activate">SMS-Activate Adapter</option>
            </select>
        </div>

        <div style="margin-bottom: 14px;">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #1E1B4B; margin-bottom: 6px; text-transform: uppercase;">Phone Number</label>
            @php
                $firstActivePhone = $activeSessions->first()?->phoneNumber?->phone_number ?? '+12025550199';
            @endphp
            <input type="text" id="sim-phone" value="{{ $firstActivePhone }}" style="width: 100%; background: #EEF1F8; border: 1px solid rgba(30, 27, 75, 0.12); border-radius: 12px; padding: 12px 14px; font-size: 14px; color: #1E1B4B; font-family: monospace; outline: none;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #1E1B4B; margin-bottom: 6px; text-transform: uppercase;">SMS Message Body</label>
            <textarea id="sim-body" rows="3" style="width: 100%; background: #EEF1F8; border: 1px solid rgba(30, 27, 75, 0.12); border-radius: 12px; padding: 12px 14px; font-size: 13px; color: #1E1B4B; font-family: monospace; resize: none; outline: none;">Your verification code is: 849-201</textarea>
        </div>

        <div id="sim-result" style="display: none; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 13px;"></div>

        <button type="button" id="btn-sim-submit" class="blitz-btn" onpointerdown="submitSimWebhook()" onclick="submitSimWebhook()" style="width: 100%; background: #7C3AED; color: #ffffff; border: none; border-radius: 14px; padding: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3); margin-bottom: 10px;">
            Send Simulated SMS
        </button>
        <button type="button" class="blitz-btn" onpointerdown="closeSimModal()" onclick="closeSimModal()" style="width: 100%; background: transparent; color: #6B7A99; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 14px; padding: 12px; font-size: 13px; font-weight: 600; cursor: pointer;">
            Close
        </button>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 6: PVADEALS V3 API GATEWAY SETTINGS
══════════════════════════════════════════════════════════════════ -->
<div id="pvadeals-key-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closePvaModal()" onclick="closePvaModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">Carrier Gateway Setup</span>
        <button type="button" class="blitz-btn" onpointerdown="closePvaModal()" onclick="closePvaModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 22px 20px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; background: #FFFFFF;">
        <div style="font-size: 20px; font-weight: 800; color: #1E1B4B; margin-bottom: 4px;">Carrier Gateway Configuration</div>
        <p style="font-size: 13px; color: #6B7A99; margin-bottom: 20px;">Connect your carrier gateway API credentials to route real cellular numbers worldwide.</p>

        <!-- Status Card -->
        <div id="pva-status-card" style="background: #EEF1F8; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 14px; padding: 14px 16px; margin-bottom: 18px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 13px; font-weight: 700; color: #1E1B4B;">Gateway Status</span>
                <span id="pva-status-badge" style="font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: {{ !empty($pvaConfigured) ? 'rgba(124, 58, 237, 0.12)' : 'rgba(245, 158, 11, 0.15)' }}; color: {{ !empty($pvaConfigured) ? '#7C3AED' : '#b45309' }};">
                    {{ !empty($pvaConfigured) ? '✓ CONNECTED' : '⚠️ API KEY REQUIRED' }}
                </span>
            </div>
            @if(!empty($pvaConfigured) && $pvaBalance !== null)
                <div style="margin-top: 10px; font-size: 13px; color: #1E1B4B;">
                    Live Carrier Balance: <strong style="color: #7C3AED;">${{ number_format($pvaBalance, 2) }} USD</strong>
                </div>
            @endif
        </div>

        <!-- API Key Input -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #1E1B4B; margin-bottom: 6px; text-transform: uppercase;">Carrier Gateway API Key</label>
            <input type="text" id="pva-api-key-input" placeholder="API-xxxxxxxxxxxxxxxxxxxx" style="width: 100%; background: #EEF1F8; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 12px; padding: 13px 14px; font-size: 14px; color: #1E1B4B; font-family: monospace; outline: none;">
            <div style="font-size: 11.5px; color: #6B7A99; margin-top: 6px;">
                Enterprise carrier credentials authorized for Verixa cellular network
            </div>
        </div>

        <div id="pva-save-feedback" style="display: none; padding: 12px 14px; border-radius: 12px; margin-bottom: 16px; font-size: 13px;"></div>

        <button type="button" id="btn-save-pvadeals" class="blitz-btn" onpointerdown="savePvaKey()" onclick="savePvaKey()" style="width: 100%; background: #7C3AED; color: #ffffff; border: none; border-radius: 14px; padding: 16px; font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3); margin-bottom: 10px;">
            Save & Verify Connection
        </button>

        <button type="button" id="btn-sync-catalog" class="blitz-btn" onpointerdown="syncPvaCatalog()" onclick="syncPvaCatalog()" style="width: 100%; background: #EEF1F8; color: #1E1B4B; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 14px; padding: 14px; font-size: 14px; font-weight: 700; cursor: pointer; margin-bottom: 14px;">
            🔄 Sync Live Services Catalog
        </button>

        <button type="button" class="blitz-btn" onpointerdown="closePvaModal()" onclick="closePvaModal()" style="width: 100%; background: transparent; color: #6B7A99; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 14px; padding: 12px; font-size: 13px; font-weight: 600; cursor: pointer;">
            Close
        </button>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 7: NOTIFICATIONS & ALERTS MODAL
══════════════════════════════════════════════════════════════════ -->
<div id="notifications-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeNotificationsModal()" onclick="closeNotificationsModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">Notifications</span>
        <button type="button" class="blitz-btn" onpointerdown="closeNotificationsModal()" onclick="closeNotificationsModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 20px 18px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box; background: #FFFFFF;">
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @if(isset($notifications) && $notifications->isNotEmpty())
                @foreach($notifications as $notif)
                    <div style="background: #FFFFFF; border: 1px solid rgba(15,42,90,0.1); box-shadow: 0 2px 6px rgba(15,42,90,0.04); border-radius: 14px; padding: 14px; display: flex; gap: 12px; align-items: flex-start;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $notif->type === 'wallet_credit' ? 'rgba(16, 185, 129, 0.12)' : 'rgba(30, 27, 75, 0.08)' }}; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            {{ $notif->type === 'wallet_credit' ? '💳' : '🔔' }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 14px; font-weight: 800; color: #1E1B4B;">{{ $notif->title }}</div>
                            <div style="font-size: 12px; color: #4B5563; margin-top: 2px; line-height: 1.4;">{{ $notif->message }}</div>
                            <div style="font-size: 10.5px; color: #9CA3AF; margin-top: 4px;">{{ $notif->created_at?->diffForHumans() ?? 'Just now' }}</div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Low Balance Alert if balance is low -->
            @if(floatval($wallet->available_balance ?? 0) < 1000)
                <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 14px; padding: 14px; display: flex; gap: 12px; align-items: flex-start;">
                    <span style="font-size: 20px;">⚠️</span>
                    <div>
                        <div style="font-size: 14px; font-weight: 800; color: #b45309;">Low Balance Alert</div>
                        <div style="font-size: 12px; color: #78350f; margin-top: 2px;">Your current balance is {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->available_balance, 2) }}. Add funds to avoid interrupted verifications.</div>
                        <button type="button" onclick="closeNotificationsModal(); openDepositTab();" style="margin-top: 8px; background: #1E1B4B; color: #FFFFFF; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 700; cursor: pointer;">Add Funds Now</button>
                    </div>
                </div>
            @endif

            <div style="background: #EEF1F8; border-radius: 14px; padding: 14px; display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 20px;">⚡</span>
                <div>
                    <div style="font-size: 14px; font-weight: 800; color: #1E1B4B;">Instant SMS Delivery Live</div>
                    <div style="font-size: 12px; color: #6B7A99; margin-top: 2px;">All carrier routes (USA, UK, T-Mobile) are running at optimal speed with zero downtime.</div>
                    <div style="font-size: 10.5px; color: #94a3b8; margin-top: 4px;">Active</div>
                </div>
            </div>

            <div style="background: #EEF1F8; border-radius: 14px; padding: 14px; display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 20px;">🛡️</span>
                <div>
                    <div style="font-size: 14px; font-weight: 800; color: #1E1B4B;">Auto-Refund Guarantee</div>
                    <div style="font-size: 12px; color: #6B7A99; margin-top: 2px;">Any number that does not receive an SMS code within the timeout window is 100% credited back to your balance automatically.</div>
                    <div style="font-size: 10.5px; color: #94a3b8; margin-top: 4px;">Active protection</div>
                </div>
            </div>
        </div>

        <button type="button" class="blitz-btn" onpointerdown="closeNotificationsModal()" onclick="closeNotificationsModal()" style="width: 100%; background: #1E1B4B; color: #ffffff; border: none; border-radius: 14px; padding: 14px; font-size: 14px; font-weight: 800; cursor: pointer; margin-top: 20px;">
            Close
        </button>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 8: COUNTRY PICKER MODAL
══════════════════════════════════════════════════════════════════ -->
<div id="country-picker-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeCountryPicker()" onclick="closeCountryPicker()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">Select Region</span>
        <button type="button" class="blitz-btn" onpointerdown="closeCountryPicker()" onclick="closeCountryPicker()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 18px 18px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box; background: #FFFFFF;">
        {{-- Search input --}}
        <div style="margin-bottom: 14px; position: relative;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7A99" stroke-width="2.2" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" id="global-country-search" placeholder="Search country or region..." oninput="filterGlobalCountries(this.value)" style="width: 100%; height: 44px; border-radius: 12px; background: #EEF1F8; border: 1.5px solid rgba(30, 27, 75, 0.1); padding: 0 14px 0 38px; color: #1E1B4B; font-size: 13.5px; font-weight: 600; outline: none; box-sizing: border-box;">
        </div>

        <div id="global-countries-list" style="display: flex; flex-direction: column; gap: 8px;">
            @php
                $flagMap = [
                    'USA' => '🇺🇸', 'US' => '🇺🇸',
                    'GBR' => '🇬🇧', 'UK' => '🇬🇧', 'GB' => '🇬🇧',
                    'CAN' => '🇨🇦', 'CA' => '🇨🇦',
                    'DEU' => '🇩🇪', 'DE' => '🇩🇪',
                    'NGA' => '🇳🇬', 'NG' => '🇳🇬',
                    'FRA' => '🇫🇷', 'FR' => '🇫🇷',
                    'NLD' => '🇳🇱', 'NL' => '🇳🇱',
                    'IND' => '🇮🇳', 'IN' => '🇮🇳',
                    'BRA' => '🇧🇷', 'BR' => '🇧🇷',
                    'GHA' => '🇬🇭', 'GH' => '🇬🇭',
                    'ESP' => '🇪🇸', 'ES' => '🇪🇸',
                    'ITA' => '🇮🇹', 'IT' => '🇮🇹',
                    'AUS' => '🇦🇺', 'AU' => '🇦🇺',
                    'KEN' => '🇰🇪', 'KE' => '🇰🇪',
                    'ZAF' => '🇿🇦', 'ZA' => '🇿🇦',
                    'PHL' => '🇵🇭', 'PH' => '🇵🇭',
                    'IDN' => '🇮🇩', 'ID' => '🇮🇩',
                    'POL' => '🇵🇱', 'PL' => '🇵🇱',
                    'SWE' => '🇸🇪', 'SE' => '🇸🇪',
                    'TUR' => '🇹🇷', 'TR' => '🇹🇷',
                    'MEX' => '🇲🇽', 'MX' => '🇲🇽',
                    'ARG' => '🇦🇷', 'AR' => '🇦🇷',
                    'COL' => '🇨🇴', 'CO' => '🇨🇴',
                    'VNM' => '🇻🇳', 'VN' => '🇻🇳',
                    'THA' => '🇹🇭', 'TH' => '🇹🇭',
                    'EGY' => '🇪🇬', 'EG' => '🇪🇬',
                    'ARE' => '🇦🇪', 'AE' => '🇦🇪',
                    'ISR' => '🇮🇱', 'IL' => '🇮🇱',
                    'PRT' => '🇵🇹', 'PT' => '🇵🇹',
                    'IRL' => '🇮🇪', 'IE' => '🇮🇪',
                    'HKG' => '🇭🇰', 'HK' => '🇭🇰',
                    'SGP' => '🇸🇬', 'SG' => '🇸🇬',
                    'MYS' => '🇲🇾', 'MY' => '🇲🇾',
                    'JPN' => '🇯🇵', 'JP' => '🇯🇵',
                    'KOR' => '🇰🇷', 'KR' => '🇰🇷',
                ];
            @endphp
            @forelse($countries as $c)
                @php
                    $code = !empty($c->iso3) ? $c->iso3 : (!empty($c->iso2) ? $c->iso2 : 'USA');
                    $displayCode = ($code === 'GBR' || $code === 'GB') ? 'UK' : ($code === 'USA' || $code === 'US' ? 'USA' : $code);
                    $flag = $flagMap[$c->iso3] ?? ($flagMap[$c->iso2] ?? '🌐');
                    $isUs = ($displayCode === 'USA');
                @endphp
                <div class="global-country-row blitz-btn"
                     data-code="{{ $displayCode }}"
                     data-name="{{ strtolower($c->name) }}"
                     onclick="selectGlobalCountry('{{ $displayCode }}', '{{ $flag }}', '{{ addslashes($c->name) }}', {{ $c->id }})"
                     style="background: {{ $isUs ? '#EEF1F8' : '#FFFFFF' }}; border: {{ $isUs ? '1.5px solid #1E1B4B' : '1px solid rgba(30, 27, 75, 0.1)' }}; border-radius: 14px; padding: 13px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.15s ease;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 22px;">{{ $flag }}</span>
                        <div>
                            <div style="font-size: 14.5px; font-weight: 800; color: #1E1B4B;">{{ $c->name }}</div>
                            <div style="font-size: 11.5px; color: {{ $isUs ? '#7C3AED' : '#6B7A99' }}; font-weight: 600;">
                                {{ $isUs ? 'T-Mobile / Major Carriers · High Success Rate' : ($displayCode === 'UK' ? 'EE / Vodafone / O2 · Real Physical SIMs' : ($displayCode === 'CA' ? 'Rogers / Bell · Real SIM' : 'Instant SMS Routing · Available')) }}
                            </div>
                        </div>
                    </div>
                    <span class="country-check" style="font-size: 16px; color: #7C3AED; font-weight: 900; display: {{ $isUs ? 'inline' : 'none' }};">✓</span>
                </div>
            @empty
                <div style="text-align: center; padding: 20px; color: #6B7A99;">No regions configured.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 9: SECURITY & TRANSACTION PIN MODAL
══════════════════════════════════════════════════════════════════ -->
<div id="security-pin-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeSecurityPinModal()" onclick="closeSecurityPinModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">Security PIN</span>
        <button type="button" class="blitz-btn" onpointerdown="closeSecurityPinModal()" onclick="closeSecurityPinModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 24px 20px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box; background: #FFFFFF; text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>

        <div style="font-size: 18px; font-weight: 800; color: #1E1B4B;">Transaction Security PIN</div>
        <div style="font-size: 12.5px; color: #6B7A99; margin-top: 6px; max-width: 300px; margin-left: auto; margin-right: auto; line-height: 1.4;">
            Your 4-digit PIN is required to authorize withdrawals and protect high-value number reservations.
        </div>

        <!-- 4-digit PIN Dots display -->
        <div style="display: flex; justify-content: center; gap: 14px; margin: 28px 0 20px;">
            <div id="pin-dot-1" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid #1E1B4B; background: transparent; transition: all 0.15s;"></div>
            <div id="pin-dot-2" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid #1E1B4B; background: transparent; transition: all 0.15s;"></div>
            <div id="pin-dot-3" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid #1E1B4B; background: transparent; transition: all 0.15s;"></div>
            <div id="pin-dot-4" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid #1E1B4B; background: transparent; transition: all 0.15s;"></div>
        </div>

        <!-- Numeric Keypad Grid -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; max-width: 280px; margin: 0 auto;">
            @for($i = 1; $i <= 9; $i++)
                <button type="button" class="blitz-btn" onclick="pressPinDigit('{{ $i }}')" style="background: #EEF1F8; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 16px; height: 56px; font-size: 20px; font-weight: 800; color: #1E1B4B; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    {{ $i }}
                </button>
            @endfor
            <button type="button" class="blitz-btn" onclick="clearPin()" style="background: none; border: none; height: 56px; font-size: 13px; font-weight: 700; color: #6B7A99; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                Clear
            </button>
            <button type="button" class="blitz-btn" onclick="pressPinDigit('0')" style="background: #EEF1F8; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 16px; height: 56px; font-size: 20px; font-weight: 800; color: #1E1B4B; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                0
            </button>
            <button type="button" class="blitz-btn" onclick="deletePinDigit()" style="background: none; border: none; height: 56px; font-size: 16px; font-weight: 700; color: #1E1B4B; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                ⌫
            </button>
        </div>

        <div id="pin-feedback-msg" style="margin-top: 20px; font-size: 12px; font-weight: 700; color: #7C3AED; min-height: 18px;"></div>

        <button type="button" class="blitz-btn" onclick="saveSecurityPin()" style="width: 100%; max-width: 280px; margin: 10px auto 0; background: #7C3AED; color: #FFFFFF; border: none; border-radius: 14px; padding: 13px; font-size: 14px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.3);">
            Save & Update PIN
        </button>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 10: 24/7 SUPPORT & HELP DESK MODAL
══════════════════════════════════════════════════════════════════ -->
<div id="support-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeSupportModal()" onclick="closeSupportModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">24/7 Live Support</span>
        <button type="button" class="blitz-btn" onpointerdown="closeSupportModal()" onclick="closeSupportModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 20px 18px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box; background: #FFFFFF;">
        <div style="display: flex; flex-direction: column; gap: 14px;">

            <!-- Header Card -->
            <div style="background: #EEF1F8; border-radius: 18px; padding: 18px; text-align: center;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #1E1B4B; color: #FFFFFF; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div style="font-size: 16px; font-weight: 800; color: #1E1B4B;">We're Here to Help</div>
                <div style="font-size: 12px; color: #6B7A99; margin-top: 4px;">Direct access to our carrier operations & support engineering team.</div>
            </div>

            <!-- Contact Channels -->
            <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 16px; overflow: hidden;">
                <!-- WhatsApp -->
                <a href="https://wa.me/2348000000000?text=Hello%20Verixa%20Support" target="_blank" style="display: flex; align-items: center; justify-content: space-between; padding: 16px; text-decoration: none; border-bottom: 1px solid rgba(30, 27, 75, 0.05);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(124, 58, 237, 0.12); color: #7C3AED; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            💬
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #1E1B4B;">WhatsApp Live Agent</div>
                            <div style="font-size: 11px; color: #7C3AED; font-weight: 600;">Average reply time: under 2 mins</div>
                        </div>
                    </div>
                    <span style="font-size: 12px; font-weight: 700; color: #7C3AED; background: rgba(124, 58, 237, 0.1); padding: 5px 12px; border-radius: 8px;">Chat</span>
                </a>

                <!-- Telegram -->
                <a href="https://t.me/VerixaSupport" target="_blank" style="display: flex; align-items: center; justify-content: space-between; padding: 16px; text-decoration: none; border-bottom: 1px solid rgba(30, 27, 75, 0.05);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(56, 189, 248, 0.12); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            ✈️
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #1E1B4B;">Telegram Operations</div>
                            <div style="font-size: 11px; color: #6B7A99;">@VerixaSupport</div>
                        </div>
                    </div>
                    <span style="font-size: 12px; font-weight: 700; color: #0284c7; background: rgba(56, 189, 248, 0.1); padding: 5px 12px; border-radius: 8px;">Open</span>
                </a>

                <!-- Email -->
                <div onclick="copyToClipboard('support@Verixa.com', this)" style="display: flex; align-items: center; justify-content: space-between; padding: 16px; cursor: pointer;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 12px; background: rgba(30, 27, 75, 0.08); color: #1E1B4B; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            ✉️
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #1E1B4B;">Email Support Desk</div>
                            <div style="font-size: 11px; color: #6B7A99;">support@Verixa.com</div>
                        </div>
                    </div>
                    <span style="font-size: 11px; font-weight: 700; color: #1E1B4B; background: #EEF1F8; padding: 5px 10px; border-radius: 8px;">Copy</span>
                </div>
            </div>

            <!-- Quick FAQ Accordion -->
            <div style="font-size: 13px; font-weight: 700; color: #1E1B4B; margin-top: 8px;">Frequently Asked Questions</div>

            <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 14px; padding: 14px;">
                <div style="font-size: 13px; font-weight: 700; color: #1E1B4B;">What happens if I do not receive an SMS code?</div>
                <div style="font-size: 11.5px; color: #6B7A99; margin-top: 4px; line-height: 1.4;">
                    If the countdown timer expires (20 minutes) without receiving a code, or if you tap "Cancel & Refund", your balance is 100% credited back immediately. You only pay for successful OTP arrivals.
                </div>
            </div>

            <div style="background: #FFFFFF; border: 1px solid rgba(30, 27, 75, 0.08); border-radius: 14px; padding: 14px;">
                <div style="font-size: 13px; font-weight: 700; color: #1E1B4B;">Are these numbers real SIM cards or VoIP?</div>
                <div style="font-size: 11.5px; color: #6B7A99; margin-top: 4px; line-height: 1.4;">
                    Verixa provisions Tier-1 mobile carrier numbers (T-Mobile, AT&T, Vodafone, etc.) through our carrier partners to guarantee maximum acceptance on WhatsApp, Telegram, Google, Claude, and banking apps.
                </div>
            </div>

            <button type="button" class="blitz-btn" onpointerdown="closeSupportModal()" onclick="closeSupportModal()" style="width: 100%; background: #1E1B4B; color: #ffffff; border: none; border-radius: 14px; padding: 14px; font-size: 14px; font-weight: 800; cursor: pointer; margin-top: 10px;">
                Close Support
            </button>
        </div>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════
     MODAL 11: TERMS OF SERVICE & PRIVACY POLICY MODAL
══════════════════════════════════════════════════════════════════ -->
<div id="terms-privacy-modal" class="blitz-modal">
    <div style="padding: calc(var(--safe-top, 0px) + 12px) 20px 14px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.12); background: #1E1B4B; flex-shrink: 0;">
        <button type="button" class="blitz-btn" onpointerdown="closeTermsModal()" onclick="closeTermsModal()" style="display: inline-flex; align-items: center; gap: 6px; background: none; border: none; color: #FFFFFF; font-size: 15px; font-weight: 600; cursor: pointer; padding: 4px 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            <span>Back</span>
        </button>
        <span style="font-size: 16px; font-weight: 800; color: #ffffff;">Terms & Privacy</span>
        <button type="button" class="blitz-btn" onpointerdown="closeTermsModal()" onclick="closeTermsModal()" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 20px 18px calc(var(--safe-bottom, 0px) + 30px); max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box; background: #FFFFFF;">
        <div style="display: flex; flex-direction: column; gap: 14px; font-size: 12.5px; color: #475569; line-height: 1.55;">

            <div style="background: rgba(124, 58, 237, 0.08); border: 1px solid rgba(124, 58, 237, 0.25); border-radius: 14px; padding: 14px;">
                <div style="font-size: 13.5px; font-weight: 800; color: #7C3AED; margin-bottom: 2px;">100% Auto-Refund Guarantee</div>
                <div>Funds are only permanently debited when a valid verification code is delivered to your terminal. Any session that expires or is cancelled returns held funds instantly to your available balance.</div>
            </div>

            <div>
                <h4 style="font-size: 14px; font-weight: 800; color: #1E1B4B; margin: 0 0 4px;">1. Service Description</h4>
                <p style="margin: 0;">Verixa provides temporary virtual telephone numbers for receiving incoming SMS authentication codes and 2-factor verification messages across supported online services.</p>
            </div>

            <div>
                <h4 style="font-size: 14px; font-weight: 800; color: #1E1B4B; margin: 0 0 4px;">2. Permitted Usage</h4>
                <p style="margin: 0;">You agree to use virtual numbers solely for legitimate account verification and lawful communications. Numbers may not be utilized for unauthorized bulk automation, fraud, harassment, or unlawful financial activities.</p>
            </div>

            <div>
                <h4 style="font-size: 14px; font-weight: 800; color: #1E1B4B; margin: 0 0 4px;">3. Data Privacy & Retention</h4>
                <p style="margin: 0;">We respect your confidentiality. Received verification codes and temporary session records are encrypted and automatically expunged following verification completion. We never share user identities with third-party advertising brokers.</p>
            </div>

            <div>
                <h4 style="font-size: 14px; font-weight: 800; color: #1E1B4B; margin: 0 0 4px;">4. Carrier Availability</h4>
                <p style="margin: 0;">Carrier line stock is dynamically coordinated with our Tier-1 network partners. Prices and delivery speeds are refreshed in real-time based on route quality.</p>
            </div>

            <button type="button" class="blitz-btn" onpointerdown="closeTermsModal()" onclick="closeTermsModal()" style="width: 100%; background: #1E1B4B; color: #ffffff; border: none; border-radius: 14px; padding: 14px; font-size: 14px; font-weight: 800; cursor: pointer; margin-top: 10px;">
                I Understand & Accept
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function() {
    'use strict';
    
    // Purge any lingering payment iframes - NEVER recreate an iframe on dashboard load
    function purgePaymentIframes() {
        const iframe = document.getElementById('complete-payment-iframe');
        const container = document.getElementById('complete-payment-iframe-container');
        if (iframe) {
            iframe.src = 'about:blank';
            iframe.remove();
        }
        if (container) {
            container.innerHTML = '';
            container.style.display = 'none';
        }
    }
    
    purgePaymentIframes();
    
    if (typeof window.stopGlobalPaymentPolling === 'function') {
        window.stopGlobalPaymentPolling();
    }
})();
</script>

<script>
    // Blitz Quick Action Controls
    function openBuyModal(e) {
        window.blitzTap(e, () => {
            const buyTabBtn = document.querySelector('[data-tab=buy]');
            if (typeof switchTab === 'function' && buyTabBtn) {
                switchTab('buy', buyTabBtn);
            }
            if (typeof pvOpenWebsiteSheet === 'function') {
                setTimeout(pvOpenWebsiteSheet, 150);
            }
        });
    }
    function closeBuyModal() {
        if (typeof pvCloseWebsiteSheet === 'function') {
            pvCloseWebsiteSheet();
        }
    }

    function openDepositTab(e) {
        if (e && e.preventDefault) e.preventDefault();
        if (typeof window.openDepositModal === 'function') {
            window.openDepositModal();
        } else {
            window.location.href = "{{ route('mobile.wallet', [], false) }}";
        }
    }

    function navigateToDeposit() {
        openDepositTab();
    }

    window.openDepositTab = openDepositTab;
    window.navigateToDeposit = navigateToDeposit;
    const openTopupModal = openDepositTab;
    function closeTopupModal() {
        if (typeof window.closeDepositModal === 'function') {
            window.closeDepositModal();
        }
    }

    function openRentModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('rent-number-modal');
            if (m) m.classList.add('blitz-open');
        });
    }
    function closeRentModal() {
        const m = document.getElementById('rent-number-modal');
        if (m) m.classList.remove('blitz-open');
    }

    function openTutorialModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('tutorial-modal');
            if (m) m.classList.add('blitz-open');
        });
    }
    function closeTutorialModal() {
        const m = document.getElementById('tutorial-modal');
        if (m) m.classList.remove('blitz-open');
    }

    function openSimModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('simulator-modal');
            if (m) m.classList.add('blitz-open');
        });
    }
    function closeSimModal() {
        const m = document.getElementById('simulator-modal');
        if (m) m.classList.remove('blitz-open');
    }

    function openPvaModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('pvadeals-key-modal');
            if (m) m.classList.add('blitz-open');
        });
    }
    function closePvaModal() {
        const m = document.getElementById('pvadeals-key-modal');
        if (m) m.classList.remove('blitz-open');
    }

    // 1. Countdown Timers (Clean 1-Second Interval, zero CPU throttling)
    const activeTimers = document.querySelectorAll('[id^="timer-"]');
    if (activeTimers.length > 0) {
        function updateTimers() {
            const now = new Date();
            activeTimers.forEach(el => {
                const expires = el.dataset.expires;
                if (!expires) return;
                const expiresAt = new Date(expires);
                const diff = Math.max(0, expiresAt - now);
                const mins = Math.floor(diff / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                el.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                if (diff <= 0) {
                    el.textContent = '00:00';
                    el.style.color = '#ef4444';
                }
            });
        }
        updateTimers();
        setInterval(updateTimers, 1000);
    }

    // 2. Realtime Session Polling for OTP arrival
    @if(isset($activeSessions) && $activeSessions->count() > 0)
        setInterval(async () => {
            @foreach($activeSessions as $session)
                @if(!$session->verification_code)
                    try {
                        const res = await fetch('/api/v1/verifications/{{ $session->id }}', {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            const sessionData = data.data || data;
                            if (sessionData && sessionData.verification_code) {
                                const container = document.getElementById('otp-container-{{ $session->id }}');
                                const waiting = document.getElementById('waiting-{{ $session->id }}');
                                const otpText = document.getElementById('otp-text-{{ $session->id }}');
                                if (container && waiting && otpText) {
                                    otpText.textContent = sessionData.verification_code;
                                    container.style.display = 'block';
                                    waiting.style.display = 'none';
                                    const copyBtn = document.getElementById('btn-copy-otp-{{ $session->id }}');
                                    if (copyBtn) copyBtn.onclick = () => copyToClipboard(sessionData.verification_code, copyBtn);
                                }
                            }
                        }
                    } catch(e) {}
                @endif
            @endforeach
        }, 2500);
    @endif

    // 3. Service Filtering and Chip Selection
    let isCustomService = false;

    function selectServiceItem(serviceId, serviceName) {
        isCustomService = false;
        document.getElementById('custom-service-container').style.display = 'none';
        const select = document.getElementById('select-service');
        if (select) {
            select.value = serviceId;
        }

        document.querySelectorAll('.service-chip').forEach(c => {
            c.style.background = '#18181b';
            c.style.borderColor = 'rgba(255,255,255,0.1)';
            c.style.color = '#e4e4e7';
        });

        const activeChip = document.getElementById('chip-' + serviceId);
        if (activeChip) {
            activeChip.style.background = 'rgba(20, 184, 166, 0.2)';
            activeChip.style.borderColor = '#14b8a6';
            activeChip.style.color = '#2dd4bf';
        }

        updatePrice();
    }

    function onServiceSelectChange() {
        const select = document.getElementById('select-service');
        if (!select) return;
        isCustomService = false;
        document.getElementById('custom-service-container').style.display = 'none';

        document.querySelectorAll('.service-chip').forEach(c => {
            c.style.background = '#18181b';
            c.style.borderColor = 'rgba(255,255,255,0.1)';
            c.style.color = '#e4e4e7';
        });
        const activeChip = document.getElementById('chip-' + select.value);
        if (activeChip) {
            activeChip.style.background = 'rgba(20, 184, 166, 0.2)';
            activeChip.style.borderColor = '#14b8a6';
            activeChip.style.color = '#2dd4bf';
        }

        updatePrice();
    }

    function filterServicesList(keyword) {
        const select = document.getElementById('select-service');
        const customBox = document.getElementById('custom-service-container');
        const term = keyword.toLowerCase().trim();

        if (!term) {
            Array.from(select.options).forEach(opt => opt.hidden = false);
            customBox.style.display = 'none';
            isCustomService = false;
            return;
        }

        let foundCount = 0;
        let firstMatch = null;

        Array.from(select.options).forEach(opt => {
            const name = (opt.dataset.name || opt.text).toLowerCase();
            const code = (opt.dataset.code || '').toLowerCase();
            if (name.includes(term) || code.includes(term)) {
                opt.hidden = false;
                foundCount++;
                if (!firstMatch) firstMatch = opt;
            } else {
                opt.hidden = true;
            }
        });

        if (firstMatch) {
            select.value = firstMatch.value;
            customBox.style.display = 'none';
            isCustomService = false;
            updatePrice();
        } else {
            // No matching catalog service: switch to custom service input
            customBox.style.display = 'block';
            document.getElementById('custom-service-name').value = keyword;
            isCustomService = true;
            updatePrice();
        }
    }

    // 4. Dynamic Live Price Fetcher (Zero Hardcoding)
    async function updatePrice() {
        const countryId = document.getElementById('select-country')?.value;
        const serviceId = document.getElementById('select-service')?.value;
        const priceEl = document.getElementById('price-display');
        const subtextEl = document.getElementById('price-subtext');
        if (!countryId || (!serviceId && !isCustomService)) return;

        try {
            const res = await fetch(`/api/v1/pricing?country_id=${countryId}&service_id=${serviceId || 1}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const data = await res.json();
                const item = data.data || data;
                if (priceEl && item) {
                    priceEl.textContent = item.formatted || `${item.currency_symbol || '₦'}${item.price}`;
                }
                if (subtextEl && item) {
                    subtextEl.textContent = `Base: $${parseFloat(item.price_usd || 0.85).toFixed(2)} USD`;
                }
            }
        } catch(e) {}
    }

    // 5. Order Number Handler (Live PVADeals Allocation)
    async function handleOrder() {
        const btn = document.getElementById('btn-order');
        const errEl = document.getElementById('provision-error');
        const errText = document.getElementById('provision-error-text');
        const errAction = document.getElementById('provision-error-action');

        if (errEl) errEl.style.display = 'none';
        if (errAction) errAction.style.display = 'none';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Connecting to Carrier Gateway...';
        }

        const countryId = parseInt(document.getElementById('select-country').value);
        const payload = { country_id: countryId };

        if (isCustomService) {
            payload.service_name = document.getElementById('custom-service-name').value.trim() || 'Custom Service';
        } else {
            payload.service_id = parseInt(document.getElementById('select-service').value);
        }

        try {
            const res = await fetch('/api/v1/verifications/request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (res.ok && data.success) {
                btn.innerHTML = '✓ Number Allocated!';
                setTimeout(() => location.reload(), 600);
            } else {
                if (errEl) {
                    const msg = data.message || 'Unable to allocate virtual number at this time.';
                    errText.textContent = msg;
                    errEl.style.display = 'block';

                    if (msg.toLowerCase().includes('api key') || msg.toLowerCase().includes('pvadeals')) {
                        if (errAction) errAction.style.display = 'block';
                    }
                }
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = 'Get Number Now';
                }
            }
        } catch (e) {
            if (errEl) {
                errText.textContent = 'Connection timeout while contacting provider. Please retry.';
                errEl.style.display = 'block';
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = 'Get Number Now';
            }
        }
    }

    // 6. Save Carrier Gateway API Key
    async function savePvaKey() {
        const input = document.getElementById('pva-api-key-input');
        const btn = document.getElementById('btn-save-pvadeals');
        const feedback = document.getElementById('pva-save-feedback');
        const key = input ? input.value.trim() : '';

        if (!key) {
            alert('Please enter your Carrier API key');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Verifying Carrier Gateway...';
        feedback.style.display = 'none';

        try {
            const res = await fetch('/api/v1/provider/pvadeals/key', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ api_key: key })
            });

            const data = await res.json();
            feedback.style.display = 'block';

            if (res.ok && data.success) {
                feedback.style.background = 'rgba(34, 197, 94, 0.15)';
                feedback.style.border = '1px solid rgba(34, 197, 94, 0.4)';
                feedback.style.color = '#A78BFA';
                feedback.textContent = `✓ Connected! Verified balance: $${data.data?.balance_usd ?? '0.00'} USD`;
                btn.disabled = false;
                btn.textContent = 'Key Saved!';
                setTimeout(() => location.reload(), 1200);
            } else {
                feedback.style.background = 'rgba(239, 68, 68, 0.12)';
                feedback.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                feedback.style.color = '#f87171';
                feedback.textContent = data.message || 'Failed to authenticate key.';
                btn.disabled = false;
                btn.textContent = 'Save & Verify Connection';
            }
        } catch(e) {
            feedback.style.display = 'block';
            feedback.style.background = 'rgba(239, 68, 68, 0.12)';
            feedback.style.color = '#f87171';
            feedback.textContent = 'Network error while connecting to server.';
            btn.disabled = false;
            btn.textContent = 'Save & Verify Connection';
        }
    }

    // 7. Sync Catalog from PVADeals
    async function syncPvaCatalog() {
        const btn = document.getElementById('btn-sync-catalog');
        const feedback = document.getElementById('pva-save-feedback');
        btn.disabled = true;
        btn.textContent = 'Synchronizing Catalog...';

        try {
            const res = await fetch('/api/v1/provider/pvadeals/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            feedback.style.display = 'block';

            if (res.ok && data.success) {
                feedback.style.background = 'rgba(34, 197, 94, 0.15)';
                feedback.style.color = '#A78BFA';
                feedback.textContent = data.message || 'Services successfully synced!';
                setTimeout(() => location.reload(), 1500);
            } else {
                feedback.style.background = 'rgba(239, 68, 68, 0.12)';
                feedback.style.color = '#f87171';
                feedback.textContent = data.message || 'Sync failed.';
                btn.disabled = false;
                btn.textContent = '🔄 Sync Live Services Catalog';
            }
        } catch (e) {
            feedback.style.display = 'block';
            feedback.style.color = '#f87171';
            feedback.textContent = 'Network error during catalog sync.';
            btn.disabled = false;
            btn.textContent = '🔄 Sync Live Services Catalog';
        }
    }

    // 8. Cancel Session Handler
    async function cancelSession(sessionId) {
        if (!confirm('Cancel this session and refund held funds?')) return;
        const btn = document.getElementById('btn-cancel-' + sessionId);
        if (btn) { btn.disabled = true; btn.textContent = 'Refunding...'; }

        try {
            const res = await fetch('/api/v1/verifications/' + sessionId + '/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                location.reload();
            } else {
                alert('Could not cancel session.');
                if (btn) { btn.disabled = false; btn.textContent = 'Cancel & Refund'; }
            }
        } catch(e) {
            if (btn) { btn.disabled = false; btn.textContent = 'Cancel & Refund'; }
        }
    }

    // 9. Balance Synchronization Engine
    function updateAllNativeBalances(newBalance) {
        const num = Number(newBalance);
        const formatted = num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const sym = "{{ $wallet->currency->symbol ?? '₦' }}";

        const heroBal = document.getElementById('wallet-tab-balance');
        if (heroBal) heroBal.textContent = formatted;

        const dashBal = document.getElementById('dash-wallet-balance');
        if (dashBal) dashBal.textContent = formatted;

        const statsBal = document.getElementById('stats-balance');
        if (statsBal) statsBal.textContent = sym + num.toLocaleString(undefined, { maximumFractionDigits: 0 });

        const settingsBal = document.getElementById('settings-wallet-balance');
        if (settingsBal) settingsBal.textContent = sym + formatted;

        const userBal = document.getElementById('user-wallet-balance');
        if (userBal) userBal.textContent = formatted;
    }

    window.addEventListener('balance-updated', function(e) {
        if (e.detail && e.detail.balance !== undefined) {
            updateAllNativeBalances(e.detail.balance);
        }
    });

    // 10. Carrier Simulator Webhook
    async function submitSimWebhook() {
        const btn = document.getElementById('btn-sim-submit');
        const provider = document.getElementById('sim-provider').value;
        const phone = document.getElementById('sim-phone').value;
        const body = document.getElementById('sim-body').value;
        const resultEl = document.getElementById('sim-result');

        btn.disabled = true;
        btn.textContent = 'Delivering Webhook...';
        resultEl.style.display = 'none';

        try {
            const payload = {
                event: 'sms_received',
                From: '+19999990000',
                To: phone,
                Body: body,
                phone: phone,
                number: phone,
                message: body,
                text: body,
                requestId: 'SIM_' + Date.now()
            };

            const res = await fetch(`/api/v1/webhooks/providers/${provider}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            resultEl.style.display = 'block';

            if (res.ok && data.success) {
                resultEl.style.background = 'rgba(34, 197, 94, 0.12)';
                resultEl.style.border = '1px solid rgba(34, 197, 94, 0.3)';
                resultEl.style.color = '#A78BFA';
                resultEl.innerHTML = `<strong>✓ Ingested!</strong> OTP Extracted: <span style="font-family: monospace; font-weight: 800;">${data.data?.extracted_otp || 'Yes'}</span>`;
                setTimeout(() => {
                    closeSimModal();
                    location.reload();
                }, 1200);
            } else {
                resultEl.style.background = 'rgba(239, 68, 68, 0.12)';
                resultEl.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                resultEl.style.color = '#f87171';
                resultEl.textContent = data.message || 'Webhook delivered but no matching session found.';
                btn.disabled = false;
                btn.textContent = 'Send Simulated SMS';
            }
        } catch (e) {
            resultEl.style.display = 'block';
            resultEl.style.background = 'rgba(239, 68, 68, 0.12)';
            resultEl.style.color = '#f87171';
            resultEl.textContent = 'Network error while delivering webhook.';
            btn.disabled = false;
            btn.textContent = 'Send Simulated SMS';
        }
    }

    // Modal backdrop click handlers
    ['simulator-modal', 'buy-number-modal', 'rent-number-modal', 'esim-order-modal', 'tutorial-modal', 'pvadeals-key-modal', 'notifications-modal', 'country-picker-modal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('blitz-open');
                this.style.display = '';
            }
        });
    });

    // 11. PVADeals Rent Tab Handlers
    const countryIdMap = {
        'USA': 1, 'US': 1,
        'UK': 2, 'GB': 2, 'GBR': 2,
        'AU': 13, 'AUS': 13,
        'ID': 17, 'IDN': 17,
        'HK': 31, 'HKG': 31
    };

    const rentCountryConfig = {
        'USA': {
            code: 'USA',
            id: 1,
            flag: '🇺🇸',
            name: 'United States',
            carrier: { name: 'T-Mobile', badge: 'T', color: '#E20074' },
            autoRenew: true,
            desc: 'Includes unlimited SMS verifications across all supported apps and services for the entire rental duration.',
            hasStr: true,
            strPrice: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '0.85' : '1500' }},
            plans: [
                { id: '3d', days: 3, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '3.00' : '5000' }}, name: '3 Days Rental' },
                { id: '7d', days: 7, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '5.00' : '8000' }}, name: '7 Days Rental' },
                { id: '14d', days: 14, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '9.00' : '14500' }}, name: '14 Days Rental' },
                { id: '30d', days: 28, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '12.99' : '24000' }}, name: '28 Days Monthly Line' },
                { id: 'allservices', days: 28, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '12.99' : '24000' }}, name: '★ All Services (Best Value)' }
            ]
        },
        'UK': {
            code: 'UK',
            id: 2,
            flag: '🇬🇧',
            name: 'United Kingdom',
            carrier: { name: 'EE', badge: 'EE', color: '#007B85' },
            autoRenew: true,
            desc: 'Unlimited UK SMS across all supported apps and services. Physical carrier routing with auto-renewal support.',
            hasStr: true,
            strPrice: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '0.85' : '1500' }},
            plans: [
                { id: '3d', days: 3, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '3.00' : '5000' }}, name: '3 Days Rental' },
                { id: '7d', days: 7, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '5.00' : '8000' }}, name: '7 Days Rental' },
                { id: '14d', days: 14, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '9.00' : '14500' }}, name: '14 Days Rental' },
                { id: '30d', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '6.99' : '12000' }}, name: '30 Days Monthly Line' },
                { id: '90d', days: 90, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '11.99' : '20000' }}, name: '90 Days Line' },
                { id: 'allservices', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '6.99' : '12000' }}, name: '★ All Services (Best Value)' }
            ]
        },
        'HK': {
            code: 'HK',
            id: 31,
            flag: '🇭🇰',
            name: 'Hong Kong',
            carrier: { name: 'HK Telecom', badge: 'HK', color: '#E11D48' },
            autoRenew: true,
            desc: 'Dedicated private Hong Kong carrier line for multi-app SMS and WhatsApp Business.',
            hasStr: false,
            plans: [
                { id: '30d', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '11.50' : '19500' }}, name: '30 Days Dedicated Line' },
                { id: '180d', days: 180, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '22.50' : '38000' }}, name: '180 Days (6 Months)' },
                { id: 'allservices', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '11.50' : '19500' }}, name: '★ All Services (Best Value)' }
            ]
        },
        'ID': {
            code: 'ID',
            id: 17,
            flag: '🇮🇩',
            name: 'Indonesia',
            carrier: { name: 'Telkomsel', badge: 'ID', color: '#DC2626' },
            autoRenew: true,
            desc: 'Exclusive Indonesia private carrier line with high delivery score for international 2FA.',
            hasStr: false,
            plans: [
                { id: '30d', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '11.99' : '20500' }}, name: '30 Days Dedicated Line' },
                { id: '180d', days: 180, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '21.99' : '37500' }}, name: '180 Days (6 Months)' },
                { id: 'allservices', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '11.99' : '20500' }}, name: '★ All Services (Best Value)' }
            ]
        },
        'AU': {
            code: 'AU',
            id: 13,
            flag: '🇦🇺',
            name: 'Australia',
            carrier: { name: 'Telstra', badge: 'AU', color: '#0284C7' },
            autoRenew: true,
            desc: 'Real Australian carrier SIM lease for continuous SMS OTP and ongoing verifications.',
            hasStr: false,
            plans: [
                { id: '30d', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '22.99' : '39000' }}, name: '30 Days Dedicated Line' },
                { id: 'allservices', days: 30, price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '22.99' : '39000' }}, name: '★ All Services (Best Value)' }
            ]
        }
    };

    let currentRentPlan = {
        planId: '30d',
        days: 28,
        price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '12.99' : '24000' }},
        planName: '28 Days Monthly Line'
    };
    let currentRentCountry = {
        code: 'USA',
        id: 1,
        flag: '🇺🇸',
        name: 'United States'
    };

    function rentSelectPlan(planId, days, price, planName) {
        currentRentPlan = { planId, days, price, planName };
        
        // Update duration pills styling
        document.querySelectorAll('.rent-duration-pill').forEach(pill => {
            pill.style.background = '#EEF1F8';
            pill.style.color = '#1E1B4B';
            pill.style.border = '1.5px solid rgba(30, 27, 75, 0.08)';
            pill.style.boxShadow = 'none';
        });

        const activePill = document.getElementById('rent-pill-' + planId);
        if (activePill) {
            activePill.style.background = '#1E1B4B';
            activePill.style.color = '#FFFFFF';
            activePill.style.borderColor = '#1E1B4B';
            activePill.style.boxShadow = '0 4px 12px rgba(30, 27, 75, 0.25)';
        }

        const sym = "{{ $wallet->currency->symbol ?? '₦' }}";
        const formattedPrice = sym + Number(price).toLocaleString();

        const priceEl = document.getElementById('rent-allservices-price');
        if (priceEl) priceEl.textContent = formattedPrice;

        const durEl = document.getElementById('rent-allservices-duration');
        if (durEl) durEl.textContent = `/ ${days >= 1 ? days + ' days' : '20 mins'}`;

        const actionBtnText = document.getElementById('rent-action-btn-text');
        if (actionBtnText) {
            if (planId === '20m') {
                actionBtnText.textContent = `Get 20 Minutes (STR) Line · ${formattedPrice}`;
            } else {
                actionBtnText.textContent = `Rent ${planName} · ${formattedPrice}`;
            }
        }

        const modalPlanName = document.getElementById('rent-modal-plan-name');
        if (modalPlanName) modalPlanName.textContent = planName;

        const modalPrice = document.getElementById('rent-modal-price');
        if (modalPrice) modalPrice.textContent = formattedPrice;
    }

    function renderRentDurationPills(cfg) {
        const ltrWrap = document.getElementById('rent-ltr-pills');
        const strPill20m = document.getElementById('rent-pill-20m');
        if (strPill20m) {
            strPill20m.style.display = cfg.hasStr ? 'inline-block' : 'none';
            if (cfg.hasStr) {
                strPill20m.onclick = () => rentSelectPlan('20m', 0.014, cfg.strPrice, '20 Minutes (STR)');
            }
        }

        if (ltrWrap && cfg.plans) {
            ltrWrap.innerHTML = '';
            cfg.plans.forEach(plan => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.id = 'rent-pill-' + plan.id;
                btn.className = 'blitz-btn rent-duration-pill';
                const isSelected = (currentRentPlan && currentRentPlan.planId === plan.id);
                btn.style.cssText = isSelected
                    ? 'background: #1E1B4B; color: #FFFFFF; border: 1.5px solid #1E1B4B; border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 800; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25);'
                    : 'background: #EEF1F8; color: #1E1B4B; border: 1.5px solid rgba(30, 27, 75, 0.08); border-radius: 9999px; padding: 8px 18px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;';
                
                if (plan.id === 'allservices') {
                    btn.innerHTML = `<span>★ All services</span><span style="background: #7C3AED; color: #FFFFFF; font-size: 10px; padding: 2px 6px; border-radius: 6px; margin-left: 6px;">BEST VALUE</span>`;
                } else {
                    btn.textContent = plan.days >= 1 ? `${plan.days} days` : '20 mins';
                }
                btn.onclick = () => rentSelectPlan(plan.id, plan.days, plan.price, plan.name);
                ltrWrap.appendChild(btn);
            });
        }

        // Keep current plan if available in new country, otherwise select default
        const defaultPlan = cfg.plans.find(p => p.id === '30d') || cfg.plans[0];
        const matchingPlan = cfg.plans.find(p => p.id === (currentRentPlan ? currentRentPlan.planId : ''));
        const chosen = matchingPlan || defaultPlan;
        if (chosen) {
            rentSelectPlan(chosen.id, chosen.days, chosen.price, chosen.name);
        }
    }

    function rentSelectCountry(code, flag, name, id) {
        const upper = (code || '').toUpperCase().trim();
        const normCode = (upper === 'US' || upper === 'USA') ? 'USA'
            : ((upper === 'GB' || upper === 'GBR' || upper === 'UK') ? 'UK'
            : upper);

        const cfg = rentCountryConfig[normCode] || rentCountryConfig['USA'];
        currentRentCountry = {
            code: normCode,
            id: id || cfg.id || countryIdMap[normCode] || 1,
            flag: flag || cfg.flag,
            name: name || cfg.name
        };

        // Update active country pills
        document.querySelectorAll('.rent-country-pill').forEach(pill => {
            pill.style.background = '#EEF1F8';
            pill.style.color = '#1E1B4B';
            pill.style.border = '1.5px solid rgba(30, 27, 75, 0.08)';
            pill.classList.remove('active');
        });
        const activePill = document.getElementById('rent-country-' + normCode.toLowerCase());
        if (activePill) {
            activePill.style.background = '#1E1B4B';
            activePill.style.color = '#FFFFFF';
            activePill.style.borderColor = '#1E1B4B';
            activePill.classList.add('active');
        }

        // Update carrier badge
        const badgeEl = document.getElementById('rent-carrier-badge');
        const carrierNameEl = document.getElementById('rent-carrier-name');
        if (badgeEl && carrierNameEl && cfg.carrier) {
            badgeEl.textContent = cfg.carrier.badge;
            badgeEl.style.background = cfg.carrier.color;
            carrierNameEl.textContent = cfg.carrier.name;
        }

        // Update description
        const descEl = document.getElementById('rent-allservices-desc');
        if (descEl && cfg.desc) descEl.textContent = cfg.desc;

        // Render duration pills for this country
        renderRentDurationPills(cfg);

        const modalCountryName = document.getElementById('rent-modal-country-name');
        if (modalCountryName) modalCountryName.textContent = `${cfg.flag} ${cfg.name}`;
    }

    function handleRentActionClick() {
        if (currentRentPlan && currentRentPlan.planId === '20m') {
            const buyTabBtn = document.querySelector('#blitz-tab-bar [data-tab="buy"]');
            if (typeof switchTab === 'function' && buyTabBtn) {
                switchTab('buy', buyTabBtn);
                if (typeof window.pvSelectDuration === 'function') {
                    window.pvSelectDuration('STR', 20);
                }
                return;
            }
        }
        openRentModal();
    }

    async function executeRentOrder() {
        const btn = document.getElementById('btn-confirm-rent');
        const statusEl = document.getElementById('rent-order-status');
        btn.disabled = true;
        btn.textContent = 'Allocating Dedicated Line...';
        if (statusEl) statusEl.style.display = 'none';

        try {
            const countryId = currentRentCountry.id || countryIdMap[currentRentCountry.code] || 1;
            const countryCode = (currentRentCountry.code === 'USA' ? 'US' : currentRentCountry.code);
            const isStr = currentRentPlan.days < 1 || currentRentPlan.planId === '20m';

            const res = await fetch('/api/v1/verifications/request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    country_id: countryId,
                    country_code: countryCode,
                    service_name: isStr ? 'Short-Term Rental' : 'All services',
                    duration: isStr ? 0 : Math.round(currentRentPlan.days)
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                if (statusEl) {
                    statusEl.style.display = 'block';
                    statusEl.style.background = 'rgba(124, 58, 237, 0.12)';
                    statusEl.style.color = '#7C3AED';
                    statusEl.innerHTML = '✓ Dedicated line allocated successfully!';
                }
                btn.textContent = '✓ Reserved!';
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Unable to complete rental order.');
            }
        } catch(e) {
            if (statusEl) {
                statusEl.style.display = 'block';
                statusEl.style.background = 'rgba(239, 68, 68, 0.12)';
                statusEl.style.color = '#ef4444';
                statusEl.textContent = e.message || 'Order failed. Please check your wallet balance.';
            }
            btn.disabled = false;
            btn.textContent = 'Confirm & Reserve Dedicated Line';
        }
    }

    // 12. PVADeals eSIM Tab Handlers
    let currentEsimCountry = {
        code: 'USA',
        flag: '🇺🇸',
        name: 'United States',
        network: '5G LTE Ready'
    };
    let currentEsimPackage = {
        pkgId: '3gb',
        name: '3 GB Best Value',
        price: {{ ($wallet->currency->code ?? 'NGN') === 'USD' ? '8.00' : '12500' }},
        validity: '30 Days Validity'
    };

    function esimSelectCountry(code, flag, name, network) {
        currentEsimCountry = { code, flag, name, network };

        document.querySelectorAll('.esim-country-card').forEach(card => {
            card.style.borderColor = 'rgba(30, 27, 75, 0.1)';
            card.style.boxShadow = '0 2px 6px rgba(30, 27, 75, 0.02)';
        });

        const activeCard = document.getElementById('esim-dest-' + code);
        if (activeCard) {
            activeCard.style.borderColor = '#1E1B4B';
            activeCard.style.borderWidth = '1.5px';
            activeCard.style.boxShadow = '0 4px 12px rgba(30, 27, 75, 0.08)';
        }

        const label = document.getElementById('esim-active-country-label');
        if (label) label.textContent = `${name} ${flag}`;

        const modalCountry = document.getElementById('esim-modal-country');
        if (modalCountry) modalCountry.textContent = `${flag} ${name}`;

        const modalNetwork = document.getElementById('esim-modal-network');
        if (modalNetwork) modalNetwork.textContent = network;
    }

    function esimSelectPackage(pkgId, name, price, validity) {
        currentEsimPackage = { pkgId, name, price, validity };

        document.querySelectorAll('.esim-package-card').forEach(card => {
            card.style.border = '1px solid rgba(30, 27, 75, 0.1)';
            card.style.boxShadow = '0 2px 6px rgba(30, 27, 75, 0.03)';
        });

        const activeCard = document.getElementById('esim-pkg-' + pkgId);
        if (activeCard) {
            activeCard.style.border = '2px solid #1E1B4B';
            activeCard.style.boxShadow = '0 4px 14px rgba(30, 27, 75, 0.12)';
        }

        const sym = "{{ $wallet->currency->symbol ?? '₦' }}";
        const formattedPrice = sym + Number(price).toLocaleString();

        const actionBtnText = document.getElementById('esim-action-btn-text');
        if (actionBtnText) actionBtnText.textContent = `Order eSIM Profile · ${formattedPrice}`;

        const modalPkg = document.getElementById('esim-modal-package');
        if (modalPkg) modalPkg.textContent = `${name} (${validity})`;

        const modalPrice = document.getElementById('esim-modal-price');
        if (modalPrice) modalPrice.textContent = formattedPrice;
    }

    function openEsimModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('esim-order-modal');
            if (m) m.classList.add('blitz-open');
        });
    }

    function closeEsimModal() {
        const m = document.getElementById('esim-order-modal');
        if (m) m.classList.remove('blitz-open');
    }

    async function executeEsimOrder() {
        const btn = document.getElementById('btn-confirm-esim');
        const statusEl = document.getElementById('esim-order-status');
        btn.disabled = true;
        btn.textContent = 'Generating Digital eSIM Profile...';
        if (statusEl) statusEl.style.display = 'none';

        try {
            await new Promise(r => setTimeout(r, 1200));
            if (statusEl) {
                statusEl.style.display = 'block';
                statusEl.style.background = 'rgba(124, 58, 237, 0.12)';
                statusEl.style.color = '#7C3AED';
                statusEl.innerHTML = '✓ eSIM Profile activated! QR Code dispatched to your account.';
            }
            btn.textContent = '✓ Activated!';
            setTimeout(() => {
                closeEsimModal();
                btn.disabled = false;
                btn.textContent = 'Confirm & Activate eSIM';
            }, 1500);
        } catch(e) {
            if (statusEl) {
                statusEl.style.display = 'block';
                statusEl.style.background = 'rgba(239, 68, 68, 0.12)';
                statusEl.style.color = '#ef4444';
                statusEl.textContent = 'eSIM order failed. Please retry.';
            }
            btn.disabled = false;
            btn.textContent = 'Confirm & Activate eSIM';
        }
    }

    // 13. Modals Controls (Notifications, Country, Security PIN, Support, Terms)
    function openNotificationsModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('notifications-modal');
            if (m) m.classList.add('blitz-open');
        });
    }
    function closeNotificationsModal() {
        const m = document.getElementById('notifications-modal');
        if (m) m.classList.remove('blitz-open');
    }

    function openCountryPicker(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('country-picker-modal');
            if (m) {
                m.classList.add('blitz-open');
                const searchInp = document.getElementById('global-country-search');
                if (searchInp) {
                    searchInp.value = '';
                    filterGlobalCountries('');
                    setTimeout(() => searchInp.focus(), 150);
                }
            }
        });
    }
    function closeCountryPicker() {
        const m = document.getElementById('country-picker-modal');
        if (m) m.classList.remove('blitz-open');
    }

    function filterGlobalCountries(term) {
        term = (term || '').toLowerCase().trim();
        document.querySelectorAll('.global-country-row').forEach(el => {
            const name = el.getAttribute('data-name') || '';
            const code = (el.getAttribute('data-code') || '').toLowerCase();
            if (!term || name.includes(term) || code.includes(term)) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }

    function selectGlobalCountry(code, flag, name, id) {
        closeCountryPicker();

        // Update header flag & code
        const flagEl = document.getElementById('header-country-flag');
        const codeEl = document.getElementById('header-country-code');
        if (flagEl && flag) flagEl.textContent = flag;
        if (codeEl && code) codeEl.textContent = code;

        // Update modal rows checkmark
        document.querySelectorAll('.global-country-row').forEach(row => {
            const isMatch = row.getAttribute('data-code') === code;
            row.style.background = isMatch ? '#EEF1F8' : '#FFFFFF';
            row.style.borderColor = isMatch ? '#1E1B4B' : 'rgba(30, 27, 75, 0.1)';
            const chk = row.querySelector('.country-check');
            if (chk) chk.style.display = isMatch ? 'inline' : 'none';
        });

        // Update Rent tab
        if (typeof rentSelectCountry === 'function') {
            rentSelectCountry(code, flag, name, id);
        }

        // Update Buy / Purchase tab
        if (typeof window.pvSetCountry === 'function') {
            window.pvSetCountry(code, flag, name, id);
        } else if (typeof pvSelectCountry === 'function') {
            const pill = document.querySelector(`.pv-pill[data-country="${code}"]`);
            if (pill) pvSelectCountry(code, pill, null);
        }

        try {
            localStorage.setItem('Verixa_preferred_country', JSON.stringify({ code, flag, name, id }));
        } catch(e) {}
    }

    // Security PIN Modal & Keypad
    let _pinBuffer = '';
    function openSecurityPinModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('security-pin-modal');
            if (m) {
                m.classList.add('blitz-open');
                _pinBuffer = '';
                renderPinDots();
                const fb = document.getElementById('pin-feedback-msg');
                if (fb) fb.textContent = '';
            }
        });
    }
    function closeSecurityPinModal() {
        const m = document.getElementById('security-pin-modal');
        if (m) m.classList.remove('blitz-open');
    }
    function pressPinDigit(d) {
        if (_pinBuffer.length < 4) {
            _pinBuffer += d;
            renderPinDots();
            if (_pinBuffer.length === 4) {
                const fb = document.getElementById('pin-feedback-msg');
                if (fb) {
                    fb.textContent = '4-digit PIN entered. Tap "Save & Update PIN" to apply.';
                    fb.style.color = '#7C3AED';
                }
            }
        }
    }
    function deletePinDigit() {
        if (_pinBuffer.length > 0) {
            _pinBuffer = _pinBuffer.slice(0, -1);
            renderPinDots();
            const fb = document.getElementById('pin-feedback-msg');
            if (fb) fb.textContent = '';
        }
    }
    function clearPin() {
        _pinBuffer = '';
        renderPinDots();
        const fb = document.getElementById('pin-feedback-msg');
        if (fb) fb.textContent = '';
    }
    function renderPinDots() {
        for (let i = 1; i <= 4; i++) {
            const dot = document.getElementById('pin-dot-' + i);
            if (!dot) continue;
            if (i <= _pinBuffer.length) {
                dot.style.background = '#1E1B4B';
                dot.style.transform = 'scale(1.15)';
            } else {
                dot.style.background = 'transparent';
                dot.style.transform = 'scale(1)';
            }
        }
    }
    function saveSecurityPin() {
        const fb = document.getElementById('pin-feedback-msg');
        if (_pinBuffer.length < 4) {
            if (fb) {
                fb.textContent = 'Please enter a complete 4-digit PIN';
                fb.style.color = '#EF4444';
            }
            return;
        }
        localStorage.setItem('Verixa_security_pin', _pinBuffer);
        if (fb) {
            fb.textContent = '✓ Security PIN saved & activated!';
            fb.style.color = '#7C3AED';
        }
        setTimeout(closeSecurityPinModal, 1100);
    }

    // Support Modal
    function openSupportModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('support-modal');
            if (m) m.classList.add('blitz-open');
        });
    }
    function closeSupportModal() {
        const m = document.getElementById('support-modal');
        if (m) m.classList.remove('blitz-open');
    }

    // Terms & Privacy Modal
    function openTermsModal(e) {
        window.blitzTap(e, () => {
            const m = document.getElementById('terms-privacy-modal');
            if (m) m.classList.add('blitz-open');
        });
    }
    // ═════════════════════════════════════════════════════════════════
    // STR NUMBERS MANAGEMENT ENGINE (PVADeals Styled)
    // ═════════════════════════════════════════════════════════════════
    let strActiveStatus = 'all';
    let strActiveCountry = 'all';

    function switchStrSubView(view) {
        const listEl = document.getElementById('str-view-list');
        const orderEl = document.getElementById('str-view-order');
        const btnList = document.getElementById('str-tab-btn-list');
        const btnOrder = document.getElementById('str-tab-btn-order');

        if (view === 'order') {
            if (listEl) listEl.style.display = 'none';
            if (orderEl) orderEl.style.display = 'flex';
            if (btnList) {
                btnList.style.background = 'transparent';
                btnList.style.color = '#6B7A99';
                btnList.style.boxShadow = 'none';
            }
            if (btnOrder) {
                btnOrder.style.background = '#1E1B4B';
                btnOrder.style.color = '#FFFFFF';
                btnOrder.style.boxShadow = '0 2px 8px rgba(30, 27, 75, 0.2)';
            }
        } else {
            if (orderEl) orderEl.style.display = 'none';
            if (listEl) listEl.style.display = 'flex';
            if (btnOrder) {
                btnOrder.style.background = 'transparent';
                btnOrder.style.color = '#6B7A99';
                btnOrder.style.boxShadow = 'none';
            }
            if (btnList) {
                btnList.style.background = '#1E1B4B';
                btnList.style.color = '#FFFFFF';
                btnList.style.boxShadow = '0 2px 8px rgba(30, 27, 75, 0.2)';
            }
        }
    }

    function toggleStrFilters() {
        const drawer = document.getElementById('str-filters-drawer');
        const chevron = document.getElementById('str-filters-chevron');
        if (!drawer) return;
        const isOpen = drawer.style.display !== 'none';
        drawer.style.display = isOpen ? 'none' : 'flex';
        if (chevron) {
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }

    function selectStrCountryFilter(code, btn) {
        strActiveCountry = code;
        document.querySelectorAll('.str-country-filter-pill').forEach(p => {
            p.classList.remove('active');
            p.style.background = '#EEF1F8';
            p.style.color = '#1E1B4B';
            p.style.borderColor = 'rgba(30, 27, 75, 0.08)';
        });
        if (btn) {
            btn.classList.add('active');
            btn.style.background = '#1E1B4B';
            btn.style.color = '#FFFFFF';
            btn.style.borderColor = '#1E1B4B';
        }
        applyStrFilters();
    }

    function filterStrStatus(status, btn) {
        strActiveStatus = status;
        document.querySelectorAll('.str-status-pill').forEach(p => {
            p.classList.remove('active');
            p.style.background = '#EEF1F8';
            p.style.color = '#1E1B4B';
            p.style.borderColor = 'rgba(30, 27, 75, 0.08)';
            p.style.boxShadow = 'none';
        });
        if (btn) {
            btn.classList.add('active');
            btn.style.background = '#1E1B4B';
            btn.style.color = '#FFFFFF';
            btn.style.borderColor = '#1E1B4B';
            btn.style.boxShadow = '0 4px 12px rgba(30, 27, 75, 0.2)';
        }
        applyStrFilters();
    }

    function applyStrFilters() {
        const fromVal = document.getElementById('str-filter-date-from')?.value || '';
        const toVal = document.getElementById('str-filter-date-to')?.value || '';
        const webVal = (document.getElementById('str-filter-website')?.value || '').toLowerCase().trim();
        const numVal = (document.getElementById('str-filter-number')?.value || '').trim().replace(/[^0-9]/g, '');

        let activeFilterCount = 0;
        if (fromVal) activeFilterCount++;
        if (toVal) activeFilterCount++;
        if (webVal) activeFilterCount++;
        if (numVal) activeFilterCount++;
        if (strActiveCountry !== 'all') activeFilterCount++;

        const badge = document.getElementById('str-filter-count-badge');
        if (badge) badge.textContent = activeFilterCount;

        const cards = document.querySelectorAll('#str-orders-list-container .str-order-item-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            const country = card.getAttribute('data-country');
            const service = (card.getAttribute('data-service') || '').toLowerCase();
            const rawNumber = card.getAttribute('data-number') || '';
            const cleanNumber = rawNumber.replace(/[^0-9]/g, '');
            const date = card.getAttribute('data-date') || '';

            let match = true;

            // Status filter
            if (strActiveStatus !== 'all' && status !== strActiveStatus) {
                match = false;
            }

            // Country filter
            if (match && strActiveCountry !== 'all' && country !== strActiveCountry) {
                match = false;
            }

            // Service / Website search
            if (match && webVal && !service.includes(webVal)) {
                match = false;
            }

            // Number search
            if (match && numVal && !cleanNumber.includes(numVal)) {
                match = false;
            }

            // Date Range
            if (match && fromVal && date < fromVal) {
                match = false;
            }
            if (match && toVal && date > toVal) {
                match = false;
            }

            if (match) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('str-empty-state');
        if (emptyState) {
            emptyState.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : (cards.length === 0 ? 'block' : 'none');
        }
    }

    function clearStrFilters() {
        const fromInput = document.getElementById('str-filter-date-from');
        const toInput = document.getElementById('str-filter-date-to');
        const webInput = document.getElementById('str-filter-website');
        const numInput = document.getElementById('str-filter-number');

        if (fromInput) fromInput.value = '';
        if (toInput) toInput.value = '';
        if (webInput) webInput.value = '';
        if (numInput) numInput.value = '';

        strActiveCountry = 'all';
        document.querySelectorAll('.str-country-filter-pill').forEach(p => {
            const isAll = p.getAttribute('onclick')?.includes("'all'");
            p.classList.toggle('active', isAll);
            p.style.background = isAll ? '#1E1B4B' : '#EEF1F8';
            p.style.color = isAll ? '#FFFFFF' : '#1E1B4B';
            p.style.borderColor = isAll ? '#1E1B4B' : 'rgba(30, 27, 75, 0.08)';
        });

        applyStrFilters();
    }

    function exportStrCsv() {
        const cards = document.querySelectorAll('#str-orders-list-container .str-order-item-card');
        let csv = 'Service,Phone Number,Country,Status,Date\n';
        cards.forEach(c => {
            if (c.style.display !== 'none') {
                const s = c.getAttribute('data-service') || '';
                const n = c.getAttribute('data-number') || '';
                const co = c.getAttribute('data-country') || '';
                const st = c.getAttribute('data-status') || '';
                const d = c.getAttribute('data-date') || '';
                csv += `"${s}","${n}","${co}","${st}","${d}"\n`;
            }
        });
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `str_numbers_${Date.now()}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }

    // ═════════════════════════════════════════════════════════════════
    // LTR NUMBERS MANAGEMENT ENGINE (PVADeals Styled)
    // ═════════════════════════════════════════════════════════════════
    let ltrActiveStatus = 'all';
    let ltrActiveCountry = 'all';

    function switchLtrSubView(view) {
        const listEl = document.getElementById('ltr-view-list');
        const rentEl = document.getElementById('ltr-view-rent');
        const btnList = document.getElementById('ltr-tab-btn-list');
        const btnRent = document.getElementById('ltr-tab-btn-rent');

        if (view === 'rent') {
            if (listEl) listEl.style.display = 'none';
            if (rentEl) rentEl.style.display = 'flex';
            if (btnList) {
                btnList.style.background = 'transparent';
                btnList.style.color = '#6B7A99';
                btnList.style.boxShadow = 'none';
            }
            if (btnRent) {
                btnRent.style.background = '#1E1B4B';
                btnRent.style.color = '#FFFFFF';
                btnRent.style.boxShadow = '0 2px 8px rgba(30, 27, 75, 0.2)';
            }
        } else {
            if (rentEl) rentEl.style.display = 'none';
            if (listEl) listEl.style.display = 'flex';
            if (btnRent) {
                btnRent.style.background = 'transparent';
                btnRent.style.color = '#6B7A99';
                btnRent.style.boxShadow = 'none';
            }
            if (btnList) {
                btnList.style.background = '#1E1B4B';
                btnList.style.color = '#FFFFFF';
                btnList.style.boxShadow = '0 2px 8px rgba(30, 27, 75, 0.2)';
            }
        }
    }

    function toggleLtrFilters() {
        const drawer = document.getElementById('ltr-filters-drawer');
        const chevron = document.getElementById('ltr-filters-chevron');
        if (!drawer) return;
        const isOpen = drawer.style.display !== 'none';
        drawer.style.display = isOpen ? 'none' : 'flex';
        if (chevron) {
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }

    function selectLtrCountryFilter(code, btn) {
        ltrActiveCountry = code;
        document.querySelectorAll('.ltr-country-filter-pill').forEach(p => {
            p.classList.remove('active');
            p.style.background = '#EEF1F8';
            p.style.color = '#1E1B4B';
            p.style.borderColor = 'rgba(30, 27, 75, 0.08)';
        });
        if (btn) {
            btn.classList.add('active');
            btn.style.background = '#1E1B4B';
            btn.style.color = '#FFFFFF';
            btn.style.borderColor = '#1E1B4B';
        }
        applyLtrFilters();
    }

    function filterLtrStatus(status, btn) {
        ltrActiveStatus = status;
        document.querySelectorAll('.ltr-status-pill').forEach(p => {
            p.classList.remove('active');
            p.style.background = '#EEF1F8';
            p.style.color = '#1E1B4B';
            p.style.borderColor = 'rgba(30, 27, 75, 0.08)';
            p.style.boxShadow = 'none';
        });
        if (btn) {
            btn.classList.add('active');
            btn.style.background = '#1E1B4B';
            btn.style.color = '#FFFFFF';
            btn.style.borderColor = '#1E1B4B';
            btn.style.boxShadow = '0 4px 12px rgba(30, 27, 75, 0.2)';
        }
        applyLtrFilters();
    }

    function applyLtrFilters() {
        const fromVal = document.getElementById('ltr-filter-date-from')?.value || '';
        const toVal = document.getElementById('ltr-filter-date-to')?.value || '';
        const webVal = (document.getElementById('ltr-filter-website')?.value || '').toLowerCase().trim();
        const numVal = (document.getElementById('ltr-filter-number')?.value || '').trim().replace(/[^0-9]/g, '');

        let activeFilterCount = 0;
        if (fromVal) activeFilterCount++;
        if (toVal) activeFilterCount++;
        if (webVal) activeFilterCount++;
        if (numVal) activeFilterCount++;
        if (ltrActiveCountry !== 'all') activeFilterCount++;

        const badge = document.getElementById('ltr-filter-count-badge');
        if (badge) badge.textContent = activeFilterCount;
    }

    function clearLtrFilters() {
        const fromInput = document.getElementById('ltr-filter-date-from');
        const toInput = document.getElementById('ltr-filter-date-to');
        const webInput = document.getElementById('ltr-filter-website');
        const numInput = document.getElementById('ltr-filter-number');

        if (fromInput) fromInput.value = '';
        if (toInput) toInput.value = '';
        if (webInput) webInput.value = '';
        if (numInput) numInput.value = '';

        ltrActiveCountry = 'all';
        document.querySelectorAll('.ltr-country-filter-pill').forEach(p => {
            const isAll = p.getAttribute('onclick')?.includes("'all'");
            p.classList.toggle('active', isAll);
            p.style.background = isAll ? '#1E1B4B' : '#EEF1F8';
            p.style.color = isAll ? '#FFFFFF' : '#1E1B4B';
            p.style.borderColor = isAll ? '#1E1B4B' : 'rgba(30, 27, 75, 0.08)';
        });

        applyLtrFilters();
    }

    function exportLtrCsv() {
        const csv = 'Service,Phone Number,Country,Status,Plan\n';
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `ltr_numbers_${Date.now()}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }

    // Tab 3: My Numbers Filter Logic
    function filterNumberSessions(status, btn) {
        document.querySelectorAll('.numbers-filter-pill').forEach(p => {
            p.classList.remove('active');
            p.style.background = '#EEF1F8';
            p.style.color = '#1E1B4B';
            p.style.borderColor = 'rgba(30, 27, 75, 0.08)';
        });
        if (btn) {
            btn.classList.add('active');
            btn.style.background = '#1E1B4B';
            btn.style.color = '#FFFFFF';
            btn.style.borderColor = '#1E1B4B';
        }
        const activeContainer = document.getElementById('numbers-active-container');
        const rows = document.querySelectorAll('#numbers-history-list .session-item-row');
        
        rows.forEach(r => {
            const s = r.getAttribute('data-status');
            if (status === 'all' || s === status) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });

        if (activeContainer) {
            if (status === 'all' || status === 'active') {
                activeContainer.style.display = '';
            } else {
                activeContainer.style.display = 'none';
            }
        }
    }

    // Tab 4: Wallet Ledger Filter Logic
    function filterWalletLedger(category, btn) {
        document.querySelectorAll('.wallet-filter-pill').forEach(p => {
            p.classList.remove('active');
            p.style.background = '#EEF1F8';
            p.style.color = '#1E1B4B';
            p.style.borderColor = 'rgba(30, 27, 75, 0.08)';
        });
        if (btn) {
            btn.classList.add('active');
            btn.style.background = '#1E1B4B';
            btn.style.color = '#FFFFFF';
            btn.style.borderColor = '#1E1B4B';
        }
        const rows = document.querySelectorAll('#wallet-transactions-list .wallet-tx-row');
        rows.forEach(r => {
            const c = r.getAttribute('data-category');
            if (category === 'all' || c === category) {
                r.style.display = 'flex';
            } else {
                r.style.display = 'none';
            }
        });
    }

    // Tab 5: Biometrics Setting Toggle
    function toggleBiometricsSetting(enabled) {
        localStorage.setItem('Verixa_biometrics', enabled ? '1' : '0');
        const slider = document.getElementById('biometrics-slider');
        if (slider) {
            slider.style.backgroundColor = enabled ? '#7C3AED' : '#CBD5E1';
        }
    }

    // 14. Theme Toggle (Unified with desktop data-theme and storage)
    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        const isDark = current !== 'dark';
        
        applyTheme(isDark);
        localStorage.setItem('dark_mode', isDark);
        localStorage.setItem('Verixa_theme', isDark ? 'dark' : 'light');
        localStorage.setItem('app-theme', isDark ? 'dark' : 'light');
    }

    function applyTheme(isDark) {
        if (isDark) {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.documentElement.classList.add('dark');
            document.documentElement.classList.add('dark-theme');
            document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#0B132B');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.remove('dark-theme');
            document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#1E1B4B');
        }
        updateThemeIcon(isDark);
    }

    function updateThemeIcon(isDark) {
        const icon = document.getElementById('theme-toggle-icon');
        if (icon) {
            if (isDark) {
                icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>';
            } else {
                icon.innerHTML = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
            }
        }
        const accountThemeLabel = document.getElementById('account-theme-label');
        if (accountThemeLabel) {
            accountThemeLabel.textContent = isDark ? 'Dark' : 'Light';
        }
    }

    // Theme & Biometrics initialization on load
    (function initThemeAndPreferences() {
        const saved = localStorage.getItem('Verixa_theme') || localStorage.getItem('app-theme') || (localStorage.getItem('dark_mode') === 'true' ? 'dark' : null);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const useDark = (saved === 'dark') || (!saved && prefersDark);
        applyTheme(useDark);

        // Biometrics switch state
        const bioEnabled = localStorage.getItem('Verixa_biometrics') === '1';
        const bioInput = document.getElementById('biometrics-toggle');
        const bioSlider = document.getElementById('biometrics-slider');
        if (bioInput) bioInput.checked = bioEnabled;
        if (bioSlider) bioSlider.style.backgroundColor = bioEnabled ? '#7C3AED' : '#CBD5E1';
    })();

    // Initial price loading and tab activation on page ready
    document.addEventListener('DOMContentLoaded', () => {
        updatePrice();

        // Auto-activate requested tab from URL (?tab=dashboard, ?tab=buy, ?tab=numbers, ?tab=wallet, ?tab=account, etc.)
        try {
            const params = new URLSearchParams(window.location.search);
            const reqTab = params.get('tab');
            if (reqTab && ['dashboard', 'buy', 'numbers', 'wallet', 'account', 'home', 'transactions', 'sms', 'rent', 'esim'].includes(reqTab)) {
                switchTab(reqTab, document.querySelector(`#blitz-tab-bar [data-tab="${reqTab}"]`));
            }
        } catch(e) {}

        // Restore saved country preference if available
        try {
            const savedCountry = localStorage.getItem('Verixa_preferred_country');
            if (savedCountry) {
                const c = JSON.parse(savedCountry);
                if (c && c.code) {
                    const flagEl = document.getElementById('header-country-flag');
                    const codeEl = document.getElementById('header-country-code');
                    if (flagEl && c.flag) flagEl.textContent = c.flag;
                    if (codeEl && c.code) codeEl.textContent = c.code;

                    if (typeof window.pvSetCountry === 'function') {
                        window.pvSetCountry(c.code, c.flag, c.name, c.id);
                    }
                    if (typeof rentSelectCountry === 'function') {
                        rentSelectCountry(c.code, c.flag, c.name, c.id);
                    }
                }
            }
        } catch(e) {}


    });

    // Payment iframe prevention on dashboard load
    (function() {
        if (typeof window.stopGlobalPaymentPolling === 'function') {
            window.stopGlobalPaymentPolling();
        }
        
        try {
            const oldIframe = document.getElementById('complete-payment-iframe');
            if (oldIframe) {
                console.log('[MOBILE-DASHBOARD] SECURITY: Cached payment iframe detected and removed: iframe.remove()');
                oldIframe.src = 'about:blank';
                oldIframe.remove();
            }
            const iframeContainer = document.getElementById('complete-payment-iframe-container');
            if (iframeContainer) {
                iframeContainer.innerHTML = '';
                iframeContainer.style.display = 'none';
            }

            const paymentModal = document.getElementById('native-complete-payment-modal');
            if (paymentModal) paymentModal.style.display = 'none';

            const depositModal = document.getElementById('native-deposit-modal');
            if (depositModal) depositModal.style.display = 'none';

            const depositScreenModal = document.getElementById('native-deposit-screen-modal');
            if (depositScreenModal) depositScreenModal.style.display = 'none';
        } catch(e) {}
    })();

</script>
@endsection
