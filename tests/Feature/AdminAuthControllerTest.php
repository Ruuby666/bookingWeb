<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminAuthControllerTest extends TestCase
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
    public function admin_can_login_with_correct_credentials(): void
    {
        // Password must satisfy AdminLoginRequest: min:8, uppercase, lowercase, number
        $this->insertUser('admin@test.com', 'Secret1A', true);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@test.com',
            'password' => 'Secret1A',
        ]);

        $response->assertRedirect(route('admin.properties'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function login_fails_with_wrong_password(): void
    {
        $this->insertUser('admin@test.com', 'Secret1A', true);

        $this->post(route('admin.login.submit'), [
            'email' => 'admin@test.com',
            'password' => 'WrongPass1',
        ])->assertRedirect();

        $this->assertGuest();
    }

    #[Test]
    public function login_fails_with_password_that_does_not_meet_requirements(): void
    {
        // Password 'secret' has no uppercase or number — should fail validation
        $this->insertUser('admin@test.com', 'Secret1A', true);

        $this->post(route('admin.login.submit'), [
            'email' => 'admin@test.com',
            'password' => 'secret',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    #[Test]
    public function non_admin_user_cannot_login_as_admin(): void
    {
        $this->insertUser('user@test.com', 'Password1A', false);

        $this->post(route('admin.login.submit'), [
            'email' => 'user@test.com',
            'password' => 'Password1A',
        ])->assertRedirect();

        $this->assertGuest();
    }

    // -----------------------------------------------------------------------
    // Logout
    // -----------------------------------------------------------------------

    #[Test]
    public function admin_can_logout(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.logout'))
            ->assertRedirect(route('index'));

        $this->assertGuest();
    }
}
