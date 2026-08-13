<?php

namespace App\Filament\Resources\CollectionAccounts\Pages;

use App\Filament\Resources\CollectionAccounts\CollectionAccountResource;
use App\Mail\CollectionAccountMail;
use App\Models\CollectionAccount;
use App\Models\CollectionAccountItem;
use App\Services\Invoicing\CollectionAccountPdf;
use App\Support\Filament\PdfPreviewModal;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class EditCollectionAccount extends EditRecord
{
    protected static string $resource = CollectionAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Vista previa')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->modalHeading(fn (CollectionAccount $record): string => 'Vista previa · '.$record->number)
                ->modalWidth(Width::FiveExtraLarge)
                ->modalContent(function (CollectionAccount $record): HtmlString {
                    return PdfPreviewModal::content(
                        route('admin.collection-accounts.pdf-preview', $record),
                        'Vista previa · '.$record->number,
                    );
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar'),

            Action::make('pdf')
                ->label('Descargar PDF')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->url(fn (CollectionAccount $record): string => route('admin.collection-accounts.pdf-download', $record))
                ->openUrlInNewTab(),

            Action::make('send')
                ->label('Enviar al cliente')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('primary')
                ->modalHeading('Enviar cuenta de cobro')
                ->modalDescription('Correo y WhatsApp prellenados. Puedes editarlos antes de enviar.')
                ->fillForm(fn (CollectionAccount $record): array => [
                    'email' => $record->client_email,
                    'phone' => $record->client_phone,
                    'send_email' => filled($record->client_email),
                    'open_whatsapp' => filled($record->client_phone),
                ])
                ->form([
                    TextInput::make('email')
                        ->label('Correo')
                        ->email()
                        ->required(fn ($get) => (bool) $get('send_email')),
                    TextInput::make('phone')
                        ->label('WhatsApp')
                        ->tel()
                        ->required(fn ($get) => (bool) $get('open_whatsapp')),
                    Toggle::make('send_email')
                        ->label('Enviar por correo (PDF adjunto)')
                        ->default(true),
                    Toggle::make('open_whatsapp')
                        ->label('Abrir WhatsApp con mensaje y enlace PDF')
                        ->default(true),
                ])
                ->action(function (CollectionAccount $record, array $data, CollectionAccountPdf $pdfService) {
                    if (empty($data['send_email']) && empty($data['open_whatsapp'])) {
                        Notification::make()
                            ->title('Elige al menos un canal')
                            ->warning()
                            ->send();

                        return;
                    }

                    $record->forceFill([
                        'client_email' => $data['email'] ?? $record->client_email,
                        'client_phone' => $data['phone'] ?? $record->client_phone,
                    ])->save();

                    $pdfService->store($record);

                    if (! empty($data['send_email'])) {
                        Mail::to($data['email'])->send(new CollectionAccountMail($record->fresh(['items', 'quote'])));
                        $record->forceFill([
                            'sent_at' => now(),
                            'status' => $record->status === 'paid' ? 'paid' : 'sent',
                        ])->save();
                    }

                    Notification::make()
                        ->title('Listo')
                        ->body(
                            (! empty($data['send_email']) ? "Correo enviado a {$data['email']}. " : '')
                            .(! empty($data['open_whatsapp']) ? 'Abriendo WhatsApp…' : '')
                        )
                        ->success()
                        ->send();

                    if (! empty($data['open_whatsapp'])) {
                        return redirect()->away(
                            $record->whatsappLink($data['phone'] ?? null, $record->temporaryPdfUrl())
                        );
                    }
                }),

            DeleteAction::make()
                ->visible(fn (CollectionAccount $record): bool => $record->status !== 'paid'),
        ];
    }

    protected function afterSave(): void
    {
        $this->recalculateTotals($this->record);
    }

    private function recalculateTotals(CollectionAccount $account): void
    {
        $account->load('items');
        $total = 0.0;

        foreach ($account->items as $line) {
            /** @var CollectionAccountItem $line */
            $lineTotal = round((float) $line->quantity * (float) $line->unit_price, 2);
            if ((float) $line->line_total !== $lineTotal) {
                $line->forceFill(['line_total' => $lineTotal])->saveQuietly();
            }
            $total += $lineTotal;
        }

        $account->forceFill([
            'total' => round($total, 2),
        ])->saveQuietly();
    }
}
