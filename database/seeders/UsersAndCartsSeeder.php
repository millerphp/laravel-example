<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class UsersAndCartsSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = now()->subDays(365);
        $endDate = now();

        // Create 100 users
        User::factory(100)->create()->each(function ($user) use ($startDate, $endDate) {
            // Assign the 'User' role
            $user->assignRole('User');

            // Create 0-5 carts for each user
            $numCarts = rand(0, 5);
            for ($i = 0; $i < $numCarts; $i++) {
                // First get the products to ensure we have items before creating the cart
                $numItems = rand(1, 5);
                $products = Product::inRandomOrder()->take($numItems)->get();
                
                // Only create cart if we have products
                if ($products->isNotEmpty()) {
                    $cart = Cart::create([
                        'user_id' => $user->id,
                        'status' => $this->getRandomStatus(),
                        'created_at' => fake()->dateTimeBetween($startDate, $endDate),
                        'updated_at' => fake()->dateTimeBetween($startDate, $endDate),
                    ]);

                    foreach ($products as $product) {
                        CartItem::create([
                            'cart_id' => $cart->id,
                            'product_id' => $product->id,
                            'quantity' => rand(1, 3),
                            'unit_price' => $product->price,
                            'discounted_price' => $product->final_price,
                            'discount_data' => [
                                'applied_discounts' => $product->applied_discounts ?? [],
                                'total_discount_percentage' => $product->total_discount_percentage ?? 0,
                            ],
                            'created_at' => $cart->created_at,
                            'updated_at' => $cart->created_at,
                        ]);
                    }
                }
            }
        });
    }

    private function getRandomStatus(): string
    {
        // Weight the statuses to have more completed orders
        return fake()->randomElement([
            'completed', 'completed', 'completed', // 3x weight for completed
            'processing', 'processing', // 2x weight for processing
            'pending',
            'cancelled'
        ]);
    }
} 