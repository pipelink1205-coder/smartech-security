<footer>
    <div class="container footer-grid">
        <div class="footer-brand-col">
            <x-logo href="{{ route('home') }}" variant="footer" class="footer-brand" />
            <p class="footer-desc">Instalación profesional de CCTV, alarmas, domótica, energía solar y redes en Medellín y el área metropolitana.</p>
        </div>
        <div class="footer-col">
            <h4>Enlaces</h4>
            <ul>
                <li><a href="{{ route('home') }}">Inicio</a></li>
                <li><a href="{{ route('servicios') }}">Servicios</a></li>
                <li><a href="{{ route('proyectos') }}">Proyectos</a></li>
                <li><a href="{{ route('contacto') }}">Contacto</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Servicios</h4>
            <ul>
                <li><a href="#servicios">Cámaras de seguridad</a></li>
                <li><a href="#servicios">Energía solar</a></li>
                <li><a href="#servicios">Domótica</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contacto</h4>
            <ul>
                <li><a href="mailto:{{ config('contact.email') }}">{{ config('contact.email') }}</a></li>
                <li><a href="mailto:{{ config('contact.admin_email') }}">{{ config('contact.admin_email') }}</a></li>
                <li>
                    <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener">
                        {{ config('contact.whatsapp_formatted') }}
                    </a>
                </li>
                @if(config('contact.whatsapp_secondary'))
                <li>
                    <a href="https://wa.me/{{ config('contact.whatsapp_secondary') }}" target="_blank" rel="noopener">
                        {{ config('contact.whatsapp_secondary_formatted') }}
                    </a>
                </li>
                @endif
                <li><a href="#contacto">Envigado, Antioquia</a></li>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>© {{ date('Y') }} Smart Tech Security. Todos los derechos reservados.</p>
        <p class="footer-deploy-test">Sitio actualizado vía Git · 24/05/2026</p>
    </div>
</footer>
