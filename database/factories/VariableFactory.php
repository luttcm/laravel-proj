<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variable>
 */
class VariableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'title' => fake()->sentence(),
            'type' => fake()->randomElement(['float', 'integer', 'string']),
            'value' => fake()->randomFloat(2, 0, 100),
            'table_type' => fake()->randomElement(['company', 'manager']),
            'counteragent_type' => fake()->randomElement(['inn', 'ooo', 'fvn']),
        ];
    }
}
