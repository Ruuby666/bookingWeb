<?php

namespace App\Listeners;

use App\Events\ReservationConfirmed;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReservationConfirmationEmail implements ShouldQueue
{
    public function __construct(
        private readonly MailService $mailService,
    ) {}

    public function handle(ReservationConfirmed $event): void
    {
        $this->mailService->sendReservationConfirmation(
            $event->reservation,
        );
    }
}