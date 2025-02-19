<?php

namespace Database\Factories;

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(-100, 100);
        $previousQuantity = fake()->numberBetween(0, 1000);
        
        return [
            'reference' => 'MOV-' . Str::upper(Str::random(8)),
            'type' => fake()->randomElement(['receive', 'ship', 'transfer', 'adjust', 'return']),
            'quantity' => $quantity,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $previousQuantity + $quantity,
            'notes' => fake()->optional(0.7)->sentence(),
            'metadata' => null,
        ];
    }
} 