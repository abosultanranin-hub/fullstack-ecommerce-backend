<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Products;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products>
 */
class ProductsFactory extends Factory
{
    protected static array $productImages = [
        'assets/img/arrivel/arrivel_1.png',
        'assets/img/arrivel/arrivel_2.png',
        'assets/img/arrivel/arrivel_3.png',
        'assets/img/arrivel/arrivel_4.png',
        'assets/img/arrivel/arrivel_5.png',
        'assets/img/arrivel/arrivel_6.png',
        'assets/img/product/product_list_1.png',
        'assets/img/product/product_list_2.png',
        'assets/img/product/product_list_3.png',
        'assets/img/product/product_list_4.png',
        'assets/img/product/product_list_5.png',
        'assets/img/product/product_list_6.png',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(10, 100),
            'image' => $this->faker->randomElement(self::$productImages),
            'stock_quantity' => $this->faker->numberBetween(5, 100),
        ];
    }
}
