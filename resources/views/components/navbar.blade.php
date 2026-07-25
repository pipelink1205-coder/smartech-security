<nav id="navbar">
    <div class="container nav-inner">
        <x-logo href="{{ route('home') }}#inicio" class="nav-brand" data-page-link="inicio" />

        <ul class="nav-links">
            <li><a href="{{ route('home') }}#inicio" data-page-link="inicio">Inicio</a></li>
            <li><a href="{{ route('home') }}#servicios" data-page-link="servicios">Servicios</a></li>
            <li><a href="{{ route('home') }}#iptv" data-page-link="iptv">IPTV Hoteles</a></li>
            <li><a href="{{ route('servicios.show', 'outsourcing-ti') }}">Outsourcing TI</a></li>
            <li><a href="{{ route('home') }}#proyectos" data-page-link="proyectos">Proyectos</a></li>
            <li><a href="{{ route('home') }}#faq" data-page-link="faq">FAQ</a></li>
            <li><a href="{{ route('home') }}#contacto" data-page-link="contacto">Contacto</a></li>
        </ul>

        <a href="{{ route('home') }}#contacto" class="btn btn-primary nav-cta" data-page-link="contacto">Cotizar Gratis</a>

        <button class="hamburger" id="hamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
