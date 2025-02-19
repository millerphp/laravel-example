<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'is_active',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function productStock()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getAvailableStock(Product $product): int
    {
        return $this->productStock()
            ->where('product_id', $product->id)
            ->value('quantity') ?? 0;
    }

    public function getReservedStock(Product $product): int
    {
        return $this->productStock()
            ->where('product_id', $product->id)
            ->value('reserved_quantity') ?? 0;
    }
} 