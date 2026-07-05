<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncBrandLogosCommand extends Command
{
    protected $signature = 'brands:sync
                            {--force : Volver a descargar aunque ya exista el archivo}
                            {--slug= : Solo una marca (slug)}';

    protected $description = 'Descarga logos locales a public/img/brands/ según config/brands.php';

    public function handle(): int
    {
        $dir = public_path('img/brands');
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            $this->error("No se pudo crear {$dir}");

            return self::FAILURE;
        }

        $slugs = array_keys(config('brands.domains', []));

        if ($only = $this->option('slug')) {
            $slugs = [$only];
        }

        $ok = 0;
        $skip = 0;
        $fail = 0;

        foreach ($slugs as $slug) {
            if ($this->hasValidLocalLogo($dir, $slug) && ! $this->option('force')) {
                $skip++;
                continue;
            }

            $this->clearInvalidLogoVariants($dir, $slug);

            if ($this->downloadLogo($dir, $slug)) {
                $ok++;
                $this->line("  ✓ {$slug}");
            } else {
                $fail++;
                $this->warn("  ✗ {$slug} (sin logo descargable)");
            }
        }

        $this->newLine();
        $this->info("Logos: {$ok} descargados, {$skip} ya existían, {$fail} fallidos.");
        $this->line('Ruta: public/img/brands/{slug}.{png|ico|svg}');

        return self::SUCCESS;
    }

    private function hasValidLocalLogo(string $dir, string $slug): bool
    {
        foreach (['png', 'svg', 'webp', 'ico'] as $ext) {
            $path = "{$dir}/{$slug}.{$ext}";
            if (is_readable($path) && $this->detectImageExtension((string) file_get_contents($path, false, null, 0, 512))) {
                return true;
            }
        }

        return false;
    }

    /** @return array<int, string> */
    private function urlsForSlug(string $slug): array
    {
        $domain = config("brands.domains.{$slug}");
        $urls = [];

        if ($override = config("brands.overrides.{$slug}")) {
            $urls[] = $override;
        }

        if ($domain) {
            $urls[] = "https://www.google.com/s2/favicons?domain={$domain}&sz=128";
            $urls[] = "https://icons.duckduckgo.com/ip3/{$domain}.ico";
            $urls[] = "https://www.{$domain}/favicon.ico";
            $urls[] = "https://{$domain}/favicon.ico";
        }

        return array_values(array_unique(array_filter($urls)));
    }

    private function downloadLogo(string $dir, string $slug): bool
    {
        foreach ($this->urlsForSlug($slug) as $url) {
            try {
                $response = Http::timeout(25)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; SmartTechSecurity/1.0)'])
                    ->get($url);

                if (! $response->successful()) {
                    continue;
                }

                $body = $response->body();
                $ext = $this->detectImageExtension($body);

                if ($ext === null || strlen($body) < 80) {
                    continue;
                }

                $this->clearLogoVariants($dir, $slug);
                file_put_contents("{$dir}/{$slug}.{$ext}", $body);

                return true;
            } catch (\Throwable) {
                continue;
            }
        }

        return false;
    }

    private function clearLogoVariants(string $dir, string $slug): void
    {
        foreach (['png', 'svg', 'webp', 'ico', 'gif'] as $ext) {
            $path = "{$dir}/{$slug}.{$ext}";
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function clearInvalidLogoVariants(string $dir, string $slug): void
    {
        foreach (['png', 'svg', 'webp', 'ico', 'gif'] as $ext) {
            $path = "{$dir}/{$slug}.{$ext}";
            if (! is_readable($path)) {
                continue;
            }

            $head = (string) file_get_contents($path, false, null, 0, 512);
            if ($this->detectImageExtension($head) === null) {
                unlink($path);
            }
        }
    }

    private function detectImageExtension(string $body): ?string
    {
        if (str_starts_with($body, "\x89PNG\r\n\x1a\n")) {
            return 'png';
        }

        if (str_starts_with($body, 'GIF87a') || str_starts_with($body, 'GIF89a')) {
            return 'gif';
        }

        if (str_starts_with($body, "\x00\x00\x01\x00")) {
            return 'ico';
        }

        if (str_starts_with($body, 'RIFF') && strlen($body) > 12 && substr($body, 8, 4) === 'WEBP') {
            return 'webp';
        }

        $trim = ltrim($body);
        if (str_starts_with($trim, '<svg') || (str_starts_with($trim, '<?xml') && str_contains($trim, '<svg'))) {
            return 'svg';
        }

        return null;
    }
}
