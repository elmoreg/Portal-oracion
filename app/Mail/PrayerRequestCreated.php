<?php

namespace App\Mail;

use App\Models\PrayerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrayerRequestCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PrayerRequest $prayerRequest
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'El enlace a tu petición de oración 🙏',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.prayer_created',
        );
    }
}
