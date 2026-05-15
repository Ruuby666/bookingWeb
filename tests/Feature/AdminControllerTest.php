<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

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

    // -----------------------------------------------------------------------
    // Login
    // -----------------------------------------------------------------------

    /** @test */
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

    /** @test */
    public function login_fails_with_wrong_password(): void
    {
        $this->insertUser('admin@test.com', 'Secret1A', true);

        $this->post(route('admin.login.submit'), [
            'email' => 'admin@test.com',
            'password' => 'WrongPass1',
        ])->assertRedirect();

        $this->assertGuest();
    }

    /** @test */
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

    /** @test */
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

    /** @test */
    public function admin_can_logout(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.logout'))
            ->assertRedirect(route('index'));

        $this->assertGuest();
    }

    // -----------------------------------------------------------------------
    // Admin properties page
    // -----------------------------------------------------------------------

    /** @test */
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

    /** @test */
    public function guest_is_redirected_from_admin_properties(): void
    {
        // IsAdmin middleware redirects to '/' (root), not '/login'
        $this->get(route('admin.properties'))
            ->assertRedirect('/');
    }

    /** @test */
    public function non_admin_is_redirected_from_admin_properties(): void
    {
        // IsAdmin middleware redirects to '/' (root), not '/login'
        $this->actingAs($this->regularUser())
            ->get(route('admin.properties'))
            ->assertRedirect('/');
    }

    // -----------------------------------------------------------------------
    // Pending reservations
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_view_pending_reservations_page(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get(route('admin.reservations.pending'))
            ->assertOk()
            ->assertViewIs('admin.pending');
    }

    // -----------------------------------------------------------------------
    // Confirm reservation (updateStatus)
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_confirm_a_pending_reservation(): void
    {
        $admin = $this->adminUser();
        $property = Property::factory()->create(['owner_id' => $admin->id]);
        $guest = $this->regularUser();

        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'pending',
            'check_in' => now()->addDays(5),
            'check_out' => now()->addDays(10),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reservations.pending.update', $reservation->id))
            ->assertRedirect();

        $this->assertEquals('confirmed', $reservation->fresh()->status);
    }

    /** @test */
    public function admin_cannot_confirm_reservation_of_another_owner(): void
    {
        $admin = $this->adminUser();
        $other = $this->adminUser();
        $property = Property::factory()->create(['owner_id' => $other->id]);
        $guest = $this->regularUser();

        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reservations.pending.update', $reservation->id))
            ->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Calendar
    // -----------------------------------------------------------------------

    /** @test */
    public function admin_can_access_the_calendar_page(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get(route('admin.calendar'))
            ->assertOk()
            ->assertViewIs('admin.calendar');
    }

    /** @test */
    public function admin_gets_confirmed_reservations_as_json(): void
    {
        $admin = $this->adminUser();
        $property = Property::factory()->create(['owner_id' => $admin->id]);
        $guest = $this->regularUser();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => now()->addDays(1),
            'check_out' => now()->addDays(5),
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.calendar.reservations'))
            ->assertOk()
            ->assertJsonCount(1);
    }
}
