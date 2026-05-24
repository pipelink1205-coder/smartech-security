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

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        $base = pathinfo($path, PATHINFO_DIRNAME) . '/' . pathinfo($path, PATHINFO_FILENAME);
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $candidate = "{$base}.{$ext}";
            if (file_exists(public_path($candidate))) {
                return asset($candidate);
            }
        }

        return asset($path);
    }
}
