<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Reservation;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;
    public string $subject;

    public function __construct(Reservation $reservation, string $subject)
    {
        $this->reservation = $reservation;
        $this->subject = $subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking',
        );
    }
}
