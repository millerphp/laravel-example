<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    public function dashboard()
    {
        $today = now();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();

        return Inertia::render('Admin/Carts/Dashboard', [
            'stats' => [
                'overview' => [
                    'total_carts' => Cart::count(),
                    'active_carts' => Cart::whereNull('completed_at')->count(),
                    'abandoned_carts' => Cart::abandoned()->count(),
                    'average_value' => Cart::avg('total_amount') ?? 0,
                ],
                'today' => [
                    'new_carts' => Cart::whereDate('created_at', $today)->count(),
                    'completed_carts' => Cart::whereDate('completed_at', $today)->count(),
                    'abandoned_carts' => Cart::abandoned()->whereDate('updated_at', $today)->count(),
                    'total_value' => Cart::whereDate('completed_at', $today)->sum('total_amount') ?? 0,
                ],
                'trends' => [
                    'daily' => $this->getDailyTrends(),
                    'hourly' => $this->getHourlyTrends(),
                ],
                'top_products' => $this->getTopProducts(),
                'conversion_rate' => $this->getConversionRate(),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = Cart::query()
            ->with(['user', 'items.product'])
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    return $query->whereNull('completed_at');
                } elseif ($status === 'completed') {
                    return $query->whereNotNull('completed_at');
                } elseif ($status === 'abandoned') {
                    return $query->abandoned();
                }
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->date_range, function ($query, $range) {
                [$start, $end] = explode(',', $range);
                return $query->whereBetween('created_at', [$start, $end]);
            })
            ->latest();

        return Inertia::render('Admin/Carts/Index', [
            'carts' => $query->paginate(10)->through(fn ($cart) => [
                'id' => $cart->id,
                'user' => $cart->user ? [
                    'name' => $cart->user->name,
                    'email' => $cart->user->email,
                ] : null,
                'items_count' => $cart->items->count(),
                'total_amount' => $cart->total_amount,
                'status' => $cart->completed_at ? 'completed' : 
                    ($cart->isAbandoned() ? 'abandoned' : 'active'),
                'created_at' => $cart->created_at->format('Y-m-d H:i:s'),
                'completed_at' => $cart->completed_at?->format('Y-m-d H:i:s'),
                'last_activity' => $cart->updated_at->format('Y-m-d H:i:s'),
            ]),
            'filters' => $request->only(['status', 'search', 'date_range']),
        ]);
    }

    public function show(Cart $cart)
    {
        return Inertia::render('Admin/Carts/Show', [
            'cart' => [
                'id' => $cart->id,
                'user' => $cart->user ? [
                    'id' => $cart->user->id,
                    'name' => $cart->user->name,
                    'email' => $cart->user->email,
                    'created_at' => $cart->user->created_at->format('Y-m-d'),
                    'orders_count' => $cart->user->orders_count ?? 0,
                ] : null,
                'items' => $cart->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product' => [
                        'title' => $item->product->title,
                        'price' => $item->product->price,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ]),
                'total_amount' => $cart->total_amount,
                'created_at' => $cart->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $cart->updated_at->format('Y-m-d H:i:s'),
                'completed_at' => $cart->completed_at?->format('Y-m-d H:i:s'),
                'status' => $cart->completed_at ? 'completed' : 
                    ($cart->isAbandoned() ? 'abandoned' : 'active'),
            ],
            'similar_carts' => $this->getSimilarCarts($cart),
            'product_recommendations' => $this->getProductRecommendations($cart),
        ]);
    }

    private function getDailyTrends()
    {
        return Cart::select(
            DB::raw('date(created_at) as date'),
            DB::raw('COUNT(*) as total_carts'),
            DB::raw('COUNT(completed_at) as completed_carts'),
            DB::raw('SUM(CASE WHEN completed_at IS NULL AND datetime(updated_at) < datetime("now", "-24 hours") THEN 1 ELSE 0 END) as abandoned_carts'),
            DB::raw('AVG(total_amount) as average_amount')
        )
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getHourlyTrends()
    {
        return Cart::select(
            DB::raw('strftime("%H", created_at) as hour'),
            DB::raw('COUNT(*) as total_carts'),
            DB::raw('COUNT(completed_at) as completed_carts')
        )
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    private function getTopProducts()
    {
        return DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.title',
                DB::raw('COUNT(*) as cart_count'),
                DB::raw('SUM(cart_items.quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('cart_count')
            ->limit(10)
            ->get();
    }

    private function getConversionRate()
    {
        $total = Cart::where('created_at', '>=', now()->subDays(30))->count();
        $completed = Cart::whereNotNull('completed_at')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function getSimilarCarts(Cart $cart)
    {
        // Find carts with similar products
        return Cart::whereHas('items', function ($query) use ($cart) {
            $query->whereIn('product_id', $cart->items->pluck('product_id'));
        })
            ->where('id', '!=', $cart->id)
            ->limit(5)
            ->get();
    }

    private function getProductRecommendations(Cart $cart)
    {
        // Get products frequently bought together with the items in this cart
        return DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->whereIn('carts.id', function ($query) use ($cart) {
                $query->select('cart_id')
                    ->from('cart_items')
                    ->whereIn('product_id', $cart->items->pluck('product_id'));
            })
            ->whereNotIn('products.id', $cart->items->pluck('product_id'))
            ->select(
                'products.id',
                'products.name',
                DB::raw('COUNT(*) as frequency')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('frequency')
            ->limit(5)
            ->get();
    }
} 