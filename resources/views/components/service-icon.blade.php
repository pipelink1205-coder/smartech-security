@props([
    'slug' => null,
    'name' => null,
    'size' => 'md',
])

@php
    use App\Support\ServiceIcon;

    $iconKey = ServiceIcon::resolve($slug, $name);
    $sizeClass = match ($size) {
        'sm' => 'service-icon-flat--sm',
        'lg' => 'service-icon-flat--lg',
        default => 'service-icon-flat--md',
    };
@endphp

<span {{ $attributes->merge(['class' => "service-icon-flat {$sizeClass}"]) }} aria-hidden="true">
    @if($iconKey === 'monogram')
        <span class="service-icon-monogram">{{ ServiceIcon::monogramLetter($name) }}</span>
    @else
        @include('components.partials.service-icon-svg', ['key' => $iconKey])
    @endif
</span>
