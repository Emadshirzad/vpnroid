<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Services>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => fake()->name(),
            'price'       => fake()->numberBetween(1, 1000),
            'type_id'     => 1,
            'update_time' => fake()->numberBetween(10, 90)
        ];
    }
}
