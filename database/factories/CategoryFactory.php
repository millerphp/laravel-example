<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'discount_percentage' => fake()->randomFloat(2, 0, 30),
        ];
    }

    /**
     * Create a root category
     */
    public function root(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Create a child category
     */
    public function child(): static
    {
        return $this->state(function (array $attributes) {
            // Get or create a parent category
            $parent = Category::inRandomOrder()->first() ?? Category::factory()->root()->create();
            
            return [
                'parent_id' => $parent->id,
            ];
        });
    }
} 