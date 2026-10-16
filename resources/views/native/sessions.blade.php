@extends('native.layouts.mobile')

@section('title', 'Sessions')
@section('header_title', 'Verifications')

@section('content')
<div style="display: flex; flex-direction: column; gap: 6px;">

    <!-- Active Sessions -->
    @if($activeSessions->count() > 0)
        <div class="fade-in">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div class="section-label" style="margin-bottom: 0; display: flex; align-items: center; gap: 8px;">
                    <span class="status-dot status-dot-pulse" style="background: var(--primary);"></span>
                    Active Now
                </div>
                <span class="badge badge-primary">{{ $activeSessions->count() }}</span>
            </div>

            @foreach($activeSessions as $session)
                <div class="session-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 18px;">
                                @php
                                    $iso = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                                    $flag = '';
                                    foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                @endphp
                                {{ $flag }}
                            </span>
                            <span style="font-size: 14px; font-weight: 700; color: var(--text-white);">{{ $session->service?->name ?? 'SMS' }}</span>
                        </div>
                        <span class="badge badge-warning" style="font-size: 10px;">{{ strtoupper($session->status) }}</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="session-phone">{{ $session->phoneNumber?->phone_number }}</div>
                        <button onclick="copyToClipboard('{{ $session->phoneNumber?->phone_number }}', this)" class="btn btn-ghost btn-sm" style="padding: 6px 10px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        </button>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span style="font-size: 16px; font-weight: 800; color: #fbbf24; font-family: 'JetBrains Mono', monospace;" id="timer-{{ $session->id }}" data-expires="{{ $session->expires_at?->toIso8601String() }}">--:--</span>
                        </div>
                        <button onclick="cancelSession({{ $session->id }})" class="btn btn-ghost btn-sm" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.2); font-size: 11px;" id="btn-cancel-{{ $session->id }}">
                            Cancel & Refund
                        </button>
                    </div>

                    @if($session->verification_code)
                        <div class="otp-display">
                            <div>
                                <div style="font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px;">✓ Code Received</div>
                                <div class="otp-code">{{ $session->verification_code }}</div>
                            </div>
                            <button onclick="copyToClipboard('{{ $session->verification_code }}', this)" class="btn btn-primary btn-sm" style="margin-left: auto;">Copy</button>
                        </div>
                    @else
                        <div style="margin-top: 14px; display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-tertiary);">
                            <span class="status-dot status-dot-pulse" style="background: var(--primary);"></span>
                            Waiting for carrier SMS...
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- History -->
    <div class="fade-in" style="animation-delay: 0.1s;">
        <div class="section-label" style="{{ $activeSessions->count() > 0 ? 'margin-top: 8px;' : '' }}">
            History
        </div>

        @forelse($historySessions as $session)
            <div class="card" style="margin-bottom: 10px;">
                <div class="card-body" style="padding: 16px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 16px;">
                                @php
                                    $iso = strtoupper($session->phoneNumber?->country?->iso2 ?? 'US');
                                    $flag = '';
                                    foreach (str_split($iso) as $c) { $flag .= mb_chr(127397 + ord($c), 'UTF-8'); }
                                @endphp
                                {{ $flag }}
                            </span>
                            <span style="font-size: 14px; font-weight: 700; color: var(--text-white);">{{ $session->service?->name ?? 'SMS' }}</span>
                        </div>
                        @if($session->status === 'completed')
                            <span class="badge badge-success" style="font-size: 10px;">Completed</span>
                        @elseif($session->status === 'cancelled')
                            <span class="badge badge-warning" style="font-size: 10px;">Cancelled</span>
                        @elseif($session->status === 'expired')
                            <span class="badge badge-danger" style="font-size: 10px;">Expired</span>
                        @else
                            <span class="badge badge-neutral" style="font-size: 10px;">{{ ucfirst($session->status) }}</span>
                        @endif
                    </div>

                    <div style="font-size: 15px; font-weight: 700; color: #6B7A99; font-family: 'JetBrains Mono', monospace;">
                        {{ $session->phoneNumber?->phone_number }}
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px;">
                        @if($session->verification_code)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 11px; color: var(--text-tertiary);">OTP:</span>
                                <span class="font-mono" style="font-size: 16px; font-weight: 800; color: #7C3AED;">{{ $session->verification_code }}</span>
                            </div>
                        @else
                            <span style="font-size: 12px; color: var(--text-tertiary);">No code received</span>
                        @endif
                        <span style="font-size: 11px; color: var(--text-tertiary);">{{ $session->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div class="empty-state-title">No history yet</div>
                    <div class="empty-state-text">Your completed, cancelled, and expired verifications will appear here.</div>
                </div>
            </div>
        @endforelse

        @if($historySessions->hasPages())
            <div style="display: flex; justify-content: center; gap: 8px; margin-top: 16px;">
                @if($historySessions->onFirstPage())
                    <span class="btn btn-ghost btn-sm" style="opacity: 0.4;">← Prev</span>
                @else
                    <a href="{{ $historySessions->previousPageUrl() }}" class="btn btn-ghost btn-sm">← Prev</a>
                @endif
                <span style="font-size: 12px; color: var(--text-tertiary); display: flex; align-items: center;">
                    {{ $historySessions->currentPage() }} / {{ $historySessions->lastPage() }}
                </span>
                @if($historySessions->hasMorePages())
                    <a href="{{ $historySessions->nextPageUrl() }}" class="btn btn-ghost btn-sm">Next →</a>
                @else
                    <span class="btn btn-ghost btn-sm" style="opacity: 0.4;">Next →</span>
                @endif
            </div>
        @endif
    </div>

    <div style="height: 20px;"></div>
</div>
@endsection

@section('scripts')
<script>
    // Countdown timers
    document.querySelectorAll('[id^="timer-"]').forEach(el => {
        const expires = el.dataset.expires;
        if (!expires) return;
        const expiresAt = new Date(expires);
        function tick() {
            const now = new Date();
            const diff = Math.max(0, expiresAt - now);
            const mins = Math.floor(diff / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            el.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            if (diff <= 0) { el.textContent = '00:00'; el.style.color = '#f87171'; return; }
            requestAnimationFrame(() => setTimeout(tick, 250));
        }
        tick();
    });

    async function cancelSession(sessionId) {
        if (!confirm('Cancel this session and refund?')) return;
        const btn = document.getElementById('btn-cancel-' + sessionId);
        if (btn) { btn.disabled = true; btn.textContent = 'Cancelling...'; }
        try {
            const res = await fetch('/api/v1/verifications/' + sessionId + '/cancel', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            if (res.ok) location.reload();
            else if (btn) { btn.disabled = false; btn.textContent = 'Cancel & Refund'; }
        } catch(e) {
            if (btn) { btn.disabled = false; btn.textContent = 'Cancel & Refund'; }
        }
    }
</script>
@endsection
