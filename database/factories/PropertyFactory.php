<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'location' => $this->faker->address,
            'price_per_night' => $this->faker->randomFloat(2, 50, 1000), // Precios entre 50 y 1000
            'capacity' => $this->faker->numberBetween(1, 20),
            'image_url' => $this->faker->imageUrl(640, 480, 'real estate', true, 'Faker'), // URL de imagen ficticia
        ];
    }
}
