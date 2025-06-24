<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable;
    use SerializesModels;
    public $data;
    public $subject;

    public function __construct($data, $subject)
    {
        $this->data = $data;
        $this->subject = $subject;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this -> subject,
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking',
        );
    }
}
