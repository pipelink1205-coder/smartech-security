@props(['service'])
@php
    $form = \App\Livewire\ServiceLeadForm::configFor($service->slug);
    $waMessage = str_replace(':service', $service->name, $form['whatsapp']);
@endphp
<section class="sc" id="contacto-servicio" data-contextual-contact aria-labelledby="sc-title">
    <div class="container sc__grid">
        <div class="sc__copy">
            <span class="sc__tag">
                <x-service-icon :icon="$service->icon" :name="$service->name" size="sm" />
                {{ $service->name }}
            </span>
            <h2 id="sc-title">Hablemos de {{ $service->name }}</h2>
            <p>Responda dos preguntas rápidas y un asesor de este servicio le contacta con información concreta para su caso.</p>
            <ul class="sc__list">
                <li>Visita técnica y diagnóstico gratuitos</li>
                <li>Cotización detallada después de la visita</li>
                <li>Garantía de 1 año en equipos y mano de obra</li>
                <li>{{ config('contact.hours') }}</li>
            </ul>
            <x-whatsapp-link from="service_contact" :message="$waMessage" class="btn sc__wa" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12.05 21.785h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                Prefiero escribir por WhatsApp
            </x-whatsapp-link>
        </div>
        <livewire:service-lead-form :service-slug="$service->slug" :service-name="$service->name" />
    </div>
</section>
