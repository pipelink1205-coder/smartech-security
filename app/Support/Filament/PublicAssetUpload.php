<?php

namespace App\Support\Filament;

use App\Support\ImageWatermark;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class PublicAssetUpload
{
    /**
     * @param  bool  $watermark  Aplicar marca de agua al guardar
     * @param  bool  $editor     Editor de recorte Filament (suele romper proporciones; mejor off)
     */
    public static function image(
        string $field,
        string $directory,
        bool $watermark = true,
        bool $editor = false,
    ): FileUpload {
        $upload = FileUpload::make($field)
            ->disk('public_assets')
            ->directory($directory)
            ->visibility('public')
            ->image()
            // FilePond también corrige EXIF; si lo dejamos ON + corrección en PHP, la foto gira 2 veces.
            ->orientImagesFromExif(false)
            ->imageEditor($editor)
            ->maxSize(8192)
            ->getUploadedFileNameForStorageUsing(
                fn (TemporaryUploadedFile $file): string => Str::slug(
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '.' . strtolower($file->getClientOriginalExtension() ?: 'jpg')
            )
            ->saveUploadedFileUsing(function ($component, TemporaryUploadedFile $file) use ($directory, $watermark): string {
                $name = Str::slug(
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '.' . strtolower($file->getClientOriginalExtension() ?: 'jpg');

                $stored = $file->storeAs($directory, $name, $component->getDiskName());
                $relative = ltrim(str_replace('\\', '/', (string) $stored), '/');
                $absolute = public_path($relative);

                // Backup del original (sirve para artisan images:watermark --restore).
                if (is_readable($absolute) && ! is_file($absolute.'.pre-watermark')) {
                    @copy($absolute, $absolute.'.pre-watermark');
                }

                // Una sola corrección EXIF en servidor (respeta vertical/horizontal; no fuerza landscape).
                ImageWatermark::normalizeOrientation($absolute);

                if ($watermark) {
                    // No volver a leer EXIF aquí: evita el doble giro.
                    ImageWatermark::apply($absolute, normalizeExif: false);
                }

                return $relative;
            });

        if (! $editor) {
            $upload->helperText(
                'La orientación se respeta (horizontal o vertical). '
                .'Si necesitas recortar, hazlo en el teléfono o en un editor externo antes de cargar.'
            );
        }

        return $upload;
    }
}
