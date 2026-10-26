@php
    $cover ??= \App\Models\HomeCover::current();
    $primaryHref = $cover->primaryHref();
    $secondaryHref = $cover->secondaryHref();
    $primaryLink = $cover->pageLinkFor($primaryHref);
    $secondaryLink = $cover->pageLinkFor($secondaryHref);
    $secondaryIntent = $cover->secondaryQuoteIntent();
@endphp

<x-section tone="hero" class="hero-block">
    @if($cover->isPreview())
        <div class="cover-preview-banner" role="status">
            Vista previa · {{ $cover->name }}
            <span>{{ $cover->kindLabel() }}</span>
        </div>
    @endif
    <div class="hero-pattern" aria-hidden="true"></div>
    <div class="hero-grid">
        <div class="hero-content">
            @if(filled($cover->badge))
                <div class="hero-badge">{{ $cover->badge }}</div>
            @endif
            <h1 class="hero-h1">{{ $cover->title }}</h1>
            @if(filled($cover->subtitle))
                <p class="hero-p">{{ $cover->subtitle }}</p>
            @endif
            <div class="hero-btns">
                <a href="{{ $primaryHref }}" class="btn btn-primary"{{ $primaryLink ? ' data-page-link="'.$primaryLink.'"' : '' }}>{{ $cover->cta_primary_label }}</a>
                <a href="{{ $secondaryHref }}" class="btn btn-outline"{{ $secondaryLink ? ' data-page-link="'.$secondaryLink.'"' : '' }}{{ $secondaryIntent ? ' data-quote-intent="'.$secondaryIntent.'"' : '' }}>{{ $cover->cta_secondary_label }}</a>
            </div>
            @if($cover->displayStats() !== [])
                <div class="hero-stats">
                    @foreach($cover->displayStats() as $stat)
                        <div>
                            <div class="hero-stat-num">{{ $stat['num'] ?? '' }}</div>
                            <div class="hero-stat-label">{{ $stat['label'] ?? '' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="hero-image">
            <div class="hero-img-wrap glass-card">
                <img src="{{ $cover->image_url }}" alt="{{ $cover->image_alt ?: $cover->title }}" loading="eager" />
            </div>
        </div>
    </div>
</x-section>
