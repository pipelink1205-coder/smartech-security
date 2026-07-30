<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
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
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
                    $url = route('admin.quotes.pdf-preview', $record);

                    return new HtmlString(
                        '<div class="space-y-3">'
                        .'<iframe src="'.e($url).'" title="Vista previa PDF" class="w-full rounded-xl border border-gray-200 dark:border-gray-700" style="height:75vh;background:#f8fafc;"></iframe>'
                        .'</div>'
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
            DeleteAction::make(),
        ];
    }
}
