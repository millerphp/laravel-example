<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'description',
        'percentage',
        'type',
        'starts_at',
        'ends_at',
        'is_active',
        'priority',
        'rules',
        'stacking_rules',
        'usage_limit',
        'usage_count',
        'minimum_order_amount',
        'maximum_discount_amount',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'rules' => 'array',
        'stacking_rules' => 'array',
        'percentage' => 'decimal:2',
    ];

    public function discountable()
    {
        return $this->morphTo();
    }

    public function isValid(): bool
    {
        return $this->is_active
            && (!$this->starts_at || $this->starts_at->isPast())
            && (!$this->ends_at || $this->ends_at->isFuture())
            && (!$this->usage_limit || $this->usage_count < $this->usage_limit);
    }

    public function canStackWith(Discount $otherDiscount): bool
    {
        if (!$this->stacking_rules) {
            return true;
        }

        return in_array($otherDiscount->type, $this->stacking_rules['allowed_types'] ?? []);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereRaw('usage_count < usage_limit');
            });
    }
} 