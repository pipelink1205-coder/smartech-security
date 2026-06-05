<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name'] ?? 'servicio');
        }

        return $data;
    }
}
