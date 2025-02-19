<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'status',
        'total_amount',
        'completed_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($cart) {
            // If no user_id, generate a guest_id
            if (!$cart->user_id) {
                $cart->guest_id = Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public static function findOrCreateCart($request)
    {
        if ($request->user()) {
            return $request->user()->cart ?? static::create(['user_id' => $request->user()->id]);
        }

        // For guests, use session to store cart ID
        $guestId = $request->session()->get('cart_guest_id');
        
        if ($guestId) {
            $cart = static::where('guest_id', $guestId)->first();
            if ($cart) {
                return $cart;
            }
        }

        // Create new guest cart
        $cart = static::create();
        $request->session()->put('cart_guest_id', $cart->guest_id);
        
        return $cart;
    }

    // Method to merge guest cart with user cart upon login
    public function mergeWith(Cart $otherCart)
    {
        foreach ($otherCart->items as $item) {
            $existingItem = $this->items()->where('product_id', $item->product_id)->first();
            
            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity
                ]);
            } else {
                $item->cart_id = $this->id;
                $item->save();
            }
        }

        $otherCart->delete();
    }

    // Scopes
    public function scopeAbandoned(Builder $query): Builder
    {
        return $query->whereNull('completed_at')
            ->whereRaw('datetime(updated_at) <= datetime("now", "-24 hours")');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('completed_at')
            ->where('updated_at', '>', now()->subHours(24));
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('completed_at');
    }

    // Helper Methods
    public function isAbandoned(): bool
    {
        return !$this->completed_at && 
            $this->updated_at->lte(now()->subHours(24));
    }

    public function isActive(): bool
    {
        return !$this->completed_at && 
            $this->updated_at->gt(now()->subHours(24));
    }

    public function isCompleted(): bool
    {
        return (bool) $this->completed_at;
    }

    public function complete(): void
    {
        $this->update([
            'completed_at' => now(),
        ]);
    }

    public function calculateTotal(): void
    {
        $this->update([
            'total_amount' => $this->items->sum(function ($item) {
                return $item->quantity * $item->price;
            }),
        ]);
    }
} 