<?php

namespace Tests\Unit\Services;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PropertyServiceTest extends TestCase
{
    use RefreshDatabase;

    private PropertyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PropertyService();
    }

    // -----------------------------------------------------------------------
    // parseBedroomsToJson (tested indirectly via createProperty)
    // -----------------------------------------------------------------------

    /** @test */
    public function it_converts_bedrooms_string_to_json_on_create(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $this->actingAs($owner);

        $property = $this->service->createProperty([
            'title'           => 'Test Villa',
            'description'     => 'Nice place',
            'location'        => 'Lanzarote',
            'price_per_night' => 150.00,
            'capacity'        => 6,
            'size'            => 120,
            'bedrooms'        => 'King, Twin, Double',
            'bathrooms'       => 2,
            'min_nights'      => 3,
            'images_div'      => 'test_folder',
            'lat'             => 28.9635,
            'lng'             => -13.5477,
            'tv'              => false,
            'entertainment'   => false,
            'parking'         => false,
            'pool'            => false,
            'garden'          => false,
            'safeBox'         => false,
            'terrace'         => false,
            'wifi'            => true,
        ]);

        $decoded = json_decode($property->bedrooms, true);

        $this->assertEquals('King',   $decoded['1']);
        $this->assertEquals('Twin',   $decoded['2']);
        $this->assertEquals('Double', $decoded['3']);
    }

    /** @test */
    public function it_assigns_owner_id_from_authenticated_user_on_create(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $this->actingAs($owner);

        $property = $this->service->createProperty([
            'title'           => 'Casa del Sol',
            'description'     => 'Sunny house',
            'location'        => 'Costa Teguise',
            'price_per_night' => 200.00,
            'capacity'        => 4,
            'size'            => 90,
            'bedrooms'        => 'King',
            'bathrooms'       => 1,
            'min_nights'      => 2,
            'images_div'      => 'casa_sol',
            'lat'             => 28.9,
            'lng'             => -13.5,
            'tv'              => false,
            'entertainment'   => false,
            'parking'         => true,
            'pool'            => false,
            'garden'          => false,
            'safeBox'         => false,
            'terrace'         => true,
            'wifi'            => true,
        ]);

        $this->assertEquals($owner->id, $property->owner_id);
    }

    // -----------------------------------------------------------------------
    // updateProperty
    // -----------------------------------------------------------------------

    /** @test */
    public function it_updates_property_fields(): void
    {
        $owner    = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'title'    => 'Old Title',
        ]);

        $updated = $this->service->updateProperty($property, [
            'title'    => 'New Title',
            'bedrooms' => 'King, Twin',
        ]);

        $this->assertEquals('New Title', $updated->title);
        $this->assertDatabaseHas('properties', ['id' => $property->id, 'title' => 'New Title']);
    }

    // -----------------------------------------------------------------------
    // getImagesForProperty – no filesystem, returns nulls gracefully
    // -----------------------------------------------------------------------

    /** @test */
    public function it_returns_null_main_image_when_folder_does_not_exist(): void
    {
        $owner    = User::factory()->create();
        $property = Property::factory()->create([
            'owner_id'   => $owner->id,
            'images_div' => 'nonexistent_folder_xyz',
        ]);

        $result = $this->service->getImagesForProperty($property);

        $this->assertNull($result['mainImage']);
        $this->assertEmpty($result['imagesWithoutFirst']);
    }

    // -----------------------------------------------------------------------
    // getAllWithFirstImage – no filesystem, defaults gracefully
    // -----------------------------------------------------------------------

    /** @test */
    public function it_returns_default_image_when_property_folder_does_not_exist(): void
    {
        $owner = User::factory()->create();
        Property::factory()->create([
            'owner_id'   => $owner->id,
            'images_div' => 'missing_folder_abc',
        ]);

        $result = $this->service->getAllWithFirstImage();

        $this->assertNotEmpty($result['properties']);
        foreach ($result['propertyWithImages'] as $imageName) {
            $this->assertEquals('default.jpg', $imageName);
        }
    }
}
