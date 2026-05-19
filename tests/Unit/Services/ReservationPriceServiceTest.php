<?php

namespace Tests\Unit\Services;

use App\Models\Property;
use App\Models\ReservationPrice;
use App\Models\User;
use App\Services\ReservationPriceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationPriceServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservationPriceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservationPriceService;
    }

    private function makeOwnerAndProperty(array $propertyOverrides = []): array
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(array_merge(
            ['owner_id' => $owner->id, 'price_per_night' => 100.00],
            $propertyOverrides,
        ));

        return [$owner, $property];
    }

    // -----------------------------------------------------------------------
    // getPriceBreakdown
    // -----------------------------------------------------------------------

    /** @test */
    public function it_returns_default_price_when_no_custom_range_exists(): void
    {
        [, $property] = $this->makeOwnerAndProperty(['price_per_night' => 80.00]);

        $breakdown = $this->service->getPriceBreakdown(
            $property->id,
            Carbon::parse('2026-06-01'),
            Carbon::parse('2026-06-04'),
        );

        $this->assertCount(3, $breakdown); // 3 nights
        foreach ($breakdown as $night) {
            $this->assertEquals(80.00, $night['price']);
        }
    }

    /** @test */
    public function it_uses_custom_price_within_range_and_default_outside(): void
    {
        [, $property] = $this->makeOwnerAndProperty(['price_per_night' => 100.00]);

        ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-06-02'),
            'end_date' => Carbon::parse('2026-06-03')->endOfDay(),
            'price_per_night' => 200.00,
        ]);

        $breakdown = $this->service->getPriceBreakdown(
            $property->id,
            Carbon::parse('2026-06-01'),
            Carbon::parse('2026-06-04'),
        );

        $this->assertEquals(100.00, $breakdown[0]['price']); // June 1 – default
        $this->assertEquals(200.00, $breakdown[1]['price']); // June 2 – custom
        $this->assertEquals(200.00, $breakdown[2]['price']); // June 3 – custom
    }

    // -----------------------------------------------------------------------
    // createPriceRange
    // -----------------------------------------------------------------------

    /** @test */
    public function it_creates_a_price_range_for_the_owner(): void
    {
        [$owner, $property] = $this->makeOwnerAndProperty();
        $this->actingAs($owner);

        $result = $this->service->createPriceRange(
            $property->id,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-10'),
            150.00,
        );

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('reservation_prices', [
            'property_id' => $property->id,
            'price_per_night' => 150.00,
        ]);
    }

    /** @test */
    public function it_returns_error_when_price_range_overlaps(): void
    {
        [$owner, $property] = $this->makeOwnerAndProperty();
        $this->actingAs($owner);

        ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-07-05'),
            'end_date' => Carbon::parse('2026-07-15')->endOfDay(),
            'price_per_night' => 120.00,
        ]);

        $result = $this->service->createPriceRange(
            $property->id,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-10'),
            200.00,
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('overlaps', $result['error']);
    }

    /** @test */
    public function it_returns_unauthorized_error_when_property_does_not_belong_to_user(): void
    {
        [, $property] = $this->makeOwnerAndProperty();
        $otherUser = User::factory()->create(['is_admin' => true]);
        $this->actingAs($otherUser);

        $result = $this->service->createPriceRange(
            $property->id,
            Carbon::parse('2026-07-01'),
            Carbon::parse('2026-07-05'),
            99.00,
        );

        $this->assertFalse($result['success']);
        $this->assertEquals('Unauthorized access.', $result['error']);
    }

    // -----------------------------------------------------------------------
    // deletePriceRange
    // -----------------------------------------------------------------------

    /** @test */
    public function it_deletes_a_price_range_owned_by_the_user(): void
    {
        [$owner, $property] = $this->makeOwnerAndProperty();
        $this->actingAs($owner);

        $price = ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-08-01'),
            'end_date' => Carbon::parse('2026-08-10')->endOfDay(),
            'price_per_night' => 130.00,
        ]);

        $result = $this->service->deletePriceRange($price->id);

        $this->assertTrue($result['success']);
        $this->assertDatabaseMissing('reservation_prices', ['id' => $price->id]);
    }

    /** @test */
    public function it_returns_error_when_deleting_a_price_range_not_owned_by_user(): void
    {
        [, $property] = $this->makeOwnerAndProperty();
        $otherUser = User::factory()->create(['is_admin' => true]);
        $this->actingAs($otherUser);

        $price = ReservationPrice::create([
            'property_id' => $property->id,
            'start_date' => Carbon::parse('2026-08-01'),
            'end_date' => Carbon::parse('2026-08-10')->endOfDay(),
            'price_per_night' => 130.00,
        ]);

        $result = $this->service->deletePriceRange($price->id);

        $this->assertFalse($result['success']);
        $this->assertEquals('Price range not found.', $result['error']);
    }
}
