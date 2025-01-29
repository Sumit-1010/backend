<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;
    protected $fillable = ['name', 'email', 'password', 'role', 'base_id'];


    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // or use a random password generator
            'role' => $this->faker->randomElement(['admin', 'base_commander', 'logistics_officer']), 
            'base_id' => \App\Models\Base::factory(), 
        ];
    }
}
