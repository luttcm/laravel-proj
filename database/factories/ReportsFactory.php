<?php

namespace Database\Factories;

use App\Models\Reports;
use App\Models\User;
use App\Models\Calculation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportsFactory extends Factory
{
    protected $model = Reports::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'manager_id' => User::factory(),
            'report_title' => $this->faker->sentence(),
            'date' => now()->toDateString(),
            'calculate_id' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
