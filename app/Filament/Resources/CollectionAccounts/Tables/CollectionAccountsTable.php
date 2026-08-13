<?php

namespace App\Filament\Resources\CollectionAccounts\Tables;

use App\Models\CollectionAccount;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CollectionAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quote.quote_number')
                    ->label('Cotización')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$'.number_format((float) $state, 0, ',', '.'))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CollectionAccount::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'issued' => 'info',
                        'sent' => 'primary',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('issued_at')
                    ->label('Emitida')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(CollectionAccount::STATUSES),
            ])
            ->recordActions([
                EditAction::make()->label('Abrir'),
            ]);
    }
}
