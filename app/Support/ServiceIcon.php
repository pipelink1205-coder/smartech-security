<?php

namespace App\Support;

class ServiceIcon
{
    /**
     * Clave SVG a partir de lo que guardaste en admin (emoji o clave).
     * null = mostrar el emoji tal cual con estilo plano, o monograma si está vacío.
     */
    public static function svgKey(?string $icon): ?string
    {
        if (blank($icon)) {
            return null;
        }

        $icon = trim($icon);

        if (self::isValidKey($icon)) {
            return $icon;
        }

        $mapped = config("service-icons.emojis.{$icon}");

        if (is_string($mapped) && self::isValidKey($mapped)) {
            return $mapped;
        }

        return null;
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
