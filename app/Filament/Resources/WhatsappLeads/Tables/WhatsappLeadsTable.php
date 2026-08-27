<?php

namespace App\Filament\Resources\WhatsappLeads\Tables;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\WhatsappLead;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WhatsappLeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Quién')
                    ->searchable()
                    ->sortable()
                    ->description(fn (WhatsappLead $record): ?string => $record->phone),
                TextColumn::make('click_count')
                    ->label('Clics')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('service')
                    ->label('Servicio')
                    ->searchable()
                    ->limit(28),
                TextColumn::make('source')
                    ->label('Botón')
                    ->formatStateUsing(fn (?string $state): string => WhatsappLead::SOURCES[$state ?? 'link'] ?? (string) $state)
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Último clic')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('source')
                    ->label('Botón')
                    ->options(WhatsappLead::SOURCES),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (WhatsappLead $record): string => $record->admin_whatsapp_url)
                    ->openUrlInNewTab(),
                Action::make('toQuote')
                    ->label('Cotizar')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('gray')
                    ->visible(fn (WhatsappLead $record): bool => blank($record->quote_id))
                    ->requiresConfirmation()
                    ->modalHeading('Crear cotización desde este contacto')
                    ->modalDescription('Se abre una cotización con el nombre, teléfono y servicio. El lead queda marcado como “Pasó a cotización”.')
                    ->action(function (WhatsappLead $record) {
                        $quote = $record->toQuote();

                        Notification::make()
                            ->title('Cotización creada')
                            ->body(($quote->quote_number ?: 'Borrador').' lista para editar.')
                            ->success()
                            ->send();

                        return redirect(QuoteResource::getUrl('edit', ['record' => $quote]));
                    }),
                EditAction::make()->label('Abrir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
