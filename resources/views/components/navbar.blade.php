@php
    $navGroups = \App\Models\Service::navGroups();
    $solutionSlugs = ['iptv-hoteles', 'outsourcing-ti', 'desarrollo-software'];
    $currentSlug = request()->route('service')?->slug ?? null;
    $onSolutions = request()->routeIs('ph.*') || in_array($currentSlug, $solutionSlugs, true);
    $onServices = ! $onSolutions && request()->routeIs('servicios', 'servicios.show');
    $caret = '<svg class="nav-caret" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>';
@endphp
<nav id="navbar">
    <div class="container nav-inner">
        <x-logo href="{{ route('home') }}#inicio" class="nav-brand" data-page-link="inicio" />

        <ul class="nav-links">
            {{-- ═══ SERVICIOS: catálogo por categoría ═══ --}}
            <li class="nav-item has-dropdown">
                <a href="{{ route('servicios') }}" @class(['nav-link-trigger', 'active' => $onServices]) aria-haspopup="true">
                    Servicios {!! $caret !!}
                </a>
                <div class="nav-dropdown nav-mega" role="menu" aria-label="Servicios por categoría">
                    <div class="nav-mega__cols">
                        @foreach($navGroups as $label => $items)
                            <div class="nav-mega__col">
                                <p class="nav-mega__title">{{ $label }}</p>
                                @foreach($items as $navService)
                                    <a href="{{ route('servicios.show', $navService->slug) }}" role="menuitem">
                                        <x-service-icon :icon="$navService->icon" :name="$navService->name" size="sm" />
                                        <span>{{ $navService->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="nav-mega__foot">
                        <a href="{{ route('servicios') }}" class="nav-mega__all" role="menuitem">Ver todos los servicios →</a>
                        <span class="nav-mega__hint">¿No sabe qué necesita? <a href="{{ route('home') }}?intent=visit#contacto" data-page-link="contacto" data-quote-intent="visit" role="menuitem">Agende una visita técnica</a></span>
                    </div>
                </div>
            </li>

            {{-- ═══ SOLUCIONES: por tipo de cliente ═══ --}}
            <li class="nav-item has-dropdown">
                <a href="{{ route('ph.hub') }}" @class(['nav-link-trigger', 'active' => $onSolutions]) aria-haspopup="true">
                    Soluciones {!! $caret !!}
                </a>
                <div class="nav-dropdown nav-solutions" role="menu" aria-label="Soluciones por sector">
                    <div class="nav-solution">
                        <a href="{{ route('ph.hub') }}" class="nav-solution__main" role="menuitem">
                            <span class="nav-solution__icon"><x-ph-icon name="building" :size="22" /></span>
                            <span class="nav-solution__text">
                                <strong>Unidades residenciales</strong>
                                <small>Cámaras, control de acceso y citofonía para administradores y consejos</small>
                            </span>
                        </a>
                        <a href="{{ route('ph.diagnostico') }}" class="nav-solution__sub" role="menuitem">Diagnóstico de seguridad gratuito →</a>
                    </div>
                    <div class="nav-solution">
                        <a href="{{ route('servicios.show', 'iptv-hoteles') }}" class="nav-solution__main" role="menuitem">
                            <span class="nav-solution__icon"><x-ph-icon name="bed" :size="22" /></span>
                            <span class="nav-solution__text">
                                <strong>Hoteles</strong>
                                <small>IPTV con más de 200 canales, video bajo demanda e integración PMS</small>
                            </span>
                        </a>
                        <a href="{{ route('home') }}#iptv" class="nav-solution__sub" data-page-link="iptv" role="menuitem">Ver presentación IPTV →</a>
                    </div>
                    <div class="nav-solution">
                        <a href="{{ route('servicios.show', 'outsourcing-ti') }}" class="nav-solution__main" role="menuitem">
                            <span class="nav-solution__icon"><x-ph-icon name="briefcase" :size="22" /></span>
                            <span class="nav-solution__text">
                                <strong>Empresas</strong>
                                <small>Outsourcing de TI, ciberseguridad y software a la medida</small>
                            </span>
                        </a>
                        <span class="nav-solution__subs">
                            <a href="{{ route('servicios.show', 'outsourcing-ti') }}" class="nav-solution__sub" role="menuitem">Outsourcing TI</a>
                            <a href="{{ route('servicios.show', 'desarrollo-software') }}" class="nav-solution__sub" role="menuitem">Desarrollo de software</a>
                        </span>
                    </div>
                </div>
            </li>

            <li><a href="{{ route('home') }}#proyectos" data-page-link="proyectos">Proyectos</a></li>
            <li><a href="{{ route('home') }}#contacto" data-page-link="contacto">Contacto</a></li>
        </ul>

        <a href="{{ route('home') }}?intent=visit#contacto" class="btn btn-primary nav-cta" data-page-link="contacto" data-quote-intent="visit">Agendar visita</a>

        <button class="hamburger" id="hamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
