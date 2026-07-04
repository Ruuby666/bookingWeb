<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Full local demo dataset: super admin (from env, if set), a demo admin
 * account, sample properties, and sample price ranges.
 *
 * Use for LOCAL DEVELOPMENT ONLY:
 *   php artisan db:seed --class=DevelopmentSeeder
 */
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
