<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'owner_id'        => User::factory(),
            'title'           => $this->faker->sentence(3),
            'description'     => $this->faker->paragraph(),
            'location'        => $this->faker->address(),
            'price_per_night' => $this->faker->randomFloat(2, 50, 1000),
            'capacity'        => $this->faker->numberBetween(1, 20),
            'size'            => $this->faker->numberBetween(30, 500),
            'bedrooms'        => json_encode(['1' => 'King', '2' => 'Twin']),
            'bathrooms'       => $this->faker->numberBetween(1, 5),
            'min_nights'      => $this->faker->numberBetween(1, 7),
            'images_div'      => 'default',
            'tv'              => $this->faker->boolean(),
            'entertainment'   => $this->faker->boolean(),
            'parking'         => $this->faker->boolean(),
            'pool'            => $this->faker->boolean(),
            'garden'          => $this->faker->boolean(),
            'safeBox'         => $this->faker->boolean(),
            'terrace'         => $this->faker->boolean(),
            'wifi'            => $this->faker->boolean(),
            'lat'             => $this->faker->randomFloat(6, 28.85, 29.25),
            'lng'             => $this->faker->randomFloat(6, -13.85, -13.35),
        ];
    }
}
