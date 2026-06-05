<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\Concerns\HandlesProjectGallery;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProject extends CreateRecord
{
    use HandlesProjectGallery;

    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->stripVirtualProjectFields($data);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title'] ?? 'proyecto');
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncPendingGallery();
    }

    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
