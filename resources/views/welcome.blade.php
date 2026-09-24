<x-app-layout
    :title="\App\Support\Seo::page('home')['title']"
    :description="\App\Support\Seo::page('home')['description']"
    :canonical="\App\Support\Seo::url('/')"
    :extra-json-ld="[\App\Support\Seo::faqGraph()]"
>

    <x-site-page id="inicio" active>
        <div class="home-hero-stage">
            @include('components.hero')
            <x-hero-tech-intro />
        </div>
        @include('components.services-strip')
    </x-site-page>

    <x-site-page id="servicios">
        @include('components.servicios', ['services' => $services])
        @include('components.stats-bar')
    </x-site-page>

    <x-site-page id="iptv">
        @include('components.iptv-section', ['iptvService' => $iptvService ?? null])
    </x-site-page>

    <x-site-page id="proyectos">
        <x-section tone="muted" class="proyectos-block" :contained="false">
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

    <x-site-page id="contacto">
        @livewire('quote-form', ['intent' => in_array(request()->query('intent'), ['info', 'visit'], true) ? request()->query('intent') : 'info'])
    </x-site-page>

</x-app-layout>
