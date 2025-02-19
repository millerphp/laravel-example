<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
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
} 