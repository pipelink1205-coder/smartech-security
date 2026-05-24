@props(['variant' => 'default', 'showText' => true])

<a {{ $attributes->merge(['class' => 'brand-logo brand-logo--' . $variant]) }}>
    <img
        src="{{ asset('images/logo.png') }}"
        alt="Smart Tech Security"
        class="brand-logo__mark"
        width="52"
        height="52"
        loading="eager"
    />
    @if($showText)
        <span class="brand-logo__text">
            <span class="brand-logo__name">SMART TECH SECURITY</span>
            <span class="brand-logo__tagline">Proveedor de Sistemas de Seguridad</span>
        </span>
    @endif
</a>
