<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu cotización de Smart Tech Security – {$this->quote->service}",
            replyTo: [new Address(config('contact.email'), 'Smart Tech Security')],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.quote-generated');
    }
}
