<section class="stats-bar">
    <div class="container stats-grid">
        @foreach(config('site.stats') as $stat)
            <div>
                <div class="stat-num">{{ $stat['num'] }}</div>
                <div class="stat-label">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>
</section>
