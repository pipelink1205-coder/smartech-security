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

                // Fija la orientación del celular en los píxeles antes de preview/marca de agua.
                ImageWatermark::normalizeOrientation($absolute);

                if ($watermark) {
                    ImageWatermark::apply($absolute);
                }

                return $relative;
            });

        if (! $editor) {
            $upload->helperText(
                'La orientación del celular se corrige sola al subir. '
                . 'Si necesitas recortar, hazlo en el teléfono o en un editor externo antes de cargar.'
            );
        }

        return $upload;
    }
}
