<x-app-layout>

    {{-- Hero --}}
    @include('components.hero')

    {{-- Strip de servicios --}}
    @include('components.services-strip')

    {{-- Servicios grid --}}
    @include('components.servicios', ['services' => $services])

    {{-- IPTV Hoteles --}}
    @include('components.iptv-section')

    {{-- Stats bar --}}
    @include('components.stats-bar')

    {{-- Proyectos --}}
    @include('components.proyectos', ['projects' => $projects])

    {{-- Cobertura --}}
    @include('components.cobertura')

    {{-- Formulario de cotización (Livewire) --}}
    <section id="contacto">
        @livewire('quote-form')
    </section>

</x-app-layout>
