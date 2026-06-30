<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminReservationControllerTest extends TestCase
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
    public function admin_can_view_pending_reservations_page(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get(route('admin.reservations.pending'))
            ->assertOk()
            ->assertViewIs('admin.pending');
    }

    #[Test]
    public function admin_can_confirm_a_pending_reservation(): void
    {
        $admin = $this->adminUser();
        $property = Property::factory()->create(['owner_id' => $admin->id]);
        $guest = Guest::factory()->create();

        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'pending',
            'check_in' => now()->addDays(5),
            'check_out' => now()->addDays(10),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reservations.pending.update', $reservation->id))
            ->assertRedirect();

        $this->assertEquals('confirmed', $reservation->fresh()->status);
    }

    #[Test]
    public function admin_cannot_confirm_reservation_of_another_owner(): void
    {
        $admin = $this->adminUser();
        $other = $this->adminUser();
        $property = Property::factory()->create(['owner_id' => $other->id]);
        $guest = Guest::factory()->create();

        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reservations.pending.update', $reservation->id))
            ->assertNotFound();
    }
}
