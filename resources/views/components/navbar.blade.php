<nav id="navbar">
    <div class="container nav-inner">
        <x-logo href="{{ route('home') }}" class="nav-brand" />

        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
            <li><a href="#servicios">Servicios</a></li>
            <li><a href="#iptv">IPTV Hoteles</a></li>
            <li><a href="#proyectos">Proyectos</a></li>
            <li><a href="#testimonios">Testimonios</a></li>
            <li><a href="#faq">FAQ</a></li>
            <li><a href="#cobertura">Cobertura</a></li>
            <li><a href="#contacto">Contacto</a></li>
        </ul>

        <a href="#contacto" class="btn btn-primary nav-cta">Cotizar Gratis</a>

        <button class="hamburger" id="hamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
