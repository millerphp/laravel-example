<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Get total users (excluding admins)
        $userCount = User::role('User')->count();

        // Get total products
        $productCount = Product::count();

        // Get cart statistics
        $cartStats = Cart::select([
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(items.quantity * items.unit_price) as total_revenue')
        ])
        ->join('cart_items as items', 'carts.id', '=', 'items.cart_id')
        ->whereNotNull('status')
        ->whereNotIn('status', ['cancelled', 'pending'])
        ->first();

        // Get recent orders (carts)
        $recentOrders = Cart::with(['items.product', 'user'])
            ->has('items')
            ->whereNotNull('status')
            ->whereNotIn('status', ['pending', 'cancelled'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($cart) => [
                'id' => $cart->id,
                'status' => $cart->status ?? 'pending',
                'date' => $cart->created_at->format('M d, Y'),
                'total' => '£' . number_format($cart->items->sum(fn($item) => $item->quantity * $item->unit_price), 2),
                'user' => $cart->user?->name ?? 'Guest',
            ]);

        // Get additional order stats
        $orderStats = [
            'averageOrderValue' => '£' . number_format(
                Cart::whereNotIn('status', ['pending', 'cancelled'])
                    ->join('cart_items as items', 'carts.id', '=', 'items.cart_id')
                    ->avg(DB::raw('items.quantity * items.unit_price')) ?? 0,
                2
            ),
            'processingOrders' => Cart::where('status', 'processing')->count(),
            'completedOrders' => Cart::where('status', 'completed')->count(),
            'lowStockProducts' => Product::whereHas('productStock', function ($query) {
                $query->whereRaw('quantity - reserved_quantity < 10');
            })->count(),
        ];

        // Get sales data for chart
        $salesData = Cart::select([
            DB::raw('strftime("%Y-%m-%d", carts.created_at) as date'),
            DB::raw('SUM(items.quantity * items.unit_price) as total')
        ])
        ->join('cart_items as items', 'carts.id', '=', 'items.cart_id')
        ->whereNotIn('status', ['pending', 'cancelled'])
        ->where('carts.created_at', '>=', now()->subDays(30))
        ->groupBy(DB::raw('strftime("%Y-%m-%d", carts.created_at)'))
        ->orderBy('date')
        ->get();

        // Fill in missing dates with zero values
        $dateRange = collect();
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dateRange[$date] = 0;
        }

        // Merge actual data with date range
        $salesData->each(function ($sale) use (&$dateRange) {
            $dateRange[$sale->date] = $sale->total;
        });

        $salesChart = [
            'data' => [
                'labels' => array_keys($dateRange->toArray()),
                'datasets' => [
                    [
                        'label' => 'Daily Sales',
                        'data' => array_values($dateRange->toArray()),
                        'borderColor' => '#4F46E5',
                        'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                        'fill' => true,
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'layout' => [
                    'padding' => [
                        'top' => 20,
                        'right' => 20,
                        'bottom' => 20,
                        'left' => 20
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'callback' => "function(value) { return '£' + value; }"
                        ]
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false
                        ],
                        'ticks' => [
                            'maxRotation' => 45,
                            'minRotation' => 45
                        ]
                    ]
                ]
            ]
        ];

        return Inertia::render('Dashboard', [
            'stats' => [
                'users' => $userCount,
                'products' => $productCount,
                'orders' => $cartStats->total_orders ?? 0,
                'revenue' => '£' . number_format($cartStats->total_revenue ?? 0, 2),
            ],
            'recentOrders' => $recentOrders,
            'orderStats' => $orderStats,
            'salesChart' => $salesChart,
        ]);
    }
}