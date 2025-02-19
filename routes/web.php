<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddToCartController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['web', 'auth', 'verified', 'role:Administrator'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Admin Product Routes
    Route::prefix('admin/products')->name('admin.products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');
    });
    // Admin Category Routes
    Route::prefix('admin/categories')->name('admin.categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [AdminCategoryController::class, 'reorder'])->name('reorder');
    });
    // We'll add other admin routes here later
});

// Cart routes (available to both guests and authenticated users)
Route::post('/cart', [AddToCartController::class, 'store'])->name('cart.add');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show'); // Inertia view
Route::get('/api/cart', [CartController::class, 'data'])->name('cart.data'); // JSON data
Route::patch('/cart/{cartItem}', [CartItemController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartItemController::class, 'destroy'])->name('cart.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Category routes with infinite nesting support
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Product routes
Route::get('/products/{product:slug}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/checkout', function () {
    return Inertia::render('Checkout/Index');
})->name('checkout');

require __DIR__.'/auth.php';
