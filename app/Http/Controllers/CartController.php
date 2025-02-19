<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CartItem;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function show(Request $request)
    {
        $cart = Cart::findOrCreateCart($request);
        
        // Load cart items with their products and categories
        $items = $cart->items()
            ->with(['product.categories'])
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'product' => [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'slug' => $item->product->slug,
                    'image' => $item->product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=600&fit=crop',
                ],
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'discounted_price' => (float) $item->discounted_price,
                'total' => (float) $item->subtotal,
                'applied_discounts' => $item->discount_data['applied_discounts'] ?? [],
            ]);

        $totals = [
            'subtotal' => $items->sum('total'),
            'cart_discounts' => [],  // Cart-level discounts
            'total_savings' => $items->sum(fn($item) => 
                ($item['unit_price'] * $item['quantity']) - $item['total']
            ),
            'final_total' => $items->sum('total'),  // After all discounts
        ];

        // If cart total is over £500, apply the big spender discount
        if ($totals['subtotal'] >= 500) {
            $totals['cart_discounts'][] = [
                'name' => 'Big Spender Discount',
                'description' => 'Special discount on orders over £500',
                'percentage' => 15,
            ];
            $totals['final_total'] = $totals['final_total'] * 0.85; // 15% off
            $totals['total_savings'] += ($totals['subtotal'] - $totals['final_total']);
        }

        return Inertia::render('Cart/Show', [
            'items' => $items,
            'subtotal' => round($totals['subtotal'], 2),
            'cart_discounts' => $totals['cart_discounts'],
            'total_savings' => round($totals['total_savings'], 2),
            'final_total' => round($totals['final_total'], 2),
        ]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
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

        return back();
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return back();
    }

    public function data(Request $request)
    {
        $cart = Cart::findOrCreateCart($request);
        
        $items = $cart->items()
            ->with(['product.categories'])
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'product' => [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'slug' => $item->product->slug,
                    'image' => $item->product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=600&fit=crop',
                ],
                'quantity' => (int) $item->quantity,
                'unit_price' => number_format((float) $item->unit_price, 2, '.', ''),
                'discounted_price' => number_format((float) $item->discounted_price, 2, '.', ''),
                'total' => number_format((float) ($item->quantity * $item->discounted_price), 2, '.', ''),
                'applied_discounts' => $item->discount_data['applied_discounts'] ?? [],
            ]);

        return response()->json([
            'items' => $items
        ]);
    }
} 