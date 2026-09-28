<?php

namespace Tests\Unit\Services;

use App\Models\Guest;
use App\Services\GuestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestServiceTest extends TestCase
{
    use RefreshDatabase;

    private GuestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GuestService;
    }

    #[Test]
    public function it_creates_a_new_guest_when_no_guest_exists_for_the_email(): void
    {
        $guest = $this->service->findOrCreate('Alice', 'alice@example.com', '600111222');

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'phone_number' => '600111222',
        ]);
    }

    #[Test]
    public function it_reuses_an_existing_guest_matched_by_email(): void
    {
        $existing = Guest::factory()->create(['email' => 'bob@example.com']);

        $guest = $this->service->findOrCreate('Bob', 'bob@example.com', '600333444');

        $this->assertSame($existing->id, $guest->id);
        $this->assertEquals(1, Guest::where('email', 'bob@example.com')->count());
    }

    #[Test]
    public function it_keeps_the_stored_name_and_phone_of_an_existing_guest(): void
    {
        $existing = Guest::factory()->create([
            'email' => 'carol@example.com',
            'name' => 'Old Name',
            'phone_number' => '600000000',
        ]);

        $guest = $this->service->findOrCreate('New Name', 'carol@example.com', '600999999');

        $this->assertSame($existing->id, $guest->id);
        $this->assertEquals('Old Name', $guest->fresh()->name);
        $this->assertEquals('600000000', $guest->fresh()->phone_number);
    }
}
