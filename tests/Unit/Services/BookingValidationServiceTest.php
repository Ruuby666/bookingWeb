<?php

namespace Tests\Unit\Services;

use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Services\BookingValidationService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingValidationServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingValidationService(new ReservationService);
    }

    private function makeProperty(array $overrides = []): Property
    {
        $owner = User::factory()->create(['is_admin' => true]);

        return Property::factory()->create(array_merge(['owner_id' => $owner->id], $overrides));
    }

    // -----------------------------------------------------------------------
    // validateMinimumNights
    // -----------------------------------------------------------------------

    #[Test]
    public function it_passes_when_nights_meet_the_minimum(): void
    {
        $property = $this->makeProperty(['min_nights' => 3]);

        $this->service->validateMinimumNights(
            $property,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-04'),
        );

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function it_throws_when_nights_are_below_the_minimum(): void
    {
        $property = $this->makeProperty(['min_nights' => 5]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This property requires a minimum of 5 nights. You selected 2 nights.');

        $this->service->validateMinimumNights(
            $property,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-03'),
        );
    }

    // -----------------------------------------------------------------------
    // validateAvailability
    // -----------------------------------------------------------------------

    #[Test]
    public function it_passes_when_no_confirmed_reservation_overlaps(): void
    {
        $property = $this->makeProperty();

        $this->service->validateAvailability(
            $property,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-05'),
        );

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function it_throws_when_a_confirmed_reservation_overlaps(): void
    {
        $property = $this->makeProperty();
        $guest = Guest::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => Carbon::parse('2026-07-03'),
            'check_out' => Carbon::parse('2026-07-09'),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Select other date range');

        $this->service->validateAvailability(
            $property,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-05'),
        );
    }

    #[Test]
    public function it_ignores_pending_reservations_when_checking_availability(): void
    {
        $property = $this->makeProperty();
        $guest = Guest::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'pending',
            'check_in' => Carbon::parse('2026-07-03'),
            'check_out' => Carbon::parse('2026-07-09'),
        ]);

        $this->service->validateAvailability(
            $property,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-05'),
        );

        $this->expectNotToPerformAssertions();
    }

    // -----------------------------------------------------------------------
    // validate (minimum nights checked before availability)
    // -----------------------------------------------------------------------

    #[Test]
    public function it_reports_the_minimum_nights_error_before_checking_availability(): void
    {
        $property = $this->makeProperty(['min_nights' => 5]);
        $guest = Guest::factory()->create();

        // Overlapping confirmed reservation AND below minimum nights.
        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => Carbon::parse('2026-07-01'),
            'check_out' => Carbon::parse('2026-07-03'),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minimum of 5 nights');

        $this->service->validate(
            $property,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-03'),
        );
    }
}
