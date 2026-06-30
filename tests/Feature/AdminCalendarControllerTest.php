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

class AdminCalendarControllerTest extends TestCase
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
    public function admin_can_access_the_calendar_page(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get(route('admin.calendar'))
            ->assertOk()
            ->assertViewIs('admin.calendar');
    }

    #[Test]
    public function admin_gets_confirmed_reservations_as_json(): void
    {
        $admin = $this->adminUser();
        $property = Property::factory()->create(['owner_id' => $admin->id]);
        $guest = Guest::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
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
