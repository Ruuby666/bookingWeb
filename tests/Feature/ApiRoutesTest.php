<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function amenities(): array
    {
        return [
            'tv' => null,
            'entertainment' => true,
            'parking' => false,
            'pool' => true,
            'garden' => false,
            'safeBox' => false,
            'terrace' => true,
            'wifi' => true,
        ];
    }

    #[Test]
    public function properties_endpoint_returns_list(): void
    {
        Property::factory()->count(2)->create();

        $this->getJson('/api/properties')
            ->assertOk()
            ->assertJsonCount(2);
    }

    #[Test]
    public function properties_endpoint_returns_the_exact_public_field_shape(): void
    {
        Property::factory()->create();

        $this->getJson('/api/properties')
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'location',
                    'description',
                    'images_div',
                    'price_per_night',
                    'capacity',
                    'lat',
                    'lng',
                ],
            ]);
    }

    #[Test]
    public function properties_list_cache_is_invalidated_after_a_property_update(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'title' => 'Original Title',
        ]);

        // Warm the properties_list cache with the original title.
        $this->getJson('/api/properties')
            ->assertJsonFragment(['title' => 'Original Title']);

        $payload = array_merge([
            'title' => 'Updated Title',
            'description' => $property->description,
            'location' => $property->location,
            'price_per_night' => $property->price_per_night,
            'capacity' => $property->capacity,
            'size' => $property->size,
            'bedrooms' => 'King',
            'bathrooms' => $property->bathrooms,
            'min_nights' => $property->min_nights,
            'images_div' => $property->images_div,
            'lat' => $property->lat,
            'lng' => $property->lng,
        ], $this->amenities());

        $this->actingAs($owner)
            ->put(route('properties.update', $property->id), $payload)
            ->assertRedirect(route('admin.properties'));

        $this->getJson('/api/properties')
            ->assertJsonFragment(['title' => 'Updated Title'])
            ->assertJsonMissing(['title' => 'Original Title']);
    }

    #[Test]
    public function reservations_endpoint_returns_confirmed_reservations(): void
    {
        $property = Property::factory()->create();
        $guest = Guest::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => now()->addDay(),
            'check_out' => now()->addDays(2),
        ]);

        // pending reservation should not be returned
        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'pending',
            'check_in' => now()->addDay(),
            'check_out' => now()->addDays(2),
        ]);

        $this->getJson('/api/reservations')
            ->assertOk()
            ->assertJsonCount(1);
    }

    #[Test]
    public function reservations_endpoint_returns_the_exact_public_field_shape(): void
    {
        $property = Property::factory()->create();
        $guest = Guest::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => now()->addDay(),
            'check_out' => now()->addDays(2),
        ]);

        $this->getJson('/api/reservations')
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'property_id',
                    'check_in',
                    'check_out',
                    'status',
                ],
            ]);
    }
}
