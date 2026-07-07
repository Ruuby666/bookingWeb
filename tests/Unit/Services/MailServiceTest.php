<?php

namespace Tests\Unit\Services;

use App\Mail\ContactMail;
use App\Mail\ReservationConfirmedMail;
use App\Mail\ReservationSuggestionMail;
use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MailServiceTest extends TestCase
{
    use RefreshDatabase;

    private MailService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MailService;
        Mail::fake();
    }

    private function makeReservation(): Reservation
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest = Guest::factory()->create(['email' => 'guest@example.com']);

        return Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
        ]);
    }

    #[Test]
    public function it_sends_a_booking_notification_to_the_site_owner(): void
    {
        $reservation = $this->makeReservation();

        $this->service->sendBookingNotification($reservation);

        Mail::assertSent(ContactMail::class, fn ($mail) => $mail->hasTo(config('mail.mailers.smtp.username')));
    }

    #[Test]
    public function it_sends_a_reservation_confirmation_to_the_guest(): void
    {
        $reservation = $this->makeReservation();

        $this->service->sendReservationConfirmation($reservation);

        Mail::assertSent(ReservationConfirmedMail::class, fn ($mail) => $mail->hasTo('guest@example.com'));
    }

    #[Test]
    public function it_sends_a_suggestion_note_to_the_guest(): void
    {
        $reservation = $this->makeReservation();

        $this->service->sendSuggestionToGuest($reservation, 'Consider moving to August.');

        Mail::assertSent(
            ReservationSuggestionMail::class,
            fn ($mail) => $mail->hasTo('guest@example.com') && $mail->note === 'Consider moving to August.',
        );
    }
}
