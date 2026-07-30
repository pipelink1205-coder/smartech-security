@props([
    'title' => 'Partners y tecnologías',
    'subtitle' => 'Trabajamos con fabricantes especializados en firewall, endpoints, backup y virtualización para proteger la operación de tu empresa.',
])

<section class="partners-section" aria-labelledby="partners-heading">
    <header class="sd-block-head partners-section__head">
        <span class="sd-block-tag">Alianzas</span>
        <h2 class="sd-block-title" id="partners-heading">{{ $title }}</h2>
        @if($subtitle)
            <p class="partners-section__sub">{{ $subtitle }}</p>
        @endif
    </header>

    <ul class="partners-grid">
        @foreach(config('partners') as $partner)
            <li>
                <a
                    href="{{ $partner['url'] }}"
                    class="partner-card glass-card"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="{{ $partner['name'] }}"
                >
                    <span class="partner-card__badge">{{ $partner['badge'] }}</span>
                    <span class="partner-card__logo">
                        <img
                            src="{{ asset($partner['logo']) }}"
                            alt="{{ $partner['name'] }}"
                            loading="lazy"
                            width="140"
                            height="48"
                        />
                    </span>
                    <span class="partner-card__name">{{ $partner['name'] }}</span>
                    <span class="partner-card__role">{{ $partner['role'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</section>
