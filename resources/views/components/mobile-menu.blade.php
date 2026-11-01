@php($mobileGroups = \App\Models\Service::navGroups())
<div class="mobile-menu" id="mobile-menu">
    <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Cerrar menú">✕</button>
    <x-logo href="{{ route('home') }}#inicio" class="mobile-menu-brand" :show-text="true" data-page-link="inicio" />

    <details class="mm-group">
        <summary>Servicios</summary>
        <div class="mm-group__body">
            @foreach($mobileGroups as $label => $items)
                <p class="mm-group__title">{{ $label }}</p>
                @foreach($items as $navService)
                    <a href="{{ route('servicios.show', $navService->slug) }}" class="mm-link">
                        <x-service-icon :icon="$navService->icon" :name="$navService->name" size="sm" />
                        <span>{{ $navService->name }}</span>
                    </a>
                @endforeach
            @endforeach
            <a href="{{ route('servicios') }}" class="mm-link mm-link--all">Ver todos los servicios →</a>
        </div>
    </details>

    <details class="mm-group" @if(request()->routeIs('ph.*')) open @endif>
        <summary>Soluciones</summary>
        <div class="mm-group__body">
            <a href="{{ route('ph.hub') }}" class="mm-solution">
                <x-ph-icon name="building" :size="20" />
                <span><strong>Unidades residenciales</strong><small>Cámaras, accesos y citofonía</small></span>
            </a>
            <a href="{{ route('ph.diagnostico') }}" class="mm-link mm-link--indent">Diagnóstico gratuito →</a>
            <a href="{{ route('servicios.show', 'iptv-hoteles') }}" class="mm-solution">
                <x-ph-icon name="bed" :size="20" />
                <span><strong>Hoteles</strong><small>IPTV y entretenimiento</small></span>
            </a>
            <a href="{{ route('servicios.show', 'outsourcing-ti') }}" class="mm-solution">
                <x-ph-icon name="briefcase" :size="20" />
                <span><strong>Empresas</strong><small>Outsourcing TI y ciberseguridad</small></span>
            </a>
            <a href="{{ route('servicios.show', 'desarrollo-software') }}" class="mm-link mm-link--indent">Desarrollo de software →</a>
        </div>
    </details>

    <a href="{{ route('home') }}#proyectos" data-page-link="proyectos">Proyectos</a>
    <a href="{{ route('home') }}#contacto" data-page-link="contacto">Contacto</a>
    <a href="{{ route('home') }}?intent=visit#contacto" class="btn btn-primary mm-cta" data-page-link="contacto" data-quote-intent="visit">Agendar visita</a>
</div>
