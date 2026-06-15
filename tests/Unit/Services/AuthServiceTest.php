<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService;
    }

    /**
     * Insert a user bypassing the model mutator to avoid double-hashing.
     * The User model's setPasswordAttribute hashes the value, so passing
     * bcrypt() via factory/create would hash an already-hashed string.
     * Using DB::table()->insert() we store the hash directly.
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
    public function it_returns_error_when_user_does_not_exist(): void
    {
        $result = $this->authService->attemptAdminLogin('nonexistent@example.com', 'Password1A');

        $this->assertFalse($result['success']);
        $this->assertEquals('Email or password is incorrect.', $result['error']);
    }

    #[Test]
    public function it_returns_error_when_password_is_wrong(): void
    {
        $this->insertUser('admin@example.com', 'Password1A', true);

        $result = $this->authService->attemptAdminLogin('admin@example.com', 'WrongPass1');

        $this->assertFalse($result['success']);
        $this->assertEquals('Email or password is incorrect.', $result['error']);
    }

    #[Test]
    public function it_returns_error_when_user_is_not_admin(): void
    {
        $this->insertUser('user@example.com', 'Password1A', false);

        $result = $this->authService->attemptAdminLogin('user@example.com', 'Password1A');

        $this->assertFalse($result['success']);
        $this->assertEquals('You are not authorized to access this page.', $result['error']);
    }

    #[Test]
    public function it_logs_in_successfully_when_credentials_are_correct_and_user_is_admin(): void
    {
        $this->insertUser('admin@example.com', 'Password1A', true);

        $result = $this->authService->attemptAdminLogin('admin@example.com', 'Password1A');

        $this->assertTrue($result['success']);
        $this->assertTrue(Auth::check());
    }

    #[Test]
    public function it_logs_out_the_authenticated_user(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        Auth::login($user);

        $this->assertTrue(Auth::check());

        $this->authService->logoutAdmin();

        $this->assertFalse(Auth::check());
    }
}
