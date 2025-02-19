<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(rand(2, 4), true),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->randomFloat(2, 9.99, 2999.99),
            'stock' => fake()->numberBetween(0, 200),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product is a premium item.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 999.99, 9999.99),
            'description' => 'Premium: ' . fake()->paragraphs(3, true),
        ]);
    }

    /**
     * Indicate that the product is a budget item.
     */
    public function budget(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 1.99, 49.99),
        ]);
    }

    /**
     * Indicate that the product is a limited edition item.
     */
    public function limitedEdition(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 20),
            'description' => 'Limited Edition: ' . fake()->paragraphs(2, true),
            'price' => fake()->randomFloat(2, 499.99, 4999.99),
        ]);
    }

    /**
     * Attach random categories to the product
     */
    public function withRandomCategories(int $count = 1): static
    {
        return $this->afterCreating(function (Product $product) use ($count) {
            $categories = Category::inRandomOrder()->limit($count)->get();
            $product->categories()->attach($categories);
        });
    }

    /**
     * Attach specific categories to the product
     */
    public function withCategories(array $categoryIds): static
    {
        return $this->afterCreating(function (Product $product) use ($categoryIds) {
            $product->categories()->attach($categoryIds);
        });
    }
} 