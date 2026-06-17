<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MailControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    // -----------------------------------------------------------------------
    // sendEmail – booking request
    // -----------------------------------------------------------------------

    #[Test]
    public function guest_can_submit_a_valid_booking_request(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'min_nights' => 2,
            'capacity' => 6,
        ]);

        $response = $this->post(route('send.email'), [
            'property_id' => $property->id,
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'verification_email' => 'alice@example.com', // Required: must match email
            'number' => '600111222',
            'adults' => 2,
            'children' => 0,
            'guests' => 2,
            'daterange' => '01/07/2026 - 05/07/2026',
            'total_price' => 400.00,
            'message' => 'Looking forward to the stay!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', ['status' => 'pending']);
        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
    }

    #[Test]
    public function booking_fails_when_verification_email_does_not_match(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id, 'capacity' => 4]);

        $this->post(route('send.email'), [
            'property_id' => $property->id,
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'verification_email' => 'different@example.com',
            'number' => '600111222',
            'adults' => 1,
            'children' => 0,
            'daterange' => '01/07/2026 - 05/07/2026',
            'total_price' => 200.00,
        ])->assertSessionHasErrors('verification_email');
    }

    #[Test]
    public function booking_fails_when_required_fields_are_missing(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $this->post(route('send.email'), [
            'property_id' => $property->id,
            // Missing name, email, verification_email, daterange, etc.
        ])->assertSessionHasErrors();
    }

    #[Test]
    public function booking_fails_when_dates_overlap_confirmed_reservation(): void
    {
        $owner = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'min_nights' => 1,
            'capacity' => 4,
        ]);
        $guest = User::factory()->create();

        // Existing confirmed reservation blocks the period
        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => '2026-07-03 15:00:00',
            'check_out' => '2026-07-09 11:00:00',
        ]);

        $response = $this->post(route('send.email'), [
            'property_id' => $property->id,
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'verification_email' => 'bob@example.com',
            'number' => '600999888',
            'adults' => 2,
            'children' => 0,
            'guests' => 2,
            'daterange' => '04/07/2026 - 07/07/2026',
            'total_price' => 300.00,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // -----------------------------------------------------------------------
    // sendSuggestion
    // -----------------------------------------------------------------------

    #[Test]
    public function admin_can_send_a_suggestion_for_a_reservation(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $admin->id]);
        $guest = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('reservations.sendSuggestion', $reservation->id), [
                'note' => 'We suggest moving to August instead.',
            ])
            ->assertRedirect(route('admin.reservations.pending'));
    }

    #[Test]
    public function suggestion_fails_when_note_is_missing(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $property = Property::factory()->create(['owner_id' => $admin->id]);
        $guest = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
        ]);

        $this->actingAs($admin)
            ->post(route('reservations.sendSuggestion', $reservation->id), [])
            ->assertSessionHasErrors('note');
    }

    #[Test]
    public function admin_cannot_send_suggestion_for_another_admin_property(): void
    {
        $adminA = User::factory()->create(['is_admin' => true]);
        $adminB = User::factory()->create(['is_admin' => true]);
        $propertyB = Property::factory()->create(['owner_id' => $adminB->id]);
        $guest = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'property_id' => $propertyB->id,
            'user_id' => $guest->id,
            'status' => 'pending',
        ]);

        $this->actingAs($adminA)
            ->post(route('reservations.sendSuggestion', $reservation->id), [
                'note' => 'Unauthorized attempt to send suggestion.',
            ])
            ->assertForbidden();
    }
}
