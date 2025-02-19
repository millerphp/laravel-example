<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\HasDiscounts;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use NodeTrait;
    use HasFactory;
    use HasDiscounts;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'discount_percentage',
        'parent_id',
        '_lft',
        '_rgt',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('navigation_categories');
        });

        static::deleted(function () {
            Cache::forget('navigation_categories');
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withTimestamps();
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function calculateEffectiveDiscount(): float
    {
        // Get all ancestor categories including this one
        $categories = $this->ancestorsAndSelf($this->id);
        
        // Calculate the combined discount
        // For example, if parent has 10% and child has 5%, final discount would be 14.5%
        // (1 - ((100 - 10)/100 * (100 - 5)/100)) * 100
        $discount = $categories->reduce(function ($carry, $category) {
            return $carry * (1 - ($category->discount_percentage ?? 0) / 100);
        }, 1);

        return round((1 - $discount) * 100);
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function scopeOrderByPosition($query)
    {
        return $query->orderBy('position');
    }
} 