<?php

namespace App\Filament\Resources\ElectronicInvoices\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ElectronicInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_number')
                    ->label('Número')
                    ->searchable(['dian_prefijo', 'dian_numero'])
                    ->sortable(query: function ($query, string $direction) {
                        $query->orderBy('dian_numero', $direction);
                    }),
                TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quote.quote_number')
                    ->label('Cotización')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$'.number_format((float) $state, 0, ',', '.'))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('dian_status')
                    ->label('DIAN')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ACCEPTED' => 'success',
                        'SIGNED', 'SENT' => 'info',
                        'PENDING' => 'gray',
                        'REJECTED', 'ERROR' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('dian_status')
                    ->label('Estado DIAN')
                    ->options([
                        'PENDING' => 'Pendiente',
                        'SIGNED' => 'Firmada',
                        'SENT' => 'Enviada',
                        'ACCEPTED' => 'Aceptada',
                        'REJECTED' => 'Rechazada',
                        'ERROR' => 'Error',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label('Abrir'),
            ]);
    }
}
