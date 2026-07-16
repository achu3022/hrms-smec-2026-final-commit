<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpiryReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $expiringCerts;

    public function __construct($expiringCerts)
    {
        $this->expiringCerts = $expiringCerts;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Document Expiry Reminders - ' . date('d M Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expiry-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
