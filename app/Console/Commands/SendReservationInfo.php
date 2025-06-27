<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationInfoMail;

class SendReservationInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reservation-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $reservations = Reservation::where('status', 'confirmed')
            ->whereDate('check_in', $tomorrow)
            ->get();

        foreach ($reservations as $reservation) {
            if ($reservation->user && !empty($reservation->user->email)) {
                Mail::to($reservation->user->email)->send(new ReservationInfoMail($reservation));
            }
        }

        $this->info('Reservation info sent for tomorrow\'s confirmed reservations.');
    }
}
