<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['categories']);
        
        return Inertia::render('Products/Show', [
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'final_price' => $product->final_price,
                'original_price' => $product->original_price,
                'total_discount_percentage' => $product->total_discount_percentage,
                'applied_discounts' => $product->applied_discounts,
                'image' => $product->image,
                'images' => [$product->image], // For now, just use the main image
                'categories' => $product->categories->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ]),
                'specifications' => [], // Add actual specifications when available
                'getTotalAvailableQuantity' => $product->getTotalAvailableQuantity(),
            ],
            'relatedProducts' => Product::whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => $product->price,
                'final_price' => $product->final_price,
                'original_price' => $product->original_price,
                'total_discount_percentage' => $product->total_discount_percentage,
                'applied_discounts' => $product->applied_discounts,
                'image' => $product->image,
                'stock' => $product->getTotalAvailableQuantity(),
            ]),
        ]);
    }
} 