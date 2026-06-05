<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name'] ?? 'servicio');
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
