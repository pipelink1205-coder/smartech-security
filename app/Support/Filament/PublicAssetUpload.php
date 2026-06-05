<?php

namespace App\Support\Filament;

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
            );
    }
}
