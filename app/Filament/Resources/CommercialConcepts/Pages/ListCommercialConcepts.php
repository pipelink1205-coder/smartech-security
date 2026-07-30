<?php

namespace App\Filament\Resources\CommercialConcepts\Pages;

use App\Filament\Resources\CommercialConcepts\CommercialConceptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommercialConcepts extends ListRecords
{
    protected static string $resource = CommercialConceptResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
