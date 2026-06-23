<div class="mobile-menu" id="mobile-menu">
    <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Cerrar menú">✕</button>
    <x-logo href="{{ route('home') }}#inicio" class="mobile-menu-brand" :show-text="true" data-page-link="inicio" />
    <a href="{{ route('home') }}#inicio" data-page-link="inicio">Inicio</a>
    <a href="{{ route('home') }}#servicios" data-page-link="servicios">Servicios</a>
    <a href="{{ route('home') }}#iptv" data-page-link="iptv">IPTV Hoteles</a>
    <a href="{{ route('home') }}#proyectos" data-page-link="proyectos">Proyectos</a>
    <a href="{{ route('home') }}#proceso" data-page-link="proceso">Proceso</a>
    <a href="{{ route('home') }}#testimonios" data-page-link="testimonios">Testimonios</a>
    <a href="{{ route('home') }}#faq" data-page-link="faq">FAQ</a>
    <a href="{{ route('home') }}#cobertura" data-page-link="cobertura">Cobertura</a>
    <a href="{{ route('home') }}#contacto" data-page-link="contacto">Contacto</a>
    <a href="{{ route('home') }}#contacto" class="btn btn-primary" data-page-link="contacto" style="margin-top:24px;justify-content:center;">Cotizar Gratis</a>
</div>
