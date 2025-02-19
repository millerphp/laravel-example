<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        // Add a category discount
        $electronics = Category::where('name', 'Electronics')->first();
        if ($electronics) {
            Discount::create([
                'name' => 'Electronics Sale',
                'description' => '15% off all electronics',
                'percentage' => 15,
                'type' => 'category',
                'discountable_type' => Category::class,
                'discountable_id' => $electronics->id,
                'is_active' => true,
                'priority' => 1,
                'starts_at' => now(),
                'ends_at' => now()->addMonths(1),
                'stacking_rules' => [
                    'allowed_types' => ['product']
                ]
            ]);
        }

        // Add some product-specific discounts
        $products = Product::inRandomOrder()->limit(5)->get();
        foreach ($products as $product) {
            Discount::create([
                'name' => 'Flash Sale',
                'description' => 'Special discount on ' . $product->title,
                'percentage' => rand(10, 30),
                'type' => 'product',
                'discountable_type' => Product::class,
                'discountable_id' => $product->id,
                'is_active' => true,
                'priority' => 2,
                'starts_at' => now(),
                'ends_at' => now()->addDays(7),
                'stacking_rules' => [
                    'allowed_types' => ['category']
                ]
            ]);
        }

        // Bulk purchase discounts
        $bulkProducts = Product::inRandomOrder()->limit(3)->get();
        foreach ($bulkProducts as $product) {
            $product->discounts()->create([
                'name' => 'Bulk Purchase Discount',
                'description' => 'Buy more, save more',
                'percentage' => 20,
                'type' => 'volume',
                'is_active' => true,
                'priority' => 1,
                'rules' => [
                    'minimum_quantity' => 5,
                ],
                'stacking_rules' => [
                    'allowed_types' => ['category']
                ]
            ]);
        }

        // Customer loyalty discounts
        $loyalCustomers = User::inRandomOrder()->limit(2)->get();
        foreach ($loyalCustomers as $customer) {
            $customer->discounts()->create([
                'name' => 'Loyalty Reward',
                'description' => 'Thank you for being a valued customer',
                'percentage' => 10,
                'type' => 'customer',
                'is_active' => true,
                'priority' => 3,
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'stacking_rules' => [
                    'allowed_types' => ['product', 'category', 'volume']
                ]
            ]);
        }

        // Seasonal sale for a category
        $clothingCategory = Category::where('name', 'Clothing')->first();
        if ($clothingCategory) {
            $clothingCategory->discounts()->create([
                'name' => 'Summer Clearance',
                'description' => 'End of season sale',
                'percentage' => 25,
                'type' => 'seasonal',
                'is_active' => true,
                'priority' => 2,
                'starts_at' => now(),
                'ends_at' => now()->addDays(30),
                'stacking_rules' => [
                    'allowed_types' => ['customer']
                ]
            ]);
        }

        // Limited usage discount for a product
        $limitedProduct = Product::inRandomOrder()->first();
        if ($limitedProduct) {
            $limitedProduct->discounts()->create([
                'name' => 'First 50 Customers',
                'description' => 'Special launch price',
                'percentage' => 40,
                'type' => 'limited',
                'is_active' => true,
                'priority' => 4,
                'usage_limit' => 50,
                'stacking_rules' => [
                    'allowed_types' => []  // Doesn't stack with anything
                ]
            ]);
        }

        // High-value order discount
        Category::first()->discounts()->create([
            'name' => 'Big Spender Discount',
            'description' => 'Special discount on orders over £500',
            'percentage' => 15,
            'type' => 'order_value',
            'is_active' => true,
            'priority' => 2,
            'minimum_order_amount' => 500,
            'stacking_rules' => [
                'allowed_types' => ['customer', 'product']
            ]
        ]);

        // New customer discount
        User::first()->discounts()->create([
            'name' => 'Welcome Discount',
            'description' => 'Special offer for new customers',
            'percentage' => 20,
            'type' => 'new_customer',
            'is_active' => true,
            'priority' => 3,
            'usage_limit' => 1,
            'stacking_rules' => [
                'allowed_types' => ['product', 'category']
            ]
        ]);
    }
} 