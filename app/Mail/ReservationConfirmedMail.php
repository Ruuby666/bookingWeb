<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Confirmación de Reserva')
                    ->view('emails.reservation-confirmed')
                    ->attach(public_path('images/nameEMLBlack.png'), [
                    'as' => 'nameEMLBlack.png',
                    'mime' => 'image/png',
                ]);
    }
}
