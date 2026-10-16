@extends('native.layouts.mobile')

@section('title', 'Settings')
@section('header_title', 'Settings')

@section('content')
<div style="display: flex; flex-direction: column; gap: 12px;">

    <!-- User Profile Hero Card -->
    <div class="card fade-in">
        <div class="card-body" style="display: flex; align-items: center; gap: 14px;">
            <x-brand-logo size="54" :glow="true" />
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->name }}</h3>
                    <span class="badge badge-primary" style="font-size: 10px;">PRO</span>
                </div>
                <p style="font-size: 12px; color: var(--text-tertiary); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->email }}</p>
                <div style="display: flex; align-items: center; gap: 6px; margin-top: 6px; font-size: 11px; color: var(--text-secondary);">
                    <span class="status-dot status-dot-pulse" style="background: var(--success);"></span>
                    <span>Account Active</span>
                    <span style="color: var(--text-quaternary);">·</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 10px; color: var(--text-tertiary);">UID #{{ $user->id }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Wallet Summary Card -->
    <div class="card fade-in" style="animation-delay: 0.05s;">
        <div class="card-body">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.04em;">Wallet Balance</div>
                    <div style="font-size: 22px; font-weight: 800; color: var(--text-white); font-family: 'JetBrains Mono', monospace; margin-top: 4px;">
                        ${{ number_format($wallet->available_balance, 2) }}
                    </div>
                    @if($wallet->active_holds_total > 0)
                        <div style="font-size: 11px; color: #fbbf24; margin-top: 2px;">
                            ${{ number_format($wallet->active_holds_total, 2) }} currently held
                        </div>
                    @endif
                </div>
                <a href="{{ route('mobile.wallet') }}" class="btn btn-primary btn-sm">
                    Manage Wallet →
                </a>
            </div>
        </div>
    </div>

    <!-- App Preferences -->
    <div class="card fade-in" style="animation-delay: 0.1s;">
        <div class="card-body" style="padding: 14px 18px;">
            <div class="section-label" style="margin-bottom: 12px;">Preferences</div>
            
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(20, 184, 166, 0.1); display: flex; align-items: center; justify-content: center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2dd4bf" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-white);">Instant SMS Alerts</div>
                            <div style="font-size: 11px; color: var(--text-tertiary);">Play sound when OTP arrives</div>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 44px; height: 24px; cursor: pointer;">
                        <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;" id="pref-sound">
                        <span style="position: absolute; inset: 0; background: var(--primary); border-radius: 24px; transition: 0.2s;" onclick="this.style.background = this.previousElementSibling.checked ? 'var(--text-quaternary)' : 'var(--primary)'">
                            <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 23px; bottom: 3px; background: white; border-radius: 50%; transition: 0.2s;"></span>
                        </span>
                    </label>
                </div>

                <div style="height: 1px; background: var(--border);"></div>

                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(6, 182, 212, 0.1); display: flex; align-items: center; justify-content: center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-white);">Auto-Copy OTP</div>
                            <div style="font-size: 11px; color: var(--text-tertiary);">Copy OTP code to clipboard on arrival</div>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 44px; height: 24px; cursor: pointer;">
                        <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;" id="pref-autocopy">
                        <span style="position: absolute; inset: 0; background: var(--primary); border-radius: 24px; transition: 0.2s;" onclick="this.style.background = this.previousElementSibling.checked ? 'var(--text-quaternary)' : 'var(--primary)'">
                            <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 23px; bottom: 3px; background: white; border-radius: 50%; transition: 0.2s;"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Device & Platform Info -->
    <div class="card fade-in" style="animation-delay: 0.15s;">
        <div class="card-body" style="padding: 16px 18px;">
            <div class="section-label" style="margin-bottom: 12px;">Platform Architecture</div>

            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-tertiary);">Runtime Framework:</span>
                    <span class="badge badge-info font-mono" style="font-size: 10px;">NativePHP Mobile 3.3.7</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-tertiary);">Application Bundle:</span>
                    <strong style="color: #a5b4fc; font-family: 'JetBrains Mono', monospace; font-size: 11px;">{{ config('nativephp.app_id', 'com.Verixa.app') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-tertiary);">Backend Core:</span>
                    <strong style="color: var(--text-white); font-family: 'JetBrains Mono', monospace; font-size: 11px;">Laravel {{ app()->version() }} · PHP {{ PHP_VERSION }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-tertiary);">Database Schema:</span>
                    <span class="badge badge-success" style="font-size: 10px;">77 Normalized Tables</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--text-tertiary);">Carriers Active:</span>
                    <strong style="color: #2dd4bf; font-size: 11px;">Twilio · 5sim · SMS-Activate · MockTelecom</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Switch Link -->
    <a href="{{ route('web.dashboard') }}" class="card fade-in" style="animation-delay: 0.2s; text-decoration: none; border-color: rgba(99, 102, 241, 0.3); background: rgba(99, 102, 241, 0.05);">
        <div class="card-body" style="padding: 16px 18px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#a5b4fc" stroke-width="2"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div>
                    <strong style="color: #a5b4fc; font-size: 13px;">Switch to Web Desktop Console</strong>
                    <p style="font-size: 11px; color: var(--text-tertiary); margin-top: 2px;">Full multi-column carrier simulator and audit view</p>
                </div>
            </div>
            <span style="color: #a5b4fc; font-size: 18px;">↗</span>
        </div>
    </a>

    <!-- Logout Button -->
    <form action="{{ route('logout') }}" method="POST" class="fade-in" style="animation-delay: 0.25s;" onsubmit="blitzLogout(event)">
        @csrf
        <button type="submit" class="btn btn-full blitz-btn blitz-logout-btn" style="
            padding: 15px 18px;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.10), rgba(220, 38, 38, 0.06));
            border: 1.5px solid rgba(239, 68, 68, 0.20);
            color: #f87171;
            font-weight: 800;
            font-size: 13.5px;
            letter-spacing: -0.01em;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            cursor: pointer;
            transition: all 0.05s ease;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 2px 12px rgba(239, 68, 68, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.04);
            touch-action: manipulation;
        ">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Sign Out Account</span>
        </button>
    </form>

    <div style="height: 20px;"></div>
</div>
@endsection
