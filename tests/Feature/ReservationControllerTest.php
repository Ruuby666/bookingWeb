<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // ReservationController – public JSON endpoint
    // -----------------------------------------------------------------------

    #[Test]
    public function it_returns_confirmed_reservations_as_json_for_a_property(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $guest = Guest::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => Carbon::parse('2026-06-01'),
            'check_out' => Carbon::parse('2026-06-07'),
        ]);

        // Pending reservation – should NOT appear
        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'pending',
        ]);

        $this->getJson(route('property.reservations.data', $property->id))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['property_id' => $property->id]);
    }

    #[Test]
    public function it_returns_empty_array_when_no_confirmed_reservations_exist(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $this->getJson(route('property.reservations.data', $property->id))
            ->assertOk()
            ->assertExactJson([]);
    }
}
