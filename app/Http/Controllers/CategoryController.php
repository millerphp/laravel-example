<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function show(Request $request, Category $category)
    {
        // Load the category with its ancestors for breadcrumbs
        $category->load(['ancestors', 'discounts']);

        // Get query parameters
        $sort = $request->input('sort', 'newest');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $inStock = $request->boolean('in_stock');

        // Start building the product query
        $query = Product::whereHas('categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        })->where('is_active', true);

        $products = $query->get();

        // Apply filters
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        if ($inStock) {
            $query->whereHas('productStock', function ($query) {
                $query->whereRaw('quantity - reserved_quantity > 0');
            });
        }

        // Apply sorting
        $query = match($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        // Get products with pagination
        $products = $query->with([
            'categories.discounts',  // Load category discounts
            'discounts'              // Load product discounts
        ])
        ->paginate(12)
        ->through(fn ($product) => [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'price' => $product->price,
            'final_price' => $product->final_price ?? $product->price,
            'original_price' => $product->original_price ?? $product->price,
            'total_discount_percentage' => $product->total_discount_percentage ?? 0,
            'applied_discounts' => $product->applied_discounts ?? [],
            'image' => $product->image,
            'categories' => $product->categories->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'discounts' => $category->discounts->map(fn ($discount) => [
                    'percentage' => $discount->percentage,
                    'name' => $discount->name,
                ])
            ]),
            'stock' => $product->getTotalAvailableQuantity(),
        ])
        ->withQueryString();

        // Get price range for filters
        $priceRange = Product::whereHas('categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        })->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return Inertia::render('Categories/Show', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'ancestors' => $category->ancestors->map(fn ($ancestor) => [
                    'id' => $ancestor->id,
                    'name' => $ancestor->name,
                    'slug' => $ancestor->slug,
                ]),
                'children' => $category->children->map(fn ($child) => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                ]),
            ],
            'products' => $products,
            'filters' => [
                'sort' => $sort,
                'min_price' => $minPrice ?? $priceRange->min_price,
                'max_price' => $maxPrice ?? $priceRange->max_price,
                'price_range' => [
                    'min' => (int) $priceRange->min_price,
                    'max' => (int) $priceRange->max_price,
                ],
                'in_stock' => $inStock,
            ],
            'categories' => Category::whereNull('parent_id')
                ->with(['allChildren' => function ($query) {
                    $query->orderBy('position');
                }])
                ->orderBy('position')
                ->get(),
        ]);
    }
} 