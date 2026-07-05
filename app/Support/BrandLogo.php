<?php

namespace App\Support;

use Illuminate\Support\Str;

class BrandLogo
{
    /**
     * URLs de logo en orden de prioridad (local → favicon del sitio → CDNs).
     *
     * @return array<int, string>
     */
    public static function sources(string $brand): array
    {
        $slug   = Str::slug($brand);
        $domain = config("brands.domains.{$slug}");

        $sources = [];

        foreach (['png', 'svg', 'webp', 'ico', 'gif'] as $ext) {
            $path = public_path("img/brands/{$slug}.{$ext}");
            if (! is_readable($path)) {
                continue;
            }

            $head = (string) file_get_contents($path, false, null, 0, 512);
            if (! self::isValidImagePayload($head)) {
                continue;
            }

            $sources[] = asset("img/brands/{$slug}.{$ext}");
        }

        $override = config("brands.overrides.{$slug}");
        if (is_string($override) && $override !== '') {
            $sources[] = $override;
        }

        if ($domain) {
            $sources[] = "https://{$domain}/favicon.ico";

            if (! str_starts_with($domain, 'www.')) {
                $sources[] = "https://www.{$domain}/favicon.ico";
            }

            $sources[] = "https://icons.duckduckgo.com/ip3/{$domain}.ico";
            $sources[] = "https://www.google.com/s2/favicons?domain={$domain}&sz=128";
        }

        return array_values(array_unique($sources));
    }

    public static function isValidImagePayload(string $body): bool
    {
        if ($body === '') {
            return false;
        }

        if (str_starts_with($body, "\x89PNG\r\n\x1a\n")) {
            return true;
        }

        if (str_starts_with($body, 'GIF87a') || str_starts_with($body, 'GIF89a')) {
            return true;
        }

        if (str_starts_with($body, "\x00\x00\x01\x00")) {
            return true;
        }

        if (str_starts_with($body, 'RIFF') && strlen($body) > 12 && substr($body, 8, 4) === 'WEBP') {
            return true;
        }

        $trim = ltrim($body);

        return str_starts_with($trim, '<svg')
            || (str_starts_with($trim, '<?xml') && str_contains($trim, '<svg'));
    }
}
