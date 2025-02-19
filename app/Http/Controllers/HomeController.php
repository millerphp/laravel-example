<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'featuredProducts' => Product::with(['categories'])
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit(8)
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
                    'categories' => $product->categories->map(fn ($category) => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ]),
                    'stock' => $product->getTotalAvailableQuantity(),
                ]),
            'categories' => $this->getNestedCategories(),
        ]);
    }

    private function getNestedCategories()
    {
        return Category::defaultOrder()  // This uses the nested set ordering
            ->get()
            ->toTree()  // This converts the flat structure to a tree
            ->map(function ($category) {
                return $this->formatCategory($category);
            });
    }

    private function formatCategory($category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'children' => $category->children->map(function ($child) {
                return $this->formatCategory($child);
            }),
        ];
    }
} 