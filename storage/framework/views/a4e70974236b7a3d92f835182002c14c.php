<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'wallet' => null,
    'countries' => collect(),
    'services' => collect(),
    'activeSessions' => collect(),
    'isModal' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'wallet' => null,
    'countries' => collect(),
    'services' => collect(),
    'activeSessions' => collect(),
    'isModal' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $walletSymbol = $wallet->currency->symbol ?? '₦';
    $walletBalance = floatval($wallet->available_balance ?? 0);
    $walletCurrency = $wallet->currency->code ?? 'NGN';
?>


<style>
    /* Main Vertical Card */
    /* Main Vertical Card */
    .pv-container {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        background: #FFFFFF;
        border: 1px solid rgba(15, 42, 90, 0.08);
        border-radius: 24px;
        padding: 24px 22px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        box-shadow: 0 4px 20px rgba(15, 42, 90, 0.06);
        color: #0F2A5A;
        box-sizing: border-box;
        position: relative;
    }

    .pv-announcement-ticker {
        background: #0F2A5A;
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 2px;
    }

    .pv-label {
        font-size: 15px;
        font-weight: 700;
        color: #0F2A5A;
        margin-bottom: 10px;
        display: block;
        letter-spacing: -0.01em;
    }

    .pv-sublabel {
        font-size: 11px;
        font-weight: 700;
        color: #6B7A99;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    /* Pill Groups (PVA-Style rounded-full) */
    .pv-pill-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .pv-pill {
        border-radius: 9999px;
        padding: 8px 18px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        user-select: none;
        background: #FFFFFF;
        color: #1E293B;
        border: 1.5px solid #E2E8F0;
    }

    .pv-pill:hover {
        border-color: #0F2A5A;
        color: #0F2A5A;
    }

    .pv-pill.active {
        background: #0F2A5A;
        border-color: #0F2A5A;
        color: #FFFFFF;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(15, 42, 90, 0.2);
    }

    /* Duration Pills */
    .pv-dur-pill {
        border-radius: 9999px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        background: #FFFFFF;
        color: #1E293B;
        border: 1.5px solid #E2E8F0;
    }

    .pv-dur-pill:hover {
        border-color: #0F2A5A;
        color: #0F2A5A;
    }

    .pv-dur-pill.active {
        background: #0F2A5A;
        border-color: #0F2A5A;
        color: #FFFFFF;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(15, 42, 90, 0.2);
    }

    /* All Services Inline Dashed Pill */
    .pv-allservices-pill {
        border-radius: 9999px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        user-select: none;
        border: 1.5px dashed #3B82F6;
        background: #F8FAFC;
        color: #1D4ED8;
    }

    .pv-allservices-pill:hover {
        background: #EFF6FF;
        border-color: #1D4ED8;
    }

    .pv-allservices-pill.active {
        background: #0F2A5A;
        border-style: solid;
        border-color: #0F2A5A;
        color: #FFFFFF;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(15, 42, 90, 0.25);
    }

    .pv-allservices-pill.active svg {
        stroke: #FFFFFF !important;
        fill: #FFFFFF !important;
    }

    .pv-allservices-pill.active .pv-best-value-pill-badge {
        background: #2E8B3F;
        color: #FFFFFF;
    }

    .pv-best-value-pill-badge {
        font-size: 9.5px;
        font-weight: 800;
        background: #2563EB;
        color: #FFFFFF;
        padding: 2px 7px;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-flex;
        align-items: center;
    }

    .pv-new-badge {
        font-size: 9.5px;
        font-weight: 800;
        background: #D97706;
        color: #ffffff;
        padding: 2px 7px;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-left: 6px;
        display: inline-block;
        vertical-align: middle;
        line-height: 1.1;
    }

    /* ═══════════════════════════════════════════════════════════════════
       DEDICATED ALL SERVICES LOADOUT CARD (PVADEALS LIGHT THEME)
    ═══════════════════════════════════════════════════════════════════ */
    .pv-as-dedicated-box {
        background: #FFFFFF;
        border: 1.5px solid rgba(15, 42, 90, 0.12);
        border-radius: 18px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 14px;
        box-shadow: 0 4px 16px rgba(15, 42, 90, 0.04);
        animation: pvFadeIn 0.25s ease;
        box-sizing: border-box;
    }

    .pv-as-top-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pv-as-tag {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: #0F2A5A;
        text-transform: uppercase;
    }

    .pv-as-carrier-badge {
        background: #EEF1F8;
        color: #0F2A5A;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .pv-tmobile-logo {
        background: #E20074;
        color: #ffffff;
        width: 16px;
        height: 16px;
        border-radius: 3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 900;
        line-height: 1;
    }

    .pv-as-renewal-badge {
        background: rgba(46, 139, 63, 0.12);
        border: 1px solid rgba(46, 139, 63, 0.25);
        color: #2E8B3F;
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .pv-as-price-row {
        display: flex;
        align-items: baseline;
        gap: 6px;
        flex-wrap: wrap;
    }

    .pv-as-price {
        font-size: 30px;
        font-weight: 900;
        color: #0F2A5A;
        letter-spacing: -0.02em;
        font-family: inherit;
    }

    .pv-as-usd-ref {
        font-size: 14px;
        font-weight: 600;
        color: #6B7A99;
    }

    .pv-as-duration-label {
        font-size: 16px;
        font-weight: 500;
        color: #6B7A99;
    }

    .pv-as-description {
        font-size: 13.5px;
        line-height: 1.55;
        color: #6B7A99;
        margin: 0;
    }

    /* Big Green Buy Button */
    .pv-as-buy-btn {
        width: 100%;
        height: 50px;
        border-radius: 14px;
        border: none;
        background: #2E8B3F;
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 14px;
        box-shadow: 0 4px 16px rgba(46, 139, 63, 0.3);
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .pv-as-buy-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 139, 63, 0.4);
    }

    .pv-as-buy-btn:active {
        transform: translateY(0);
    }

    /* ═══════════════════════════════════════════════════════════════════
       PVADEALS HORIZONTAL BOTTOM CONTROLS (Website | Location | CTA Button)
    ═══════════════════════════════════════════════════════════════════ */
    .pv-bottom-controls-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 16px;
        margin-top: 20px;
    }

    .pv-control-field {
        flex: 1 1 200px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .pv-field-label {
        font-size: 14px;
        font-weight: 700;
        color: #0F2A5A;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .pv-trigger-control {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        padding: 0 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.18s ease;
        color: #0F2A5A;
        box-sizing: border-box;
    }

    .pv-trigger-control:hover:not(.disabled) {
        border-color: #0F2A5A;
        background: #F8FAFC;
    }

    .pv-trigger-control.disabled {
        background: #F8FAFC;
        border-color: #E2E8F0;
        cursor: not-allowed;
    }

    .pv-trigger-left {
        display: flex;
        align-items: center;
        gap: 10px;
        overflow: hidden;
    }

    .pv-trigger-text {
        font-size: 14px;
        font-weight: 500;
        color: #64748B;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pv-btn-col {
        flex: 0 0 auto;
        min-width: 240px;
    }

    @media (max-width: 768px) {
        .pv-bottom-controls-wrap {
            flex-direction: column;
            align-items: stretch;
        }
        .pv-btn-col {
            min-width: 100%;
        }
    }

    .pv-main-cta-btn {
        height: 48px;
        border-radius: 12px;
        border: none;
        padding: 0 24px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        user-select: none;
        width: 100%;
        box-sizing: border-box;
    }

    .pv-main-cta-btn.waiting {
        background: #D1FAE5;
        color: #065F46;
        cursor: pointer;
    }

    .pv-main-cta-btn.waiting:hover {
        background: #BBF7D0;
    }

    .pv-main-cta-btn.ready {
        background: #16A34A;
        color: #FFFFFF;
        font-weight: 800;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        cursor: pointer;
    }

    .pv-main-cta-btn.ready:hover {
        background: #15803D;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(22, 163, 74, 0.45);
    }

    .pv-main-cta-btn.ready:active {
        transform: translateY(0);
    }

    /* Website & Location Trigger Buttons (Legacy compat) */
    .pv-trigger-btn {
        width: 100%;
        height: 50px;
        border-radius: 14px;
        background: #EEF1F8;
        border: 1.5px solid rgba(15, 42, 90, 0.1);
        padding: 0 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.18s ease;
        color: #0F2A5A;
        box-sizing: border-box;
    }

    .pv-trigger-btn:hover:not(:disabled) {
        border-color: #0F2A5A;
        background: #FFFFFF;
    }

    .pv-trigger-btn.disabled,
    .pv-trigger-btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        background: #F5F6FA;
        border-color: rgba(15, 42, 90, 0.06);
        color: #94A3B8;
    }

    /* Green / Emerald Order Summary Card */
    .pv-summary-card {
        background: #F0FDF4;
        border: 1.5px solid rgba(46, 139, 63, 0.25);
        border-radius: 16px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        animation: pvFadeIn 0.25s ease;
    }

    @keyframes pvFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pv-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }

    .pv-summary-label {
        color: #6B7A99;
        font-weight: 500;
    }

    .pv-summary-val {
        color: #0F2A5A;
        font-weight: 700;
    }

    /* Primary Purchase Button */
    .pv-buy-btn {
        width: 100%;
        height: 52px;
        border-radius: 14px;
        border: none;
        cursor: pointer;
        font-size: 15.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #2E8B3F;
        color: #FFFFFF;
        box-shadow: 0 4px 16px rgba(46, 139, 63, 0.3);
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .pv-buy-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 139, 63, 0.4);
    }

    .pv-buy-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .pv-buy-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
        background: #EEF1F8;
        color: #94A3B8;
    }

    /* Guarantee tag */
    .pv-guarantee {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 11.5px;
        color: #6B7A99;
        font-weight: 500;
        text-align: center;
        margin-top: -6px;
    }

    /* ═══════════════════════════════════════════════════════════════════
       STATE-OF-THE-ART BOTTOM SHEET MODAL ("Select Website") - BLITZ GPU ACCELERATED
    ═══════════════════════════════════════════════════════════════════ */
    .pv-sheet-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 42, 90, 0.55);
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.16s ease, visibility 0.16s ease;
        will-change: opacity;
    }

    .pv-sheet-backdrop.open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .pv-sheet-container {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        max-width: 620px;
        margin: 0 auto;
        max-height: 82vh;
        height: 620px;
        background: #FFFFFF;
        border-top-left-radius: 28px;
        border-top-right-radius: 28px;
        border: 1px solid rgba(15, 42, 90, 0.12);
        border-bottom: none;
        box-shadow: 0 -10px 40px rgba(15, 42, 90, 0.15);
        z-index: 10001;
        display: flex;
        flex-direction: column;
        transform: translate3d(0, 105%, 0);
        transition: transform 0.16s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
        overflow: hidden;
        color: #0F2A5A;
    }

    .pv-sheet-container.open {
        transform: translate3d(0, 0, 0);
    }

    .pv-sheet-handle-wrap {
        padding: 10px 0 6px;
        display: flex;
        justify-content: center;
        cursor: grab;
    }

    .pv-sheet-handle {
        width: 42px;
        height: 5px;
        border-radius: 9999px;
        background: rgba(15, 42, 90, 0.2);
    }

    .pv-sheet-header {
        padding: 6px 20px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .pv-sheet-title {
        font-size: 19px;
        font-weight: 800;
        color: #0F2A5A;
        letter-spacing: -0.02em;
    }

    .pv-sheet-done-btn {
        background: transparent;
        border: none;
        color: #2E8B3F;
        font-size: 15.5px;
        font-weight: 700;
        cursor: pointer;
        padding: 4px 10px;
        border-radius: 8px;
        transition: background 0.15s ease;
    }

    .pv-sheet-done-btn:hover {
        background: rgba(46, 139, 63, 0.12);
    }

    .pv-sheet-cancel-btn {
        background: transparent;
        border: none;
        color: #6B7A99;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        padding: 4px 10px;
        border-radius: 8px;
        transition: all 0.15s ease;
    }

    .pv-sheet-cancel-btn:hover, .pv-sheet-cancel-btn:active {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }

    .pv-sheet-search-wrap {
        padding: 0 18px 12px;
        position: relative;
        flex-shrink: 0;
    }

    .pv-sheet-search-input {
        width: 100%;
        height: 48px;
        background: #EEF1F8;
        border: 1.5px solid rgba(15, 42, 90, 0.12);
        border-radius: 14px;
        padding: 0 40px 0 44px;
        color: #0F2A5A;
        font-size: 14px;
        font-weight: 600;
        outline: none;
        box-sizing: border-box;
        font-family: inherit;
        transition: border-color 0.18s ease;
    }

    .pv-sheet-search-input:focus {
        border-color: #0F2A5A;
        background: #FFFFFF;
    }

    .pv-sheet-search-input::placeholder {
        color: #6B7A99;
    }

    .pv-sheet-chips {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding: 0 18px 12px;
        scrollbar-width: none;
        flex-shrink: 0;
    }
    .pv-sheet-chips::-webkit-scrollbar { display: none; }

    .pv-sheet-chip {
        background: #EEF1F8;
        border: 1px solid rgba(15, 42, 90, 0.08);
        color: #0F2A5A;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .pv-sheet-chip:hover {
        background: #0F2A5A;
        border-color: #0F2A5A;
        color: #FFFFFF;
    }

    /* Scrollable Sheet List */
    .pv-sheet-list {
        flex: 1;
        overflow-y: auto;
        padding: 4px 14px 20px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        scrollbar-width: thin;
        scrollbar-color: rgba(15, 42, 90, 0.2) transparent;
    }

    .pv-sheet-list::-webkit-scrollbar { width: 4px; }
    .pv-sheet-list::-webkit-scrollbar-thumb { background: rgba(15, 42, 90, 0.2); border-radius: 4px; }

    /* Single Website Item matching PVADeals */
    .pv-service-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        border-radius: 14px;
        background: #FFFFFF;
        border: 1px solid rgba(15, 42, 90, 0.08);
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
        box-shadow: 0 1px 3px rgba(15, 42, 90, 0.02);
    }

    .pv-service-row:hover {
        background: #EEF1F8;
        border-color: rgba(15, 42, 90, 0.18);
    }

    .pv-service-row.selected {
        background: rgba(46, 139, 63, 0.08);
        border-color: #2E8B3F;
    }

    .pv-service-left {
        display: flex;
        align-items: center;
        gap: 12px;
        overflow: hidden;
    }

    .pv-service-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #EEF1F8;
        border: 1px solid rgba(15, 42, 90, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
        font-weight: 800;
        color: #0F2A5A;
        box-shadow: 0 2px 6px rgba(15, 42, 90, 0.04);
    }

    .pv-service-title-wrap {
        display: flex;
        flex-direction: column;
        gap: 2px;
        overflow: hidden;
    }

    .pv-service-name-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pv-service-name {
        font-size: 14.5px;
        font-weight: 700;
        color: #0F2A5A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* PVA Stock Pill Badge (e.g. 4942) */
    .pv-stock-badge {
        font-size: 11px;
        font-weight: 700;
        background: rgba(46, 139, 63, 0.12);
        color: #2E8B3F;
        padding: 2px 8px;
        border-radius: 6px;
        letter-spacing: 0.02em;
    }

    .pv-stock-badge.empty {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }

    .pv-service-price-sub {
        font-size: 12px;
        font-weight: 700;
        color: #2E8B3F;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Checkbox on the right */
    .pv-checkbox {
        width: 22px;
        height: 22px;
        border-radius: 7px;
        border: 2px solid rgba(15, 42, 90, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s ease;
        background: transparent;
    }

    .pv-service-row.selected .pv-checkbox {
        background: #2E8B3F;
        border-color: #2E8B3F;
    }

    /* ── ACTIVE VERIFICATION STAGE (STAGE 2) ── */
    #pv-active-stage {
        animation: pvFadeIn 0.3s ease;
    }

    .pv-active-hero-card {
        background: #FFFFFF;
        border: 1.5px solid rgba(15, 42, 90, 0.12);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(15, 42, 90, 0.05);
    }

    .pv-phone-display-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #EEF1F8;
        border: 1px solid rgba(15, 42, 90, 0.12);
        border-radius: 14px;
        padding: 12px 16px;
        margin-top: 6px;
        gap: 10px;
    }

    .pv-phone-number-text {
        font-size: 21px;
        font-weight: 800;
        color: #0F2A5A;
        letter-spacing: 0.03em;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    .pv-copy-btn {
        background: #0F2A5A;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(15, 42, 90, 0.2);
        transition: transform 0.15s ease;
        flex-shrink: 0;
    }

    .pv-copy-btn:active {
        transform: scale(0.95);
    }

    .pv-sms-monitor-box {
        background: #FFFFFF;
        border: 1.5px dashed rgba(15, 42, 90, 0.2);
        border-radius: 18px;
        padding: 22px 18px;
        text-align: center;
    }

    .pv-pulse-ring-wrap {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(46, 139, 63, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        position: relative;
    }

    .pv-pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid #2E8B3F;
        animation: pvPulseRing 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }

    @keyframes pvPulseRing {
        0% { transform: scale(0.85); opacity: 0.9; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    .pv-sms-received-box {
        background: #F0FDF4;
        border: 1.5px solid #2E8B3F;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 4px 16px rgba(46, 139, 63, 0.15);
        animation: pvFadeIn 0.35s ease;
    }

    .pv-otp-hero-display {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #FFFFFF;
        border: 1px solid rgba(46, 139, 63, 0.3);
        border-radius: 14px;
        padding: 12px 16px;
        gap: 12px;
    }

    .pv-otp-code-text {
        font-size: 28px;
        font-weight: 900;
        letter-spacing: 0.12em;
        color: #2E8B3F;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    .pv-copy-otp-btn {
        background: #2E8B3F;
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        padding: 9px 15px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(46, 139, 63, 0.3);
        transition: transform 0.15s ease;
        flex-shrink: 0;
    }

    .pv-copy-otp-btn:active {
        transform: scale(0.95);
    }

    .pv-cancel-btn {
        width: 100%;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #ef4444;
        border-radius: 14px;
        padding: 12px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .pv-cancel-btn:hover {
        background: rgba(239, 68, 68, 0.16);
    }

    .pv-new-number-btn {
        width: 100%;
        background: #EEF1F8;
        border: 1px solid rgba(15, 42, 90, 0.12);
        color: #0F2A5A;
        border-radius: 14px;
        padding: 12px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .pv-new-number-btn:hover {
        background: rgba(15, 42, 90, 0.08);
    }

    @keyframes pvSpin { to { transform: rotate(360deg); } }

    /* ═══════════════════════════════════════════════════════════════════
       MOBILE & NATIVE RESPONSIVE POLISH + STR PROFESSIONAL LAYOUT
    ═══════════════════════════════════════════════════════════════════ */
    @media (max-width: 640px) {
        .pv-container {
            padding: 16px 14px !important;
            border-radius: 20px !important;
            gap: 16px !important;
        }
    }

    /* Segmented Duration Switcher */
    .pv-dur-switcher {
        display: flex;
        background: #F1F5F9;
        padding: 4px;
        border-radius: 14px;
        gap: 4px;
        border: 1px solid rgba(15, 23, 42, 0.06);
    }
    .pv-dur-switch-btn {
        flex: 1;
        height: 42px;
        border-radius: 10px;
        border: none;
        background: transparent;
        color: #64748B;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        user-select: none;
    }
    .pv-dur-switch-btn.active {
        background: #FFFFFF;
        color: #0F2A5A;
        font-weight: 800;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.08);
    }
    .pv-dur-subbadge {
        font-size: 9px;
        font-weight: 800;
        background: rgba(34, 197, 94, 0.15);
        color: #15803D;
        padding: 2px 6px;
        border-radius: 6px;
        letter-spacing: 0.03em;
    }

    /* STR Highlight Card Banner */
    .pv-str-card-banner {
        background: #F0FDF4;
        border: 1.5px solid rgba(34, 197, 94, 0.3);
        border-radius: 14px;
        padding: 12px 14px;
        margin-top: 10px;
        animation: pvFadeIn 0.2s ease;
    }

    /* Popular Services Quick-Picks Grid */
    .pv-popular-apps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    @media (max-width: 600px) {
        .pv-popular-apps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .pv-popular-app-card {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        padding: 10px 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-align: center;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        user-select: none;
    }
    .pv-popular-app-card:hover {
        border-color: #0F2A5A;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 42, 90, 0.08);
    }
    .pv-popular-app-card.active {
        border-color: #16A34A;
        background: #F0FDF4;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.18);
    }
    .pv-popular-app-card.active::after {
        content: "✓";
        position: absolute;
        top: 6px;
        right: 8px;
        font-size: 11px;
        font-weight: 900;
        color: #16A34A;
    }
    .pv-popular-app-name {
        font-size: 12px;
        font-weight: 700;
        color: #0F2A5A;
        line-height: 1.2;
    }
    .pv-popular-app-price {
        font-size: 11px;
        font-weight: 800;
        color: #16A34A;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Dark Mode Support for New Elements */
    [data-theme="dark"] .pv-dur-switcher {
        background: #151F36 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .pv-dur-switch-btn {
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .pv-dur-switch-btn.active {
        background: #0B0F19 !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4) !important;
    }
    [data-theme="dark"] .pv-str-card-banner {
        background: rgba(34, 197, 94, 0.08) !important;
        border-color: rgba(34, 197, 94, 0.25) !important;
    }
    [data-theme="dark"] .pv-str-card-banner div {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .pv-popular-app-card {
        background: #0B0F19 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .pv-popular-app-card:hover {
        border-color: #38BDF8 !important;
    }
    [data-theme="dark"] .pv-popular-app-card.active {
        background: rgba(22, 163, 74, 0.12) !important;
        border-color: #16A34A !important;
    }
    [data-theme="dark"] .pv-popular-app-name {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .pv-trigger-control {
        background: #0B0F19 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .pv-summary-card {
        background: #0B0F19 !important;
        border-color: rgba(34, 197, 94, 0.3) !important;
    }
    [data-theme="dark"] .pv-summary-val {
        color: #F8FAFC !important;
    }
</style>

<div class="pv-container" id="pv-container-root">

    
    <div id="pv-purchase-stage">

    
    <div class="pv-announcement-ticker" id="pv-top-ticker">
        <span>⚡ Instant SMS Delivery · 100% Auto-Refund If No Code Received · 🔒 Physical Non-VoIP SIMs</span>
    </div>

    
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <span class="pv-label" style="margin-bottom: 0;">Select country</span>
            <span style="font-size: 11px; font-weight: 700; color: #16A34A;">● Physical Carrier Lines</span>
        </div>
        <div class="pv-pill-group" id="pv-country-group" style="overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px;">
            <button type="button" class="pv-pill blitz-btn active" data-country="USA" onclick="event.stopPropagation(); pvSelectCountry('USA', this)">
                <span style="font-size: 15px; margin-right: 6px;">🇺🇸</span> United States
            </button>
            <button type="button" class="pv-pill blitz-btn" data-country="UK" onclick="event.stopPropagation(); pvSelectCountry('UK', this)">
                <span style="font-size: 15px; margin-right: 6px;">🇬🇧</span> United Kingdom
            </button>
            <button type="button" class="pv-pill blitz-btn" data-country="HK" onclick="event.stopPropagation(); pvSelectCountry('HK', this)">
                <span style="font-size: 15px; margin-right: 6px;">🇭🇰</span> Hong Kong <span class="pv-new-badge">LTR</span>
            </button>
            <button type="button" class="pv-pill blitz-btn" data-country="ID" onclick="event.stopPropagation(); pvSelectCountry('ID', this)">
                <span style="font-size: 15px; margin-right: 6px;">🇮🇩</span> Indonesia <span class="pv-new-badge">LTR</span>
            </button>
            <button type="button" class="pv-pill blitz-btn" data-country="AU" onclick="event.stopPropagation(); pvSelectCountry('AU', this)">
                <span style="font-size: 15px; margin-right: 6px;">🇦🇺</span> Australia <span class="pv-new-badge">LTR</span>
            </button>
        </div>
    </div>

    
    <div id="pv-standard-duration-block">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <span class="pv-label" style="margin-bottom: 0;">Rental type</span>
            <span id="pv-duration-badge-mode" style="font-size: 11px; font-weight: 800; background: rgba(34, 197, 94, 0.12); color: #15803d; border: 1px solid rgba(34, 197, 94, 0.25); padding: 2px 8px; border-radius: 9999px;">
                ⚡ 20-Min Disposable Line
            </span>
        </div>

        
        <div class="pv-dur-switcher" id="pv-duration-switcher">
            <button type="button" class="pv-dur-switch-btn active blitz-btn" id="pv-switch-btn-str" onclick="pvSwitchRentalMode('STR')">
                <span style="font-size: 14px;">⚡</span>
                <span>Short-Term (20 Mins)</span>
                <span class="pv-dur-subbadge">DISPOSABLE</span>
            </button>
            <button type="button" class="pv-dur-switch-btn blitz-btn" id="pv-switch-btn-ltr" onclick="pvSwitchRentalMode('LTR')">
                <span style="font-size: 14px;">📅</span>
                <span>Long-Term (Multi-Day)</span>
            </button>
        </div>

        
        <div id="pv-str-active-banner" class="pv-str-card-banner">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 32px; height: 32px; border-radius: 10px; background: rgba(34, 197, 94, 0.15); color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        ⚡
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 800; color: #0F2A5A;">20-Minute Instant Disposable Line</div>
                        <div style="font-size: 11.5px; color: #64748B;">Physical carrier SIM · Auto-refunds if SMS is not received</div>
                    </div>
                </div>
                <button type="button" class="pv-dur-pill blitz-btn active" data-type="STR" data-duration="20" style="display: none;" onclick="pvSelectDuration('STR', 20, this)">
                    20 minutes
                </button>
            </div>
        </div>

        
        <div id="pv-ltr-duration-section" style="display: none; margin-top: 10px; animation: pvFadeIn 0.2s ease;">
            <span class="pv-sublabel" id="pv-ltr-sublabel">CHOOSE RENTAL PERIOD</span>
            <div class="pv-pill-group" id="pv-ltr-pill-group">
                <button type="button" class="pv-dur-pill blitz-btn" data-type="LTR" data-duration="3" onclick="pvSelectDuration('LTR', 3, this)">3 days</button>
                <button type="button" class="pv-dur-pill blitz-btn" data-type="LTR" data-duration="7" onclick="pvSelectDuration('LTR', 7, this)">7 days</button>
                <button type="button" class="pv-dur-pill blitz-btn" data-type="LTR" data-duration="14" onclick="pvSelectDuration('LTR', 14, this)">14 days</button>
                <button type="button" class="pv-dur-pill blitz-btn" data-type="LTR" data-duration="30" onclick="pvSelectDuration('LTR', 30, this)">30 days</button>
                <button type="button" class="pv-allservices-pill blitz-btn" id="pv-all-services-pill" onclick="pvToggleAllServices(event)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" id="pv-all-services-star">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span id="pv-all-services-title">All services</span>
                    <span class="pv-best-value-pill-badge">BEST VALUE</span>
                </button>
            </div>
        </div>
    </div>

    
    <div id="pv-website-location-fields" style="display: flex; flex-direction: column; gap: 14px; margin-top: 4px;">
        
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span class="pv-label" style="margin-bottom: 0;">Select service</span>
                <span style="font-size: 11.5px; font-weight: 700; color: #16A34A;">Fast One-Tap Pick</span>
            </div>
            <div class="pv-popular-apps-grid" id="pv-popular-apps-grid">
                
            </div>
        </div>

        
        <div>
            <span class="pv-sublabel" style="margin-bottom: 6px;">OR SEARCH FULL CATALOG</span>
            <button type="button" class="pv-trigger-control blitz-btn" id="pv-website-trigger" onpointerdown="pvOpenWebsiteSheet(event)" onclick="pvOpenWebsiteSheet(event)">
                <div class="pv-trigger-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;" id="pv-website-icon">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <span id="pv-website-trigger-text" class="pv-trigger-text">
                        Browse all 1,600+ supported websites...
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 11px; font-weight: 700; color: #7C3AED; background: rgba(124, 58, 237, 0.08); padding: 2px 7px; border-radius: 6px;">1,680 apps</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" class="pv-chevron">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </button>
            <span id="pv-selected-website-status" style="display: none;">None selected</span>
        </div>

        
        <div id="pv-location-field-container">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                <label class="pv-field-label" style="margin: 0;">Location / Area code <span style="font-weight: 500; color: #64748B; font-size: 12px;">(optional)</span></label>
                <span style="font-size: 11px; color: #16A34A; font-weight: 700;">● National carrier pool</span>
            </div>
            <div id="pv-location-field-wrap">
                <div class="pv-trigger-control disabled" id="pv-location-disabled-btn">
                    <div class="pv-trigger-left">
                        <span style="color: #94A3B8; font-size: 13.5px; font-weight: 500;">Select service first to choose location</span>
                    </div>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2.5" class="pv-chevron">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>

                <button type="button" class="pv-trigger-control blitz-btn" id="pv-location-trigger-btn" style="display: none;" onpointerdown="pvOpenLocationSheet(event)" onclick="pvOpenLocationSheet(event)">
                    <div class="pv-trigger-left">
                        <span id="pv-location-trigger-text" class="pv-trigger-text" style="color: #0F2A5A; font-weight: 600;">
                            Any Location (Recommended)
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                        <span id="pv-location-trigger-badge" style="display: none; background: #EEF1F8; color: #0F2A5A; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; font-family: monospace;"></span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5" class="pv-chevron">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </div>
                </button>
                <input type="hidden" id="pv-area-code-input" value="">
            </div>
        </div>

        
        <div id="pv-order-summary-card" class="pv-summary-card" style="display: none; margin-top: 4px;">
            <div class="pv-summary-row">
                <span class="pv-summary-label">Selected Service</span>
                <span class="pv-summary-val" id="pv-summary-service">—</span>
            </div>
            <div class="pv-summary-row">
                <span class="pv-summary-label">Country</span>
                <span class="pv-summary-val" id="pv-summary-country">United States</span>
            </div>
            <div class="pv-summary-row" id="pv-summary-location-row">
                <span class="pv-summary-label">Location / Area</span>
                <span class="pv-summary-val" id="pv-summary-location">Any Location (Optimal)</span>
            </div>
            <div class="pv-summary-row">
                <span class="pv-summary-label">Rental Duration</span>
                <span class="pv-summary-val" id="pv-summary-duration">20 minutes (Short-Term)</span>
            </div>
            <div class="pv-summary-row">
                <span class="pv-summary-label">Stock Status</span>
                <span class="pv-summary-val" id="pv-summary-stock" style="color: #2E8B3F;">● In Stock</span>
            </div>

            <div style="border-top: 1px solid rgba(46, 139, 63, 0.2); padding-top: 10px; margin-top: 4px;" class="pv-summary-row">
                <div>
                    <span class="pv-summary-label" style="font-size: 14px; font-weight: 700; color: #0F2A5A;">Total Amount</span>
                    <div id="pv-summary-usd-ref" style="font-size: 11px; color: #6B7A99;">Instant SMS Delivery</div>
                </div>
                <div style="text-align: right;">
                    <span id="pv-summary-price-naira" style="font-size: 22px; font-weight: 800; color: #2E8B3F; font-family: 'JetBrains Mono', monospace;">₦0</span>
                </div>
            </div>
        </div>

        
        <div style="margin-top: 4px;">
            <button type="button" class="pv-main-cta-btn waiting blitz-btn" id="pv-main-cta" onpointerdown="pvHandleCtaClick(event)" onclick="pvHandleCtaClick(event)">
                <span id="pv-main-cta-text">⚡ Choose a service to start</span>
            </button>
        </div>

        
        <div class="pv-guarantee" style="margin-top: 2px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2E8B3F" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
            <span>100% Auto-Refund if SMS verification code is not received</span>
        </div>
    </div>

    
    <button type="button" id="pv-buy-button" style="display: none;" disabled><span id="pv-buy-btn-text"></span></button>
    <button type="button" id="pv-as-buy-btn" style="display: none;"><span id="pv-as-btn-text"></span></button>
    <div id="pv-all-services-card" style="display: none;"></div>

    
    <div id="pv-allservices-dedicated-wrap" style="display: none; animation: pvFadeIn 0.25s ease; margin-top: 14px;">
        <div class="pv-as-dedicated-box">
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <span style="font-size: 12px; font-weight: 700; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.04em;">Country</span>
                <div class="pv-pill-group" id="pv-as-country-pills" style="margin-bottom: 4px; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 4px;">
                    <button type="button" class="pv-pill blitz-btn active" data-as-country="US" onclick="pvSelectCountry('USA')">
                        <span style="font-size: 14px; margin-right: 5px;">🇺🇸</span> United States
                    </button>
                    <button type="button" class="pv-pill blitz-btn" data-as-country="UK" onclick="pvSelectCountry('UK')">
                        <span style="font-size: 14px; margin-right: 5px;">🇬🇧</span> United Kingdom
                    </button>
                    <button type="button" class="pv-pill blitz-btn" data-as-country="HK" onclick="pvSelectCountry('HK')">
                        <span style="font-size: 14px; margin-right: 5px;">🇭🇰</span> Hong Kong
                    </button>
                    <button type="button" class="pv-pill blitz-btn" data-as-country="ID" onclick="pvSelectCountry('ID')">
                        <span style="font-size: 14px; margin-right: 5px;">🇮🇩</span> Indonesia
                    </button>
                    <button type="button" class="pv-pill blitz-btn" data-as-country="AU" onclick="pvSelectCountry('AU')">
                        <span style="font-size: 14px; margin-right: 5px;">🇦🇺</span> Australia
                    </button>
                </div>
            </div>

            
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <span style="font-size: 12px; font-weight: 700; color: #6B7A99; text-transform: uppercase; letter-spacing: 0.04em;">Rental Plan</span>
                <div class="pv-pill-group" id="pv-as-duration-pills" style="margin-bottom: 4px;">
                    
                </div>
            </div>

            
            <div class="pv-as-top-row">
                <span class="pv-as-tag">ALL SERVICES</span>

                
                <div class="pv-as-carrier-badge" id="pv-as-carrier-badge">
                    <span class="pv-tmobile-logo" id="pv-as-carrier-logo">T</span>
                    <span id="pv-as-carrier-name">T-Mobile</span>
                </div>

                
                <div class="pv-as-renewal-badge" id="pv-as-renewal-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink: 0;"><path d="m12 1 2.5 7.5L22 11l-7.5 2.5L12 21l-2.5-7.5L2 11l7.5-2.5z"/></svg>
                    <span>Auto-renewal</span>
                </div>
            </div>

            
            <div id="pv-as-lockout-notice" style="display: none; font-size: 11.5px; color: #d97706; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 6px 10px;">
                ⚠️ Auto-renewal modification is locked within 4 days of expiry for this region.
            </div>

            
            <div class="pv-as-price-row">
                <span class="pv-as-price" id="pv-as-price-val"><?php echo e(($wallet->currency->symbol ?? '₦') === '$' ? '$12.99' : '₦21,450'); ?></span>
                <span class="pv-as-usd-ref" id="pv-as-usd-ref"><?php echo e(($wallet->currency->symbol ?? '₦') === '$' ? '' : '(≈ $12.99 USD)'); ?></span>
                <span class="pv-as-duration-label" id="pv-as-duration-suffix">/ 28 days</span>
            </div>

            
            <p class="pv-as-description" id="pv-as-description-text">
                One number that works on any website in the United States — no website to choose, and unlimited use for the full 28 days.
            </p>
        </div>
    </div>

        <?php if($isModal): ?>
            <button type="button" class="blitz-btn" onpointerdown="closeBuyModal()" onclick="closeBuyModal()" style="width: 100%; background: transparent; color: #6B7A99; border: 1px solid rgba(15, 42, 90, 0.12); border-radius: 14px; padding: 12px; font-size: 13px; font-weight: 600; cursor: pointer; margin-top: 10px;">
                Cancel
            </button>
        <?php endif; ?>
    </div>

    
    <div id="pv-active-stage" style="display: none;">
        
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <button type="button" onclick="pvReturnToPurchaseStage()" style="background: #EEF1F8; border: 1px solid rgba(15, 42, 90, 0.12); color: #0F2A5A; border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                <span>Buy another number</span>
            </button>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(46, 139, 63, 0.12); border: 1px solid rgba(46, 139, 63, 0.3); padding: 4px 10px; border-radius: 9999px;">
                <span class="pv-pulse-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #2E8B3F; display: inline-block;"></span>
                <span style="font-size: 11px; font-weight: 700; color: #2E8B3F; text-transform: uppercase; letter-spacing: 0.05em;">Live Session</span>
            </div>
        </div>

        
        <div class="pv-active-hero-card">
            
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span id="pv-active-country-flag" style="font-size: 22px;">🇺🇸</span>
                    <div>
                        <div id="pv-active-service-title" style="font-size: 15px; font-weight: 800; color: #0F2A5A;">All Services (T-Mobile)</div>
                        <div id="pv-active-country-name" style="font-size: 11.5px; color: #6B7A99; font-weight: 500;">United States · Carrier SIM</div>
                    </div>
                </div>
                <span id="pv-active-duration-badge" style="background: rgba(46, 139, 63, 0.12); border: 1px solid rgba(46, 139, 63, 0.25); color: #2E8B3F; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700;">
                    20 mins
                </span>
            </div>

            
            <div class="pv-phone-display-wrap">
                <div>
                    <div style="font-size: 10px; font-weight: 700; color: #2E8B3F; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 3px;">Allocated Phone Number</div>
                    <div id="pv-active-phone-number" class="pv-phone-number-text">+1 (205) 492-8194</div>
                </div>
                <button type="button" class="pv-copy-btn" id="pv-copy-phone-btn" onclick="pvCopyActivePhone()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    <span id="pv-copy-phone-text">Copy</span>
                </button>
            </div>

            
            <div style="margin-top: 14px; background: #EEF1F8; border-radius: 12px; padding: 10px 14px; border: 1px solid rgba(15, 42, 90, 0.08);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #6B7A99; font-weight: 500;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Time Remaining</span>
                    </div>
                    <div id="pv-active-timer" style="font-size: 15px; font-weight: 800; color: #0F2A5A; font-family: 'JetBrains Mono', monospace;">19:59</div>
                </div>
                <div style="width: 100%; height: 5px; background: rgba(15, 42, 90, 0.08); border-radius: 9999px; overflow: hidden;">
                    <div id="pv-active-timer-bar" style="width: 100%; height: 100%; background: linear-gradient(90deg, #2E8B3F, #0F2A5A); transition: width 1s linear;"></div>
                </div>
            </div>
        </div>

        
        <div style="margin-top: 14px;">
            
            <div id="pv-sms-waiting-box" class="pv-sms-monitor-box">
                <div class="pv-pulse-ring-wrap">
                    <div class="pv-pulse-ring"></div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2E8B3F" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div style="font-size: 14.5px; font-weight: 700; color: #0F2A5A; margin-bottom: 4px;">Waiting for Carrier SMS...</div>
                <p style="font-size: 12.5px; color: #6B7A99; line-height: 1.5; margin: 0 auto; max-width: 320px;">
                    Enter this number into <span id="pv-waiting-service-name" style="color: #2E8B3F; font-weight: 600;">the service</span>. The SMS verification code will arrive here automatically in seconds.
                </p>

                
                <button type="button" onclick="pvSimulateIncomingSms()" style="margin-top: 14px; background: rgba(46, 139, 63, 0.1); border: 1px dashed rgba(46, 139, 63, 0.35); color: #2E8B3F; border-radius: 10px; padding: 7px 14px; font-size: 11.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    <span>Simulate Incoming SMS</span>
                </button>
            </div>

            
            <div id="pv-sms-received-box" class="pv-sms-received-box" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <div style="display: inline-flex; align-items: center; gap: 6px; color: #2E8B3F; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Verification Code Received</span>
                    </div>
                    <span id="pv-sms-received-time" style="font-size: 11px; color: #6B7A99; font-weight: 500;">Just now</span>
                </div>

                <div class="pv-otp-hero-display">
                    <div id="pv-active-otp-val" class="pv-otp-code-text">849 201</div>
                    <button type="button" class="pv-copy-otp-btn" id="pv-copy-otp-btn" onclick="pvCopyActiveOtp()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        <span id="pv-copy-otp-text">Copy Code</span>
                    </button>
                </div>

                <div style="margin-top: 10px; background: #EEF1F8; border-radius: 10px; padding: 10px 12px; font-size: 12px; color: #0F2A5A; line-height: 1.4; border-left: 3px solid #2E8B3F;">
                    <span style="color: #6B7A99; font-size: 11px; display: block; margin-bottom: 2px;">Full Message:</span>
                    <span id="pv-sms-message-body">Your WhatsApp code is 849-201. Do not share this code with anyone.</span>
                </div>
            </div>
        </div>

        
        <div style="margin-top: 18px; display: flex; flex-direction: column; gap: 10px;">
            <button type="button" onclick="pvCancelActiveSession()" class="pv-cancel-btn" id="pv-active-cancel-btn">
                Cancel & Auto-Refund
            </button>
            <button type="button" onclick="pvReturnToPurchaseStage()" class="pv-new-number-btn">
                Get Another Number
            </button>
        </div>
    </div>

</div>


<div class="pv-sheet-backdrop" id="pv-sheet-backdrop" onclick="pvCloseWebsiteSheet()"></div>

<div class="pv-sheet-container" id="pv-bottom-sheet">
    
    <div class="pv-sheet-handle-wrap" onclick="pvCloseWebsiteSheet()">
        <div class="pv-sheet-handle"></div>
    </div>

    
    <div class="pv-sheet-header">
        <button type="button" class="pv-sheet-cancel-btn" onclick="pvCloseWebsiteSheet()">Cancel</button>
        <span class="pv-sheet-title">Select website</span>
        <button type="button" class="pv-sheet-done-btn" onclick="pvCloseWebsiteSheet()">Done</button>
    </div>

    
    <div class="pv-sheet-search-wrap">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" style="position: absolute; left: 32px; top: 50%; transform: translateY(-70%); pointer-events: none;">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
        </svg>
        <input type="text" class="pv-sheet-search-input" id="pv-sheet-search-input"
               placeholder="Search websites"
               oninput="pvFilterWebsites(this.value)">
    </div>

    
    <div class="pv-sheet-chips">
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('')">All</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('WhatsApp')">WhatsApp</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('Telegram')">Telegram</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('OpenAI')">OpenAI / ChatGPT</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('Google')">Google</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('TikTok')">TikTok</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('Instagram')">Instagram</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('Twitter')">Twitter / X</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('Discord')">Discord</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPick('Tinder')">Tinder</button>
    </div>

    
    <div class="pv-sheet-list" id="pv-sheet-list">
        <div id="pv-sheet-loading" style="text-align: center; padding: 40px 20px; color: #6B7A99; font-size: 13.5px;">
            <div style="display: inline-block; width: 22px; height: 22px; border: 2.5px solid rgba(46, 139, 63, 0.25); border-top-color: #2E8B3F; border-radius: 50%; animation: pvSpin 0.7s linear infinite; margin-bottom: 10px;"></div>
            <div>Loading verified services in Naira...</div>
        </div>
    </div>
</div>


<div class="pv-sheet-backdrop" id="pv-location-sheet-backdrop" onclick="pvCloseLocationSheet()"></div>

<div class="pv-sheet-container" id="pv-location-sheet">
    
    <div class="pv-sheet-handle-wrap" onclick="pvCloseLocationSheet()">
        <div class="pv-sheet-handle"></div>
    </div>

    
    <div class="pv-sheet-header">
        <button type="button" class="pv-sheet-cancel-btn" onclick="pvCloseLocationSheet()">Cancel</button>
        <span class="pv-sheet-title">Select Location</span>
        <button type="button" class="pv-sheet-done-btn" onclick="pvCloseLocationSheet()">Done</button>
    </div>

    
    <div class="pv-sheet-search-wrap">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" style="position: absolute; left: 32px; top: 50%; transform: translateY(-70%); pointer-events: none;">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
        </svg>
        <input type="text" class="pv-sheet-search-input" id="pv-location-search-input"
               placeholder="Search by state, city, or area code (e.g. 212, California)"
               oninput="pvFilterLocations(this.value)">
    </div>

    
    <div style="padding: 0 18px 12px; display: flex; gap: 8px; align-items: center;">
        <div style="position: relative; flex: 1;">
            <input type="text" id="pv-custom-areacode-input" placeholder="Type 3-digit area code (e.g. 310)" maxlength="5"
                   style="width: 100%; height: 42px; border-radius: 12px; background: #EEF1F8; border: 1.5px solid rgba(15, 42, 90, 0.12); padding: 0 14px; font-size: 13.5px; font-weight: 600; color: #0F2A5A; outline: none; box-sizing: border-box;">
        </div>
        <button type="button" class="blitz-btn" onclick="pvApplyCustomAreaCode()"
                style="height: 42px; background: #0F2A5A; color: #FFFFFF; border: none; border-radius: 12px; padding: 0 18px; font-size: 13px; font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
            Apply
        </button>
    </div>

    
    <div class="pv-sheet-chips" id="pv-location-chips">
        <button type="button" class="pv-sheet-chip" onclick="pvSelectLocation('', 'Any Location (Recommended)', '')">★ Any Location</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('California')">California</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('New York')">New York</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Texas')">Texas</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Florida')">Florida</button>
        <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Chicago')">Chicago</button>
    </div>

    
    <div class="pv-sheet-list" id="pv-location-sheet-list">
        <div id="pv-location-loading" style="text-align: center; padding: 40px 20px; color: #6B7A99; font-size: 13.5px;">
            <div style="display: inline-block; width: 22px; height: 22px; border: 2.5px solid rgba(46, 139, 63, 0.25); border-top-color: #2E8B3F; border-radius: 50%; animation: pvSpin 0.7s linear infinite; margin-bottom: 10px;"></div>
            <div>Fetching verified locations & area codes...</div>
        </div>
    </div>
</div>


<script>
(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';

    // State
    let pvState = {
        country: 'USA',
        countryId: 1,
        durationType: 'STR',
        durationDays: 20,
        isAllServices: false,
        selectedWebsite: null,
        areaCode: '',
        locationName: 'Any Location (Recommended)',
        catalog: [],
        locationsList: [],
        lastLocationCountry: '',
        allServicesData: null,
        filterQuery: '',
        currencySymbol: '₦',
        exchangeRate: 1650,
    };

    // Helper for robust API endpoint resolution across XAMPP subpath & standard domain
    function getApiEndpoint(endpoint) {
        const cleanEndpoint = endpoint.startsWith('/') ? endpoint : '/' + endpoint;
        const laravelBase = "<?php echo e(url('/api/v1')); ?>";
        if (laravelBase && !laravelBase.includes('{{')) {
            try {
                const urlObj = new URL(laravelBase, window.location.origin);
                const basePath = urlObj.pathname.replace(/\/+$/, '');
                return basePath + cleanEndpoint;
            } catch (e) {
                return laravelBase + cleanEndpoint;
            }
        }
        return '/api/v1' + cleanEndpoint;
    }

    function formatNaira(amount) {
        return pvState.currencySymbol + Number(amount).toLocaleString('en-NG', { maximumFractionDigits: 0 });
    }

    // ── 1. Country Switching (PVADeals Native Functionality) ──
    window.pvSelectCountry = function(country, btn) {
        // Normalize country code
        const countryMap = {
            'GB': 'UK', 'GBR': 'UK', 'UK': 'UK',
            'US': 'USA', 'USA': 'USA',
            'HK': 'HK', 'HKG': 'HK',
            'ID': 'ID', 'IDN': 'ID',
            'AU': 'AU', 'AUS': 'AU'
        };
        const countryIdMap = {
            'USA': 1, 'US': 1,
            'UK': 2, 'GB': 2,
            'AU': 13,
            'ID': 17,
            'HK': 31
        };
        const normCountry = countryMap[country] || country;
        pvState.country = normCountry;
        pvState.countryId = countryIdMap[normCountry] || 1;
        const asCode = normCountry === 'USA' ? 'US' : normCountry;
        pvState.allServicesCountry = asCode;

        // Synchronize top country pills (#pv-country-group)
        document.querySelectorAll('#pv-country-group .pv-pill').forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-country') === normCountry);
        });

        // Synchronize All-Services card country pills (#pv-as-country-pills)
        document.querySelectorAll('#pv-as-country-pills button').forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-as-country') === asCode);
        });

        // Sync header country chip if present
        const flagMap = { 'USA': '🇺🇸', 'UK': '🇬🇧', 'HK': '🇭🇰', 'ID': '🇮🇩', 'AU': '🇦🇺' };
        const flagEl = document.getElementById('header-country-flag');
        const codeEl = document.getElementById('header-country-code');
        if (codeEl) codeEl.textContent = normCountry;
        if (flagEl) {
            flagEl.textContent = flagMap[normCountry] || '🏳️';
        }

        // Sync Rent Tab country if present on mobile page
        if (typeof rentSelectCountry === 'function') {
            const countryNames = { 'USA': 'United States', 'UK': 'United Kingdom', 'HK': 'Hong Kong', 'ID': 'Indonesia', 'AU': 'Australia' };
            rentSelectCountry(normCountry, flagMap[normCountry], countryNames[normCountry], pvState.countryId);
        }

        // In PVADeals: Hong Kong, Indonesia, and Australia are ALL-SERVICES EXCLUSIVE countries
        const isAllServicesOnly = (normCountry === 'HK' || normCountry === 'ID' || normCountry === 'AU');

        updateDurationBlockForCountry(normCountry, isAllServicesOnly);

        if (isAllServicesOnly) {
            // Automatically switch to All Services mode for this country
            pvToggleAllServices(null, true);
            pvSelectAllServicesCountry(asCode);
        } else {
            // US or UK
            if (pvState.isAllServices) {
                // If user was already in All Services mode, stay in All Services for US/UK
                pvSelectAllServicesCountry(asCode);
            } else {
                // Standard mode
                resetWebsiteState();
                pvFetchCatalog();
                pvFetchLocations();
            }
        }
    };

    function updateDurationBlockForCountry(country, isAllServicesOnly) {
        const switcher = document.getElementById('pv-duration-switcher');
        const strBanner = document.getElementById('pv-str-active-banner');
        const ltrSection = document.getElementById('pv-ltr-duration-section');
        const modeBadge = document.getElementById('pv-duration-badge-mode');

        if (isAllServicesOnly) {
            // HK, ID, AU do not have STR 20m or per-website LTR (3, 7, 14 days)
            if (switcher) switcher.style.display = 'none';
            if (strBanner) strBanner.style.display = 'none';
            if (ltrSection) ltrSection.style.display = 'none';
            if (modeBadge) {
                modeBadge.textContent = '★ All-Services Exclusive';
                modeBadge.style.color = '#0284C7';
                modeBadge.style.background = 'rgba(2, 132, 199, 0.12)';
                modeBadge.style.borderColor = 'rgba(2, 132, 199, 0.25)';
            }
        } else {
            // US or UK: STR 20m and LTR are available
            if (switcher) switcher.style.display = 'flex';
            const isStr = (pvState.durationType === 'STR');
            if (strBanner) strBanner.style.display = isStr ? 'block' : 'none';
            if (ltrSection) ltrSection.style.display = isStr ? 'none' : 'block';
            if (modeBadge) {
                modeBadge.textContent = isStr ? '⚡ 20-Min Disposable Line' : `📅 ${pvState.durationDays}-Day Dedicated Line`;
                modeBadge.style.color = isStr ? '#15803d' : '#4338ca';
                modeBadge.style.background = isStr ? 'rgba(34, 197, 94, 0.12)' : 'rgba(99, 102, 241, 0.12)';
                modeBadge.style.borderColor = isStr ? 'rgba(34, 197, 94, 0.25)' : 'rgba(99, 102, 241, 0.25)';
            }
        }

        // Update location field label for non-US
        const locText = document.getElementById('pv-location-trigger-text');
        if (locText && country !== 'USA') {
            const labelMap = {
                'UK': 'Any Location (UK Standard)',
                'HK': 'Hong Kong National (HK Telecom)',
                'ID': 'Indonesia National (Telkomsel)',
                'AU': 'Australia National (Telstra)'
            };
            locText.textContent = labelMap[country] || 'Any Location (Optimal)';
        }
    }

    window.pvSetCountry = function(code, flag, name, countryId) {
        const upper = (code || '').toUpperCase().trim();
        const normCode = (upper === 'USA' || upper === 'US') ? 'USA'
            : ((upper === 'GBR' || upper === 'GB' || upper === 'UK') ? 'UK'
            : ((upper === 'HKG' || upper === 'HK') ? 'HK'
            : ((upper === 'IDN' || upper === 'ID') ? 'ID'
            : ((upper === 'AUS' || upper === 'AU') ? 'AU' : upper))));

        const pill = document.querySelector(`#pv-country-group .pv-pill[data-country="${normCode}"]`);
        pvSelectCountry(normCode, pill);
    };

    // ── 2. Duration Switching & Segmented Control ──
    window.pvSwitchRentalMode = function(mode) {
        if (mode === 'STR') {
            pvSelectDuration('STR', 20);
        } else {
            const defaultLtrPill = document.querySelector('.pv-dur-pill[data-type="LTR"][data-duration="30"]') 
                || document.querySelector('.pv-dur-pill[data-type="LTR"]');
            const days = defaultLtrPill ? parseInt(defaultLtrPill.getAttribute('data-duration') || '30') : 30;
            pvSelectDuration('LTR', days, defaultLtrPill);
        }
    };

    window.pvSelectDuration = function(type, days, btn) {
        // If user is currently on an All-Services-only country (HK, ID, AU) and selects STR (20 mins)
        // or a per-service LTR duration, automatically switch to USA where STR/LTR website rentals exist!
        if (pvState.country !== 'USA' && pvState.country !== 'UK') {
            pvSelectCountry('USA');
        }

        pvState.durationType = type;
        pvState.durationDays = parseInt(days) || 20;

        const isStr = (type === 'STR');
        const strBtn = document.getElementById('pv-switch-btn-str');
        const ltrBtn = document.getElementById('pv-switch-btn-ltr');
        const strBanner = document.getElementById('pv-str-active-banner');
        const ltrSection = document.getElementById('pv-ltr-duration-section');
        const modeBadge = document.getElementById('pv-duration-badge-mode');

        if (strBtn) strBtn.classList.toggle('active', isStr);
        if (ltrBtn) ltrBtn.classList.toggle('active', !isStr);
        if (strBanner) strBanner.style.display = isStr ? 'block' : 'none';
        if (ltrSection) ltrSection.style.display = isStr ? 'none' : 'block';

        if (modeBadge) {
            modeBadge.textContent = isStr ? '⚡ 20-Min Disposable Line' : `📅 ${pvState.durationDays}-Day Dedicated Line`;
            modeBadge.style.color = isStr ? '#15803d' : '#4338ca';
            modeBadge.style.background = isStr ? 'rgba(34, 197, 94, 0.12)' : 'rgba(99, 102, 241, 0.12)';
            modeBadge.style.borderColor = isStr ? 'rgba(34, 197, 94, 0.25)' : 'rgba(99, 102, 241, 0.25)';
        }

        document.querySelectorAll('.pv-dur-pill').forEach(b => b.classList.remove('active'));
        if (btn && btn.classList) {
            btn.classList.add('active');
        } else {
            const target = document.querySelector(`.pv-dur-pill[data-type="${type}"][data-duration="${days}"]`);
            if (target) target.classList.add('active');
        }

        const allSrvPill = document.getElementById('pv-all-services-pill');
        if (allSrvPill) allSrvPill.classList.remove('active');

        // If user selects any standard per-service duration, deactivate All Services mode
        if (pvState.isAllServices) {
            pvToggleAllServices(null, false);
        }

        // Re-render sheet list if populated to reflect duration prices
        if (pvState.catalog.length > 0) {
            renderWebsites(getFilteredList());
            renderPopularAppsGrid();
        }
        updateSummary();
    };

    // ── Popular Services Quick-Picks Engine ──
    const POPULAR_APPS_LIST = [
        { 
            key: 'whatsapp', 
            name: 'WhatsApp', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22" fill="#25D366"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm5.79 14.07c-.24.68-1.4 1.26-1.93 1.34-.51.08-1.17.14-3.37-.77-2.82-1.16-4.63-4.04-4.77-4.23-.14-.19-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.25-.28.56-.35.75-.35.19 0 .37.002.53.01.17.01.4.06.62.59.24.57.82 2 .89 2.15.07.15.12.33.02.53-.1.2-.15.33-.3.51-.15.18-.32.4-.46.54-.15.15-.31.31-.13.62.18.31.8 1.32 1.72 2.14 1.18 1.05 2.18 1.38 2.49 1.53.31.15.49.13.67-.08.18-.21.78-.91.99-1.22.21-.31.42-.26.71-.15.29.11 1.86.88 2.18 1.04.32.16.53.24.61.37.08.14.08.79-.16 1.47z"/></svg>`, 
            match: ['whatsapp'] 
        },
        { 
            key: 'telegram', 
            name: 'Telegram', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22" fill="#0088cc"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 0 0-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>`, 
            match: ['telegram'] 
        },
        { 
            key: 'openai', 
            name: 'ChatGPT / OpenAI', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22" fill="#10a37f"><path d="M22.28 10.42a5.45 5.45 0 0 0-.46-4.42 5.56 5.56 0 0 0-4.77-2.73 5.43 5.43 0 0 0-1.89.34A5.5 5.5 0 0 0 10.74 2a5.57 5.57 0 0 0-5.32 3.86 5.45 5.45 0 0 0-3.41 2.48 5.54 5.54 0 0 0 .58 6.45 5.45 5.45 0 0 0 .46 4.42 5.56 5.56 0 0 0 4.77 2.73 5.4 5.4 0 0 0 1.89-.34A5.5 5.5 0 0 0 14.13 23a5.57 5.57 0 0 0 5.32-3.86 5.45 5.45 0 0 0 3.41-2.48 5.54 5.54 0 0 0-.58-6.24z"/></svg>`, 
            match: ['openai', 'chatgpt'] 
        },
        { 
            key: 'google', 
            name: 'Google / Gmail', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27a7.195 7.195 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.93 6.72-4.93z"/></svg>`, 
            match: ['google', 'gmail', 'youtube'] 
        },
        { 
            key: 'instagram', 
            name: 'Instagram', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22" fill="#E1306C"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>`, 
            match: ['instagram'] 
        },
        { 
            key: 'tiktok', 
            name: 'TikTok', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22" fill="#000000"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.89 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3 15.67 6.34 6.34 0 0 0 9.34 22a6.34 6.34 0 0 0 6.34-6.33V9.22a8.16 8.16 0 0 0 4.82 1.56V7.32a4.85 4.85 0 0 1-.91-.63z"/></svg>`, 
            match: ['tiktok'] 
        },
        { 
            key: 'tinder', 
            name: 'Tinder', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22" fill="#FE3C72"><path d="M12.76 2.05c-.32-.23-.74-.18-.99.11-1.12 1.3-3.66 4.39-4.23 7.03-.51 2.37.15 4.6 1.78 6.13 1.62 1.52 3.91 2.15 6.08 1.64 2.16-.51 3.93-2.09 4.69-4.18.77-2.09.43-4.42-.89-6.22-1.32-1.79-3.26-3.15-5.32-4.13-.37-.18-.78-.18-1.12-.38zm.72 13.91c-.81.25-1.68.04-2.28-.53-.61-.57-.85-1.4-.66-2.2.19-.8.78-1.74 1.25-2.31.22-.26.5-.47.81-.6.31-.13.66-.15.98-.06.32.09.6.28.79.54.19.26.27.58.24.9-.06.63-.32 1.23-.74 1.71-.42.48-.99.82-1.62.97-.26.06-.52.09-.77.08z"/></svg>`, 
            match: ['tinder'] 
        },
        { 
            key: 'twitter', 
            name: 'Twitter / X', 
            iconSvg: `<svg viewBox="0 0 24 24" width="22" height="22" fill="#000000"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`, 
            match: ['twitter', 'twitter-x', 'x'] 
        }
    ];

    function renderPopularAppsGrid() {
        const grid = document.getElementById('pv-popular-apps-grid');
        if (!grid) return;
        grid.innerHTML = '';

        POPULAR_APPS_LIST.forEach(app => {
            const card = document.createElement('div');
            card.className = 'pv-popular-app-card blitz-btn';
            card.id = `pv-popular-card-${app.key}`;

            let matched = null;
            if (pvState.catalog && pvState.catalog.length > 0) {
                matched = pvState.catalog.find(it => {
                    const low = (it.name || '').toLowerCase();
                    return app.match.some(m => low.includes(m)) || low === app.key;
                });
            }

            const isSelected = pvState.selectedWebsite && matched && (pvState.selectedWebsite.id === matched.id);
            if (isSelected) card.classList.add('active');

            let priceText = '...';
            if (matched) {
                const price = getPriceForItem(matched);
                priceText = formatNaira(price);
            }

            card.innerHTML = `
                <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    ${app.iconSvg}
                </div>
                <div class="pv-popular-app-name">${app.name}</div>
                <div class="pv-popular-app-price">${priceText}</div>
            `;

            card.onclick = (e) => {
                if (window.blitzTap) window.blitzTap(e);
                pvSelectPopularApp(app.key);
            };

            grid.appendChild(card);
        });
    }

    window.pvSelectPopularApp = function(appKey) {
        const appCfg = POPULAR_APPS_LIST.find(a => a.key === appKey);
        if (!appCfg) return;

        let matched = null;
        if (pvState.catalog && pvState.catalog.length > 0) {
            matched = pvState.catalog.find(it => {
                const low = (it.name || '').toLowerCase();
                return appCfg.match.some(m => low.includes(m)) || low === appKey;
            });
        }

        if (matched) {
            pvSelectWebsite(matched);
        } else {
            // If catalog is not loaded yet, open sheet
            pvOpenWebsiteSheet();
        }

        // Highlight card
        document.querySelectorAll('.pv-popular-app-card').forEach(c => c.classList.remove('active'));
        const activeCard = document.getElementById(`pv-popular-card-${appKey}`);
        if (activeCard) activeCard.classList.add('active');
    };

    // ── 3. All Services Functionality (Exact PVADeals Behavior) ──
    window.pvToggleAllServices = function(evt, forceState) {
        if (typeof forceState === 'boolean') {
            pvState.isAllServices = forceState;
        } else {
            if (evt) {
                const now = Date.now();
                if (window._pvLastAllSrvToggle && (now - window._pvLastAllSrvToggle < 350)) {
                    if (evt.cancelable) evt.preventDefault();
                    return;
                }
                window._pvLastAllSrvToggle = now;
            }
            pvState.isAllServices = !pvState.isAllServices;
        }

        const pill = document.getElementById('pv-all-services-pill');
        const dedicatedWrap = document.getElementById('pv-allservices-dedicated-wrap');
        const triggerText = document.getElementById('pv-website-trigger-text');
        const locTriggerText = document.getElementById('pv-location-trigger-text');
        const locDisabled = document.getElementById('pv-location-disabled-btn');
        const locActive = document.getElementById('pv-location-trigger-btn');

        if (pvState.isAllServices) {
            if (pill) pill.classList.add('active');
            document.querySelectorAll('.pv-dur-pill').forEach(b => b.classList.remove('active'));

            if (dedicatedWrap) dedicatedWrap.style.display = 'block';
            pvCloseWebsiteSheet();

            const asCode = pvState.allServicesCountry || (pvState.country === 'USA' ? 'US' : pvState.country) || 'US';
            const asCfg = allServicesCountryConfig[asCode] || allServicesCountryConfig['US'];

            // Sync country pills
            document.querySelectorAll('#pv-as-country-pills button').forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-as-country') === asCode);
            });
            document.querySelectorAll('#pv-country-group .pv-pill').forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-country') === (asCode === 'US' ? 'USA' : asCode));
            });

            if (triggerText) {
                triggerText.textContent = `All services (${asCfg.name})`;
                triggerText.style.color = '#0F2A5A';
                triggerText.style.fontWeight = '700';
            }
            if (locDisabled) locDisabled.style.display = 'none';
            if (locActive) locActive.style.display = 'flex';
            if (locTriggerText) {
                locTriggerText.textContent = `Any Location (${asCfg.carrier.name})`;
            }

            renderAllServicesDurationPills(asCfg);
            renderAllServicesDedicatedCard();
        } else {
            if (pill) pill.classList.remove('active');
            if (dedicatedWrap) dedicatedWrap.style.display = 'none';

            // If current country was an All-Services-only country (HK, ID, AU), switch to USA for standard website rentals
            if (pvState.country === 'HK' || pvState.country === 'ID' || pvState.country === 'AU') {
                pvSelectCountry('USA');
                return;
            }

            // Reactivate current duration pill
            const durTarget = document.querySelector(`.pv-dur-pill[data-type="${pvState.durationType}"][data-duration="${pvState.durationDays}"]`) 
                || document.querySelector('.pv-dur-pill[data-type="STR"]');
            if (durTarget) durTarget.classList.add('active');

            resetWebsiteState();
            pvFetchCatalog();
        }
    };

    const allServicesCountryConfig = {
        'US': {
            code: 'US',
            name: 'United States',
            flag: '🇺🇸',
            carrier: { name: 'T-Mobile', badge: 'T', color: '#E20074' },
            defaultDuration: 28,
            plans: {
                28: { duration: 28, label: '28 days', priceUsd: 12.99, desc: 'full 28 days' }
            },
            lockoutDays: 0
        },
        'UK': {
            code: 'UK',
            name: 'United Kingdom',
            flag: '🇬🇧',
            carrier: { name: 'EE', badge: 'EE', color: '#007B85' },
            defaultDuration: 30,
            plans: {
                30: { duration: 30, label: '30 days', priceUsd: 6.99, desc: 'full 30 days' },
                90: { duration: 90, label: '90 days', priceUsd: 11.99, desc: 'full 90 days' }
            },
            lockoutDays: 4
        },
        'HK': {
            code: 'HK',
            name: 'Hong Kong',
            flag: '🇭🇰',
            carrier: { name: 'HK Telecom', badge: 'HK', color: '#E11D48' },
            defaultDuration: 30,
            plans: {
                30: { duration: 30, label: '30 days', priceUsd: 11.50, desc: 'full 30 days' },
                180: { duration: 180, label: '180 days', priceUsd: 22.50, desc: 'full 180 days' }
            },
            lockoutDays: 4
        },
        'ID': {
            code: 'ID',
            name: 'Indonesia',
            flag: '🇮🇩',
            carrier: { name: 'Telkomsel', badge: 'ID', color: '#DC2626' },
            defaultDuration: 30,
            plans: {
                30: { duration: 30, label: '30 days', priceUsd: 11.99, desc: 'full 30 days' },
                180: { duration: 180, label: '180 days', priceUsd: 21.99, desc: 'full 180 days' }
            },
            lockoutDays: 4
        },
        'AU': {
            code: 'AU',
            name: 'Australia',
            flag: '🇦🇺',
            carrier: { name: 'Telstra', badge: 'AU', color: '#0284C7' },
            defaultDuration: 30,
            plans: {
                30: { duration: 30, label: '30 days', priceUsd: 22.99, desc: 'full 30 days' }
            },
            lockoutDays: 0
        }
    };

    window.pvSelectAllServicesCountry = function(code) {
        pvState.allServicesCountry = code;
        const normCountry = (code === 'US' ? 'USA' : code);
        pvState.country = normCountry;
        const countryIdMap = {
            'US': 1, 'USA': 1,
            'UK': 2, 'GB': 2,
            'AU': 13,
            'ID': 17,
            'HK': 31
        };
        pvState.countryId = countryIdMap[code] || 1;

        // Keep BOTH country pill sets in sync
        document.querySelectorAll('#pv-as-country-pills button').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-as-country') === code);
        });
        document.querySelectorAll('#pv-country-group .pv-pill').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-country') === normCountry);
        });

        // Sync mobile header flag & code
        const flagMap = { 'US': '🇺🇸', 'USA': '🇺🇸', 'UK': '🇬🇧', 'HK': '🇭🇰', 'ID': '🇮🇩', 'AU': '🇦🇺' };
        const flagEl = document.getElementById('header-country-flag');
        const codeEl = document.getElementById('header-country-code');
        if (codeEl) codeEl.textContent = normCountry;
        if (flagEl) flagEl.textContent = flagMap[code] || '🏳️';

        const cfg = allServicesCountryConfig[code] || allServicesCountryConfig['US'];
        pvState.durationDays = cfg.defaultDuration;

        updateDurationBlockForCountry(normCountry, (normCountry === 'HK' || normCountry === 'ID' || normCountry === 'AU'));

        renderAllServicesDurationPills(cfg);
        renderAllServicesDedicatedCard();
    };

    function renderAllServicesDurationPills(cfg) {
        const wrap = document.getElementById('pv-as-duration-pills');
        if (!wrap) return;
        wrap.innerHTML = '';

        const plans = cfg.plans || {};
        const rate = pvState.exchangeRate || 1650;
        const sym = pvState.currencySymbol || '₦';

        Object.keys(plans).forEach(dur => {
            const plan = plans[dur];
            const active = (parseInt(dur) === pvState.durationDays);
            const priceNgn = Math.round(plan.priceUsd * rate);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pv-pill blitz-btn' + (active ? ' active' : '');
            btn.textContent = `${plan.label} (${sym}${priceNgn.toLocaleString()})`;
            btn.onclick = () => {
                pvState.durationDays = parseInt(dur);
                document.querySelectorAll('#pv-as-duration-pills button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                renderAllServicesDedicatedCard();
            };
            wrap.appendChild(btn);
        });
    }

    function renderAllServicesDedicatedCard() {
        const countryCode = pvState.allServicesCountry || (pvState.country === 'USA' ? 'US' : pvState.country) || 'US';
        const cfg = allServicesCountryConfig[countryCode] || allServicesCountryConfig['US'];
        const rate = pvState.exchangeRate || 1650;
        const sym = pvState.currencySymbol || '₦';

        if (!cfg.plans[pvState.durationDays]) {
            pvState.durationDays = cfg.defaultDuration;
        }

        const plan = cfg.plans[pvState.durationDays] || Object.values(cfg.plans)[0];
        const priceUsd = plan.priceUsd;
        const priceNgn = Math.round(priceUsd * rate);
        const displayPrice = `${sym}${priceNgn.toLocaleString()}`;

        pvState.selectedWebsite = {
            id: 'ALL_SERVICES',
            name: `All services (${cfg.name})`,
            price: priceNgn,
            price_usd: priceUsd,
            stock: 9999,
        };

        const priceVal = document.getElementById('pv-as-price-val');
        if (priceVal) priceVal.textContent = displayPrice;

        const usdRef = document.getElementById('pv-as-usd-ref');
        if (usdRef) usdRef.textContent = `(≈ $${priceUsd.toFixed(2)} USD)`;

        const durSuffix = document.getElementById('pv-as-duration-suffix');
        if (durSuffix) durSuffix.textContent = `/ ${plan.label}`;

        const descText = document.getElementById('pv-as-description-text');
        if (descText) {
            descText.textContent = `One non-VoIP number that works on any website or app in ${cfg.name} — no service to choose, and unlimited use for the ${plan.desc}.`;
        }

        const carrierLogo = document.getElementById('pv-as-carrier-logo');
        const carrierName = document.getElementById('pv-as-carrier-name');
        if (carrierLogo && carrierName) {
            carrierLogo.textContent = cfg.carrier.badge;
            carrierLogo.style.background = cfg.carrier.color;
            carrierName.textContent = cfg.carrier.name;
        }

        const lockoutNotice = document.getElementById('pv-as-lockout-notice');
        if (lockoutNotice) {
            lockoutNotice.style.display = cfg.lockoutDays > 0 ? 'block' : 'none';
        }

        const buyBtnText = document.getElementById('pv-as-btn-text');
        if (buyBtnText) {
            buyBtnText.textContent = `Buy all-services number · ${displayPrice}`;
        }

        const mainCta = document.getElementById('pv-main-cta');
        const mainCtaText = document.getElementById('pv-main-cta-text');
        if (mainCta) {
            mainCta.className = 'pv-main-cta-btn ready blitz-btn';
            mainCta.disabled = false;
        }
        if (mainCtaText) {
            mainCtaText.textContent = `Buy all-services number · ${displayPrice}`;
        }
    }

    // ── 4. Bottom Sheet Modal Controls (Screenshot 1) ──
    window.pvOpenWebsiteSheet = function(e) {
        const action = () => {
            const backdrop = document.getElementById('pv-sheet-backdrop');
            const sheet = document.getElementById('pv-bottom-sheet');

            if (backdrop) backdrop.classList.add('open');
            if (sheet) sheet.classList.add('open');
            document.body.style.overflow = 'hidden';

            const input = document.getElementById('pv-sheet-search-input');
            if (input) {
                input.value = '';
                setTimeout(() => input.focus(), 100);
            }

            if (pvState.catalog.length === 0) {
                pvFetchCatalog();
            } else {
                renderWebsites(pvState.catalog);
            }
        };

        if (window.blitzTap && e) {
            window.blitzTap(e, action);
        } else {
            action();
        }
    };

    window.pvCloseWebsiteSheet = function(e) {
        if (window.blitzTap && e) window.blitzTap(e);
        const backdrop = document.getElementById('pv-sheet-backdrop');
        const sheet = document.getElementById('pv-bottom-sheet');

        if (backdrop) backdrop.classList.remove('open');
        if (sheet) sheet.classList.remove('open');
        document.body.style.overflow = '';

        if (window._returnTabAfterSheet && typeof switchTab === 'function') {
            const retTab = window._returnTabAfterSheet;
            window._returnTabAfterSheet = null;
            const btn = document.querySelector(`#blitz-tab-bar [data-tab="${retTab}"]`);
            switchTab(retTab, btn);
        }
    };

    // ── 5. Fetch Catalog from API with Naira Conversion ──
    async function pvFetchCatalog() {
        const listEl = document.getElementById('pv-sheet-list');
        listEl.innerHTML = `
            <div style="text-align: center; padding: 40px 20px; color: #6B7A99; font-size: 13.5px;">
                <div style="display: inline-block; width: 22px; height: 22px; border: 2.5px solid rgba(46, 139, 63, 0.25); border-top-color: #2E8B3F; border-radius: 50%; animation: pvSpin 0.7s linear infinite; margin-bottom: 10px;"></div>
                <div>Converting prices to Naira & loading catalog...</div>
            </div>
        `;

        const triggerText = document.getElementById('pv-website-trigger-text');
        if (triggerText && !pvState.selectedWebsite && !pvState.isAllServices) {
            triggerText.textContent = 'Loading websites...';
            triggerText.style.color = '#64748B';
            triggerText.style.fontWeight = '500';
        }

        try {
            const endpoint = getApiEndpoint('/pvadeals/websites?country=' + encodeURIComponent(pvState.country) + '&currency=NGN');
            const res = await fetch(endpoint);
            const json = await res.json();

            if (json.success && Array.isArray(json.data)) {
                pvState.catalog = json.data;
                if (json.currency) {
                    pvState.currencySymbol = json.currency.symbol || '₦';
                    pvState.exchangeRate = json.currency.exchange_rate || 1650;
                }
                if (json.all_services) {
                    pvState.allServicesData = json.all_services;
                }
                renderWebsites(pvState.catalog);
                renderPopularAppsGrid();

                if (triggerText && !pvState.selectedWebsite && !pvState.isAllServices) {
                    triggerText.textContent = 'Search website';
                }

                if (pvState.isAllServices) {
                    renderAllServicesDedicatedCard();
                }
            } else {
                listEl.innerHTML = '<div style="padding: 30px; text-align: center; color: #f87171; font-size: 13.5px;">Failed to load catalog. Please retry.</div>';
                if (triggerText && !pvState.selectedWebsite && !pvState.isAllServices) {
                    triggerText.textContent = 'Search website';
                }
            }
        } catch (err) {
            console.error('PVADeals Catalog error', err);
            listEl.innerHTML = '<div style="padding: 30px; text-align: center; color: #f87171; font-size: 13.5px;">Network connection error.</div>';
            if (triggerText && !pvState.selectedWebsite && !pvState.isAllServices) {
                triggerText.textContent = 'Search website';
            }
        }
    }

    // ── 6. Filtering & Quick Brand Picks ──
    window.pvFilterWebsites = function(term) {
        pvState.filterQuery = (term || '').toLowerCase().trim();
        renderWebsites(getFilteredList());
    };

    window.pvQuickPick = function(brandName) {
        const input = document.getElementById('pv-sheet-search-input');
        input.value = brandName;
        pvFilterWebsites(brandName);
    };

    function getFilteredList() {
        if (!pvState.filterQuery) return pvState.catalog;
        return pvState.catalog.filter(s => s.name.toLowerCase().includes(pvState.filterQuery));
    }

    // ── Brand Avatar Generator ──
    function getServiceAvatarHtml(name) {
        const clean = name.replace(/^[^a-zA-Z0-9]+/, '').trim();
        const initial = clean.charAt(0).toUpperCase() || '★';

        const lower = name.toLowerCase();
        if (lower.includes('whatsapp')) {
            return `<div class="pv-service-avatar" style="background: rgba(37, 211, 102, 0.15); border-color: #25D366; color: #25D366;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#25D366"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm5.79 14.07c-.24.68-1.4 1.26-1.93 1.34-.51.08-1.17.14-3.37-.77-2.82-1.16-4.63-4.04-4.77-4.23-.14-.19-1.14-1.52-1.14-2.9 0-1.38.72-2.06.98-2.34.25-.28.56-.35.75-.35.19 0 .37.002.53.01.17.01.4.06.62.59.24.57.82 2 .89 2.15.07.15.12.33.02.53-.1.2-.15.33-.3.51-.15.18-.32.4-.46.54-.15.15-.31.31-.13.62.18.31.8 1.32 1.72 2.14 1.18 1.05 2.18 1.38 2.49 1.53.31.15.49.13.67-.08.18-.21.78-.91.99-1.22.21-.31.42-.26.71-.15.29.11 1.86.88 2.18 1.04.32.16.53.24.61.37.08.14.08.79-.16 1.47z"/></svg>
            </div>`;
        }
        if (lower.includes('telegram')) {
            return `<div class="pv-service-avatar" style="background: rgba(0, 136, 204, 0.15); border-color: #0088cc; color: #0088cc;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#0088cc"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 0 0-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>
            </div>`;
        }
        if (lower.includes('openai') || lower.includes('chatgpt')) {
            return `<div class="pv-service-avatar" style="background: rgba(16, 163, 127, 0.15); border-color: #10a37f; color: #10a37f;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#10a37f"><path d="M22.28 10.42a5.45 5.45 0 0 0-.46-4.42 5.56 5.56 0 0 0-4.77-2.73 5.43 5.43 0 0 0-1.89.34A5.5 5.5 0 0 0 10.74 2a5.57 5.57 0 0 0-5.32 3.86 5.45 5.45 0 0 0-3.41 2.48 5.54 5.54 0 0 0 .58 6.45 5.45 5.45 0 0 0 .46 4.42 5.56 5.56 0 0 0 4.77 2.73 5.4 5.4 0 0 0 1.89-.34A5.5 5.5 0 0 0 14.13 23a5.57 5.57 0 0 0 5.32-3.86 5.45 5.45 0 0 0 3.41-2.48 5.54 5.54 0 0 0-.58-6.24z"/></svg>
            </div>`;
        }
        if (lower.includes('google') || lower.includes('gmail')) {
            return `<div class="pv-service-avatar" style="background: #ffffff;">
                <svg viewBox="0 0 24 24" width="20" height="20"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27a7.195 7.195 0 0 1 0-4.54V6.58H1.25a11.96 11.96 0 0 0 0 10.84l4.03-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.93 6.72-4.93z"/></svg>
            </div>`;
        }

        // Palette generator based on initials
        const colors = [
            'linear-gradient(135deg, #14b8a6, #0d9488)',
            'linear-gradient(135deg, #3b82f6, #1d4ed8)',
            'linear-gradient(135deg, #8b5cf6, #6d28d9)',
            'linear-gradient(135deg, #ec4899, #be185d)',
            'linear-gradient(135deg, #f59e0b, #b45309)',
        ];
        const colorIdx = Math.abs(name.split('').reduce((a, b) => a + b.charCodeAt(0), 0)) % colors.length;

        return `<div class="pv-service-avatar" style="background: ${colors[colorIdx]}; color: #ffffff;">
            ${initial}
        </div>`;
    }

    // ── 7. Render Website List matching Screenshot 1 ──
    function renderWebsites(items) {
        const listEl = document.getElementById('pv-sheet-list');
        listEl.innerHTML = '';

        if (!items || items.length === 0) {
            listEl.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b; font-size: 14px;">No services matching your search.</div>';
            return;
        }

        const maxDisplay = 120;
        const slice = items.slice(0, maxDisplay);

        slice.forEach(item => {
            const isSelected = pvState.selectedWebsite?.id === item.id;
            const priceNaira = getPriceForItem(item);
            const priceUsd = getPriceUsdForItem(item);
            const stock = getStockForItem(item);

            const row = document.createElement('div');
            row.className = 'pv-service-row blitz-btn' + (isSelected ? ' selected' : '');
            row.onclick = () => {
                pvSelectWebsite(item);
            };

            row.innerHTML = `
                <div class="pv-service-left">
                    ${getServiceAvatarHtml(item.name)}
                    <div class="pv-service-title-wrap">
                        <div class="pv-service-name-row">
                            <span class="pv-service-name">${item.name}</span>
                            ${item.is_best_value ? '<span class="pv-best-value-badge" style="background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.35); font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 6px; margin-left: 6px; display: inline-flex; align-items: center; gap: 3px;">★ Best Value</span>' : ''}
                            <span class="pv-stock-badge ${stock <= 0 ? 'empty' : ''}">${stock > 0 ? stock : '0'}</span>
                        </div>
                        <div class="pv-service-price-sub">${formatNaira(priceNaira)} <span style="font-weight: 500; font-size: 10.5px; color: #64748b;">(≈ $${priceUsd.toFixed(2)})</span></div>
                    </div>
                </div>
                <div class="pv-checkbox">
                    ${isSelected ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#022c22" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : ''}
                </div>
            `;
            listEl.appendChild(row);
        });

        if (items.length > maxDisplay) {
            const footer = document.createElement('div');
            footer.style.cssText = 'text-align: center; padding: 14px; color: #64748b; font-size: 12px;';
            footer.textContent = `Showing top ${maxDisplay} of ${items.length} verified services. Type above to filter.`;
            listEl.appendChild(footer);
        }
    }

    // ── 8. Website Selection ──
    window.pvSelectWebsite = function(item) {
        if (!item) return;

        if (item.id === 'ALL_SERVICES' || item.name.toLowerCase() === 'all services') {
            pvCloseWebsiteSheet();
            pvToggleAllServices(null, true);
            return;
        }

        pvState.selectedWebsite = item;
        pvState.isAllServices = false;

        // Reset All services card & restore website/location fields
        const allSrvCard = document.getElementById('pv-all-services-card');
        if (allSrvCard) allSrvCard.classList.remove('active');
        const asWrap = document.getElementById('pv-allservices-dedicated-wrap');
        if (asWrap) asWrap.style.display = 'none';
        const fields = document.getElementById('pv-website-location-fields');
        if (fields) fields.style.display = 'block';

        // Update trigger text with brand icon
        const triggerText = document.getElementById('pv-website-trigger-text');
        triggerText.textContent = item.name;
        triggerText.style.color = '#0F2A5A';
        triggerText.style.fontWeight = '700';

        // Update status label
        document.getElementById('pv-selected-website-status').textContent = item.name;

        // Enable location input
        enableLocationInput();

        // Close bottom sheet
        pvCloseWebsiteSheet();

        // Sync popular apps grid active state
        document.querySelectorAll('.pv-popular-app-card').forEach(c => c.classList.remove('active'));
        POPULAR_APPS_LIST.forEach(app => {
            const low = (item.name || '').toLowerCase();
            if (app.match.some(m => low.includes(m)) || low === app.key) {
                const card = document.getElementById(`pv-popular-card-${app.key}`);
                if (card) card.classList.add('active');
            }
        });

        // Update summary
        updateSummary();
    };

    function enableLocationInput() {
        const disabledBtn = document.getElementById('pv-location-disabled-btn');
        const triggerBtn = document.getElementById('pv-location-trigger-btn');
        if (disabledBtn) disabledBtn.style.display = 'none';
        if (triggerBtn) triggerBtn.style.display = 'flex';
        pvFetchLocations();
    }

    function resetWebsiteState() {
        pvState.selectedWebsite = null;
        pvState.areaCode = '';
        pvState.locationName = 'Any Location (Recommended)';

        const triggerText = document.getElementById('pv-website-trigger-text');
        if (triggerText) {
            triggerText.textContent = 'Search & select website';
            triggerText.style.color = '#6B7A99';
            triggerText.style.fontWeight = '500';
        }
        const statusEl = document.getElementById('pv-selected-website-status');
        if (statusEl) statusEl.textContent = 'None selected';

        // Reset location
        const disabledBtn = document.getElementById('pv-location-disabled-btn');
        const triggerBtn = document.getElementById('pv-location-trigger-btn');
        const locText = document.getElementById('pv-location-trigger-text');
        const locBadge = document.getElementById('pv-location-trigger-badge');
        const areaInp = document.getElementById('pv-area-code-input');

        if (disabledBtn) disabledBtn.style.display = 'flex';
        if (triggerBtn) triggerBtn.style.display = 'none';
        if (locText) {
            locText.textContent = pvState.country === 'USA' ? 'Any Location (Recommended)' : (pvState.country === 'UK' ? 'Any Location (UK Standard)' : 'Any Location (Optimal)');
        }
        if (locBadge) locBadge.style.display = 'none';
        if (areaInp) areaInp.value = '';

        const mainCta = document.getElementById('pv-main-cta');
        const mainCtaText = document.getElementById('pv-main-cta-text');
        if (mainCta && !pvState.isAllServices) {
            mainCta.className = 'pv-main-cta-btn waiting blitz-btn';
            mainCta.disabled = false;
        }
        if (mainCtaText && !pvState.isAllServices) {
            mainCtaText.textContent = 'Choose a website to start';
        }

        updateSummary();
    }

    // ── Location Sheet Modal Controls ──
    window.pvOpenLocationSheet = function(e) {
        const action = () => {
            const backdrop = document.getElementById('pv-location-sheet-backdrop');
            const sheet = document.getElementById('pv-location-sheet');

            if (backdrop) backdrop.classList.add('open');
            if (sheet) sheet.classList.add('open');
            document.body.style.overflow = 'hidden';

            const input = document.getElementById('pv-location-search-input');
            if (input) {
                input.value = '';
                setTimeout(() => input.focus(), 100);
            }

            if (!pvState.locationsList || pvState.locationsList.length === 0 || pvState.lastLocationCountry !== pvState.country) {
                pvFetchLocations();
            } else {
                renderLocations(pvState.locationsList);
            }
        };

        if (window.blitzTap && e) {
            window.blitzTap(e, action);
        } else {
            action();
        }
    };

    window.pvCloseLocationSheet = function(e) {
        if (window.blitzTap && e) window.blitzTap(e);
        const backdrop = document.getElementById('pv-location-sheet-backdrop');
        const sheet = document.getElementById('pv-location-sheet');

        if (backdrop) backdrop.classList.remove('open');
        if (sheet) sheet.classList.remove('open');
        document.body.style.overflow = '';
    };

    // ── Fetch Locations for Selected Country ──
    async function pvFetchLocations() {
        const listEl = document.getElementById('pv-location-sheet-list');
        if (listEl) {
            listEl.innerHTML = `
                <div style="text-align: center; padding: 40px 20px; color: #6B7A99; font-size: 13.5px;">
                    <div style="display: inline-block; width: 22px; height: 22px; border: 2.5px solid rgba(46, 139, 63, 0.25); border-top-color: #2E8B3F; border-radius: 50%; animation: pvSpin 0.7s linear infinite; margin-bottom: 10px;"></div>
                    <div>Fetching verified locations & area codes...</div>
                </div>
            `;
        }

        try {
            const endpoint = getApiEndpoint('/pvadeals/locations?country=' + encodeURIComponent(pvState.country));
            const res = await fetch(endpoint);
            const json = await res.json();

            if (json.success && Array.isArray(json.data)) {
                pvState.locationsList = json.data;
                pvState.lastLocationCountry = pvState.country;
                renderLocations(pvState.locationsList);
                renderLocationChips();
            } else {
                throw new Error('API failed');
            }
        } catch (err) {
            console.warn('Using fallback location dataset', err);
            pvState.locationsList = getFallbackLocations(pvState.country);
            pvState.lastLocationCountry = pvState.country;
            renderLocations(pvState.locationsList);
            renderLocationChips();
        }
    }

    function renderLocationChips() {
        const wrap = document.getElementById('pv-location-chips');
        if (!wrap) return;
        const isUk = (pvState.country === 'UK' || pvState.country === 'GB');
        const isCa = (pvState.country === 'CA');

        if (isUk) {
            wrap.innerHTML = `
                <button type="button" class="pv-sheet-chip active" onclick="pvSelectLocation('', 'Any Location (Optimal Route)', '')">★ Any Location</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('London')">London</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Manchester')">Manchester</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Birmingham')">Birmingham</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Leeds')">Leeds</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Glasgow')">Glasgow</button>
            `;
        } else if (isCa) {
            wrap.innerHTML = `
                <button type="button" class="pv-sheet-chip active" onclick="pvSelectLocation('', 'Any Location (Optimal Route)', '')">★ Any Location</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Toronto')">Toronto</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Montreal')">Montreal</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Vancouver')">Vancouver</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Calgary')">Calgary</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Ottawa')">Ottawa</button>
            `;
        } else {
            wrap.innerHTML = `
                <button type="button" class="pv-sheet-chip active" onclick="pvSelectLocation('', 'Any Location (Optimal Route)', '')">★ Any Location</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('California')">California</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('New York')">New York</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Texas')">Texas</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Florida')">Florida</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Chicago')">Chicago</button>
                <button type="button" class="pv-sheet-chip" onclick="pvQuickPickLocation('Seattle')">Seattle</button>
            `;
        }
    }

    function renderLocations(items) {
        const listEl = document.getElementById('pv-location-sheet-list');
        if (!listEl) return;
        listEl.innerHTML = '';

        if (!items || items.length === 0) {
            listEl.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748b; font-size: 14px;">No locations found matching your search.</div>';
            return;
        }

        items.forEach(loc => {
            const isSelected = (pvState.areaCode === loc.area_code) || (!pvState.areaCode && !loc.area_code);
            const row = document.createElement('div');
            row.className = 'pv-service-row blitz-btn' + (isSelected ? ' selected' : '');
            row.onclick = () => pvSelectLocation(loc.area_code, loc.name, loc.state);

            const badgeHtml = loc.badge 
                ? `<span style="background: ${loc.is_default ? '#2E8B3F' : '#EEF1F8'}; color: ${loc.is_default ? '#FFFFFF' : '#0F2A5A'}; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 6px;">${loc.badge}</span>` 
                : '';

            const codeHtml = loc.area_code 
                ? `<span style="font-family: monospace; font-size: 13px; font-weight: 800; color: #0F2A5A; background: rgba(15, 42, 90, 0.08); padding: 3px 8px; border-radius: 6px;">${loc.area_code}</span>` 
                : `<span style="font-size: 12px; color: #2E8B3F; font-weight: 700;">Optimal</span>`;

            row.innerHTML = `
                <div class="pv-service-left">
                    <div class="pv-service-avatar" style="background: rgba(15, 42, 90, 0.08); color: #0F2A5A;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="pv-service-title-wrap">
                        <div class="pv-service-name-row">
                            <span class="pv-service-name">${loc.name}</span>
                            ${badgeHtml}
                        </div>
                        <div class="pv-service-price-sub">${loc.state}</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    ${codeHtml}
                    <div class="pv-checkbox">
                        ${isSelected ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#022c22" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : ''}
                    </div>
                </div>
            `;
            listEl.appendChild(row);
        });
    }

    window.pvFilterLocations = function(term) {
        term = (term || '').toLowerCase().trim();
        if (!pvState.locationsList) return;
        if (!term) {
            renderLocations(pvState.locationsList);
            return;
        }
        const filtered = pvState.locationsList.filter(loc => 
            loc.name.toLowerCase().includes(term) ||
            loc.state.toLowerCase().includes(term) ||
            (loc.area_code && loc.area_code.includes(term))
        );
        renderLocations(filtered);
    };

    window.pvQuickPickLocation = function(term) {
        const inp = document.getElementById('pv-location-search-input');
        if (inp) inp.value = term;
        pvFilterLocations(term);
    };

    window.pvSelectLocation = function(areaCode, name, state) {
        pvState.areaCode = areaCode || '';
        pvState.locationName = name || 'Any Location (Recommended)';

        const triggerText = document.getElementById('pv-location-trigger-text');
        const triggerBadge = document.getElementById('pv-location-trigger-badge');
        const hiddenInp = document.getElementById('pv-area-code-input');

        if (triggerText) {
            triggerText.textContent = name || 'Any Location (Recommended)';
            triggerText.style.color = '#0F2A5A';
        }

        if (triggerBadge) {
            if (areaCode) {
                triggerBadge.style.display = 'inline-block';
                triggerBadge.textContent = areaCode;
            } else {
                triggerBadge.style.display = 'none';
            }
        }

        if (hiddenInp) {
            hiddenInp.value = areaCode || '';
        }

        pvCloseLocationSheet();
        updateSummary();
    };

    window.pvApplyCustomAreaCode = function() {
        const val = (document.getElementById('pv-custom-areacode-input')?.value || '').trim();
        if (!val) {
            pvSelectLocation('', 'Any Location (Recommended)', '');
            return;
        }
        pvSelectLocation(val, `Area Code (${val})`, 'Custom Area Code');
    };

    function getFallbackLocations(country) {
        if (country === 'UK' || country === 'GB') {
            return [
                { name: 'Any Location (Optimal Route)', state: 'UK National', area_code: '', badge: 'Recommended', is_default: true },
                { name: 'Greater London', state: 'London', area_code: '020', badge: 'High Capacity' },
                { name: 'Manchester', state: 'Greater Manchester', area_code: '0161', badge: 'Popular' },
                { name: 'Birmingham', state: 'West Midlands', area_code: '0121', badge: 'Popular' },
                { name: 'Leeds', state: 'West Yorkshire', area_code: '0113', badge: 'Fast' },
                { name: 'Glasgow', state: 'Scotland', area_code: '0141', badge: 'Fast' },
                { name: 'Liverpool', state: 'Merseyside', area_code: '0151', badge: 'Active' },
                { name: 'Edinburgh', state: 'Scotland', area_code: '0131', badge: 'Active' },
            ];
        }
        return [
            { name: 'Any Location (Optimal Route)', state: 'United States', area_code: '', badge: 'Recommended', is_default: true },
            { name: 'California (Los Angeles)', state: 'California', area_code: '213', badge: 'Popular' },
            { name: 'California (Beverly Hills / West LA)', state: 'California', area_code: '310', badge: 'High Demand' },
            { name: 'California (San Francisco)', state: 'California', area_code: '415', badge: 'Tech Hub' },
            { name: 'California (San Diego)', state: 'California', area_code: '619', badge: 'Fast' },
            { name: 'New York (Manhattan)', state: 'New York', area_code: '212', badge: 'High Demand' },
            { name: 'New York (NYC Metro)', state: 'New York', area_code: '646', badge: 'Popular' },
            { name: 'New York (Brooklyn / Queens)', state: 'New York', area_code: '718', badge: 'Popular' },
            { name: 'Texas (Houston)', state: 'Texas', area_code: '713', badge: 'Popular' },
            { name: 'Texas (Dallas)', state: 'Texas', area_code: '214', badge: 'Popular' },
            { name: 'Texas (Austin)', state: 'Texas', area_code: '512', badge: 'Tech Hub' },
            { name: 'Florida (Miami)', state: 'Florida', area_code: '305', badge: 'High Demand' },
            { name: 'Florida (Orlando)', state: 'Florida', area_code: '407', badge: 'Popular' },
            { name: 'Illinois (Chicago Downtown)', state: 'Illinois', area_code: '312', badge: 'Popular' },
            { name: 'Washington (Seattle)', state: 'Washington', area_code: '206', badge: 'Tech Hub' },
            { name: 'Georgia (Atlanta)', state: 'Georgia', area_code: '404', badge: 'Popular' },
            { name: 'District of Columbia (Washington DC)', state: 'DC', area_code: '202', badge: 'High Demand' },
        ];
    }

    // ── 9. Price / Stock Calculation in Naira ──
    function getPriceForItem(item) {
        if (!item) return 0;
        if (pvState.durationType === 'STR') {
            return parseFloat(item.price ?? 0);
        }
        return parseFloat(item[`ltr${pvState.durationDays}`] ?? item.price ?? 0);
    }

    function getPriceUsdForItem(item) {
        if (!item) return 0;
        if (pvState.durationType === 'STR') {
            return parseFloat(item.price_usd ?? 0);
        }
        return parseFloat(item[`ltr${pvState.durationDays}_usd`] ?? item.price_usd ?? 0);
    }

    function getStockForItem(item) {
        if (!item) return 0;
        if (pvState.durationType === 'STR') {
            return parseInt(item.stock ?? 0);
        }
        return parseInt(item.ltr_stock ?? item.stock ?? 0);
    }

    // ── 10. Update Summary & Button State (in Naira) ──
    function updateSummary() {
        const card = document.getElementById('pv-order-summary-card');
        const buyBtn = document.getElementById('pv-buy-button');
        const buyBtnText = document.getElementById('pv-buy-btn-text');
        const mainCta = document.getElementById('pv-main-cta');
        const mainCtaText = document.getElementById('pv-main-cta-text');

        if (!pvState.selectedWebsite) {
            if (card) card.style.display = 'none';
            if (buyBtn) buyBtn.disabled = true;
            if (buyBtnText) buyBtnText.textContent = 'Select website first';
            if (mainCta && !pvState.isAllServices) {
                mainCta.className = 'pv-main-cta-btn waiting blitz-btn';
                mainCta.disabled = false;
            }
            if (mainCtaText && !pvState.isAllServices) {
                mainCtaText.textContent = 'Choose a website to start';
            }
            return;
        }

        const item = pvState.selectedWebsite;
        const priceNaira = getPriceForItem(item);
        const priceUsd = getPriceUsdForItem(item);
        const stock = getStockForItem(item);
        const durText = pvState.durationType === 'STR' 
            ? '20 minutes (Short-Term)' 
            : `${pvState.durationDays} days (Long-Term)`;

        // Fill summary card matching Image 2
        if (card) card.style.display = 'flex';
        const srvEl = document.getElementById('pv-summary-service');
        if (srvEl) srvEl.textContent = item.name;
        const countryEl = document.getElementById('pv-summary-country');
        if (countryEl) countryEl.textContent = pvState.country === 'USA' ? 'United States' : (pvState.country === 'UK' ? 'United Kingdom' : pvState.country);
        const locSummary = document.getElementById('pv-summary-location');
        if (locSummary) {
            locSummary.textContent = pvState.areaCode ? `${pvState.locationName || 'Area'} (${pvState.areaCode})` : 'Any Location (Optimal)';
        }
        const durSummary = document.getElementById('pv-summary-duration');
        if (durSummary) durSummary.textContent = durText;

        const stockEl = document.getElementById('pv-summary-stock');
        if (stock > 0) {
            if (stockEl) {
                stockEl.textContent = `● In Stock (${stock > 5000 ? 'Plenty' : stock} ready)`;
                stockEl.style.color = '#2E8B3F';
            }
            if (buyBtn) buyBtn.disabled = false;
            if (buyBtnText) buyBtnText.textContent = `Get Number Now — ${formatNaira(priceNaira)}`;
            if (mainCta) {
                mainCta.className = 'pv-main-cta-btn ready blitz-btn';
                mainCta.disabled = false;
            }
            if (mainCtaText) {
                mainCtaText.textContent = `Buy ${item.name} · ${formatNaira(priceNaira)}`;
            }
        } else {
            if (stockEl) {
                stockEl.textContent = '● Out of Stock';
                stockEl.style.color = '#f87171';
            }
            if (buyBtn) buyBtn.disabled = true;
            if (buyBtnText) buyBtnText.textContent = 'Service Currently Out of Stock';
            if (mainCta) {
                mainCta.className = 'pv-main-cta-btn waiting blitz-btn';
                mainCta.disabled = true;
            }
            if (mainCtaText) {
                mainCtaText.textContent = 'Service Out of Stock';
            }
        }

        // Show price in Naira + USD reference
        const priceDisplay = document.getElementById('pv-summary-price-naira');
        if (priceDisplay) priceDisplay.textContent = formatNaira(priceNaira);
        const usdRefDisplay = document.getElementById('pv-summary-usd-ref');
        if (usdRefDisplay) usdRefDisplay.textContent = `Instant SMS Delivery (≈ $${priceUsd.toFixed(2)} USD)`;
    }

    // ── CTA Button Handler (routes to purchase or opens website sheet) ──
    window.pvHandleCtaClick = function(e) {
        if (window.blitzTap && e) window.blitzTap(e);

        // If All Services mode is active, directly execute purchase
        if (pvState.isAllServices && pvState.selectedWebsite) {
            pvExecutePurchase(e);
            return;
        }

        // If a specific website is selected, execute purchase
        if (pvState.selectedWebsite) {
            pvExecutePurchase(e);
            return;
        }

        // Otherwise open the website selection sheet
        pvOpenWebsiteSheet(e);
    };

    // ── 11. Stage 2 State & Timers ──
    let activeTimerInterval = null;
    let activePollInterval = null;

    // ── 12. Execute Purchase (Loads Next Stage) ──
    window.pvExecutePurchase = async function(e) {
        if (window.blitzTap && e) window.blitzTap(e);
        const isAllSrv = pvState.isAllServices;
        const buyBtn = isAllSrv 
            ? document.getElementById('pv-as-buy-btn') 
            : document.getElementById('pv-buy-button');
        const buyBtnText = isAllSrv 
            ? document.getElementById('pv-as-btn-text') 
            : document.getElementById('pv-buy-btn-text');
        const errBanner = document.getElementById('pv-error-banner');
        const errMsg = document.getElementById('pv-error-msg');

        if (!pvState.selectedWebsite && !isAllSrv) return;
        if (errBanner) errBanner.style.display = 'none';

        if (buyBtn) buyBtn.disabled = true;
        buyBtnText.innerHTML = `
            <div style="display: inline-block; width: 16px; height: 16px; border: 2.5px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: pvSpin 0.7s linear infinite;"></div>
            <span>Allocating Virtual Number...</span>
        `;

        const areaCode = pvState.areaCode || (document.getElementById('pv-area-code-input')?.value || '').trim() || null;
        const srvName = isAllSrv ? 'All services' : (pvState.selectedWebsite ? pvState.selectedWebsite.name : 'All Services');

        const payload = isAllSrv ? {
            country_id: pvState.countryId || 1,
            country_code: pvState.allServicesCountry || (pvState.country === 'USA' ? 'US' : pvState.country) || 'US',
            service_name: 'All services',
            duration: pvState.durationDays || ((pvState.allServicesCountry === 'US' || pvState.country === 'USA') ? 28 : 30),
        } : {
            country_id: pvState.countryId || (pvState.country === 'USA' ? 1 : 2),
            country_code: pvState.country === 'USA' ? 'US' : pvState.country,
            area_code: pvState.country === 'USA' ? areaCode : null,
            service_name: srvName,
            duration: pvState.durationType === 'LTR' ? (parseInt(pvState.durationDays) || 3) : 0,
        };

        const isPreview = window.location.pathname.includes('preview') || !csrfToken;

        try {
            const endpoint = getApiEndpoint('/verifications/request');
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                buyBtnText.textContent = '✓ Number Provisioned!';
                buyBtn.style.background = '#16a34a';
                setTimeout(() => {
                    pvShowActiveStage(data.data);
                }, 350);
            } else {
                if (isPreview) {
                    buyBtnText.textContent = '✓ Number Provisioned!';
                    buyBtn.style.background = '#16a34a';
                    setTimeout(() => {
                        pvShowActiveStage({
                            id: 'sim_' + Date.now(),
                            phone_number: '+1 (205) ' + Math.floor(200 + Math.random() * 700) + '-' + Math.floor(1000 + Math.random() * 9000),
                            service: { name: srvName },
                            expires_at: new Date(Date.now() + 20 * 60 * 1000).toISOString()
                        });
                    }, 350);
                    return;
                }

                const message = data.message || 'Unable to reserve number. Please try again or select another service.';
                if (errMsg && errBanner) {
                    errMsg.textContent = message;
                    errBanner.style.display = 'block';
                } else {
                    alert(message);
                }
                buyBtn.disabled = false;
                if (isAllSrv) {
                    renderAllServicesDedicatedCard();
                } else {
                    const priceNaira = getPriceForItem(pvState.selectedWebsite);
                    buyBtnText.textContent = `Get Number Now — ${formatNaira(priceNaira)}`;
                }
            }
        } catch (err) {
            if (isPreview) {
                buyBtnText.textContent = '✓ Number Provisioned!';
                buyBtn.style.background = '#16a34a';
                setTimeout(() => {
                    pvShowActiveStage({
                        id: 'sim_' + Date.now(),
                        phone_number: '+1 (205) ' + Math.floor(200 + Math.random() * 700) + '-' + Math.floor(1000 + Math.random() * 9000),
                        service: { name: srvName },
                        expires_at: new Date(Date.now() + 20 * 60 * 1000).toISOString()
                    });
                }, 350);
                return;
            }

            console.error('Purchase request failed', err);
            const msg = 'Connection error while communicating with provider gateway. Please retry.';
            if (errMsg && errBanner) {
                errMsg.textContent = msg;
                errBanner.style.display = 'block';
            } else {
                alert(msg);
            }
            buyBtn.disabled = false;
            if (isAllSrv) {
                renderAllServicesDedicatedCard();
            } else {
                const priceNaira = getPriceForItem(pvState.selectedWebsite);
                buyBtnText.textContent = `Get Number Now — ${formatNaira(priceNaira)}`;
            }
        }
    };

    // ── 13. Load Stage 2: Active Verification View ──
    window.pvShowActiveStage = function(sessionData) {
        const isAllSrv = pvState.isAllServices;
        const countryCode = pvState.allServicesCountry || (pvState.country === 'USA' ? 'US' : pvState.country);
        const cfg = allServicesCountryConfig[countryCode] || allServicesCountryConfig['US'];

        const srvTitle = isAllSrv 
            ? `All Services (${cfg.name})` 
            : (pvState.selectedWebsite?.name || sessionData?.service?.name || 'SMS Verification');
        const countryName = isAllSrv ? cfg.name : (pvState.country === 'USA' ? 'United States' : 'United Kingdom');
        const countryFlag = isAllSrv ? cfg.flag : (pvState.country === 'USA' ? '🇺🇸' : '🇬🇧');
        const durationLabel = isAllSrv 
            ? `${pvState.durationDays || cfg.defaultDuration} days rental` 
            : (pvState.durationType === 'STR' ? '20 mins rental' : `${pvState.durationDays || 30} days rental`);

        let rawPhone = (typeof sessionData?.phone_number === 'object' ? (sessionData?.phone_number?.e164 || sessionData?.phone_number?.phone_number) : sessionData?.phone_number)
            || sessionData?.phoneNumber?.phone_number 
            || sessionData?.phoneNumber?.e164
            || sessionData?.phoneNumber
            || '';

        const isPending = !rawPhone || String(rawPhone).includes('PENDING') || sessionData?.status === 'pending';

        pvState.activeSessionId = sessionData?.id || ('sess_' + Date.now());

        // Update elements
        document.getElementById('pv-active-service-title').textContent = srvTitle;
        document.getElementById('pv-active-country-name').textContent = `${countryName} · Carrier SIM`;
        document.getElementById('pv-active-country-flag').textContent = countryFlag;
        document.getElementById('pv-active-duration-badge').textContent = durationLabel;
        document.getElementById('pv-waiting-service-name').textContent = srvTitle;

        const phoneEl = document.getElementById('pv-active-phone-number');
        if (isPending) {
            phoneEl.textContent = 'Allocating Carrier Line... (Pending)';
            phoneEl.style.color = '#d97706';
        } else {
            phoneEl.textContent = rawPhone;
            phoneEl.style.color = '#0F2A5A';
        }

        // Reset SMS monitor state
        document.getElementById('pv-sms-waiting-box').style.display = 'block';
        document.getElementById('pv-sms-received-box').style.display = 'none';

        // Stage transition
        document.getElementById('pv-purchase-stage').style.display = 'none';
        const activeStage = document.getElementById('pv-active-stage');
        activeStage.style.display = 'block';

        // Scroll smoothly to top of container
        const root = document.getElementById('pv-container-root');
        if (root) root.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Start countdown timer
        const expiresAt = sessionData?.expires_at 
            ? new Date(sessionData.expires_at).getTime()
            : (Date.now() + 20 * 60 * 1000);
        pvStartCountdown(expiresAt);

        // Poll server for number delivery if pending, or for SMS if active
        if (sessionData?.id) {
            if (isPending) {
                pvPollForPendingNumber(sessionData.id);
            } else if (typeof sessionData.id === 'number') {
                pvPollForSms(sessionData.id);
            }
        }
    };

    function pvPollForPendingNumber(sessionId) {
        let attempts = 0;
        const maxAttempts = 20;
        const interval = setInterval(async () => {
            attempts++;
            try {
                const endpoint = getApiEndpoint(`/verifications/${sessionId}`);
                const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const json = await res.json();
                    const s = json.data;
                    const num = (typeof s?.phone_number === 'object' ? (s?.phone_number?.e164 || s?.phone_number?.phone_number) : s?.phone_number) || s?.phoneNumber?.phone_number;
                    if (num && !String(num).includes('PENDING')) {
                        clearInterval(interval);
                        const phoneEl = document.getElementById('pv-active-phone-number');
                        if (phoneEl) {
                            phoneEl.textContent = num;
                            phoneEl.style.color = '#0F2A5A';
                        }
                        pvPollForSms(sessionId);
                    }
                }
            } catch (e) {}

            if (attempts >= maxAttempts) {
                clearInterval(interval);
            }
        }, 2000);
    }

    // ── 14. Return to Stage 1 (Purchase Form) ──
    window.pvReturnToPurchaseStage = function() {
        if (activeTimerInterval) clearInterval(activeTimerInterval);
        if (activePollInterval) clearInterval(activePollInterval);

        document.getElementById('pv-active-stage').style.display = 'none';
        document.getElementById('pv-purchase-stage').style.display = 'block';

        const buyBtn = pvState.isAllServices 
            ? document.getElementById('pv-as-buy-btn') 
            : document.getElementById('pv-buy-button');
        const buyBtnText = pvState.isAllServices 
            ? document.getElementById('pv-as-btn-text') 
            : document.getElementById('pv-buy-btn-text');
        if (buyBtn) {
            buyBtn.disabled = false;
            buyBtn.style.background = '';
        }
        if (pvState.isAllServices) {
            renderAllServicesDedicatedCard();
        } else if (pvState.selectedWebsite) {
            const priceNaira = getPriceForItem(pvState.selectedWebsite);
            buyBtnText.textContent = `Get Number Now — ${formatNaira(priceNaira)}`;
        } else {
            buyBtnText.textContent = 'Select website first';
        }
    };

    // ── 15. Copy Phone Number ──
    window.pvCopyActivePhone = function() {
        const text = document.getElementById('pv-active-phone-number')?.textContent || '';
        if (!text) return;
        const btnText = document.getElementById('pv-copy-phone-text');
        
        const copySuccess = () => {
            if (btnText) {
                const orig = btnText.textContent;
                btnText.textContent = '✓ Copied!';
                setTimeout(() => { btnText.textContent = orig; }, 1800);
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(copySuccess).catch(() => fallbackCopy(text, copySuccess));
        } else {
            fallbackCopy(text, copySuccess);
        }
    };

    // ── 16. Copy OTP Code ──
    window.pvCopyActiveOtp = function() {
        const text = (document.getElementById('pv-active-otp-val')?.textContent || '').replace(/\s+/g, '');
        if (!text) return;
        const btnText = document.getElementById('pv-copy-otp-text');

        const copySuccess = () => {
            if (btnText) {
                const orig = btnText.textContent;
                btnText.textContent = '✓ Copied!';
                setTimeout(() => { btnText.textContent = orig; }, 1800);
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(copySuccess).catch(() => fallbackCopy(text, copySuccess));
        } else {
            fallbackCopy(text, copySuccess);
        }
    };

    function fallbackCopy(text, cb) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy');
            if (cb) cb();
        } catch (e) {}
        document.body.removeChild(ta);
    }

    // ── 17. Countdown Timer Engine ──
    function pvStartCountdown(targetMs) {
        if (activeTimerInterval) clearInterval(activeTimerInterval);
        const totalDurationMs = 20 * 60 * 1000;

        function tick() {
            const diff = targetMs - Date.now();
            if (diff <= 0) {
                const timerEl = document.getElementById('pv-active-timer');
                if (timerEl) timerEl.textContent = '00:00';
                const barEl = document.getElementById('pv-active-timer-bar');
                if (barEl) barEl.style.width = '0%';
                clearInterval(activeTimerInterval);
                return;
            }

            const mins = Math.floor(diff / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            const formatted = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

            const timerEl = document.getElementById('pv-active-timer');
            if (timerEl) timerEl.textContent = formatted;

            const barEl = document.getElementById('pv-active-timer-bar');
            if (barEl) {
                const pct = Math.max(0, Math.min(100, (diff / totalDurationMs) * 100));
                barEl.style.width = `${pct}%`;
            }
        }

        tick();
        activeTimerInterval = setInterval(tick, 1000);
    }

    // ── 18. SMS Polling Engine ──
    function pvPollForSms(sessionId) {
        if (activePollInterval) clearInterval(activePollInterval);
        if (!sessionId) return;

        activePollInterval = setInterval(async () => {
            try {
                const endpoint = getApiEndpoint(`/verifications/${sessionId}`);
                const res = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const json = await res.json();
                    const session = json.data;
                    if (session && session.verification_code) {
                        const body = session.sms_messages?.[0]?.body || session.latest_otp?.sms_message?.body;
                        pvDisplayReceivedOtp(session.verification_code, body);
                        clearInterval(activePollInterval);
                    }
                }
            } catch(e) {}
        }, 3000);
    }

    function pvDisplayReceivedOtp(code, messageBody) {
        document.getElementById('pv-sms-waiting-box').style.display = 'none';
        const recBox = document.getElementById('pv-sms-received-box');
        recBox.style.display = 'block';

        const otpVal = document.getElementById('pv-active-otp-val');
        if (otpVal) otpVal.textContent = code;

        const msgBody = document.getElementById('pv-sms-message-body');
        if (msgBody && messageBody) msgBody.textContent = messageBody;

        const timeEl = document.getElementById('pv-sms-received-time');
        if (timeEl) timeEl.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // ── 19. Simulate Incoming SMS (Sandbox Testing) ──
    window.pvSimulateIncomingSms = function() {
        const randomOtp = Math.floor(100000 + Math.random() * 900000);
        const srvName = pvState.isAllServices ? 'T-Mobile Service' : (pvState.selectedWebsite?.name || 'Online Service');
        const msg = `Your ${srvName} verification code is ${randomOtp}. Valid for 10 minutes. Do not share this code.`;
        pvDisplayReceivedOtp(String(randomOtp), msg);
    };

    // ── 20. Cancel Active Session & Refund ──
    window.pvCancelActiveSession = async function() {
        if (!confirm('Cancel this active number and refund held balance?')) return;
        const btn = document.getElementById('pv-active-cancel-btn');
        if (btn) { btn.disabled = true; btn.textContent = 'Refunding...'; }

        const sessionId = pvState.activeSessionId;
        if (sessionId && typeof sessionId === 'number') {
            try {
                const endpoint = getApiEndpoint(`/verifications/${sessionId}/cancel`);
                await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    }
                });
            } catch(e) {}
        }

        pvReturnToPurchaseStage();
    };

    // Auto-fetch catalog on load & render initial popular apps grid
    document.addEventListener('DOMContentLoaded', () => {
        renderPopularAppsGrid();
        pvFetchCatalog();
    });
})();
</script>
<?php /**PATH C:\xampp\htdocs\segyrictext\resources\views/components/purchase-number-section.blade.php ENDPATH**/ ?>