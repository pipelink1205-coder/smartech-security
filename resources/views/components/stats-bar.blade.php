<x-section tone="gradient" class="stats-section">
    <div class="stats-grid">
        @foreach(config('site.stats') as $stat)
            <div>
                <div class="stat-num">{{ $stat['num'] }}</div>
                <div class="stat-label">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>
</x-section>
