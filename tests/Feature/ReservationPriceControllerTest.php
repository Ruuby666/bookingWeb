<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\ReservationPrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
    public function reservation_prices_list_is_paginated(): void
    {
        $admin = $this->admin();
        $property = Property::factory()->create(['owner_id' => $admin->id]);

        for ($i = 0; $i < 25; $i++) {
            ReservationPrice::create([
                'property_id' => $property->id,
                'start_date' => Carbon::parse('2026-01-01')->addMonths($i)->startOfDay(),
                'end_date' => Carbon::parse('2026-01-05')->addMonths($i)->endOfDay(),
                'price_per_night' => 100.00,
            ]);
        }

        $response = $this->actingAs($admin)
            ->get(route('admin.reservation_prices'))
            ->assertOk();

        $this->assertCount(20, $response->viewData('reservationPrices'));
        $this->assertTrue($response->viewData('reservationPrices')->hasMorePages());
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
