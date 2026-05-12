<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    /**
     * Full set of amenity fields required by StorePropertyRequest / UpdatePropertyRequest.
     * tv is nullable|string, all others are required|boolean.
     */
    private function amenities(): array
    {
        return [
            'tv'            => null,   // nullable string
            'entertainment' => true,
            'parking'       => false,
            'pool'          => true,
            'garden'        => false,
            'safeBox'       => false,
            'terrace'       => true,
            'wifi'          => true,
        ];
    }

    // -----------------------------------------------------------------------
    // Show (public)
    // -----------------------------------------------------------------------

    /** @test */
    public function anyone_can_view_a_property_page(): void
    {
        $owner    = $this->admin();
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        // The ImageGallery component requires a non-null mainImage string.
        // Since no images folder exists in tests, we mock PropertyService
        // to return a placeholder so the view renders without crashing.
        $this->mock(PropertyService::class, function ($mock) use ($property) {
            $mock->shouldReceive('getPropertyById')
                 ->andReturn($property);
            $mock->shouldReceive('getImagesForProperty')
                 ->andReturn([
                     'mainImage'          => 'placeholder.jpg',
                     'imagesWithoutFirst' => [],
                 ]);
        });

        $this->get(route('property.show', $property->id))
             ->assertOk()
             ->assertViewIs('property.show');
    }

    /** @test */
    public function it_returns_404_for_nonexistent_property(): void
    {
        $this->get(route('property.show', 9999))
             ->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Create form
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_access_property_create_form(): void
    {
        $this->actingAs($this->admin())
             ->get(route('properties.create'))
             ->assertOk()
             ->assertViewIs('property.add_or_edit_property');
    }

    /** @test */
    public function guest_cannot_access_property_create_form(): void
    {
        // IsAdmin middleware redirects to '/' not '/login'
        $this->get(route('properties.create'))
             ->assertRedirect('/');
    }

    // -----------------------------------------------------------------------
    // Store
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_store_a_new_property(): void
    {
        $admin = $this->admin();

        $data = array_merge([
            'title'           => 'Villa Sunset',
            'description'     => 'Beautiful villa with ocean view.',
            'location'        => 'Lanzarote',
            'price_per_night' => 250.00,
            'capacity'        => 8,
            'size'            => 200,
            'bedrooms'        => 'King, Twin, Double',
            'bathrooms'       => 3,
            'min_nights'      => 3,
            'images_div'      => 'villa_sunset',
            'lat'             => 28.9635,
            'lng'             => -13.5477,
        ], $this->amenities());

        $this->actingAs($admin)
             ->post(route('properties.store'), $data)
             ->assertRedirect(route('admin.properties'));

        $this->assertDatabaseHas('properties', ['title' => 'Villa Sunset', 'owner_id' => $admin->id]);
    }

    /** @test */
    public function store_fails_when_required_fields_are_missing(): void
    {
        $this->actingAs($this->admin())
             ->post(route('properties.store'), [])
             ->assertSessionHasErrors();
    }

    // -----------------------------------------------------------------------
    // Edit form
    // -----------------------------------------------------------------------

    /** @test */
    public function owner_can_access_edit_form_for_their_property(): void
    {
        $owner    = $this->admin();
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($owner)
             ->get(route('properties.edit', $property->id))
             ->assertOk()
             ->assertViewIs('property.add_or_edit_property');
    }

    /** @test */
    public function non_owner_cannot_access_edit_form(): void
    {
        $otherAdmin = $this->admin();
        $owner      = $this->admin();
        $property   = Property::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($otherAdmin)
             ->get(route('properties.edit', $property->id))
             ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Update
    // -----------------------------------------------------------------------

    /** @test */
    public function owner_can_update_their_property(): void
    {
        $owner    = $this->admin();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'title' => 'Old Title']);

        // Build a clean update payload with all required fields + amenities.
        // Do NOT use $property->toArray() — booleans stored as 0/1 in DB would
        // fail 'boolean' validation when sent as integers via HTTP.
        $payload = array_merge([
            'title'           => 'Updated Title',
            'description'     => 'Updated description.',
            'location'        => $property->location,
            'price_per_night' => $property->price_per_night,
            'capacity'        => $property->capacity,
            'size'            => $property->size,
            'bedrooms'        => 'King',
            'bathrooms'       => $property->bathrooms,
            'min_nights'      => $property->min_nights,
            'images_div'      => $property->images_div,
            'lat'             => $property->lat,
            'lng'             => $property->lng,
        ], $this->amenities());

        $this->actingAs($owner)
             ->put(route('properties.update', $property->id), $payload)
             ->assertRedirect(route('admin.properties'));

        $this->assertDatabaseHas('properties', ['id' => $property->id, 'title' => 'Updated Title']);
    }

    /** @test */
    public function non_owner_cannot_update_a_property(): void
    {
        $owner      = $this->admin();
        $otherAdmin = $this->admin();
        $property   = Property::factory()->create(['owner_id' => $owner->id]);

        $payload = array_merge([
            'title'           => 'Hack',
            'description'     => 'x',
            'location'        => 'x',
            'price_per_night' => 100,
            'capacity'        => 2,
            'size'            => 50,
            'bedrooms'        => 'King',
            'bathrooms'       => 1,
            'min_nights'      => 1,
            'images_div'      => 'x',
            'lat'             => 28.0,
            'lng'             => -13.0,
        ], $this->amenities());

        $this->actingAs($otherAdmin)
             ->put(route('properties.update', $property->id), $payload)
             ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------

    /** @test */
    public function owner_can_delete_their_property(): void
    {
        $owner    = $this->admin();
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($owner)
             ->delete(route('properties.destroy', $property->id))
             ->assertRedirect(route('admin.properties'));

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    /** @test */
    public function non_owner_cannot_delete_a_property(): void
    {
        $owner      = $this->admin();
        $otherAdmin = $this->admin();
        $property   = Property::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($otherAdmin)
             ->delete(route('properties.destroy', $property->id))
             ->assertForbidden();
    }
}

