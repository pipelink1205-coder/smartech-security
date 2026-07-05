@props([
    'icon' => null,
    'name' => null,
    'size' => 'md',
])

@php
    use App\Support\ServiceIcon;

    $svgKey = ServiceIcon::svgKey($icon);
    $rawIcon = trim((string) ($icon ?? ''));
    $sizeClass = match ($size) {
        'sm' => 'service-icon-flat--sm',
        'lg' => 'service-icon-flat--lg',
        default => 'service-icon-flat--md',
    };
@endphp

<span {{ $attributes->merge(['class' => "service-icon-flat {$sizeClass}"]) }} aria-hidden="true">
    @if($svgKey)
        @include('components.partials.service-icon-svg', ['key' => $svgKey])
    @elseif($rawIcon !== '')
        <span class="service-icon-emoji">{{ $rawIcon }}</span>
    @else
        <span class="service-icon-monogram">{{ ServiceIcon::monogramLetter($name) }}</span>
    @endif
</span>
