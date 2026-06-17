<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;



class SmokeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Available Properties')
            ->assertSee('Map');
    }

    #[Test]
    public function test_api_properties_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/properties');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    #[Test]
    public function test_api_reservations_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/reservations');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    #[Test]
    public function test_non_existent_route_returns_404(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
    }

    #[Test]
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    #[Test]
    public function test_property_detail_page_exists(): void
    {
        Storage::fake('public');

        $property = Property::factory()->create([
            'images_div' => 'test-property',
        ]);

        Storage::disk('public')->put(
            'images/test-property/image1.jpg',
            'fake-image-content'
        );

        Storage::disk('public')->put(
            'images/test-property/image2.jpg',
            'fake-image-content'
        );

        $response = $this->get("/property/{$property->id}");

        $response->assertStatus(200);
    }

    #[Test]
    public function test_property_reservations_endpoint(): void
    {
        $property = Property::factory()->create();

        $response = $this->getJson("/property/{$property->id}/reservations");

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    #[Test]
    public function test_send_email_endpoint_accepts_post(): void
    {
        $response = $this->postJson('/send-email', [
            'name' => 'Test User',
            'email' => '[test@example.com](mailto:test@example.com)',
            'message' => 'Test message',
            'verify_email' => '[test@example.com](mailto:test@example.com)',
        ]);

        $this->assertNotEquals(404, $response->status());
        $this->assertNotEquals(405, $response->status());
    }


    #[Test]
    public function test_homepage_contains_main_elements(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('BookingOcra', false);
    }
}
