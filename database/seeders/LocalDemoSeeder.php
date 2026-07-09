<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * LocalDemoSeeder
 *
 * Creates demo/test users for LOCAL DEVELOPMENT ONLY.
 * This seeder should NEVER run in staging or production.
 *
 * To use:
 *   php artisan db:seed --class=LocalDemoSeeder
 */
class LocalDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (app()->isProduction()) {
            $this->command->warn('LocalDemoSeeder should not run in production.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => 'admin@example.local'],
            [
                'name' => 'Demo Admin',
                'phone_number' => '1234567890',
                'password' => 'Password1A',
            ],
        );

        // is_admin/is_super_admin are guarded (not mass-assignable) to prevent
        // privilege escalation via forms, so they must be set explicitly here.
        $user->forceFill([
            'is_admin' => true,
            'is_super_admin' => false,
        ])->save();

        $this->command->info('Demo admin user created: admin@example.local / Password1A');
    }
}
