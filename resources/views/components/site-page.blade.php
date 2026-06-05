@props([
    'id',
    'active' => false,
])

<div
    id="{{ $id }}"
    @class([
        'site-page',
        'active' => $active,
    ])
    data-page="{{ $id }}"
>
    {{ $slot }}
</div>
