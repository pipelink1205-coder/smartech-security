@php
    $cover ??= \App\Models\HomeCover::current();
@endphp

<div class="services-strip">
    <div class="container">
        <p class="strip-label">{{ $cover->displayStripLabel() }}</p>
        <div class="services-strip-inner">
            @foreach($cover->displayHighlights() as $item)
                <div class="strip-item"><span class="strip-dot"></span>{{ $item }}</div>
            @endforeach
        </div>
    </div>
</div>
