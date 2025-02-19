<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;
use App\Services\CartService;

class MergeGuestCart
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function handle(Login $event)
    {
        $guestId = session()->get('cart_guest_id');
        
        if (!$guestId) {
            return;
        }

        try {
            $guestCart = Cart::where('guest_id', $guestId)->first();
            if (!$guestCart) {
                return;
            }

            $userCart = Cart::firstOrCreate(
                ['user_id' => $event->user->id],
                ['guest_id' => null]
            );

            // Merge items from guest cart to user cart
            foreach ($guestCart->items as $item) {
                $existingItem = $userCart->items()
                    ->where('product_id', $item->product_id)
                    ->first();

                // Recalculate discounts for this item with the authenticated user
                $discounts = $this->cartService->calculateItemDiscounts(
                    $item->product,
                    $existingItem ? $existingItem->quantity + $item->quantity : $item->quantity,
                    $event->user
                );

                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $item->quantity,
                        'discounted_price' => $discounts['final_price'],
                        'discount_data' => [
                            'applied_discounts' => $discounts['applied_discounts'],
                            'total_discount_percentage' => $discounts['total_discount_percentage']
                        ]
                    ]);
                    $item->delete();
                } else {
                    $item->update([
                        'cart_id' => $userCart->id,
                        'discounted_price' => $discounts['final_price'],
                        'discount_data' => [
                            'applied_discounts' => $discounts['applied_discounts'],
                            'total_discount_percentage' => $discounts['total_discount_percentage']
                        ]
                    ]);
                }
            }

            // Delete the guest cart
            $guestCart->delete();
            
            // Clear the guest cart ID from session
            session()->forget('cart_guest_id');

            Log::info('Guest cart merged successfully with recalculated discounts', [
                'user_id' => $event->user->id,
                'guest_cart_id' => $guestCart->id,
                'user_cart_id' => $userCart->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to merge guest cart', [
                'error' => $e->getMessage(),
                'guest_id' => $guestId,
                'user_id' => $event->user->id
            ]);
        }
    }
} 