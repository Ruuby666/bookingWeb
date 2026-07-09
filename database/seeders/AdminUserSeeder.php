<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates the super admin user from environment variables.
     * For local demo users, use LocalDemoSeeder instead.
     */
    public function run()
    {
        if (! env('ADMIN_EMAIL')) {
            $this->command->warn('Skipping AdminUserSeeder: ADMIN_EMAIL not set.');

            return;
        }

        $user = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'name' => 'Super Admin',
                'phone_number' => '1234567890',
                'password' => env('ADMIN_PASSWORD'),
            ],
        );

        // is_admin/is_super_admin are guarded (not mass-assignable) to prevent
        // privilege escalation via forms, so they must be set explicitly here.
        $user->forceFill([
            'is_admin' => true,
            'is_super_admin' => true,
        ])->save();

        $this->command->info(' Super admin user created from environment variables.');
    }
}
