<?php

namespace App\Support\Filament;

use App\Support\ImageWatermark;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class PublicAssetUpload
{
    public static function image(string $field, string $directory): FileUpload
    {
        return FileUpload::make($field)
            ->disk('public_assets')
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->imageEditor()
            ->maxSize(8192)
            ->getUploadedFileNameForStorageUsing(
                fn (TemporaryUploadedFile $file): string => Str::slug(
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '.' . $file->getClientOriginalExtension()
            )
            ->saveUploadedFileUsing(function ($component, TemporaryUploadedFile $file) use ($directory): string {
                $name = Str::slug(
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '.' . strtolower($file->getClientOriginalExtension());

                $stored = $file->storeAs($directory, $name, $component->getDiskName());
                $relative = ltrim(str_replace('\\', '/', (string) $stored), '/');

                ImageWatermark::apply(public_path($relative));

                return $relative;
            });
    }
}
