<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),  // توليد اسم عشوائي للمتجر
            'location' => $this->faker->address(),  // توليد عنوان عشوائي للموقع
            'capacity' => $this->faker->numberBetween(50, 1000),  // توليد سعة عشوائية بين 50 و 1000
            'description' => $this->faker->paragraph(),  // توليد وصف عشوائي
        ];
    }
}
