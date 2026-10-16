@props([
    'size' => 48,
    'class' => '',
    'glow' => true,
    'badge' => true,
])

@php
    $logoSrc = asset('images/verixa-logo.jpg');
    $innerSize = $badge ? intval($size * 0.88) : $size;
    $radius = $badge ? ($size >= 64 ? '22px' : '14px') : '50%';
@endphp

<div class="verixa-logo-badge {{ $class }}" 
     style="width: {{ $size }}px; height: {{ $size }}px; position: relative; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
            @if($badge)
                border-radius: {{ $radius }};
                background: linear-gradient(145deg, rgba(15, 10, 40, 0.9) 0%, rgba(8, 5, 25, 0.95) 100%);
                border: 1.5px solid rgba(124, 58, 237, 0.45);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5){{ $glow ? ', 0 0 20px rgba(124, 58, 237, 0.35)' : '' }};
            @endif">
    <img src="{{ $logoSrc }}"
         alt="Verixa"
         width="{{ $innerSize }}"
         height="{{ $innerSize }}"
         loading="eager"
         decoding="async"
         style="width: {{ $innerSize }}px; height: {{ $innerSize }}px; object-fit: contain; display: block; border-radius: 50%;"
         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
    {{-- High-reliability inline SVG fallback --}}
    <svg viewBox="0 0 100 100" width="{{ $innerSize }}" height="{{ $innerSize }}" style="display: none; width: {{ $innerSize }}px; height: {{ $innerSize }}px; border-radius: 50%;">
        <defs>
            <linearGradient id="bgGrad-{{ $size }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#0F0A28"/>
                <stop offset="100%" stop-color="#080519"/>
            </linearGradient>
            <linearGradient id="rimGrad-{{ $size }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#7C3AED"/>
                <stop offset="50%" stop-color="#4F46E5"/>
                <stop offset="100%" stop-color="#06B6D4"/>
            </linearGradient>
            <linearGradient id="accentGrad-{{ $size }}" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#A78BFA"/>
                <stop offset="100%" stop-color="#7C3AED"/>
            </linearGradient>
        </defs>
        <circle cx="50" cy="50" r="48" fill="url(#bgGrad-{{ $size }})" stroke="url(#rimGrad-{{ $size }})" stroke-width="3"/>
        <circle cx="50" cy="50" r="43" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>
        {{-- Shield + Checkmark --}}
        <path d="M50 18 L72 30 L72 50 C72 65 62 76 50 82 C38 76 28 65 28 50 L28 30 Z" fill="none" stroke="url(#accentGrad-{{ $size }})" stroke-width="2.5"/>
        <path d="M38 50 L46 58 L62 42" fill="none" stroke="#06B6D4" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        <rect x="16" y="44" width="68" height="12" rx="2" fill="#0F0A28" stroke="rgba(124,58,237,0.6)" stroke-width="1.2"/>
        <text x="50" y="52.8" font-family="-apple-system, BlinkMacSystemFont, 'Inter', sans-serif" font-size="7" font-weight="900" fill="#ffffff" text-anchor="middle" letter-spacing="0.5">VERIXA</text>
    </svg>
</div>
