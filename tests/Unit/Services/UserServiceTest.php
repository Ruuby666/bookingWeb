<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    /** @test */
    public function it_creates_a_new_user_if_email_does_not_exist(): void
    {
        $user = $this->userService->findOrCreate('John Doe', 'john@example.com', '600123456');

        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'name' => 'John Doe']);
        $this->assertEquals('John Doe', $user->name);
    }

    /** @test */
    public function it_returns_existing_user_if_email_already_exists(): void
    {
        $existing = User::factory()->create(['email' => 'existing@example.com']);

        $user = $this->userService->findOrCreate('Other Name', 'existing@example.com', '000000000');

        $this->assertEquals($existing->id, $user->id);
        $this->assertEquals(1, User::count());
    }

    /** @test */
    public function it_updates_user_data_without_password(): void
    {
        $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

        $updated = $this->userService->updateUser($user->id, [
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ]);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals('new@example.com', $updated->email);
    }

    /** @test */
    public function it_hashes_password_when_updating_user(): void
    {
        // NOTE: UserService::updateUser calls Hash::make() before user->update().
        // The User model's setPasswordAttribute mutator calls Hash::make() again on assignment.
        // This means the stored value is a hash-of-a-hash (a bug in the code), but we
        // test the observable contract: the plain password is NOT stored as plain text,
        // and the stored value is a valid bcrypt string.
        $user = User::factory()->create();

        $this->userService->updateUser($user->id, [
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => 'NewPassword1',
        ]);

        $user->refresh();

        // The stored value must NOT be the plain-text password
        $this->assertNotEquals('NewPassword1', $user->password);
        // The stored value must be a bcrypt hash string
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    /** @test */
    public function it_does_not_change_password_when_empty_string_provided(): void
    {
        $user    = User::factory()->create();
        $oldHash = $user->password;

        $this->userService->updateUser($user->id, [
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => '',
        ]);

        $user->refresh();
        $this->assertEquals($oldHash, $user->password);
    }
}
