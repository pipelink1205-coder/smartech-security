@props(['name' => 'shield', 'size' => 24])
@php
    $paths = [
        'camera' => '<path d="M3 7.5 15 3.5l3 6.5-12 4z"/><path d="m7.5 13.2 1.2 3.3H13"/><path d="m18 9.2 3-1"/><path d="M3 21v-5.5h4"/>',
        'fingerprint' => '<path d="M12 11v4.5"/><path d="M8.5 11a3.5 3.5 0 0 1 7 0v2"/><path d="M5.5 12a6.5 6.5 0 0 1 13 0v1"/><path d="M8.5 15.5c0 2 .8 4 1.8 5.5"/><path d="M15.5 15c0 2.2-.5 4.2-1.5 6"/><path d="M4 7.5A9 9 0 0 1 20 7.5"/>',
        'phone' => '<rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M10.5 18h3"/><path d="M9.5 7.5a3.5 3.5 0 0 1 5 0"/><path d="M8 5.8a5.8 5.8 0 0 1 8 0"/>',
        'wrench' => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3.5 17.5l3 3 5.8-5.8a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.3-.6-.6-2.3z"/>',
        'parking' => '<rect x="3.5" y="3.5" width="17" height="17" rx="3"/><path d="M9.5 16.5v-9h3.2a2.8 2.8 0 0 1 0 5.6H9.5"/>',
        'camera-off' => '<path d="m3 3 18 18"/><path d="M7.5 7H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12.5"/><path d="M10 5h5l1.8 2.5H19a2 2 0 0 1 2 2v6.5"/><path d="M14.1 14.1A3 3 0 0 1 9.9 9.9"/>',
        'bell' => '<path d="M6 8.5a6 6 0 0 1 12 0c0 6.5 2.5 8.5 2.5 8.5h-17S6 15 6 8.5"/><path d="M10 20.5a2.2 2.2 0 0 0 4 0"/>',
        'key' => '<circle cx="7.5" cy="15.5" r="4"/><path d="m10.4 12.6 9.6-9.6"/><path d="m16.5 6.5 3 3"/><path d="m14 9 2 2"/>',
        'pin' => '<path d="M12 21s-6.5-5.8-6.5-11a6.5 6.5 0 0 1 13 0c0 5.2-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.4"/>',
        'file' => '<path d="M14 3H6.5A2 2 0 0 0 4.5 5v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V8.5z"/><path d="M14 3v5.5h5.5"/><path d="M8 13h8M8 16.5h5"/>',
        'shield' => '<path d="M12 3 19.5 6v5.8c0 4.6-3.2 7.8-7.5 9.2-4.3-1.4-7.5-4.6-7.5-9.2V6z"/><path d="m9 12 2 2 4-4"/>',
        'arrow' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'users' => '<circle cx="9" cy="8.5" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 5.2a3.5 3.5 0 0 1 0 6.6"/><path d="M18.5 14.2A6.5 6.5 0 0 1 21.5 20"/>',
        'coins' => '<ellipse cx="9" cy="7" rx="6" ry="3"/><path d="M3 7v4c0 1.7 2.7 3 6 3s6-1.3 6-3V7"/><path d="M3 11v4c0 1.7 2.7 3 6 3 1.3 0 2.5-.2 3.5-.5"/><circle cx="17" cy="16" r="4"/>',
        'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
        'eye' => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/>',
        'zap' => '<path d="M13 2.5 4.5 13.5H12l-1 8 8.5-11H12z"/>',
        'bed' => '<path d="M3 19V6"/><path d="M3 15h18v4"/><path d="M21 15v-2.5A3.5 3.5 0 0 0 17.5 9H11v6"/><circle cx="7" cy="11.5" r="2"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8.5 7V5.5A1.5 1.5 0 0 1 10 4h4a1.5 1.5 0 0 1 1.5 1.5V7"/><path d="M3 12.5h18"/><path d="M11 12.5h2"/>',
        'building' => '<rect x="4.5" y="3" width="15" height="18" rx="1.5"/><path d="M9 7h1.5M13.5 7H15M9 11h1.5M13.5 11H15M9 15h1.5M13.5 15H15"/><path d="M10.5 21v-3h3v3"/>',
    ];
@endphp
<svg {{ $attributes->merge(['class' => 'ph-icon']) }} xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? $paths['shield'] !!}</svg>
