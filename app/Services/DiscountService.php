<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DiscountService
{
    /**
     * Calculate the best discount for a product
     */
    public function calculateBestDiscount(Product $product, ?User $user = null): array
    {
        // Guard against null or zero prices
        if (!$product->price || $product->price <= 0) {
            return [
                'final_price' => $product->price ?? 0,
                'total_discount_percentage' => 0,
                'applied_discounts' => [],
            ];
        }

        $allDiscounts = $this->getAllApplicableDiscounts($product, $user);
        
        // Get the best discount combination
        $bestCombination = $this->calculateBestCombination($allDiscounts, $product->price);
        
        return [
            'final_price' => $bestCombination['final_price'],
            'total_discount_percentage' => $bestCombination['total_percentage'],
            'applied_discounts' => $bestCombination['applied_discounts'],
        ];
    }

    /**
     * Get all possible discounts that could apply
     */
    private function getAllApplicableDiscounts(Product $product, ?User $user): Collection
    {
        $discounts = collect();

        // 1. Product-specific discounts
        $discounts = $discounts->merge(
            $this->getProductDiscounts($product)
        );

        // 2. Category discounts (including parent categories)
        $discounts = $discounts->merge(
            $this->getCategoryDiscounts($product)
        );

        // 3. Customer-specific discounts
        if ($user) {
            $discounts = $discounts->merge(
                $this->getCustomerDiscounts($user)
            );
        }

        // 4. Time-based discounts (flash sales, seasonal)
        $discounts = $discounts->merge(
            $this->getTimeBasedDiscounts($product)
        );

        // 5. Volume discounts
        $discounts = $discounts->merge(
            $this->getVolumeDiscounts($product)
        );

        return $discounts;
    }

    /**
     * Calculate the best combination of discounts
     */
    private function calculateBestCombination(Collection $discounts, float $originalPrice): array
    {
        // Guard against invalid original price
        if ($originalPrice <= 0) {
            return [
                'final_price' => 0,
                'total_percentage' => 0,
                'applied_discounts' => [],
            ];
        }

        $bestCombination = [
            'final_price' => $originalPrice,
            'total_percentage' => 0,
            'applied_discounts' => [],
        ];

        // Sort discounts by percentage (highest first)
        $sortedDiscounts = $discounts->sortByDesc('percentage');

        if ($sortedDiscounts->isEmpty()) {
            return $bestCombination;
        }

        // Calculate final price after applying all best discounts
        $finalPrice = $originalPrice;
        $appliedDiscounts = [];

        foreach ($sortedDiscounts as $discount) {
            // Validate discount percentage
            if (!isset($discount['percentage']) || !is_numeric($discount['percentage'])) {
                continue;
            }
            
            // Ensure percentage is between 0 and 100
            $percentage = min(100, max(0, $discount['percentage']));

            // Some discount types might not stack (implement rules here)
            if ($this->canApplyDiscount($discount, $appliedDiscounts)) {
                $finalPrice *= (1 - $percentage / 100);
                $appliedDiscounts[] = $discount;
            }
        }

        // Ensure we have valid numbers
        $finalPrice = max(0, round($finalPrice, 2));
        $totalPercentage = $originalPrice > 0 
            ? round(100 - ($finalPrice / $originalPrice * 100))
            : 0;

        return [
            'final_price' => $finalPrice,
            'total_percentage' => $totalPercentage,
            'applied_discounts' => $appliedDiscounts,
        ];
    }

    private function canApplyDiscount(array $discount, array $appliedDiscounts): bool
    {
        if (empty($appliedDiscounts)) {
            return true;
        }

        // Check if this discount can stack with already applied discounts
        foreach ($appliedDiscounts as $appliedDiscount) {
            // Discounts of the same type don't stack
            if ($discount['type'] === $appliedDiscount['type']) {
                return false;
            }
        }

        return true;
    }

    private function getProductDiscounts(Product $product): Collection
    {
        return $product->activeDiscounts()->get()->map(function ($discount) {
            return [
                'type' => 'product',
                'percentage' => $discount->percentage,
                'description' => $discount->description ?? "Product: {$discount->name}",
                'priority' => $discount->priority,
                'source' => $discount,
            ];
        });
    }

    private function getCategoryDiscounts(Product $product): Collection
    {
        return $product->categories->flatMap(function ($category) {
            $activeDiscounts = $category->activeDiscounts()
                ->where('type', 'category')  // Only get category-level discounts
                ->get();
            
            return $activeDiscounts->map(function ($discount) use ($category) {
                return [
                    'type' => 'category',
                    'percentage' => $discount->percentage,
                    'description' => $discount->description ?? "Category: {$category->name}",
                    'priority' => $discount->priority,
                    'source' => $discount,
                ];
            });
        });
    }

    private function getCustomerDiscounts(User $user): Collection
    {
        return $user->activeDiscounts()->get()->map(function ($discount) {
            return [
                'type' => 'customer',
                'percentage' => $discount->percentage,
                'description' => $discount->description ?? "Customer: {$discount->name}",
                'priority' => $discount->priority,
                'source' => $discount,
            ];
        });
    }

    private function getTimeBasedDiscounts(Product $product): Collection
    {
        // Implement time-based discount logic
        return collect();
    }

    private function getVolumeDiscounts(Product $product): Collection
    {
        // Implement volume discount logic
        return collect();
    }
} 