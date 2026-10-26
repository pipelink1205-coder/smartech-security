<?php

$format = static function (string $number): string {
    $digits = preg_replace('/\D/', '', $number);
    if (str_starts_with($digits, '57') && strlen($digits) >= 12) {
        $digits = substr($digits, 2);
    }
    if (strlen($digits) === 10) {
        return substr($digits, 0, 3) . ' ' . substr($digits, 3, 3) . ' ' . substr($digits, 6);
    }

    return $number;
};

$whatsapp = (string) env('WHATSAPP_NUMBER', '573014589464');
$whatsapp2 = trim((string) env('WHATSAPP_NUMBER_2', ''));
$primaryDigits = preg_replace('/\D/', '', $whatsapp);
$secondaryDigits = preg_replace('/\D/', '', $whatsapp2);

if ($whatsapp2 === '' || $secondaryDigits === $primaryDigits) {
    $whatsapp2 = null;
}

return [
    'whatsapp' => $whatsapp,
    'whatsapp_formatted' => $format($whatsapp),
    'whatsapp_secondary' => $whatsapp2,
    'whatsapp_secondary_formatted' => $whatsapp2 ? $format($whatsapp2) : null,

    'email' => strtolower(env('CONTACT_EMAIL', 'comercial@smarttechsecurity.com.co')),
    'admin_email' => strtolower(env('ADMIN_EMAIL', 'seguridadsmarttech@gmail.com')),

    'address' => env('CONTACT_ADDRESS', 'Carrera 31 #39 Sur 20, Envigado, Antioquia, Colombia'),
    'hours' => env('CONTACT_HOURS', 'Lun–Vie 8:00 a.m. – 6:00 p.m. · Sáb 9:00 a.m. – 1:00 p.m.'),
    'support_note' => 'Soporte técnico 24/7 los 365 días',

    /**
     * Texto precargado en WhatsApp. En primera persona, corto,
     * para que el visitante solo pulse Enviar.
     */
    'whatsapp_message' => 'Hola, vi Smart Tech Security y quiero información. ¿Me pueden orientar?',
    'whatsapp_message_service' => 'Hola, quiero información sobre :service. ¿Me pueden orientar?',

    // Redes sociales: cuando existan las cuentas, agrega la URL completa en el .env
    // (ej. INSTAGRAM_URL=https://instagram.com/smarttechsecurity) y el icono aparece
    // automáticamente en el footer. Mientras no haya valor, el icono no se muestra.
    'social' => [
        'instagram' => env('INSTAGRAM_URL'),
        'facebook' => env('FACEBOOK_URL'),
        'linkedin' => env('LINKEDIN_URL'),
        'tiktok' => env('TIKTOK_URL'),
        'youtube' => env('YOUTUBE_URL'),
    ],
];
