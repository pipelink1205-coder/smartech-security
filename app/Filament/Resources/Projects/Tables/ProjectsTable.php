<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->getStateUsing(fn ($record) => $record->image_url)
                    ->checkFileExistence(false)
                    ->square()
                    ->size(48),
                TextColumn::make('title')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->location),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->searchable(),
                TextColumn::make('comuna_numero')
                    ->label('Comuna')
                    ->placeholder('—')
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
            ])
            ->defaultSort('title')
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label('Destacados'),
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
