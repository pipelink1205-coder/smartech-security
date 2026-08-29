<?php

namespace App\Filament\Resources\WhatsappLeads\Tables;

use App\Models\WhatsappLead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class WhatsappLeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Hora')
                    ->formatStateUsing(fn ($state): string => $state
                        ? Carbon::parse($state, 'UTC')->timezone('America/Bogota')->format('d/m/Y H:i')
                        : '—')
                    ->sortable(),
                TextColumn::make('visitor_kind')
                    ->label('Tipo')
                    ->getStateUsing(fn (WhatsappLead $record): string => $record->visitorKindLabel())
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Persona' => 'success',
                        'Bot' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable()
                    ->placeholder('—')
                    ->formatStateUsing(function (?string $state, WhatsappLead $record): string {
                        if (! $state) {
                            return '—';
                        }

                        return $record->ipIsCloudflare()
                            ? $state.' (Cloudflare)'
                            : $state;
                    }),
                TextColumn::make('user_agent')
                    ->label('Navegador')
                    ->formatStateUsing(fn ($state, WhatsappLead $record): string => $record->browserLabel())
                    ->tooltip(fn (WhatsappLead $record): ?string => $record->user_agent)
                    ->toggleable(),
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
                SelectFilter::make('visitor_kind')
                    ->label('Tipo')
                    ->options(WhatsappLead::VISITOR_KINDS)
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        return is_string($value) && $value !== ''
                            ? $query->ofVisitorKind($value)
                            : $query;
                    }),
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
