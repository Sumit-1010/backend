<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Asset;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $asset = Asset::inRandomOrder()->first() ?? Asset::factory()->create();

        return [
            'user_id' => User::factory(),
            'asset_id' => $asset->id,
            'opening_balance' => $asset->id, // Reference the same asset for opening balance
            'closing_balance' => $asset->id, // Reference the same asset for closing balance
            'transaction_type' => fake()->randomElement(['purchases', 'transfer_in', 'transfer_out']),
            'quantity' => fake()->numberBetween(1, 50),
        ];
    }
}
