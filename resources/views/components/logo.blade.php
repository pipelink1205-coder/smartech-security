@props(['variant' => 'default', 'showText' => true])

@php
    $mark = file_exists(public_path('images/logo-transparent.png'))
        ? 'images/logo-transparent.png'
        : 'images/logo.png';
@endphp

<a {{ $attributes->merge(['class' => 'brand-logo brand-logo--' . $variant]) }}>
    <img
        src="{{ asset($mark) }}"
        alt="Smart Tech Security"
        class="brand-logo__mark"
        width="64"
        height="64"
        loading="eager"
    />
    @if($showText)
        <span class="brand-logo__text">
            <span class="brand-logo__name">SMART TECH SECURITY</span>
            <span class="brand-logo__tagline">Tecnología y seguridad para empresas y hogares</span>
        </span>
    @endif
</a>
