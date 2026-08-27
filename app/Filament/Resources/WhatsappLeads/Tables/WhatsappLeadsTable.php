<?php

namespace App\Filament\Resources\WhatsappLeads\Tables;

use App\Models\WhatsappLead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WhatsappLeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('source')
                    ->label('Desde')
                    ->formatStateUsing(fn (?string $state): string => WhatsappLead::SOURCES[$state ?? 'link'] ?? (string) $state),
                TextColumn::make('page_title')
                    ->label('Página')
                    ->placeholder('—')
                    ->limit(40),
                TextColumn::make('page_url')
                    ->label('URL')
                    ->limit(48)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('source')
                    ->label('Desde')
                    ->options(WhatsappLead::SOURCES),
            ])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
