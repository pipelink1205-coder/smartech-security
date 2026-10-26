<?php

namespace App\Filament\Resources\HomeCovers\Tables;

use App\Filament\Resources\HomeCovers\HomeCoverResource;
use App\Models\HomeCover;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HomeCoversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(' ')
                    ->getStateUsing(fn (HomeCover $record) => $record->image_url)
                    ->checkFileExistence(false)
                    ->square()
                    ->size(48),
                TextColumn::make('name')
                    ->label('Edición')
                    ->searchable()
                    ->sortable()
                    ->description(fn (HomeCover $record) => $record->kindLabel()),
                TextColumn::make('title')
                    ->label('Titular')
                    ->limit(42)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('lifecycle')
                    ->label('Estado')
                    ->getStateUsing(fn (HomeCover $record) => $record->lifecycle())
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'en_vivo' => 'success',
                        'programada' => 'info',
                        'respaldo' => 'primary',
                        'finalizada' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en_vivo' => 'En vivo',
                        'programada' => 'Programada',
                        'respaldo' => 'Respaldo',
                        'finalizada' => 'Finalizada',
                        default => 'Borrador',
                    }),
                TextColumn::make('starts_at')
                    ->label('Desde')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Hasta')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Pri.')
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_fallback')
                    ->label('Respaldo')
                    ->boolean(),
            ])
            ->defaultSort('priority', 'desc')
            ->filters([
                SelectFilter::make('kind')
                    ->label('Tipo')
                    ->options(HomeCover::KINDS),
                TernaryFilter::make('is_published')
                    ->label('Publicadas'),
                TernaryFilter::make('is_fallback')
                    ->label('Respaldo'),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Ver')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray')
                    ->url(fn (HomeCover $record): string => route('home', ['preview_portada' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('duplicate')
                    ->label('Duplicar')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Duplicar portada')
                    ->modalDescription('Se crea un borrador con los mismos textos e imagen para que ajustes la nueva edición.')
                    ->action(function (HomeCover $record) {
                        $copy = $record->replicateForEdition();

                        Notification::make()
                            ->title('Portada duplicada')
                            ->body($copy->name.' lista para editar.')
                            ->success()
                            ->send();

                        return redirect(HomeCoverResource::getUrl('edit', ['record' => $copy]));
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
