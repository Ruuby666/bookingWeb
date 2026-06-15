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

        User::create([
            'name' => 'Super Admin',
            'email' => env('ADMIN_EMAIL'),
            'phone_number' => '1234567890',
            'password' => env('ADMIN_PASSWORD'),
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->command->info(' Super admin user created from environment variables.');
    }
}
