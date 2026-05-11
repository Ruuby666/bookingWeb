<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $env = parse_ini_file('.env');
        User::create([
            'name' => 'Admin',
            'email' => $env["ADMIN_EMAIL"],
            'phone_number' => '1234567890',
            'password' => $env["ADMIN_PASSWORD"],
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

