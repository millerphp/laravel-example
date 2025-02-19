<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Warehouse;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['categories', 'productStock', 'discounts', 'categories.discounts'])
            ->withCount(['cartItems as in_cart_count'])
            ->withSum('cartItems as total_quantity_sold', 'quantity')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->whereHas('categories', function ($q) use ($category) {
                    $q->where('categories.id', $category);
                });
            })
            ->when($request->stock_status, function ($query, $status) {
                if ($status === 'low') {
                    $query->whereHas('productStock', function ($q) {
                        $q->whereRaw('quantity - reserved_quantity < 10');
                    });
                } elseif ($status === 'out') {
                    $query->whereHas('productStock', function ($q) {
                        $q->whereRaw('quantity - reserved_quantity <= 0');
                    });
                }
            });

        $products = $query->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($product) => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => $product->price,
                'final_price' => $product->final_price,
                'total_discount_percentage' => $product->total_discount_percentage,
                'applied_discounts' => $product->applied_discounts,
                'image' => $product->image,
                'is_active' => $product->is_active,
                'categories' => $product->categories->map->only(['id', 'name']),
                'stock' => $product->getTotalAvailableQuantity(),
                'in_cart_count' => $product->in_cart_count,
                'total_sold' => $product->total_quantity_sold ?? 0,
                'created_at' => $product->created_at->format('M d, Y'),
            ]);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'stock_status' => $request->stock_status,
            ],
            'categories' => Category::all()->map->only(['id', 'name']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Form', [
            'categories' => Category::all()->map->only(['id', 'name']),
            'warehouses' => Warehouse::all()->map->only(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|string',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'stock' => 'array',
            'stock.*.warehouse_id' => 'required|exists:warehouses,id',
            'stock.*.quantity' => 'required|integer|min:0',
            'stock.*.reorder_point' => 'required|integer|min:0',
        ]);

        $product = Product::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'],
            'image' => $validated['image'],
        ]);

        $product->categories()->sync($validated['category_ids']);

        // Handle stock levels
        foreach ($validated['stock'] as $stockData) {
            $product->productStock()->create([
                'warehouse_id' => $stockData['warehouse_id'],
                'quantity' => $stockData['quantity'],
                'reorder_point' => $stockData['reorder_point'],
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load(['categories', 'productStock.warehouse']);

        return Inertia::render('Admin/Products/Form', [
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image,
                'is_active' => $product->is_active,
                'category_ids' => $product->categories->pluck('id'),
                'stock' => $product->productStock->map(fn($stock) => [
                    'warehouse_id' => $stock->warehouse_id,
                    'quantity' => $stock->quantity,
                    'reorder_point' => $stock->reorder_point,
                ]),
            ],
            'categories' => Category::all()->map->only(['id', 'name']),
            'warehouses' => Warehouse::all()->map->only(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|string',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'stock' => 'array',
            'stock.*.warehouse_id' => 'required|exists:warehouses,id',
            'stock.*.quantity' => 'required|integer|min:0',
            'stock.*.reorder_point' => 'required|integer|min:0',
        ]);

        $product->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'],
            'image' => $validated['image'],
        ]);

        $product->categories()->sync($validated['category_ids']);

        // Update stock levels
        $product->productStock()->delete(); // Remove existing stock entries
        foreach ($validated['stock'] as $stockData) {
            $product->productStock()->create([
                'warehouse_id' => $stockData['warehouse_id'],
                'quantity' => $stockData['quantity'],
                'reorder_point' => $stockData['reorder_point'],
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Check if product can be safely deleted
        if ($product->cartItems()->exists()) {
            return back()->with('error', 'Cannot delete product - it exists in customer carts.');
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }
} 