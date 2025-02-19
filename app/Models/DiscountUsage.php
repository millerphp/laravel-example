<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountUsage extends Model
{
    protected $fillable = [
        'discount_id',
        'order_id',
        'product_id',
        'user_id',
        'amount_saved',
        'percentage_applied',
        'type', // 'product', 'category', 'cart'
    ];

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 