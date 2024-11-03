<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkSub>
 */
class LinkSubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'link'       => fake()->url,
            'is_encode'  => false,
            'service_id' => fake()->numberBetween(1, 10),
        ];
    }
}
