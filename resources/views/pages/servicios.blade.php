<x-app-layout
    :title="\App\Support\Seo::page('servicios')['title']"
    :description="\App\Support\Seo::page('servicios')['description']"
    :canonical="\App\Support\Seo::url('/servicios')"
>
    @include('components.servicios', ['services' => $services])

    <div class="container partners-page-wrap">
        <x-partners
            title="Partners tecnológicos"
            subtitle="En ciberseguridad y redes trabajamos con estas plataformas. Los logos enlazan al sitio del fabricante."
        />
    </div>
</x-app-layout>
