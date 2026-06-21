<?php

namespace Database\Factories;

use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('now', '+1 year');
        $checkOut = (clone $checkIn)->modify('+' . rand(2, 14) . ' days');

        return [
            'guest_id' => Guest::factory(),
            'property_id' => Property::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'pending',
            'notes' => $this->faker->optional()->sentence(),
            'invoice' => false,
            'guests' => $this->faker->numberBetween(1, 10),
            'total_price' => $this->faker->randomFloat(2, 100, 10000),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}