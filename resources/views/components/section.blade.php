@props([
    'tag' => null,
    'title' => null,
    'highlight' => null,
    'subtitle' => null,
    'tone' => 'default',
    'contained' => true,
])

<section {{ $attributes->merge(['class' => 'site-section site-section--' . $tone]) }}>
    @if($contained)
        <div class="container">
            @if($tag || $title || $subtitle || isset($heading))
                <div class="section-header section-header--center">
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
            @endif

            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</section>
