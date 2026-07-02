<?php

namespace App\Support;

class ServiceIcon
{
    /**
     * Icono en el sitio: automático según slug (servicios base) o monograma del nombre.
     */
    public static function resolve(?string $slug, ?string $name): string
    {
        $slugKey = config("service-icons.slugs.{$slug}");

        if (is_string($slugKey) && self::isValidKey($slugKey)) {
            return $slugKey;
        }

        return 'monogram';
    }

    public static function monogramLetter(?string $name): string
    {
        $name = trim((string) $name);

        return $name !== ''
            ? mb_strtoupper(mb_substr($name, 0, 1))
            : '?';
    }

    public static function isValidKey(string $key): bool
    {
        return array_key_exists($key, config('service-icons.options', []));
    }
}
