<?php

namespace Tests\Unit\Services;

use App\Mail\ReservationConfirmedMail;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservationService();
        Mail::fake();
    }

    private function makeProperty(array $overrides = []): Property
    {
        $owner = User::factory()->create(['is_admin' => true]);
        return Property::factory()->create(array_merge(['owner_id' => $owner->id], $overrides));
    }

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    // -----------------------------------------------------------------------
    // createReservation
    // -----------------------------------------------------------------------

    /** @test */
    public function it_creates_a_pending_reservation(): void
    {
        $property = $this->makeProperty();
        $user     = $this->makeUser();

        $reservation = $this->service->createReservation($property, [
            'checkIn'     => Carbon::parse('2026-07-01 15:00'),
            'checkOut'    => Carbon::parse('2026-07-07 11:00'),
            'message'     => 'Hello!',
            'adults'      => 2,
            'children'    => 1,
            'total_price' => 600.00,
        ], $user);

        $this->assertEquals('pending', $reservation->status);
        $this->assertEquals(3, $reservation->guests);
        $this->assertEquals(600.00, $reservation->total_price);
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'pending']);
    }

    /** @test */
    public function it_swaps_dates_if_check_in_is_after_check_out(): void
    {
        $property = $this->makeProperty();
        $user     = $this->makeUser();

        $reservation = $this->service->createReservation($property, [
            'checkIn'     => Carbon::parse('2026-07-10'),
            'checkOut'    => Carbon::parse('2026-07-01'),
            'adults'      => 2,
            'children'    => 0,
            'total_price' => 500.00,
        ], $user);

        $this->assertTrue($reservation->check_in->lt($reservation->check_out));
    }

    // -----------------------------------------------------------------------
    // confirmReservation
    // -----------------------------------------------------------------------

    /** @test */
    public function it_confirms_a_reservation_and_sends_email(): void
    {
        $property    = $this->makeProperty();
        $user        = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'status'      => 'pending',
            'check_in'    => Carbon::parse('2026-08-01'),
            'check_out'   => Carbon::parse('2026-08-07'),
        ]);

        $result = $this->service->confirmReservation($reservation);

        $this->assertTrue($result['success']);
        $reservation->refresh();
        $this->assertEquals('confirmed', $reservation->status);
        Mail::assertSent(ReservationConfirmedMail::class);
    }

    /** @test */
    public function it_returns_error_when_confirming_overlapping_reservation(): void
    {
        $property = $this->makeProperty();
        $user     = $this->makeUser();

        // Existing confirmed reservation
        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'status'      => 'confirmed',
            'check_in'    => Carbon::parse('2026-08-01'),
            'check_out'   => Carbon::parse('2026-08-10'),
        ]);

        // Overlapping pending reservation
        $pending = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'status'      => 'pending',
            'check_in'    => Carbon::parse('2026-08-05'),
            'check_out'   => Carbon::parse('2026-08-12'),
        ]);

        $result = $this->service->confirmReservation($pending);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('already booked', $result['error']);
        Mail::assertNotSent(ReservationConfirmedMail::class);
    }

    // -----------------------------------------------------------------------
    // updateReservationTime
    // -----------------------------------------------------------------------

    /** @test */
    public function it_updates_reservation_check_in_and_check_out_times(): void
    {
        $property    = $this->makeProperty();
        $user        = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'check_in'    => Carbon::parse('2026-09-01 15:00'),
            'check_out'   => Carbon::parse('2026-09-07 11:00'),
        ]);

        $result = $this->service->updateReservationTime($reservation, '16:00', '12:00');

        $this->assertTrue($result['success']);
        $reservation->refresh();
        $this->assertEquals('16:00', $reservation->check_in->format('H:i'));
        $this->assertEquals('12:00', $reservation->check_out->format('H:i'));
    }

    /** @test */
    public function it_returns_error_when_times_are_invalid_on_same_day(): void
    {
        $property    = $this->makeProperty();
        $user        = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'check_in'    => Carbon::parse('2026-09-01 10:00'),
            'check_out'   => Carbon::parse('2026-09-01 15:00'),
        ]);

        $result = $this->service->updateReservationTime($reservation, '14:00', '10:00');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Check-out time must be after check-in time', $result['error']);
    }

    // -----------------------------------------------------------------------
    // findOverlappingReservation
    // -----------------------------------------------------------------------

    /** @test */
    public function it_finds_an_overlapping_confirmed_reservation(): void
    {
        $property = $this->makeProperty();
        $user     = $this->makeUser();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'status'      => 'confirmed',
            'check_in'    => Carbon::parse('2026-10-01'),
            'check_out'   => Carbon::parse('2026-10-10'),
        ]);

        $overlap = $this->service->findOverlappingReservation(
            $property->id,
            Carbon::parse('2026-10-05'),
            Carbon::parse('2026-10-15')
        );

        $this->assertNotNull($overlap);
    }

    /** @test */
    public function it_returns_null_when_no_overlap_exists(): void
    {
        $property = $this->makeProperty();
        $user     = $this->makeUser();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'status'      => 'confirmed',
            'check_in'    => Carbon::parse('2026-10-01'),
            'check_out'   => Carbon::parse('2026-10-07'),
        ]);

        $overlap = $this->service->findOverlappingReservation(
            $property->id,
            Carbon::parse('2026-10-08'),
            Carbon::parse('2026-10-15')
        );

        $this->assertNull($overlap);
    }

    // -----------------------------------------------------------------------
    // getPendingAndConfirmedForOwner
    // -----------------------------------------------------------------------

    /** @test */
    public function it_returns_pending_and_confirmed_reservations_for_owner(): void
    {
        $owner    = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest    = $this->makeUser();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $guest->id,
            'status'      => 'confirmed',
        ]);
        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id'     => $guest->id,
            'status'      => 'pending',
        ]);

        $this->actingAs($owner);

        $result = $this->service->getPendingAndConfirmedForOwner();

        $this->assertCount(1, $result['confirmed']);
        $this->assertCount(1, $result['pending']);
    }
}
