<?php

namespace App\Mail;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class FormalQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Cotización {$this->quote->quote_number} — Smart Tech Security",
            replyTo: [new Address(config('contact.email'), 'Smart Tech Security')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.formal-quote',
            with: [
                'downloadUrl' => URL::temporarySignedRoute(
                    'quotes.pdf',
                    now()->addDays(30),
                    ['quote' => $this->quote],
                ),
            ],
        );
    }

    public function attachments(): array
    {
        $this->quote->loadMissing('items');
        $pdf = Pdf::loadView('pdf.quote', ['quote' => $this->quote])
            ->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                ($this->quote->quote_number ?: 'COT-'.$this->quote->id).'.pdf',
            )->withMime('application/pdf'),
        ];
    }
}
