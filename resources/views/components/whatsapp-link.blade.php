@props([
    'from' => 'link',
    'number' => null,
])
@php
    $digits = preg_replace('/\D/', '', (string) ($number ?: config('contact.whatsapp')));
@endphp
<a href="{{ route('whatsapp.click', ['from' => $from, 'n' => $digits]) }}" {{ $attributes->merge(['rel' => 'noopener']) }}>{{ $slot }}</a>
