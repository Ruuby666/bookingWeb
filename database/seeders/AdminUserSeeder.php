<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => env('SUPER_ADMIN_EMAIL', env('ADMIN_EMAIL')),
            'phone_number' => '1234567890',
            'password' => env('SUPER_ADMIN_PASSWORD', env('ADMIN_PASSWORD')),
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        User::create([
            'name' => 'Ruben',
            'email' => 'ruben@gmail.com',
            'phone_number' => '1234567890',
            'password' => 'Ruben1234Q',
            'is_admin' => true,
            'is_super_admin' => false,
        ]);

    }
}
