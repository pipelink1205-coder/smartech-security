<?php

namespace App\Mail;

use App\Models\CollectionAccount;
use App\Services\Invoicing\CollectionAccountPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class CollectionAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CollectionAccount $account) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Cuenta de cobro {$this->account->number} — Smart Tech Security",
            replyTo: [new Address(config('contact.email'), 'Smart Tech Security')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.collection-account',
            with: [
                'downloadUrl' => URL::temporarySignedRoute(
                    'collection-accounts.pdf',
                    now()->addDays(30),
                    ['account' => $this->account],
                ),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = app(CollectionAccountPdf::class)->make($this->account->loadMissing('items'));

        return [
            Attachment::fromData(fn () => $pdf->output(), $this->account->number.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
