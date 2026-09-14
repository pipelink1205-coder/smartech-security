<div class="mobile-menu" id="mobile-menu">
    <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Cerrar menú">✕</button>
    <x-logo href="{{ route('home') }}#inicio" class="mobile-menu-brand" :show-text="true" data-page-link="inicio" />
    <a href="{{ route('home') }}#inicio" data-page-link="inicio">Inicio</a>
    <a href="{{ route('servicios') }}">Servicios</a>
    <a href="{{ route('home') }}#iptv" data-page-link="iptv">IPTV Hoteles</a>
    <a href="{{ route('servicios.show', 'outsourcing-ti') }}">Outsourcing TI</a>
    <a href="{{ route('home') }}#proyectos" data-page-link="proyectos">Proyectos</a>
    <a href="{{ route('home') }}#faq" data-page-link="faq">FAQ</a>
    <a href="{{ route('home') }}#contacto" data-page-link="contacto">Contacto</a>
    <a href="{{ route('home') }}?intent=visit#contacto" class="btn btn-primary" data-page-link="contacto" data-quote-intent="visit" style="margin-top:24px;justify-content:center;">Agendar visita</a>
</div>
