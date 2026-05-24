<x-app-layout>

    @include('components.hero')
    @include('components.services-strip')
    @include('components.servicios', ['services' => $services])
    @include('components.iptv-section')
    @include('components.stats-bar')
    @include('components.why-us')
    @include('components.process')
    <section class="proyectos" id="proyectos">
        @livewire('project-gallery', ['featuredOnly' => true])
    </section>
    @include('components.cobertura')
    @include('components.testimonials')
    @include('components.faq')
    @livewire('quote-form')

</x-app-layout>
