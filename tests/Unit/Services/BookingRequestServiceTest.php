<?php

namespace Tests\Unit\Services;

use App\Data\BookingData;
use App\Events\BookingCreated;
use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Services\BookingDateService;
use App\Services\BookingRequestService;
use App\Services\BookingValidationService;
use App\Services\GuestService;
use App\Services\ReservationPriceService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingRequestService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new BookingRequestService(
            new ReservationService,
            new GuestService,
            new BookingDateService,
            new BookingValidationService(new ReservationService),
            new ReservationPriceService,
        );
    }

    private function makeProperty(array $overrides = []): Property
    {
        $owner = User::factory()->create(['is_admin' => true]);

        return Property::factory()->create(array_merge([
            'owner_id' => $owner->id,
            'min_nights' => 1,
            'price_per_night' => 100.00,
        ], $overrides));
    }

    private function bookingData(array $overrides = []): BookingData
    {
        return new BookingData(
            propertyId: $overrides['propertyId'] ?? 0,
            name: $overrides['name'] ?? 'Alice Smith',
            email: $overrides['email'] ?? 'alice@example.com',
            phone: $overrides['phone'] ?? '600111222',
            adults: $overrides['adults'] ?? 2,
            children: $overrides['children'] ?? 0,
            daterange: $overrides['daterange'] ?? '01/07/2026 - 04/07/2026',
            message: $overrides['message'] ?? null,
        );
    }

    #[Test]
    public function it_creates_a_pending_reservation_for_a_valid_booking(): void
    {
        Event::fake();
        $property = $this->makeProperty();

        $result = $this->service->process($property, $this->bookingData(['propertyId' => $property->id]));

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('reservations', [
            'property_id' => $property->id,
            'status' => 'pending',
            'total_price' => 300.00, // 3 nights at 100.00/night
        ]);
        $this->assertDatabaseHas('guests', ['email' => 'alice@example.com']);
        Event::assertDispatched(BookingCreated::class);
    }

    #[Test]
    public function it_reuses_an_existing_guest_instead_of_creating_a_duplicate(): void
    {
        Event::fake();
        $property = $this->makeProperty();
        $existingGuest = Guest::factory()->create(['email' => 'alice@example.com']);

        $this->service->process($property, $this->bookingData(['propertyId' => $property->id]));

        $this->assertEquals(1, Guest::where('email', 'alice@example.com')->count());
        $this->assertDatabaseHas('reservations', ['guest_id' => $existingGuest->id]);
    }

    #[Test]
    public function it_fails_gracefully_on_an_invalid_daterange_and_creates_nothing(): void
    {
        Event::fake();
        $property = $this->makeProperty();

        $result = $this->service->process(
            $property,
            $this->bookingData(['propertyId' => $property->id, 'daterange' => 'not-a-valid-range']),
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid date format', $result['error']);
        $this->assertDatabaseCount('reservations', 0);
        Event::assertNotDispatched(BookingCreated::class);
    }

    #[Test]
    public function it_fails_when_stay_is_below_the_property_minimum_nights(): void
    {
        Event::fake();
        $property = $this->makeProperty(['min_nights' => 5]);

        $result = $this->service->process(
            $property,
            $this->bookingData(['propertyId' => $property->id, 'daterange' => '01/07/2026 - 03/07/2026']),
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('minimum of 5 nights', $result['error']);
        $this->assertDatabaseCount('reservations', 0);
    }

    #[Test]
    public function it_fails_when_dates_overlap_a_confirmed_reservation(): void
    {
        Event::fake();
        $property = $this->makeProperty();
        $guest = Guest::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => Carbon::parse('2026-07-02'),
            'check_out' => Carbon::parse('2026-07-06'),
        ]);

        $result = $this->service->process(
            $property,
            $this->bookingData(['propertyId' => $property->id, 'daterange' => '01/07/2026 - 04/07/2026']),
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Select other date range', $result['error']);
        $this->assertDatabaseCount('reservations', 1); // only the pre-existing confirmed one
        Event::assertNotDispatched(BookingCreated::class);
    }

    #[Test]
    public function it_calculates_total_price_server_side_ignoring_any_client_value(): void
    {
        Event::fake();
        $property = $this->makeProperty(['price_per_night' => 50.00]);

        $this->service->process($property, $this->bookingData([
            'propertyId' => $property->id,
            'daterange' => '01/07/2026 - 06/07/2026', // 5 nights
        ]));

        $this->assertDatabaseHas('reservations', [
            'property_id' => $property->id,
            'total_price' => 250.00,
        ]);
    }
}
