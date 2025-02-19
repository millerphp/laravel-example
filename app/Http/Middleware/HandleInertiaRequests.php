<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $isAdminRoute = str_starts_with($request->path(), 'admin/') || str_starts_with($request->path(), 'dashboard');
        $categories = Cache::remember('navigation_categories', 3600, function () {
            return Category::whereNull('parent_id')
                ->with(['allChildren' => function ($query) {
                    $query->orderBy('position');
                }])
                ->orderBy('position')
                ->get();
        });

        \Log::info('Categories being shared:', [
            'count' => $categories->count(),
            'data' => $categories->toArray()
        ]);

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'can' => [
                    'manage_categories' => $user?->can('manage_categories') ?? false,
                ],
                'intended_url' => $request->session()->get('url.intended'),
            ],
            'csrf_token' => csrf_token(),
            'cart' => fn () => $isAdminRoute ? null : $request->session()->get('cart'),
            'categories' => $user ? $categories : [],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
