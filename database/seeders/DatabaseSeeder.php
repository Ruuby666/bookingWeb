<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //Antes de correr el DatabaseSeeder usa este comando:  php artisan db:seed --class=PropertiesTableSeeder

        User::factory()->count(5)->create();

        $properties = Property::all();

        // Generar reservas no solapadas para cada propiedad
        foreach ($properties as $property) {
            $this->createReservations($property);
        }
    }

    private function createReservations($property)
    {
        $dates = [
            ['check_in' => '2024-11-01', 'check_out' => '2024-11-07'],
            ['check_in' => '2024-11-10', 'check_out' => '2024-11-15'],
            ['check_in' => '2024-11-25', 'check_out' => '2024-11-30'],
        ];

        foreach ($dates as $date) {
            Reservation::create([
                'user_id' => User::inRandomOrder()->first()->id,
                'property_id' => $property->id,
                'check_in' => $date['check_in'],
                'check_out' => $date['check_out'],
                'status' => 'confirmed', 
                'notes' => '', 
                'guests' => rand(1, $property->capacity), 
                'total_price' => $property->price_per_night * (strtotime($date['check_out']) - strtotime($date['check_in'])) / 86400,
            ]);
        }
    }
}
