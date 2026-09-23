@props([
    'from' => 'link',
    'number' => null,
    'message' => null,
])
@php
    $digits = preg_replace('/\D/', '', (string) ($number ?: config('contact.whatsapp')));
@endphp
<a href="{{ route('whatsapp.click', array_filter(['from' => $from, 'n' => $digits, 'text' => $message], fn ($value) => $value !== null && $value !== '')) }}" {{ $attributes->merge(['rel' => 'noopener']) }}>{{ $slot }}</a>
