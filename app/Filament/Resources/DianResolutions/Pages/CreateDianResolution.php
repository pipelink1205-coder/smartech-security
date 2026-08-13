<?php

namespace App\Filament\Resources\DianResolutions\Pages;

use App\Filament\Resources\DianResolutions\DianResolutionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDianResolution extends CreateRecord
{
    protected static string $resource = DianResolutionResource::class;

    protected function getRedirectUrl(): string
    {
        return DianResolutionResource::getUrl('index');
    }
}
