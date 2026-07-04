<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Production-safe by design: only creates the super admin configured via
     * ADMIN_EMAIL/ADMIN_PASSWORD. Demo properties and prices are intentionally
     * excluded — use `php artisan db:seed --class=DevelopmentSeeder` for those.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}
