<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['vehicle', 'munition', 'medics', 'food']);

        $valueRanges = [
            'vehicle' => [1000, 5000],
            'munition' => [500, 2500],
            'medics' => [50, 100],
            'food' => [1000, 2500],
        ];
        
        $minValue = $valueRanges[$type][0];
        $maxValue = $valueRanges[$type][1];

        return [
            'name' => fake()->word(),
            'type' => $type,
            'base_id' => \App\Models\Base::factory(),
            'opening_balance' => $this->faker->numberBetween(0, 500),
            'purchases' => $this->faker->numberBetween(0, 100),
            'transfers_in' => $this->faker->numberBetween(0, 50),
            'transfers_out' => $this->faker->numberBetween(0, 50),
            'closing_balance' => 0, // To be calculated later
            'net_movements' => 0,  // To be calculated later
        ];
    }
}
