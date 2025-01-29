<?php

namespace Database\Factories;

use App\Models\Base;
use Illuminate\Database\Eloquent\Factories\Factory;

class BaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyName = $this->generateUniqueCompanyName();

        return [
            'name' => $companyName,
            'location' => fake()->city(),
        ];
    }

    /**
     * Generate a unique company name that is 3 to 10 characters long.
     *
     * @return string
     */
    private function generateUniqueCompanyName(): string
    {
        do {
            $name = fake()->unique()->company();
        } while (strlen($name) < 3 || strlen($name) > 10 || strpos($name, ' ') !== false);

        return $name;
    }
}
