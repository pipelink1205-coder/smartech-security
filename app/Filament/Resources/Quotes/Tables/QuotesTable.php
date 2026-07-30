<?php

namespace App\Filament\Resources\Quotes\Tables;

use App\Models\Quote;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quote_number')
                    ->label('Nº')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('service')
                    ->label('Servicio')
                    ->searchable()
                    ->limit(28),
                TextColumn::make('intent')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Quote::INTENTS[$state ?? 'info'] ?? 'Info')
                    ->color(fn (?string $state): string => ($state ?? 'info') === 'visit' ? 'success' : 'gray'),
                TextColumn::make('preferred_visit_summary')
                    ->label('Visita preferida')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('project_title')
                    ->label('Proyecto')
                    ->limit(24)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('grand_total')
                    ->label('Total')
                    ->state(function (Quote $record): string {
                        if ($record->items->isEmpty()) {
                            return '—';
                        }

                        return '$'.number_format($record->grand_total, 0, ',', '.');
                    })
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Quote::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'contacted' => 'warning',
                        'visit_scheduled' => 'success',
                        'draft' => 'gray',
                        'quoted' => 'primary',
                        'sent' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'expired' => 'warning',
                        'cancelled' => 'gray',
                        'closed' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Recibida')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Quote::STATUSES),
                SelectFilter::make('intent')
                    ->label('Tipo')
                    ->options(Quote::INTENTS),
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
