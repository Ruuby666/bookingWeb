<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            LocalDemoSeeder::class,
            PropertiesTableSeeder::class,
            ReservationPriceSeeder::class,
        ]);
    }
}
