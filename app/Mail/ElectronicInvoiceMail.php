<?php

namespace App\Mail;

use App\Models\ElectronicInvoice;
use App\Services\Invoicing\ElectronicInvoicePdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ElectronicInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ElectronicInvoice $invoice) {}

    public function envelope(): Envelope
    {
        $number = $this->invoice->full_number ?: ('FACTURA-'.$this->invoice->id);

        return new Envelope(
            subject: "Factura {$number} — Smart Tech Security",
            replyTo: [new Address(config('contact.email'), 'Smart Tech Security')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.electronic-invoice',
            with: [
                'downloadUrl' => URL::temporarySignedRoute(
                    'invoices.pdf',
                    now()->addDays(30),
                    ['invoice' => $this->invoice],
                ),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = app(ElectronicInvoicePdf::class)->make($this->invoice->loadMissing('details'));
        $name = ($this->invoice->full_number ?: 'FACTURA-'.$this->invoice->id).'.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $name)->withMime('application/pdf'),
        ];
    }
}
