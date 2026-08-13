<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Domain\Invoicing\QuoteToCollectionAccountMapper;
use App\Domain\Invoicing\QuoteToElectronicInvoiceMapper;
use App\Filament\Resources\CollectionAccounts\CollectionAccountResource;
use App\Filament\Resources\ElectronicInvoices\ElectronicInvoiceResource;
use App\Filament\Resources\Quotes\Pages\Concerns\SyncsQuoteClient;
use App\Filament\Resources\Quotes\QuoteResource;
use App\Support\Filament\PdfPreviewModal;
use App\Mail\FormalQuoteMail;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Mail;

class EditQuote extends EditRecord
{
    use SyncsQuoteClient;

    protected static string $resource = QuoteResource::class;

    protected function afterSave(): void
    {
        $this->syncQuoteClient();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('duplicate')
                ->label('Duplicar')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Duplicar cotización')
                ->modalDescription('Se creará un borrador con los mismos datos e ítems para que ajustes lo necesario.')
                ->action(function (Quote $record) {
                    $copy = $record->duplicate();

                    Notification::make()
                        ->title('Cotización duplicada')
                        ->body(($copy->quote_number ?: 'Borrador').' lista para editar.')
                        ->success()
                        ->send();

                    return redirect(QuoteResource::getUrl('edit', ['record' => $copy]));
                }),
            Action::make('whatsapp')
                ->label('Abrir WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(fn (Quote $record): string => $record->whatsapp_link)
                ->openUrlInNewTab(),
            Action::make('preview')
                ->label('Vista previa')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->modalHeading(fn (Quote $record): string => 'Vista previa · '.($record->quote_number ?: 'COT-'.$record->id))
                ->modalDescription('Se muestra lo guardado. Guarde cambios antes de previsualizar para ver la versión más reciente.')
                ->modalWidth(Width::FiveExtraLarge)
                ->modalContent(function (Quote $record): HtmlString {
                    return PdfPreviewModal::content(
                        route('admin.quotes.pdf-preview', $record),
                        'Vista previa · '.($record->quote_number ?: 'COT-'.$record->id),
                    );
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar'),
            Action::make('pdf')
                ->label('Descargar PDF')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->url(fn (Quote $record): string => route('admin.quotes.pdf-download', $record))
                ->openUrlInNewTab(),
            Action::make('send')
                ->label('Enviar al cliente')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Enviar cotización por correo')
                ->modalDescription(fn (Quote $record): string => "Se enviará el PDF a {$record->email}.")
                ->visible(fn (Quote $record): bool => filled($record->email) && $record->items()->exists())
                ->action(function (Quote $record): void {
                    $record->recalculateTotals();
                    $record->issued_at = $record->issued_at ?: now();

                    Mail::to($record->email)->send(new FormalQuoteMail($record->fresh('items')));

                    $record->forceFill([
                        'status' => 'sent',
                        'issued_at' => $record->issued_at,
                        'sent_at' => now(),
                    ])->save();

                    Notification::make()
                        ->title('Cotización enviada')
                        ->body("El documento se envió a {$record->email}.")
                        ->success()
                        ->send();
                }),
            Action::make('generateInvoice')
                ->label('Generar factura')
                ->icon(Heroicon::OutlinedReceiptPercent)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Generar factura desde cotización')
                ->modalDescription('Se creará una factura con los mismos ítems y datos del cliente. Luego podrás completar documento fiscal, emitir a DIAN y enviarla.')
                ->visible(fn (Quote $record): bool => $record->items()->exists())
                ->action(function (Quote $record, QuoteToElectronicInvoiceMapper $mapper) {
                    try {
                        $invoice = $mapper->fromQuote($record);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('No se pudo generar la factura')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($record->status !== 'accepted') {
                        $record->forceFill([
                            'status' => 'accepted',
                            'accepted_at' => $record->accepted_at ?: now(),
                        ])->save();
                    }

                    Notification::make()
                        ->title('Factura creada')
                        ->body($invoice->display_number.' lista para revisar y enviar.')
                        ->success()
                        ->send();

                    return redirect(ElectronicInvoiceResource::getUrl('edit', ['record' => $invoice]));
                }),
            Action::make('generateCollectionAccount')
                ->label('Generar cuenta de cobro')
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Generar cuenta de cobro')
                ->modalDescription('Documento comercial (no DIAN) con los mismos ítems y datos del cliente, listo para PDF, correo o WhatsApp.')
                ->visible(fn (Quote $record): bool => $record->items()->exists())
                ->action(function (Quote $record, QuoteToCollectionAccountMapper $mapper) {
                    try {
                        $account = $mapper->fromQuote($record);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('No se pudo generar la cuenta de cobro')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($record->status !== 'accepted') {
                        $record->forceFill([
                            'status' => 'accepted',
                            'accepted_at' => $record->accepted_at ?: now(),
                        ])->save();
                    }

                    Notification::make()
                        ->title('Cuenta de cobro creada')
                        ->body($account->number.' lista para revisar y enviar.')
                        ->success()
                        ->send();

                    return redirect(CollectionAccountResource::getUrl('edit', ['record' => $account]));
                }),
            DeleteAction::make(),
        ];
    }
}

