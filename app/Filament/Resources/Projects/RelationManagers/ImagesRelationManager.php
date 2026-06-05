<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Support\Filament\PublicAssetUpload;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Galería de fotos';

    protected static ?string $modelLabel = 'foto';

    protected static ?string $pluralModelLabel = 'fotos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                PublicAssetUpload::image('path', 'images/projects')
                    ->label('Imagen')
                    ->required(),
                TextInput::make('caption')
                    ->label('Descripción corta')
                    ->maxLength(200),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('path')
                    ->label('Vista previa')
                    ->getStateUsing(fn ($record) => $record->url)
                    ->checkFileExistence(false)
                    ->square()
                    ->size(56),
                TextColumn::make('caption')
                    ->label('Descripción')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar foto'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
