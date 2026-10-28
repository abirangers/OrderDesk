<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'name' => ucfirst(fake()->words(3, true)),
            'price' => fake()->numberBetween(15_000, 750_000),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}