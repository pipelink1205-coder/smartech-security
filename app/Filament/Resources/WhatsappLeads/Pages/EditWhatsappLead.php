<?php

namespace App\Filament\Resources\WhatsappLeads\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Filament\Resources\WhatsappLeads\WhatsappLeadResource;
use App\Models\WhatsappLead;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditWhatsappLead extends EditRecord
{
    protected static string $resource = WhatsappLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('Escribirle por WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(fn (WhatsappLead $record): string => $record->admin_whatsapp_url)
                ->openUrlInNewTab(),
            Action::make('toQuote')
                ->label('Crear cotización')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('gray')
                ->visible(fn (WhatsappLead $record): bool => blank($record->quote_id))
                ->requiresConfirmation()
                ->modalHeading('Crear cotización desde este contacto')
                ->action(function (WhatsappLead $record) {
                    $quote = $record->toQuote();

                    Notification::make()
                        ->title('Cotización creada')
                        ->body(($quote->quote_number ?: 'Borrador').' lista para editar.')
                        ->success()
                        ->send();

                    return redirect(QuoteResource::getUrl('edit', ['record' => $quote]));
                }),
            Action::make('openQuote')
                ->label('Ver cotización')
                ->icon(Heroicon::OutlinedDocumentText)
                ->visible(fn (WhatsappLead $record): bool => filled($record->quote_id))
                ->url(fn (WhatsappLead $record): string => QuoteResource::getUrl('edit', ['record' => $record->quote_id])),
            DeleteAction::make(),
        ];
    }
}
