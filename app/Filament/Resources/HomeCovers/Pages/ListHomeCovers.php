<?php

namespace App\Filament\Resources\HomeCovers\Pages;

use App\Filament\Resources\HomeCovers\HomeCoverResource;
use App\Models\HomeCover;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeCovers extends ListRecords
{
    protected static string $resource = HomeCoverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva edición'),
        ];
    }

    public function getSubheading(): ?string
    {
        $live = HomeCover::current();

        if (! $live->exists) {
            return 'La home usa la portada de respaldo del sitio. Crea la primera edición para gestionar campañas desde aquí.';
        }

        return 'Ahora se muestra: '.$live->name.' ('.$live->kindLabel().').';
    }
}
