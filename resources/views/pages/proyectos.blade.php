<x-app-layout
    :title="\App\Support\Seo::page('proyectos')['title']"
    :description="\App\Support\Seo::page('proyectos')['description']"
    :canonical="\App\Support\Seo::url('/proyectos')"
>
    <section class="proyectos page-proyectos">
        @livewire('project-gallery')
    </section>
</x-app-layout>
