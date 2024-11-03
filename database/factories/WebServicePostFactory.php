<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebSrvicePost>
 */
class WebServicePostFactory extends Factory
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
            'key'        => 'Item key',
            'header'     => fake()->text(10),
            'body'       => fake()->text(20),
            'is_encode'  => false,
            'service_id' => fake()->numberBetween(1, 10),
        ];
    }
}
