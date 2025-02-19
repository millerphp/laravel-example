<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CartService
{
    public function __construct(
        private readonly DiscountService $discountService
    ) {}

    public function calculateItemDiscounts(Product $product, int $quantity, ?User $user = null): array
    {
        $discounts = $this->discountService->calculateBestDiscount($product, $user);
        
        return [
            'final_price' => $discounts['final_price'],
            'total_discount_percentage' => $discounts['total_discount_percentage'],
            'applied_discounts' => $discounts['applied_discounts'],
            'total_savings' => ($product->price - $discounts['final_price']) * $quantity
        ];
    }

    public function calculateTotals(Collection $items, ?User $user = null): array
    {
        $subtotal = 0;
        $itemsWithDiscounts = [];
        
        // Calculate individual item discounts first
        foreach ($items as $item) {
            $itemsWithDiscounts[] = [
                'product' => $item->product,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discounted_price' => $item->discounted_price,
                'total' => $item->subtotal,
                'applied_discounts' => $item->discount_data['applied_discounts'] ?? [],
            ];
            
            $subtotal += $item->subtotal;
        }

        // Now check for cart-level discounts
        $cartDiscounts = $this->getApplicableCartDiscounts($subtotal, $user);
        $finalTotal = $subtotal;
        
        foreach ($cartDiscounts as $discount) {
            $finalTotal *= (1 - $discount['percentage'] / 100);
        }

        $totalSavings = $subtotal - $finalTotal;
        
        return [
            'items' => $itemsWithDiscounts,
            'subtotal' => round($subtotal, 2),
            'cart_discounts' => $cartDiscounts,
            'total_savings' => round($totalSavings, 2),
            'final_total' => round($finalTotal, 2),
        ];
    }

    private function getApplicableCartDiscounts(float $subtotal, ?User $user): array
    {
        $cartDiscounts = [];

        // Add order value discounts if applicable
        if ($subtotal >= 500) {
            $cartDiscounts[] = [
                'type' => 'order_value',
                'name' => 'Big Spender Discount',
                'description' => 'Special discount on orders over £500',
                'percentage' => 15,
            ];
        }

        // Add any user-specific cart discounts
        if ($user && $user->hasCartDiscount()) {
            $cartDiscounts[] = [
                'type' => 'customer_cart',
                'name' => $user->cartDiscount->name,
                'description' => $user->cartDiscount->description,
                'percentage' => $user->cartDiscount->percentage,
            ];
        }

        return $cartDiscounts;
    }
} 