<?php

namespace App\Filament\Resources\CommercialConcepts\Tables;

use App\Models\QuoteCatalogItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CommercialConceptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Concepto')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => QuoteCatalogItem::TYPES[$state] ?? $state),
                TextColumn::make('unit')
                    ->label('Unidad')
                    ->formatStateUsing(fn (string $state) => QuoteCatalogItem::UNITS[$state] ?? $state),
                TextColumn::make('default_unit_price')
                    ->label('Valor sugerido')
                    ->money('COP', divideBy: 1)
                    ->sortable(),
                TextColumn::make('default_tax_rate')
                    ->label('IVA')
                    ->suffix('%'),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(QuoteCatalogItem::TYPES),
                TernaryFilter::make('is_active')
                    ->label('Disponibles'),
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
