<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function regularUser(): User
    {
        return User::factory()->create(['is_admin' => false]);
    }

    /**
     * Insert a user bypassing the model mutator to avoid double-hashing.
     * The AdminLoginRequest requires min:8 + uppercase + lowercase + numbers.
     */
    private function insertUser(string $email, string $plainPassword, bool $isAdmin): void
    {
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => $email,
            'password' => bcrypt($plainPassword),
            'is_admin' => $isAdmin,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function admin_can_see_their_properties(): void
    {
        $admin = $this->adminUser();
        Property::factory()->count(2)->create(['owner_id' => $admin->id]);

        $this->actingAs($admin)
            ->get(route('admin.properties'))
            ->assertOk()
            ->assertViewIs('admin.admin')
            ->assertViewHas('properties');
    }

    #[Test]
    public function guest_is_redirected_from_admin_properties(): void
    {
        // IsAdmin middleware redirects to '/' (root), not '/login'
        $this->get(route('admin.properties'))
            ->assertRedirect('/');
    }

    #[Test]
    public function non_admin_is_redirected_from_admin_properties(): void
    {
        // IsAdmin middleware redirects to '/' (root), not '/login'
        $this->actingAs($this->regularUser())
            ->get(route('admin.properties'))
            ->assertRedirect('/');
    }
}
