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

        foreach (['png', 'svg', 'webp', 'ico'] as $ext) {
            $path = public_path("img/brands/{$slug}.{$ext}");
            if (is_readable($path)) {
                $sources[] = asset("img/brands/{$slug}.{$ext}");
            }
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
}
