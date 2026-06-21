<?php

namespace Tests\Unit;

use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\ReservationPrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // User model
    // -----------------------------------------------------------------------

    #[Test]
    public function user_is_admin_returns_true_for_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->assertTrue($admin->isAdmin());
    }

    #[Test]
    public function user_is_admin_returns_false_for_regular_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->assertFalse($user->isAdmin());
    }

    #[Test]
    public function user_password_is_hashed_via_mutator(): void
    {
        $user = User::factory()->create(['password' => 'plain-password']);

        // The mutator should have hashed it; plain text should not match stored value
        $this->assertNotEquals('plain-password', $user->password);
    }

    #[Test]
    public function owner_has_many_properties(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        Property::factory()->count(2)->create(['owner_id' => $owner->id]);

        $this->assertCount(2, $owner->properties);
    }

    #[Test]
    public function guest_has_many_reservations(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest = Guest::factory()->create();

        Reservation::factory()->count(3)->create([
            'guest_id' => $guest->id,
            'property_id' => $property->id,
        ]);

        $this->assertCount(3, $guest->reservations);
    }

    // -----------------------------------------------------------------------
    // Property model
    // -----------------------------------------------------------------------

    #[Test]
    public function property_belongs_to_owner(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $this->assertEquals($owner->id, $property->owner->id);
    }

    #[Test]
    public function property_has_many_reservations(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest = Guest::factory()->create();

        Reservation::factory()->count(2)->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
        ]);

        $this->assertCount(2, $property->reservations);
    }

    #[Test]
    public function property_has_many_reservation_prices(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-06-01'),
            'end_date' => Carbon::parse('2026-06-15')->endOfDay(),
            'price_per_night' => 120.00,
        ]);

        $this->assertCount(1, $property->reservationPrices);
    }

    #[Test]
    public function property_price_for_date_returns_custom_price_when_in_range(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'price_per_night' => 100.00,
        ]);

        ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-07-01'),
            'end_date' => Carbon::parse('2026-07-31')->endOfDay(),
            'price_per_night' => 200.00,
        ]);

        $price = $property->priceForDate('2026-07-15');

        $this->assertEquals(200.00, $price);
    }

    #[Test]
    public function property_price_for_date_returns_null_outside_range(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-07-01'),
            'end_date' => Carbon::parse('2026-07-15')->endOfDay(),
            'price_per_night' => 200.00,
        ]);

        $price = $property->priceForDate('2026-08-01');

        $this->assertNull($price);
    }

    // -----------------------------------------------------------------------
    // Reservation model
    // -----------------------------------------------------------------------

    #[Test]
    public function reservation_belongs_to_guest(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest = Guest::factory()->create();
        $reservation = Reservation::factory()->create([
            'guest_id' => $guest->id,
            'property_id' => $property->id,
        ]);

        $this->assertEquals($guest->id, $reservation->guest->id);
    }

    #[Test]
    public function reservation_belongs_to_property(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest = Guest::factory()->create();
        $reservation = Reservation::factory()->create([
            'guest_id' => $guest->id,
            'property_id' => $property->id,
        ]);

        $this->assertEquals($property->id, $reservation->property->id);
    }

    #[Test]
    public function reservation_casts_check_in_and_check_out_as_datetime(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest = Guest::factory()->create();

        $reservation = Reservation::factory()->create([
            'guest_id' => $guest->id,
            'property_id' => $property->id,
            'check_in' => '2026-09-01 15:00:00',
            'check_out' => '2026-09-07 11:00:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $reservation->check_in);
        $this->assertInstanceOf(Carbon::class, $reservation->check_out);
    }

    // -----------------------------------------------------------------------
    // ReservationPrice model
    // -----------------------------------------------------------------------

    #[Test]
    public function reservation_price_belongs_to_property(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $price = ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-06-01'),
            'end_date' => Carbon::parse('2026-06-15')->endOfDay(),
            'price_per_night' => 150.00,
        ]);

        $this->assertEquals($property->id, $price->property->id);
    }
}
