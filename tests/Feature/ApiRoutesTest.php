<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function properties_endpoint_returns_list(): void
    {
        Property::factory()->count(2)->create();

        $this->getJson('/api/properties')
            ->assertOk()
            ->assertJsonCount(2);
    }

    #[Test]
    public function reservations_endpoint_returns_confirmed_reservations(): void
    {
        $property = Property::factory()->create();
        $guest = User::factory()->create();

        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => now()->addDay(),
            'check_out' => now()->addDays(2),
        ]);

        // pending reservation should not be returned
        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'pending',
            'check_in' => now()->addDay(),
            'check_out' => now()->addDays(2),
        ]);

        $this->getJson('/api/reservations')
            ->assertOk()
            ->assertJsonCount(1);
    }
}
