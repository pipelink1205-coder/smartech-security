<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createFrom')
                ->label('Nueva desde…')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('gray')
                ->modalHeading('Nueva cotización desde una existente')
                ->modalDescription('Elige la cotización base. Se creará un borrador para que ajustes ítems o descripciones.')
                ->form([
                    Select::make('quote_id')
                        ->label('Cotización base')
                        ->options(fn () => Quote::query()
                            ->orderByDesc('id')
                            ->limit(80)
                            ->get()
                            ->mapWithKeys(fn (Quote $q) => [
                                $q->id => trim(($q->quote_number ?: 'Sin número').' · '.$q->name
                                    .(filled($q->service) ? ' · '.$q->service : '')),
                            ])
                            ->all())
                        ->searchable()
                        ->required()
                        ->default(fn () => Quote::query()->latest('id')->value('id')),
                ])
                ->action(function (array $data) {
                    $source = Quote::query()->find($data['quote_id'] ?? null);

                    if (! $source) {
                        Notification::make()
                            ->title('No se encontró la cotización')
                            ->danger()
                            ->send();

                        return;
                    }

                    $copy = $source->duplicate();

                    Notification::make()
                        ->title('Borrador creado')
                        ->body(($copy->quote_number ?: 'Cotización').' lista para editar.')
                        ->success()
                        ->send();

                    return redirect(QuoteResource::getUrl('edit', ['record' => $copy]));
                }),
            CreateAction::make()
                ->label('Nueva en blanco'),
        ];
    }
}
