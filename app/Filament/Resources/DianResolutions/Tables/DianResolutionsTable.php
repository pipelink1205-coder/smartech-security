<?php

namespace App\Filament\Resources\DianResolutions\Tables;

use App\Models\DianResolution;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DianResolutionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('environment')
                    ->label('Ambiente')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => $state === 1 ? 'Producción' : 'Habilitación')
                    ->color(fn (int $state): string => $state === 1 ? 'danger' : 'warning'),
                TextColumn::make('numero_resolucion')
                    ->label('Resolución')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prefijo')
                    ->label('Prefijo')
                    ->searchable(),
                TextColumn::make('rango')
                    ->label('Rango')
                    ->state(fn (DianResolution $record): string => number_format((int) $record->rango_desde, 0, ',', '.')
                        .' – '.number_format((int) $record->rango_hasta, 0, ',', '.')),
                TextColumn::make('consecutivo_actual')
                    ->label('Último usado')
                    ->numeric()
                    ->alignEnd(),
                TextColumn::make('vigencia_hasta')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('environment')
                    ->label('Ambiente')
                    ->options([
                        2 => 'Habilitación',
                        1 => 'Producción',
                    ]),
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
