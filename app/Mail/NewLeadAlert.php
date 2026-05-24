<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔔 Nuevo lead: {$this->quote->name} – {$this->quote->service}",
            replyTo: $this->quote->email
                ? [new Address($this->quote->email, $this->quote->name)]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-lead-alert');
    }
}
