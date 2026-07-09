<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function homepage_is_accessible_to_guests(): void
    {
        $this->get(route('index'))
            ->assertOk()
            ->assertViewIs('index');
    }

    #[Test]
    public function homepage_shows_all_properties(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        Property::factory()->count(3)->create(['owner_id' => $owner->id]);

        $this->get(route('index'))
            ->assertOk()
            ->assertViewHas('properties', function ($properties) {
                return $properties->count() === 3;
            });
    }

    #[Test]
    public function homepage_works_with_no_properties(): void
    {
        $this->get(route('index'))
            ->assertOk()
            ->assertViewHas('properties');
    }
}
