<?php

namespace Database\Factories;

use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStockFactory extends Factory
{
    protected $model = ProductStock::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(10, 1000);
        return [
            'quantity' => $quantity,
            'reserved_quantity' => fake()->numberBetween(0, $quantity / 10),
            'minimum_quantity' => fake()->numberBetween(5, 20),
            'reorder_point' => fake()->numberBetween(10, 50),
            'shelf_location' => fake()->bothify('AISLE-##-?#'),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => fake()->numberBetween(1, 10),
            'minimum_quantity' => 10,
            'reorder_point' => 20,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0,
            'reserved_quantity' => 0,
        ]);
    }
} 