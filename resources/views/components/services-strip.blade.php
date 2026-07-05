<div class="services-strip">
    <div class="container">
        <p class="strip-label">{{ config('site.strip_label') }}</p>
        <div class="services-strip-inner">
            @foreach(config('site.strip_highlights') as $item)
                <div class="strip-item"><span class="strip-dot"></span>{{ $item }}</div>
            @endforeach
        </div>
    </div>
</div>
