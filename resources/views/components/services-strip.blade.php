<div class="services-strip">
    <div class="container">
        <p class="strip-label">Cobertura total en el Valle de Aburrá</p>
        <div class="services-strip-inner">
            @foreach(config('site.strip_zones') as $item)
                <div class="strip-item"><span class="strip-dot"></span>{{ $item }}</div>
            @endforeach
        </div>
    </div>
</div>
