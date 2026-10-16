@props([
    'iso' => 'US',
    'size' => 20,
    'class' => '',
])

@php
    $iso = strtoupper(trim($iso));
    $flagEmoji = '';
    foreach (str_split($iso) as $char) {
        $flagEmoji .= mb_chr(127397 + ord($char), 'UTF-8');
    }
@endphp

<span class="country-flag-badge {{ $class }}" data-iso="{{ $iso }}" style="display: inline-flex; align-items: center; justify-content: center; width: {{ $size }}px; height: {{ intval($size * 0.75) }}px; border-radius: 3px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.3); flex-shrink: 0; font-size: {{ $size * 0.9 }}px; line-height: 1;">
@switch($iso)
    @case('US')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <g fill-rule="evenodd">
                <path fill="#bd3d44" d="M0 0h640v480H0z"/>
                <path stroke="#fff" stroke-width="37" d="M0 55.4h640M0 129.2h640M0 203h640M0 277h640M0 350.8h640M0 424.6h640"/>
                <path fill="#192f5d" d="M0 0h296v258.5H0z"/>
                <g fill="#fff">
                    <circle cx="40" cy="30" r="8"/><circle cx="100" cy="30" r="8"/><circle cx="160" cy="30" r="8"/><circle cx="220" cy="30" r="8"/>
                    <circle cx="70" cy="65" r="8"/><circle cx="130" cy="65" r="8"/><circle cx="190" cy="65" r="8"/><circle cx="250" cy="65" r="8"/>
                    <circle cx="40" cy="100" r="8"/><circle cx="100" cy="100" r="8"/><circle cx="160" cy="100" r="8"/><circle cx="220" cy="100" r="8"/>
                    <circle cx="70" cy="135" r="8"/><circle cx="130" cy="135" r="8"/><circle cx="190" cy="135" r="8"/><circle cx="250" cy="135" r="8"/>
                    <circle cx="40" cy="170" r="8"/><circle cx="100" cy="170" r="8"/><circle cx="160" cy="170" r="8"/><circle cx="220" cy="170" r="8"/>
                    <circle cx="70" cy="205" r="8"/><circle cx="130" cy="205" r="8"/><circle cx="190" cy="205" r="8"/><circle cx="250" cy="205" r="8"/>
                </g>
            </g>
        </svg>
        @break

    @case('GB')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <path fill="#012169" d="M0 0h640v480H0z"/>
            <path fill="#FFF" d="m75 0 245 180L565 0h75v50L440 240l200 150v90h-75L320 300 75 480H0v-50l200-150L0 50V0h75z"/>
            <path fill="#C8102E" d="m420 280 220 160v40L390 300l30-20zm-200-80L0 40V0l250 180-30 20zM640 0 390 185l20 25L640 40V0zM0 440l250-185-20-25L0 400v40z"/>
            <path fill="#FFF" d="M260 0h120v480H260zM0 180h640v120H0z"/>
            <path fill="#C8102E" d="M280 0h80v480H280zM0 200h640v80H0z"/>
        </svg>
        @break

    @case('CA')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <path fill="#f00" d="M0 0h160v480H0zm480 0h160v480H480z"/>
            <path fill="#fff" d="M160 0h320v480H160z"/>
            <path fill="#f00" d="m320 70 20 50 40-20-10 40 40 10-30 30 30 40-50-10-10 40-20-30-10 120h-20l-10-120-20 30-10-40-50 10 30-40-30-30 40-10-10-40 40 20z"/>
        </svg>
        @break

    @case('NG')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <g fill-rule="evenodd">
                <path fill="#12833b" d="M0 0h213.3v480H0zm426.7 0H640v480H426.7z"/>
                <path fill="#fff" d="M213.3 0h213.4v480H213.3z"/>
            </g>
        </svg>
        @break

    @case('DE')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <path fill="#000" d="M0 0h640v160H0z"/>
            <path fill="#d00" d="M0 160h640v160H0z"/>
            <path fill="#ffce00" d="M0 320h640v160H0z"/>
        </svg>
        @break

    @case('FR')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <path fill="#002395" d="M0 0h213.3v480H0z"/>
            <path fill="#fff" d="M213.3 0h213.4v480H213.3z"/>
            <path fill="#ed2939" d="M426.7 0H640v480H426.7z"/>
        </svg>
        @break

    @case('NL')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <path fill="#ae1c28" d="M0 0h640v160H0z"/>
            <path fill="#fff" d="M0 160h640v160H0z"/>
            <path fill="#21468b" d="M0 320h640v160H0z"/>
        </svg>
        @break

    @case('BR')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <path fill="#009c3b" d="M0 0h640v480H0z"/>
            <path fill="#ffdf00" d="m320 40 280 200-280 200L40 240z"/>
            <circle cx="320" cy="240" r="110" fill="#002776"/>
            <path fill="#fff" d="M215 240c0-50 90-80 210-40-10-15-90-25-180 10-10 10-20 20-30 30z"/>
        </svg>
        @break

    @case('IN')
        <svg viewBox="0 0 640 480" width="{{ $size }}" height="{{ intval($size * 0.75) }}">
            <path fill="#f93" d="M0 0h640v160H0z"/>
            <path fill="#fff" d="M0 160h640v160H0z"/>
            <path fill="#128807" d="M0 320h640v160H0z"/>
            <circle cx="320" cy="240" r="45" fill="none" stroke="#008" stroke-width="6"/>
            <circle cx="320" cy="240" r="10" fill="#008"/>
        </svg>
        @break

    @default
        <span style="display: inline-block;">{{ $flagEmoji }}</span>
@endswitch
</span>
