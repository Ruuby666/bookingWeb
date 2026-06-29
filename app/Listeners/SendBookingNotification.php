<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingNotification implements ShouldQueue
{
    public function __construct(
        private readonly MailService $mailService,
    ) {}

    public function handle(BookingCreated $event): void
    {
        $this->mailService->sendBookingNotification(
            [
                'property' => $event->reservation->property,
                'reservation' => $event->reservation,
            ]
        );
    }
}
