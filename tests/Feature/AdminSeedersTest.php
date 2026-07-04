<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AuthService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\LocalDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression coverage for a real bug found during manual verification:
 * is_admin/is_super_admin are intentionally guarded (not mass-assignable)
 * on the User model, so any seeder passing them through create()/
 * updateOrCreate() silently drops them — the seeded user exists but can
 * never actually log in as an admin.
 */
class AdminSeedersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_user_seeder_creates_a_user_that_can_actually_log_in_as_admin(): void
    {
        // env() reads $_SERVER before $_ENV/getenv(), so all three must be set
        // for the seeder's env('ADMIN_EMAIL') call to see the override.
        putenv('ADMIN_EMAIL=seeded-admin@example.com');
        putenv('ADMIN_PASSWORD=SeededPass123');
        $_ENV['ADMIN_EMAIL'] = $_SERVER['ADMIN_EMAIL'] = 'seeded-admin@example.com';
        $_ENV['ADMIN_PASSWORD'] = $_SERVER['ADMIN_PASSWORD'] = 'SeededPass123';

        try {
            $this->seed(AdminUserSeeder::class);
        } finally {
            putenv('ADMIN_EMAIL');
            putenv('ADMIN_PASSWORD');
            unset($_ENV['ADMIN_EMAIL'], $_ENV['ADMIN_PASSWORD'], $_SERVER['ADMIN_EMAIL'], $_SERVER['ADMIN_PASSWORD']);
        }

        $user = User::where('email', 'seeded-admin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);
        $this->assertTrue($user->is_super_admin);

        $result = app(AuthService::class)->attemptAdminLogin('seeded-admin@example.com', 'SeededPass123');
        $this->assertTrue($result['success']);
    }

    #[Test]
    public function local_demo_seeder_creates_a_working_demo_admin(): void
    {
        $this->seed(LocalDemoSeeder::class);

        $user = User::where('email', 'admin@example.local')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);
        $this->assertFalse($user->is_super_admin);

        $result = app(AuthService::class)->attemptAdminLogin('admin@example.local', 'Password1A');
        $this->assertTrue($result['success']);
    }
}
