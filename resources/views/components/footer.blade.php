@php
    $socialIcons = [
        'instagram' => ['label' => 'Instagram', 'path' => 'M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.256 1.216.6 1.772 1.153a4.902 4.902 0 011.153 1.772c.247.637.415 1.363.465 2.428.05 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.05-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.637-.415-1.363-.465-2.428C2.01 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.065.218-1.79.465-2.428a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.637-.248 1.363-.415 2.428-.465C8.944 2.01 9.283 2 12 2zm0 1.802c-2.67 0-2.987.01-4.04.059-.976.045-1.505.207-1.858.344-.467.181-.8.398-1.15.748-.35.35-.567.683-.748 1.15-.137.353-.3.882-.344 1.858-.05 1.053-.059 1.37-.059 4.039 0 2.67.01 2.987.059 4.04.045.976.207 1.505.344 1.858.181.466.399.8.748 1.15.35.35.683.566 1.15.747.353.137.882.3 1.858.345 1.052.048 1.369.058 4.04.058 2.67 0 2.987-.01 4.04-.058.976-.045 1.505-.208 1.858-.345.466-.181.8-.398 1.15-.748.35-.35.566-.683.747-1.15.137-.352.3-.881.345-1.857.048-1.053.058-1.37.058-4.04 0-2.67-.01-2.986-.058-4.039-.045-.976-.208-1.505-.345-1.858a3.098 3.098 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.352-.137-.881-.3-1.857-.344-1.053-.05-1.37-.059-4.04-.059zm0 4.594a5.604 5.604 0 110 11.208 5.604 5.604 0 010-11.208zm0 1.802a3.802 3.802 0 100 7.604 3.802 3.802 0 000-7.604zm5.83-1.998a1.31 1.31 0 11-2.62 0 1.31 1.31 0 012.62 0z'],
        'facebook' => ['label' => 'Facebook', 'path' => 'M22 12.06C22 6.505 17.523 2 12 2S2 6.505 2 12.06c0 5.02 3.657 9.184 8.438 9.94v-7.03H7.898v-2.91h2.54V9.845c0-2.53 1.492-3.926 3.777-3.926 1.094 0 2.238.196 2.238.196v2.475h-1.26c-1.243 0-1.63.775-1.63 1.57v1.9h2.773l-.443 2.91h-2.33V22c4.78-.756 8.437-4.92 8.437-9.94z'],
        'linkedin' => ['label' => 'LinkedIn', 'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.06 2.06 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.554V9h3.565v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
        'tiktok' => ['label' => 'TikTok', 'path' => 'M16.6 5.82c-.973-.94-1.514-2.204-1.514-3.514h-3.116v13.658c0 1.75-1.42 3.17-3.17 3.17a3.17 3.17 0 01-3.17-3.17 3.17 3.17 0 013.17-3.17c.29 0 .57.04.836.117v-3.19a6.39 6.39 0 00-.836-.055A6.29 6.29 0 003 15.966a6.29 6.29 0 006.29 6.29 6.29 6.29 0 006.29-6.29V9.163a8.83 8.83 0 004.964 1.522V7.577a5.62 5.62 0 01-3.944-1.757z'],
        'youtube' => ['label' => 'YouTube', 'path' => 'M23.5 6.19a3.02 3.02 0 00-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.378.554A3.02 3.02 0 00.5 6.19 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.81 3.02 3.02 0 002.122 2.136C4.495 20.5 12 20.5 12 20.5s7.505 0 9.378-.554A3.02 3.02 0 0023.5 17.81 31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.81zM9.75 15.568V8.432L15.818 12l-6.068 3.568z'],
    ];

    $activeSocials = collect(config('contact.social', []))
        ->filter()
        ->map(fn ($url, $key) => array_merge($socialIcons[$key] ?? [], ['url' => $url]))
        ->filter(fn ($item) => isset($item['path']));
@endphp
<footer>
    <div class="container footer-grid">
        <div class="footer-brand-col">
            <x-logo href="{{ route('home') }}" variant="footer" class="footer-brand" />
            <p class="footer-desc">Tecnología para hogares y empresas: CCTV, alarmas, domótica, energía solar, redes, ciberseguridad y outsourcing de TI en Medellín y el Valle de Aburrá.</p>
            <p class="footer-desc footer-hours">{{ config('contact.hours') }}<br>{{ config('contact.support_note') }}</p>

            @if($activeSocials->isNotEmpty())
                <nav class="footer-socials" aria-label="Redes sociales">
                    @foreach($activeSocials as $social)
                        <a href="{{ $social['url'] }}" class="social-link" target="_blank" rel="noopener" aria-label="{{ $social['label'] }}" title="{{ $social['label'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="{{ $social['path'] }}"/></svg>
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
        <div class="footer-col">
            <h4>Empresa</h4>
            <ul>
                <li><a href="{{ route('home') }}">Inicio</a></li>
                <li><a href="{{ route('proyectos') }}">Proyectos</a></li>
                <li><a href="{{ route('home') }}#proceso">Nuestro proceso</a></li>
                <li><a href="{{ route('home') }}#testimonios">Testimonios</a></li>
                <li><a href="{{ route('home') }}#faq">Preguntas frecuentes</a></li>
                <li><a href="{{ route('contacto') }}">Contacto</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Servicios</h4>
            <ul>
                <li><a href="{{ route('servicios.show', 'outsourcing-ti') }}">Outsourcing de TI</a></li>
                <li><a href="{{ route('servicios.show', 'ciberseguridad-empresas') }}">Ciberseguridad</a></li>
                <li><a href="{{ route('servicios.show', 'camaras-4k') }}">Cámaras de seguridad</a></li>
                <li><a href="{{ route('servicios.show', 'alarmas') }}">Alarmas</a></li>
                <li><a href="{{ route('servicios.show', 'domotica') }}">Domótica</a></li>
                <li><a href="{{ route('servicios.show', 'energia-solar') }}">Energía solar</a></li>
                <li><a href="{{ route('servicios') }}">Ver todos los servicios</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Legal</h4>
            <ul>
                <li><a href="{{ route('privacidad') }}">Política de privacidad</a></li>
                <li><a href="{{ route('terminos') }}">Términos y condiciones</a></li>
                <li><a href="{{ route('politica-ventas') }}">Política de ventas</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contacto</h4>
            <ul class="footer-contact-list">
                <li>
                    <span class="footer-contact-icon" aria-hidden="true">✉️</span>
                    <a href="mailto:{{ config('contact.email') }}">{{ config('contact.email') }}</a>
                </li>
                <li>
                    <span class="footer-contact-icon" aria-hidden="true">📞</span>
                    <x-whatsapp-link from="footer" target="_blank">
                        {{ config('contact.whatsapp_formatted') }}
                    </x-whatsapp-link>
                </li>
                @if(config('contact.whatsapp_secondary'))
                <li>
                    <span class="footer-contact-icon" aria-hidden="true">📞</span>
                    <x-whatsapp-link from="footer" :number="config('contact.whatsapp_secondary')" target="_blank">
                        {{ config('contact.whatsapp_secondary_formatted') }}
                    </x-whatsapp-link>
                </li>
                @endif
                <li>
                    <span class="footer-contact-icon" aria-hidden="true">📍</span>
                    <a href="{{ route('contacto') }}">{{ config('contact.address') }}</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <p>© {{ date('Y') }} Smart Tech Security. Todos los derechos reservados.</p>
        <p class="footer-bottom-area">Medellín y Valle de Aburrá, Colombia</p>
    </div>
</footer>
