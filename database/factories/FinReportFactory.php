<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinReport>
 */
class FinReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'report_title' => fake()->sentence(3),
            'customer' => fake()->company(),
            'order_number' => fake()->bothify('ORD-####-????'),
            'spk' => fake()->name(),
            'tz_count' => fake()->numberBetween(1, 100),
            'amount' => fake()->randomFloat(2, 1000, 100000),
            'received_amount' => fake()->randomFloat(2, 0, 100000),
            'date' => fake()->date(),
            'spk_id' => \App\Models\Spk::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'nds_id' => \App\Models\Nds::factory(),
            'bonus_client' => fake()->randomFloat(2, 0, 5000),
            'kickback' => fake()->randomFloat(2, 0, 5000),
            'net_sales' => fake()->randomFloat(2, 1000, 100000),
            'remainder' => fake()->randomFloat(2, 0, 10000),
            'manager_name' => fake()->name(),
            'supplier_invoice_number' => fake()->bothify('INV-####'),
            'supplier_amount' => fake()->randomFloat(2, 500, 50000),
            'payment_manager' => fake()->randomFloat(2, 100, 10000),
            'payment_spk' => fake()->randomFloat(2, 100, 10000),
            'sold_from' => fake()->company(),
            'profit' => fake()->randomFloat(2, 100, 50000),
            'markup' => fake()->randomFloat(2, 0, 100),
            'nds_percent' => fake()->randomElement([0, 10, 20]),
        ];
    }
}
