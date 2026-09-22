<?php

namespace App\Mail;

use App\Models\PrayerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SomeonePrayedForYou extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PrayerRequest $prayerRequest
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alguien ha orado por tu petición 🙏',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.prayed',
        );
    }
}
