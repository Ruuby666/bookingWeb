<?php

namespace App\Services;

use App\Mail\ContactMail;
use App\Mail\ReservationSuggestionMail;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Send a new-booking notification to the site owner.
     *
     * @param  array  $data  Booking data (name, email, checkIn, checkOut, property, …)
     */
    public function sendBookingNotification(array $data): void
    {
        Mail::to(config('mail.mailers.smtp.username'))
            ->send(new ContactMail($data, 'New Booking'));
    }

    /**
     * Send a custom suggestion/note to the guest linked to a reservation.
     */
    public function sendSuggestionToGuest(Reservation $reservation, string $note): void
    {
        Mail::to($reservation->guest->email)
            ->send(new ReservationSuggestionMail($reservation, $note));
    }
}
