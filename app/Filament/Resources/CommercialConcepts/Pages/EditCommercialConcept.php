<?php

namespace App\Filament\Resources\CommercialConcepts\Pages;

use App\Filament\Resources\CommercialConcepts\CommercialConceptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommercialConcept extends EditRecord
{
    protected static string $resource = CommercialConceptResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
