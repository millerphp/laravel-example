<?php

namespace App\Models\Traits;

use App\Models\Discount;

trait HasDiscounts
{
    public function discounts()
    {
        return $this->morphMany(Discount::class, 'discountable');
    }

    public function activeDiscounts()
    {
        return $this->discounts()->active();
    }
} 