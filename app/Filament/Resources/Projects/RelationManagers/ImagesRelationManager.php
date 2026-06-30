<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Support\Filament\PublicAssetUpload;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

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
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                ImageColumn::make('path')
                    ->label('Vista previa')
                    ->getStateUsing(fn ($record) => $record->url)
                    ->checkFileExistence(false)
                    ->height(120)
                    ->width(160),
                IconColumn::make('is_cover')
                    ->label('Portada')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star'),
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
                    ->label('Agregar foto')
                    ->after(function () {
                        $project = $this->getOwnerRecord();

                        if ($project->images()->where('is_cover', true)->exists()) {
                            return;
                        }

                        $project->images()->orderBy('sort_order')->first()?->update(['is_cover' => true]);
                    }),
            ])
            ->recordActions([
                Action::make('makeCover')
                    ->label('Portada')
                    ->icon('heroicon-o-star')
                    ->visible(fn ($record) => ! $record->is_cover)
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $record->project->images()->update(['is_cover' => false]);
                            $record->update(['is_cover' => true]);
                        });

                        Notification::make()
                            ->title('Portada actualizada')
                            ->success()
                            ->send();
                    }),
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
