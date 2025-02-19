<?php

namespace App\Models;

use App\Models\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Services\DiscountService;
use App\Models\Traits\HasDiscounts;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    use HasUniqueSlug;
    use HasDiscounts;
    
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'image',
    ];

    protected $appends = [
        'final_price',
        'original_price',
        'applied_discounts',
        'total_discount_percentage'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withTimestamps();
    }

    public function calculateFinalPrice(?User $user = null): float
    {
        $price = $this->price;
        
        // Apply highest category discount
        $categoryDiscount = $this->getHighestCategoryDiscount();
        if ($categoryDiscount > 0) {
            $price = $price * (1 - $categoryDiscount / 100);
        }

        // Apply customer discount if user is provided and has active discount
        if ($user && $user->activeDiscount) {
            $price = $price * (1 - $user->activeDiscount->discount_percentage / 100);
        }

        return round($price, 2);
    }

    /**
     * Get the highest effective discount from all associated categories
     */
    public function getHighestCategoryDiscount(): float
    {
        return $this->categories()
            ->get()
            ->map(function ($category) {
                return $category->calculateEffectiveDiscount();
            })
            ->max() ?? 0;
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function productStock(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getTotalStockQuantity(): int
    {
        return $this->productStock()->sum('quantity');
    }

    public function getTotalReservedQuantity(): int
    {
        return $this->productStock()->sum('reserved_quantity');
    }

    public function getTotalAvailableQuantity(): int
    {
        return $this->productStock()
            ->sum(DB::raw('quantity - reserved_quantity'));
    }

    public function getStockInWarehouse(Warehouse $warehouse): ?ProductStock
    {
        return $this->productStock()
            ->where('warehouse_id', $warehouse->id)
            ->first();
    }

    public function needsReorder(): bool
    {
        return $this->productStock()
            ->where('quantity', '<=', DB::raw('reorder_point'))
            ->exists();
    }

    /**
     * Calculate the final price after all discounts
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                $discountService = app(DiscountService::class);
                $discount = $discountService->calculateBestDiscount($this);
                return $discount['final_price'];
            }
        );
    }

    /**
     * Get the original price before discounts
     */
    protected function originalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price
        );
    }

    /**
     * Get all applied discounts
     */
    protected function appliedDiscounts(): Attribute
    {
        return Attribute::make(
            get: function () {
                $discountService = app(DiscountService::class);
                $discount = $discountService->calculateBestDiscount($this);
                return $discount['applied_discounts'];
            }
        );
    }

    /**
     * Get total discount percentage
     */
    protected function totalDiscountPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $discountService = app(DiscountService::class);
                $discount = $discountService->calculateBestDiscount($this);
                return $discount['total_discount_percentage'];
            }
        );
    }
} 