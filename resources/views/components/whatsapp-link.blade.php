@props([
    'from' => 'link',
    'number' => null,
    'message' => null,
    'service' => null,
])
@php
    $digits = preg_replace('/\D/', '', (string) ($number ?: config('contact.whatsapp')));
    $text = \App\Support\WhatsappMessage::for($message, $service);
@endphp
<a href="{{ route('whatsapp.click', array_filter(['from' => $from, 'n' => $digits, 'text' => $text], fn ($value) => $value !== null && $value !== '')) }}" {{ $attributes->merge(['rel' => 'noopener']) }}>{{ $slot }}</a>
