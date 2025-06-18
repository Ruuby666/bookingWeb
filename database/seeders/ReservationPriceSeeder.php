<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReservationPrice;
use App\Models\Property;
use Carbon\Carbon;

class ReservationPriceSeeder extends Seeder
{
    public function run(): void
    {
        $properties = Property::all();

        if ($properties->isEmpty()) {
            $this->command->warn('No properties found. Please seed properties first.');
            return;
        }

        foreach ($properties as $property) {
            // Creamos 3 rangos de fechas por propiedad
            for ($i = 0; $i < 3; $i++) {
                $startDate = Carbon::now()->addMonths($i * 4);
                $endDate = (clone $startDate)->addMonths(3);

                ReservationPrice::create([
                    'property_id'      => $property->id,
                    'start_date'       => $startDate->format('Y-m-d'),
                    'end_date'         => $endDate->format('Y-m-d'),
                    'price_per_night'  => rand(60, 250) + rand(0, 99) / 100, // Precio aleatorio entre 60.00 y 250.99
                ]);
            }
        }
    }
}
