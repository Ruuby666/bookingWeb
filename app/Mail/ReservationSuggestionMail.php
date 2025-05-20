<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationSuggestionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $note;

    public function __construct(Reservation $reservation, string $note)
    {
        $this->reservation = $reservation;
        $this->note = $note;
    }

    public function build()
    {
        return $this->subject('Sugerencia sobre tu reserva en ' . $this->reservation->property->title)
                    ->view('emails.reservation-suggestion');
    }
}
