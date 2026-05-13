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
            'name' => 'Admin',
            'email' => env('ADMIN_EMAIL'),
            'phone_number' => '1234567890',
            'password' => env('ADMIN_PASSWORD'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Ruben',
            'email' => 'ruben@gmail.com',
            'phone_number' => '1234567890',
            'password' => 'Ruben1234Q',
            'is_admin' => true,
        ]);

    }
}
