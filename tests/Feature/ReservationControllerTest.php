<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\ReservationPrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
        $guest = User::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => Carbon::parse('2026-06-01'),
            'check_out' => Carbon::parse('2026-06-07'),
        ]);

        // Pending reservation – should NOT appear
        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
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

// ============================================================================

class ReservationPriceControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    // -----------------------------------------------------------------------
    // index
    // -----------------------------------------------------------------------

    #[Test]
    public function admin_can_view_reservation_prices_index(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.reservation_prices'))
            ->assertOk()
            ->assertViewIs('admin.reservation_price');
    }

    #[Test]
    public function guest_is_redirected_from_reservation_prices_index(): void
    {
        $this->get(route('admin.reservation_prices'))
            ->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // create (POST)
    // -----------------------------------------------------------------------

    #[Test]
    public function admin_can_create_a_price_range(): void
    {
        $admin = $this->admin();
        $property = Property::factory()->create(['owner_id' => $admin->id]);

        $this->actingAs($admin)
            ->post(route('reservation-prices.create'), [
                'property_id' => $property->id,
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-15',
                'price_per_night' => 180.00,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservation_prices', [
            'property_id' => $property->id,
            'price_per_night' => 180.00,
        ]);
    }

    #[Test]
    public function price_range_creation_fails_with_missing_fields(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('reservation-prices.create'), [])
            ->assertSessionHasErrors();
    }

    // -----------------------------------------------------------------------
    // destroy (DELETE)
    // -----------------------------------------------------------------------

    #[Test]
    public function owner_can_delete_their_price_range(): void
    {
        $admin = $this->admin();
        $property = Property::factory()->create(['owner_id' => $admin->id]);
        $price = ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-08-01'),
            'end_date' => Carbon::parse('2026-08-10')->endOfDay(),
            'price_per_night' => 200.00,
        ]);

        $this->actingAs($admin)
            ->delete(route('reservation-prices.destroy', $price->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reservation_prices', ['id' => $price->id]);
    }

    #[Test]
    public function non_owner_cannot_delete_a_price_range(): void
    {
        $owner = $this->admin();
        $otherUser = $this->admin();
        $property = Property::factory()->create(['owner_id' => $owner->id]);
        $price = ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-08-01'),
            'end_date' => Carbon::parse('2026-08-10')->endOfDay(),
            'price_per_night' => 200.00,
        ]);

        $this->actingAs($otherUser)
            ->delete(route('reservation-prices.destroy', $price->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('reservation_prices', ['id' => $price->id]);
    }

    // -----------------------------------------------------------------------
    // getPriceRange (API)
    // -----------------------------------------------------------------------

    #[Test]
    public function it_returns_price_breakdown_for_a_date_range(): void
    {
        $owner = $this->admin();
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'price_per_night' => 100.00,
        ]);

        $response = $this->getJson('/api/property-price-range?' . http_build_query([
            'property_id' => $property->id,
            'start_date' => '2026-06-01 00:00:00 GMT+0000',
            'end_date' => '2026-06-04 00:00:00 GMT+0000',
        ]));

        $response->assertOk()
            ->assertJsonCount(3); // 3 nights: June 1, 2, 3
    }
}
