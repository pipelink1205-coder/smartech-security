@props(['brand'])

@php
    $sources = \App\Support\BrandLogo::sources($brand);
    $src = $sources[0] ?? null;
    $fallbacks = array_slice($sources, 1);
    $initial = mb_strtoupper(mb_substr(trim($brand), 0, 1));
@endphp

<li class="sd-brand {{ $src ? '' : 'sd-brand--fallback' }}" title="{{ $brand }}">
    @if($src)
        <img
            src="{{ $src }}"
            alt="Logo {{ $brand }}"
            loading="lazy"
            width="26"
            height="26"
            decoding="async"
            @if($fallbacks !== [])
                data-fallbacks="{{ e(json_encode($fallbacks)) }}"
            @endif
            onerror="window.brandLogoFallback?.(this)"
        />
    @endif
    <span class="sd-brand__mono" aria-hidden="true">{{ $initial }}</span>
    <span class="sd-brand__name">{{ $brand }}</span>
</li>
