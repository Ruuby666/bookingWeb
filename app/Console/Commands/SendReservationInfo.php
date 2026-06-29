<?php

namespace App\Console\Commands;

use App\Mail\ReservationInfoMail;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReservationInfo extends Command
{
    protected $signature = 'app:send-reservation-info';

    protected $description = 'Command description';

    // To try this command, you can run it manually using the following Artisan command:
    // docker compose exec app php artisan app:send-reservation-info
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $reservations = Reservation::with(['guest', 'property'])
            ->where('status', 'confirmed')
            ->whereDate('check_in', $tomorrow)
            ->get();

        foreach ($reservations as $reservation) {
            if ($reservation->guest && ! empty($reservation->guest->email)) {
                Mail::to($reservation->guest->email)->send(new ReservationInfoMail($reservation));
            }
        }

        $this->info('Reservation info sent for tomorrow\'s confirmed reservations.');
    }
}
