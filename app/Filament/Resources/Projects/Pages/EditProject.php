<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\Concerns\HandlesProjectGallery;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditProject extends EditRecord
{
    use HandlesProjectGallery;

    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->stripVirtualProjectFields($data);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title'] ?? 'proyecto');
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncPendingGallery();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
