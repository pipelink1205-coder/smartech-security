@props([
    'tag' => null,
    'title' => null,
    'highlight' => null,
    'subtitle' => null,
    'align' => 'center',
])

<div {{ $attributes->merge(['class' => 'section-header section-header--' . $align]) }}>
    @if($tag)
        <span class="section-tag">{{ $tag }}</span>
    @endif

    @if(isset($heading))
        {{ $heading }}
    @elseif($title)
        <h2 class="section-title">
            {{ $title }}@if($highlight) <span>{{ $highlight }}</span>@endif
        </h2>
    @endif

    @if($subtitle)
        <p class="section-sub">{{ $subtitle }}</p>
    @endif
</div>
