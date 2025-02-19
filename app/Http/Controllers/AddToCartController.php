<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Models\Cart;

class AddToCartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with('categories')->findOrFail($validated['product_id']);
        $cart = Cart::findOrCreateCart($request);

        // Calculate discounts before adding to cart
        $discounts = $this->cartService->calculateItemDiscounts(
            $product, 
            $validated['quantity'],
            $request->user()
        );

        // Add or update cart item with discount information
        $cartItem = $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'quantity' => $validated['quantity'],
                'unit_price' => $product->price,
                'discounted_price' => $discounts['final_price'],
                'discount_data' => [
                    'applied_discounts' => $discounts['applied_discounts'],
                    'total_discount_percentage' => $discounts['total_discount_percentage']
                ]
            ]
        );

        return response()->json([
            'message' => 'Item added to cart',
            'item' => [
                'id' => $cartItem->id,
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'image' => $product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=600&fit=crop',
                ],
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
                'discounted_price' => $cartItem->discounted_price,
                'discount_data' => $cartItem->discount_data,
                'total' => $cartItem->subtotal
            ]
        ]);
    }
} 