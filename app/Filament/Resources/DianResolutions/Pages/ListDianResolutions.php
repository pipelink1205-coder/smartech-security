<?php

namespace App\Filament\Resources\DianResolutions\Pages;

use App\Filament\Resources\DianResolutions\DianResolutionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDianResolutions extends ListRecords
{
    protected static string $resource = DianResolutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nueva resolución'),
        ];
    }
}
