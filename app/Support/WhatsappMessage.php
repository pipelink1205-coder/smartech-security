<?php

namespace App\Support;

final class WhatsappMessage
{
    public static function for(?string $custom = null, ?string $service = null): string
    {
        $custom = trim((string) $custom);
        if ($custom !== '') {
            return $custom;
        }

        $service = trim((string) $service);
        if ($service !== '') {
            return str_replace(
                ':service',
                $service,
                (string) config('contact.whatsapp_message_service')
            );
        }

        return (string) config('contact.whatsapp_message');
    }
}
