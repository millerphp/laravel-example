<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Services\CartService;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function update(Request $request, CartItem $cartItem)
    {
        // Verify the cart item belongs to the current cart
        if ($cartItem->cart_id !== Cart::findOrCreateCart($request)->id) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Recalculate discounts with new quantity
        $discounts = $this->cartService->calculateItemDiscounts(
            $cartItem->product,
            $validated['quantity'],
            $request->user()
        );

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'discounted_price' => $discounts['final_price'],
            'discount_data' => [
                'applied_discounts' => $discounts['applied_discounts'],
                'total_discount_percentage' => $discounts['total_discount_percentage']
            ]
        ]);

        return response()->json([
            'message' => 'Cart item updated',
            'item' => [
                'id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
                'discounted_price' => $cartItem->discounted_price,
                'discount_data' => $cartItem->discount_data,
                'total' => $cartItem->subtotal
            ]
        ]);
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        // Verify the cart item belongs to the current cart
        if ($cartItem->cart_id !== Cart::findOrCreateCart($request)->id) {
            abort(403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Cart item removed'
        ]);
    }
} 