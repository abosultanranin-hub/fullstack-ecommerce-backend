<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
             'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '12341234', // password
            'super_admin' => 0, // 20% فرصة أن يكون super admin
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
