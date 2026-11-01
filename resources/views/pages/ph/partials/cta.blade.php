{{-- CTA final con formulario de diagnóstico. $servicioSlug / $municipioSlug opcionales para preseleccionar --}}
<section class="ph-cta" id="diagnostico" aria-labelledby="ph-cta-title" data-contextual-contact>
    <div class="ph-cta__grid">
        <div class="ph-cta__copy">
            <span class="ph-cta__tag">Diagnóstico gratuito</span>
            <h2 id="ph-cta-title">{{ $titulo ?? 'Pida el diagnóstico de seguridad de su unidad residencial' }}</h2>
            <p>{{ $texto ?? 'Hacemos un recorrido de 45 minutos y le entregamos un informe en PDF con fotos, puntos ciegos y tres opciones de mejora. Sirve para presentarlo al consejo, aunque al final contrate con otro proveedor.' }}</p>
            <ul class="ph-cta__list">
                <li><x-ph-icon name="eye" :size="18" /> Portería, parqueaderos, perímetro y cuarto de equipos</li>
                <li><x-ph-icon name="file" :size="18" /> Informe en PDF listo para el consejo</li>
                <li><x-ph-icon name="coins" :size="18" /> Opción de pago en cuotas</li>
                <li><x-ph-icon name="shield" :size="18" /> Sin costo y sin compromiso</li>
            </ul>
            <x-whatsapp-link from="ph_cta" :message="'Hola, soy de una unidad residencial y quiero agendar el diagnóstico de seguridad gratuito.'" class="ph-cta__wa" target="_blank">
                ¿Prefiere WhatsApp? Escríbanos aquí <x-ph-icon name="arrow" :size="16" />
            </x-whatsapp-link>
        </div>
        <livewire:ph-diagnostic-form :pre-servicio="$servicioSlug ?? null" :pre-municipio="$municipioSlug ?? null" />
    </div>
</section>
