<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(),
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to use a specific guard name.
     */
    public function withGuard(string $guardName): static
    {
        return $this->state(fn (array $attributes) => [
            'guard_name' => $guardName,
        ]);
    }

    /**
     * Configure the factory to use a specific role name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
} 