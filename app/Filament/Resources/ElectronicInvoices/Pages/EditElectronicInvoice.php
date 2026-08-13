<?php

namespace App\Filament\Resources\ElectronicInvoices\Pages;

use App\Domain\Invoicing\QuoteToElectronicInvoiceMapper;
use App\Filament\Resources\ElectronicInvoices\ElectronicInvoiceResource;
use App\Mail\ElectronicInvoiceMail;
use App\Models\ElectronicInvoice;
use App\Models\ElectronicInvoiceItem;
use App\Services\Dian\DianConfig;
use App\Services\Dian\DianService;
use App\Services\Invoicing\ElectronicInvoicePdf;
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

class EditElectronicInvoice extends EditRecord
{
    protected static string $resource = ElectronicInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('assignNumber')
                ->label('Asignar número DIAN')
                ->icon(Heroicon::OutlinedHashtag)
                ->color('gray')
                ->visible(fn (ElectronicInvoice $record): bool => blank($record->dian_numero))
                ->requiresConfirmation()
                ->modalHeading('Asignar consecutivo DIAN')
                ->modalDescription('Requiere una resolución activa en dian_resolutions.')
                ->action(function (ElectronicInvoice $record, QuoteToElectronicInvoiceMapper $mapper): void {
                    $mapper->tryAssignNumber($record);
                    $record->refresh();

                    if (blank($record->dian_numero)) {
                        Notification::make()
                            ->title('Sin resolución activa')
                            ->body('Configura una resolución DIAN antes de numerar.')
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Número asignado')
                        ->body($record->display_number)
                        ->success()
                        ->send();
                }),

            Action::make('preview')
                ->label('Vista previa')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->modalHeading(fn (ElectronicInvoice $record): string => 'Vista previa · '.$record->display_number)
                ->modalWidth(Width::FiveExtraLarge)
                ->modalContent(function (ElectronicInvoice $record): HtmlString {
                    return PdfPreviewModal::content(
                        route('admin.invoices.pdf-preview', $record),
                        'Vista previa · '.$record->display_number,
                    );
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar'),

            Action::make('pdf')
                ->label('Descargar PDF')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->url(fn (ElectronicInvoice $record): string => route('admin.invoices.pdf-download', $record))
                ->openUrlInNewTab(),

            Action::make('emitDian')
                ->label('Emitir a DIAN')
                ->icon(Heroicon::OutlinedCloudArrowUp)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Emitir factura electrónica')
                ->modalDescription('Envía el XML firmado al ambiente configurado. Requiere kill switch .env, interruptor del panel, certificado y resolución activa.')
                ->visible(fn (): bool => app(DianConfig::class)->isEnabled())
                ->action(function (ElectronicInvoice $record, QuoteToElectronicInvoiceMapper $mapper, DianService $dian): void {
                    $this->recalculateTotals($record);
                    $mapper->tryAssignNumber($record);
                    $record->refresh();

                    if (! $record->isElectronic()) {
                        Notification::make()
                            ->title('Falta numeración DIAN')
                            ->body('Asigna un número con resolución activa.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if (blank($record->client_document)) {
                        Notification::make()
                            ->title('Falta documento del cliente')
                            ->body('Completa el NIT/CC del adquiriente antes de emitir.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $result = $dian->sendInvoice($record->fresh('details'));

                    Notification::make()
                        ->title($result->dian_status === 'ACCEPTED' ? 'Aceptada por DIAN' : 'Resultado DIAN: '.$result->dian_status)
                        ->body($result->dian_description ?: 'Sin descripción')
                        ->color($result->dian_status === 'ACCEPTED' ? 'success' : 'warning')
                        ->send();
                }),

            Action::make('send')
                ->label('Enviar al cliente')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('primary')
                ->modalHeading('Enviar factura')
                ->modalDescription('Correo y WhatsApp prellenados desde la cotización / factura. Puedes editarlos.')
                ->fillForm(fn (ElectronicInvoice $record): array => [
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
                ->action(function (ElectronicInvoice $record, array $data, ElectronicInvoicePdf $pdfService) {
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
                        Mail::to($data['email'])->send(new ElectronicInvoiceMail($record->fresh(['details', 'quote'])));
                        $record->forceFill(['sent_at' => now()])->save();
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
                        $url = $record->whatsappLink($data['phone'] ?? null, $record->temporaryPdfUrl());

                        return redirect()->away($url);
                    }
                }),

            DeleteAction::make()
                ->visible(fn (ElectronicInvoice $record): bool => ! in_array($record->dian_status, ['ACCEPTED', 'SIGNED'], true)),
        ];
    }

    protected function afterSave(): void
    {
        $this->recalculateTotals($this->record);
    }

    private function recalculateTotals(ElectronicInvoice $invoice): void
    {
        $invoice->load('details');
        $factor = 1 + (((float) (\App\Models\Setting::where('key', 'iva_rate')->value('value') ?? 19)) / 100);

        $base = 0.0;
        $iva = 0.0;
        $total = 0.0;

        foreach ($invoice->details as $line) {
            /** @var ElectronicInvoiceItem $line */
            $lineTotal = (float) $line->quantity * (float) $line->price;
            $lineBase = round($lineTotal / $factor, 2);
            $lineIva = round($lineTotal - $lineBase, 2);
            $base += $lineBase;
            $iva += $lineIva;
            $total += $lineTotal;
        }

        $invoice->forceFill([
            'subtotal' => round($base, 2),
            'iva' => round($iva, 2),
            'total' => round($total, 2),
            'total_a_pagar' => round($total, 2),
        ])->saveQuietly();
    }
}
