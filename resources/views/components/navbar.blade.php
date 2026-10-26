<nav id="navbar">
    <div class="container nav-inner">
        <x-logo href="{{ route('home') }}#inicio" class="nav-brand" data-page-link="inicio" />

        <ul class="nav-links">
            <li><a href="{{ route('home') }}#inicio" data-page-link="inicio">Inicio</a></li>
            <li class="nav-item has-dropdown">
                <a href="{{ route('servicios') }}" class="nav-link-trigger" aria-haspopup="true">
                    Servicios
                    <svg class="nav-caret" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </a>
                <div class="nav-dropdown" role="menu" aria-label="Más servicios">
                    @foreach(\App\Models\Service::extraForNavMenu() as $navService)
                        <a href="{{ route('servicios.show', $navService->slug) }}" role="menuitem">
                            <x-service-icon :icon="$navService->icon" :name="$navService->name" size="sm" />
                            <span>{{ $navService->name }}</span>
                        </a>
                    @endforeach
                    <a href="{{ route('servicios') }}" class="nav-dropdown__all" role="menuitem">Ver todos los servicios</a>
                </div>
            </li>
            <li class="nav-item has-dropdown">
                <a href="{{ route('home') }}#iptv" class="nav-link-trigger" data-page-link="iptv" aria-haspopup="true">
                    IPTV Hoteles
                    <svg class="nav-caret" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </a>
                <div class="nav-dropdown nav-dropdown--compact" role="menu" aria-label="IPTV para hoteles">
                    <a href="{{ route('home') }}#iptv" data-page-link="iptv" role="menuitem">
                        <x-service-icon icon="tv" name="IPTV" size="sm" />
                        <span>Presentación IPTV</span>
                    </a>
                    <a href="{{ route('servicios.show', 'iptv-hoteles') }}" role="menuitem">
                        <x-service-icon icon="tv" name="IPTV" size="sm" />
                        <span>Ficha del servicio</span>
                    </a>
                    <a href="{{ route('servicios.show', 'iptv-hoteles') }}#sd-inc" role="menuitem">
                        <span>+200 canales HD</span>
                    </a>
                    <a href="{{ route('servicios.show', 'iptv-hoteles') }}#sd-inc" role="menuitem">
                        <span>Video On Demand</span>
                    </a>
                    <a href="{{ route('servicios.show', 'iptv-hoteles') }}#sd-inc" role="menuitem">
                        <span>Integración PMS</span>
                    </a>
                    <a href="{{ route('servicios.show', 'iptv-hoteles') }}#sd-inc" role="menuitem">
                        <span>WiFi por habitación</span>
                    </a>
                    <a href="{{ route('servicios.show', 'iptv-hoteles') }}#sd-faq" role="menuitem">
                        <span>Preguntas frecuentes</span>
                    </a>
                    <a href="{{ route('contacto', ['servicio' => 'IPTV para Hoteles']) }}" class="nav-dropdown__all" role="menuitem">Solicitar información</a>
                </div>
            </li>
            <li class="nav-item has-dropdown">
                <a href="{{ route('servicios.show', 'outsourcing-ti') }}" class="nav-link-trigger" aria-haspopup="true">
                    Outsourcing TI
                    <svg class="nav-caret" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </a>
                <div class="nav-dropdown nav-dropdown--compact" role="menu" aria-label="Outsourcing de TI">
                    <a href="{{ route('servicios.show', 'outsourcing-ti') }}" role="menuitem">
                        <x-service-icon icon="support" name="Outsourcing de TI" size="sm" />
                        <span>Ficha del servicio</span>
                    </a>
                    <a href="{{ route('servicios.show', 'outsourcing-ti') }}#comparador" role="menuitem">
                        <span>Calcula tu ahorro</span>
                    </a>
                    <a href="{{ route('servicios.show', 'outsourcing-ti') }}#planes" role="menuitem">
                        <span>Planes</span>
                    </a>
                    <a href="{{ route('servicios.show', 'outsourcing-ti') }}#ot-inc" role="menuitem">
                        <span>Mesa de ayuda</span>
                    </a>
                    <a href="{{ route('servicios.show', 'outsourcing-ti') }}#ot-inc" role="menuitem">
                        <span>Ciberseguridad incluida</span>
                    </a>
                    <a href="{{ route('servicios.show', 'outsourcing-ti') }}#ot-faq" role="menuitem">
                        <span>Preguntas frecuentes</span>
                    </a>
                    <a href="{{ route('servicios.show', 'outsourcing-ti') }}#diagnostico" class="nav-dropdown__all" role="menuitem">Diagnóstico gratuito</a>
                </div>
            </li>
            <li class="nav-item has-dropdown">
                <a href="{{ route('servicios.show', 'desarrollo-software') }}" class="nav-link-trigger" aria-haspopup="true">
                    Desarrollo de Software
                    <svg class="nav-caret" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </a>
                <div class="nav-dropdown nav-dropdown--compact" role="menu" aria-label="Desarrollo de Software">
                    <a href="{{ route('servicios.show', 'desarrollo-software') }}" role="menuitem">
                        <x-service-icon icon="code" name="Desarrollo de Software" size="sm" />
                        <span>Ficha del servicio</span>
                    </a>
                    <a href="{{ route('servicios.show', 'desarrollo-software') }}#sw-solutions" role="menuitem">
                        <span>Software a la medida</span>
                    </a>
                    <a href="{{ route('servicios.show', 'desarrollo-software') }}#sw-solutions" role="menuitem">
                        <span>Aplicaciones web</span>
                    </a>
                    <a href="{{ route('servicios.show', 'desarrollo-software') }}#sw-analytics" role="menuitem">
                        <span>Analítica de datos</span>
                    </a>
                    <a href="{{ route('servicios.show', 'desarrollo-software') }}#sw-ai" role="menuitem">
                        <span>Inteligencia Artificial</span>
                    </a>
                    <a href="{{ route('servicios.show', 'desarrollo-software') }}#sw-faq" role="menuitem">
                        <span>Preguntas frecuentes</span>
                    </a>
                    <a href="{{ route('contacto', ['servicio' => 'Desarrollo de Software e Inteligencia de Datos']) }}" class="nav-dropdown__all" role="menuitem">Solicitar diagnóstico</a>
                </div>
            </li>
            <li><a href="{{ route('home') }}#proyectos" data-page-link="proyectos">Proyectos</a></li>
            <li><a href="{{ route('home') }}#faq" data-page-link="faq">FAQ</a></li>
            <li><a href="{{ route('home') }}#contacto" data-page-link="contacto">Contacto</a></li>
        </ul>

        <a href="{{ route('home') }}?intent=visit#contacto" class="btn btn-primary nav-cta" data-page-link="contacto" data-quote-intent="visit">Agendar visita</a>

        <button class="hamburger" id="hamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
