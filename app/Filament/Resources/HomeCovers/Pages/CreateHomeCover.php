<?php

namespace App\Filament\Resources\HomeCovers\Pages;

use App\Filament\Resources\HomeCovers\HomeCoverResource;
use App\Models\HomeCover;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateHomeCover extends CreateRecord
{
    protected static string $resource = HomeCoverResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name'] ?? 'portada');
        }

        if (! HomeCover::query()->exists()) {
            $data['is_fallback'] = true;
            $data['is_published'] = true;
        }

        return $data;
    }
}
