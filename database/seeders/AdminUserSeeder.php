<?php

namespace database\seeders;

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
        User::create([
            'name' => 'Admin',
            'email' => 'oscar@gmail.com',
            'password' => Hash::make('$2y$12$yvV9Rre6OKb9EQhOL/qxOuJlhMD702heA66vp5pCCqyp3gb1nkpZO'),
            'is_admin' => true,
        ]);
    }


}

