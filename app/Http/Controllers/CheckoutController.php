<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function show(Request $request)
    {
        $cart = $request->user()->cart;
        $items = $cart->items()->with('product')->get();

        $totals = $this->cartService->calculateTotals(
            $items,
            $request->user()
        );

        return Inertia::render('Checkout/Show', [
            'items' => $totals['items'],
            'subtotal' => $totals['subtotal'],
            'cart_discounts' => $totals['cart_discounts'],
            'total_savings' => $totals['total_savings'],
            'final_total' => $totals['final_total'],
        ]);
    }
} 