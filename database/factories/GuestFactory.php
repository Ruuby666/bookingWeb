<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
{
    protected $model = Guest::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->optional()->phoneNumber,
            'country' => $this->faker->optional()->country,
            'language' => $this->faker->optional()->languageCode,
            'notes' => $this->faker->optional()->text,
        ];
    }
}
