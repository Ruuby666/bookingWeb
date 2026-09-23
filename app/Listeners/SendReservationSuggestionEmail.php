<?php

namespace App\Listeners;

use App\Events\ReservationSuggestionRequested;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReservationSuggestionEmail implements ShouldQueue
{
    public function __construct(
        private readonly MailService $mailService,
    ) {}

    public function handle(ReservationSuggestionRequested $event): void
    {
        $this->mailService->sendSuggestionToGuest($event->reservation, $event->note);
    }
}
