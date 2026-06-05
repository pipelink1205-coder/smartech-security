<x-app-layout>

    <x-site-page id="inicio" active>
        @include('components.hero')
        @include('components.services-strip')
    </x-site-page>

    <x-site-page id="servicios">
        @include('components.servicios', ['services' => $services])
        @include('components.stats-bar')
    </x-site-page>

    <x-site-page id="iptv">
        @include('components.iptv-section')
    </x-site-page>

    <x-site-page id="proyectos">
        <x-section tone="dark" class="proyectos-block" :contained="false">
            @livewire('project-gallery', ['featuredOnly' => true])
        </x-section>
    </x-site-page>

    <x-site-page id="proceso">
        @include('components.why-us')
        @include('components.process')
    </x-site-page>

    <x-site-page id="testimonios">
        @include('components.testimonials')
    </x-site-page>

    <x-site-page id="faq">
        @include('components.faq')
    </x-site-page>

    <x-site-page id="cobertura">
        @include('components.cobertura')
    </x-site-page>

    <x-site-page id="contacto">
        @livewire('quote-form')
    </x-site-page>

</x-app-layout>
