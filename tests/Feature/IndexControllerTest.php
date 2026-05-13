<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function homepage_is_accessible_to_guests(): void
    {
        $this->get(route('index'))
            ->assertOk()
            ->assertViewIs('index');
    }

    /** @test */
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

    /** @test */
    public function homepage_works_with_no_properties(): void
    {
        $this->get(route('index'))
            ->assertOk()
            ->assertViewHas('properties');
    }
}

class IsAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_is_redirected_from_admin_routes(): void
    {
        // IsAdmin middleware redirects to '/' not '/login'
        $this->get(route('admin.properties'))
            ->assertRedirect('/');
    }

    /** @test */
    public function authenticated_non_admin_is_redirected_from_admin_routes(): void
    {
        // IsAdmin middleware redirects to '/' not '/login'
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.properties'))
            ->assertRedirect('/');
    }

    /** @test */
    public function admin_user_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.properties'))
            ->assertOk();
    }

    /** @test */
    public function login_page_is_accessible_to_guests(): void
    {
        $this->get(route('login'))
            ->assertOk();
    }
}
