<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Service;
use App\Support\ImageWatermark;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WatermarkProjectImagesCommand extends Command
{
    protected $signature = 'images:watermark
                            {--force : Volver a marcar aunque ya se haya procesado}
                            {--dry-run : Solo listar, no modificar}
                            {--all-files : Incluir todos los jpg/png/webp de public/images/projects (no solo BD)}
                            {--restore : Restaurar desde *.pre-watermark (corrige fotos giradas) y volver a marcar}';

    protected $description = 'Aplica marca de agua (escudo) a fotos de proyectos ya subidas';

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('Falta la extensión PHP gd. Actívala en php.ini (extension=gd) y reinicia el servidor.');

            return self::FAILURE;
        }

        $paths = $this->collectPaths();
        $registry = $this->loadRegistry();
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $restore = (bool) $this->option('restore');

        if ($restore) {
            $force = true;
        }

        $ok = 0;
        $skip = 0;
        $fail = 0;
        $restored = 0;

        foreach ($paths as $relative) {
            $absolute = public_path($relative);

            if (! is_readable($absolute) && ! ($restore && is_readable($absolute . '.pre-watermark'))) {
                $this->warn("  ✗ no existe: {$relative}");
                $fail++;
                continue;
            }

            if (! $force && isset($registry[$relative])) {
                $skip++;
                continue;
            }

            if ($dry) {
                $backup = $absolute . '.pre-watermark';
                $note = ($restore && is_file($backup)) ? ' [restore]' : '';
                $this->line("  · {$relative}{$note}");
                $ok++;
                continue;
            }

            $backup = $absolute . '.pre-watermark';

            if ($restore) {
                if (! is_file($backup)) {
                    $this->warn("  ✗ sin backup: {$relative}");
                    $fail++;
                    continue;
                }
                if (! @copy($backup, $absolute)) {
                    $this->warn("  ✗ no se pudo restaurar: {$relative}");
                    $fail++;
                    continue;
                }
                $restored++;
                unset($registry[$relative]);
            } elseif (! is_file($backup)) {
                // Copia de seguridad ligera por si hay que revertir
                @copy($absolute, $backup);
            }

            if (ImageWatermark::apply($absolute)) {
                $registry[$relative] = now()->toIso8601String();
                $this->line("  ✓ {$relative}" . ($restore ? ' (restaurada + marcada)' : ''));
                $ok++;
            } else {
                $this->warn("  ✗ no se pudo marcar: {$relative}");
                $fail++;
            }
        }

        if (! $dry) {
            $this->saveRegistry($registry);
        }

        $this->newLine();
        $this->info($dry
            ? "Dry-run: {$ok} archivos candidatos, {$skip} ya marcados (usa --force para incluirlos)."
            : "Listo: {$ok} marcadas, {$skip} omitidas (ya procesadas), {$fail} fallidas"
                . ($restore ? ", {$restored} restauradas desde backup" : '') . '.'
        );

        if ($skip > 0 && ! $force) {
            $this->line('Para remarcar todas: php artisan images:watermark --force');
        }

        if (! $restore) {
            $this->line('Si las fotos quedaron giradas: php artisan images:watermark --restore --all-files');
        }

        return self::SUCCESS;
    }

    /** @return list<string> */
    private function collectPaths(): array
    {
        $paths = [];

        foreach (ProjectImage::query()->pluck('path') as $path) {
            $normalized = $this->normalizePublicPath((string) $path);
            if ($normalized) {
                $paths[] = $normalized;
            }
        }

        foreach (Project::query()->whereNotNull('image')->pluck('image') as $path) {
            $normalized = $this->normalizePublicPath((string) $path);
            if ($normalized) {
                $paths[] = $normalized;
            }
        }

        foreach (Service::query()->whereNotNull('image')->pluck('image') as $path) {
            $normalized = $this->normalizePublicPath((string) $path);
            if ($normalized && str_starts_with($normalized, 'images/')) {
                $paths[] = $normalized;
            }
        }

        if ($this->option('all-files')) {
            $dir = public_path('images/projects');
            if (is_dir($dir)) {
                foreach (File::allFiles($dir) as $file) {
                    $ext = strtolower($file->getExtension());
                    if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                        continue;
                    }

                    $relative = 'images/projects/' . str_replace('\\', '/', $file->getRelativePathname());

                    // Evitar logos de clientes y backups
                    if (str_contains(strtolower($relative), 'logos empresas')) {
                        continue;
                    }
                    if (str_ends_with($relative, '.pre-watermark')) {
                        continue;
                    }

                    $paths[] = $relative;
                }
            }
        }

        $paths = array_values(array_unique($paths));
        sort($paths);

        return $paths;
    }

    private function normalizePublicPath(string $path): ?string
    {
        $path = trim(str_replace('\\', '/', $path));
        if ($path === '' || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        $path = ltrim($path, '/');

        if (! preg_match('/\.(jpe?g|png|webp)$/i', $path)) {
            return null;
        }

        // Solo archivos bajo public/
        if (! is_readable(public_path($path))) {
            return null;
        }

        return $path;
    }

    /** @return array<string, string> */
    private function loadRegistry(): array
    {
        $file = storage_path('app/watermarked-images.json');
        if (! is_readable($file)) {
            return [];
        }

        $data = json_decode((string) file_get_contents($file), true);

        return is_array($data) ? $data : [];
    }

    /** @param array<string, string> $registry */
    private function saveRegistry(array $registry): void
    {
        $file = storage_path('app/watermarked-images.json');
        File::ensureDirectoryExists(dirname($file));
        file_put_contents($file, json_encode($registry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
