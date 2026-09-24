<x-app-layout
    :title="\App\Support\Seo::page('contacto')['title']"
    :description="\App\Support\Seo::page('contacto')['description']"
    :canonical="\App\Support\Seo::url('/contacto')"
>
    @livewire('quote-form', [
        'intent' => in_array(request()->query('intent'), ['info', 'visit'], true) ? request()->query('intent') : 'info',
        'service' => (string) request()->query('servicio', ''),
    ])
</x-app-layout>
