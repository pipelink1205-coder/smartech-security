<x-app-layout>
    @livewire('quote-form', ['intent' => in_array(request()->query('intent'), ['info', 'visit'], true) ? request()->query('intent') : 'info'])
</x-app-layout>
