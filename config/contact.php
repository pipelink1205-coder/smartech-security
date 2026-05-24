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

$whatsapp = env('WHATSAPP_NUMBER', '573014589464');
$whatsapp2 = env('WHATSAPP_NUMBER_2', '573006035941');

return [
    'whatsapp' => $whatsapp,
    'whatsapp_formatted' => $format($whatsapp),
    'whatsapp_secondary' => $whatsapp2,
    'whatsapp_secondary_formatted' => $format($whatsapp2),

    'email' => strtolower(env('CONTACT_EMAIL', 'comercial@smarttechsecurity.com.co')),
    'admin_email' => strtolower(env('ADMIN_EMAIL', 'seguridadsmarttech@gmail.com')),

    'address' => env('CONTACT_ADDRESS', 'Carrera 31 #39 Sur 20, Envigado, Antioquia, Colombia'),
    'hours' => env('CONTACT_HOURS', 'Lun–Vie 8:00 a.m. – 6:00 p.m. · Sáb 9:00 a.m. – 1:00 p.m.'),
    'support_note' => 'Soporte técnico 24/7 los 365 días',
];
