<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label(' ')
                    ->size('lg'),
                ImageColumn::make('image')
                    ->label('Imagen')
                    ->getStateUsing(fn ($record) => $record->image_url)
                    ->checkFileExistence(false)
                    ->square()
                    ->size(40),
                TextColumn::make('name')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price_from')
                    ->label('Desde')
                    ->formatStateUsing(fn ($state) => $state
                        ? '$' . number_format((float) $state, 0, ',', '.')
                        : '—')
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('order')
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
