<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'discounted_price',
        'discount_data'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'discount_data' => 'array',
        'quantity' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->discounted_price;
    }

    public function getSavingsAttribute()
    {
        return ($this->unit_price - $this->discounted_price) * $this->quantity;
    }
} 