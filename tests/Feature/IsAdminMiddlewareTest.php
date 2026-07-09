<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IsAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_user_is_redirected_from_admin_routes(): void
    {
        $this->get(route('admin.properties'))
            ->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_non_admin_is_redirected_from_admin_routes(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.properties'))
            ->assertRedirect('/login');
    }

    #[Test]
    public function admin_user_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.properties'))
            ->assertOk();
    }

    #[Test]
    public function login_page_is_accessible_to_guests(): void
    {
        $this->get(route('login'))
            ->assertOk();
    }
}
