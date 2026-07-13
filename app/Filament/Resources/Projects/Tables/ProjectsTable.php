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
                    ->getStateUsing(function ($record): ?string {
                        $path = $record->image_url;
                        if (! filled($path)) {
                            return null;
                        }

                        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
                            ? $path
                            : url($path);
                    })
                    ->checkFileExistence(false)
                    ->defaultImageUrl(url('/images/projects/placeholder-domotica.svg'))
                    ->square()
                    ->size(48),
                ImageColumn::make('client_logo')
                    ->label('Logo')
                    ->getStateUsing(function ($record): ?string {
                        $path = $record->client_logo_url;
                        if (! filled($path)) {
                            return null;
                        }

                        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
                            ? $path
                            : url($path);
                    })
                    ->checkFileExistence(false)
                    ->defaultImageUrl(null)
                    ->placeholder('—')
                    ->square()
                    ->size(40)
                    ->toggleable(),
                TextColumn::make('title')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->location),
                TextColumn::make('service.name')
                    ->label('Servicio')
                    ->badge()
                    ->searchable()
                    ->sortable(),
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
