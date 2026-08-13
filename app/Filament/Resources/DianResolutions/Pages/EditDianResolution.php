<?php

namespace App\Filament\Resources\DianResolutions\Pages;

use App\Filament\Resources\DianResolutions\DianResolutionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDianResolution extends EditRecord
{
    protected static string $resource = DianResolutionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
