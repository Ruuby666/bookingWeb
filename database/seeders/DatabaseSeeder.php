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
    public function run()
    {
        // Crear 5 usuarios
        User::factory()->count(5)->create();

        // Crear una propiedad
        $property = Property::create([
            'title' => 'Beautiful Beach House',
            'description' => 'A stunning beach house with an amazing view.',
            'location' => 'Santa Monica Beach',
            'price_per_night' => 150.00,
            'capacity' => 6,
            'image_url' => 'https://www.fincalasnubes.com/wp-content/uploads/casa-img-4.png',
        ]);

        // Generar reservas no solapadas para la propiedad
        $this->createReservations($property);
    }

    private function createReservations($property)
    {
        $dates = [
            ['start_date' => '2024-09-01', 'end_date' => '2024-09-07'],
            ['start_date' => '2024-09-10', 'end_date' => '2024-09-15'],
            ['start_date' => '2024-09-25', 'end_date' => '2024-09-30'],
        ];

        foreach ($dates as $date) {
            Reservation::create([
                'user_id' => User::inRandomOrder()->first()->id,
                'property_id' => $property->id,
                'start_date' => $date['start_date'],
                'end_date' => $date['end_date'],
            ]);
        }
    }
}
