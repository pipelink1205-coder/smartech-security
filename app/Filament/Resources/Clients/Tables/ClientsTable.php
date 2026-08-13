<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Models\Client;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->description(fn (Client $record): ?string => $record->name !== $record->company ? $record->name : null),
                TextColumn::make('name')
                    ->label('Contacto')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('document')
                    ->label('Documento')
                    ->formatStateUsing(function (Client $record): string {
                        if (blank($record->document)) {
                            return '—';
                        }

                        $value = $record->document;
                        if ($record->document_type === '31' && filled($record->dv)) {
                            $value .= '-'.$record->dv;
                        }

                        return $value;
                    })
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('quotes_count')
                    ->label('Cotizaciones')
                    ->counts('quotes')
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('company')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Activos'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
