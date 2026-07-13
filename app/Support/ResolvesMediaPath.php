<?php

namespace App\Support;

trait ResolvesMediaPath
{
    protected function resolveMediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (file_exists(public_path($path))) {
            return $this->publicAssetUrl($path);
        }

        $base = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $candidate = "{$base}.{$ext}";
            if (file_exists(public_path($candidate))) {
                return $this->publicAssetUrl($candidate);
            }
        }

        return null;
    }

    /** Ruta relativa al dominio (evita mixed-content si APP_URL usa http en un sitio https). */
    protected function publicAssetUrl(string $path): string
    {
        return '/' . ltrim(str_replace('\\', '/', $path), '/');
    }
}
