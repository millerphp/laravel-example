<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        \Log::info('Loading categories in admin controller');
        $categories = Category::withCount(['products', 'discounts'])
            ->with(['parent', 'children'])
            ->orderBy('position')
            ->get();

        \Log::info('Categories found:', [
            'count' => $categories->count(),
            'data' => $categories->toArray()
        ]);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'parent' => $category->parent?->only(['id', 'name']),
                'children_count' => $category->children->count(),
                'products_count' => $category->products_count,
                'discounts_count' => $category->discounts_count,
                'discount_percentage' => $category->discount_percentage,
                'position' => $category->position,
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Categories/Form', [
            'parentCategories' => Category::all()->map->only(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'parent_id' => $validated['parent_id'],
            'discount_percentage' => $validated['discount_percentage'],
            'position' => Category::max('position') + 1,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Admin/Categories/Form', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'parent_id' => $category->parent_id,
                'discount_percentage' => $category->discount_percentage,
            ],
            'parentCategories' => Category::where('id', '!=', $category->id)
                ->get()
                ->map
                ->only(['id', 'name']),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value === $category->id) {
                        $fail('A category cannot be its own parent.');
                    }
                    if ($value && $category->children->contains($value)) {
                        $fail('A category cannot have one of its descendants as its parent.');
                    }
                },
            ],
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category with subcategories.');
        }

        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with products.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.position' => 'required|integer|min:0',
        ]);

        foreach ($validated['categories'] as $item) {
            Category::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return back()->with('success', 'Categories reordered successfully.');
    }
} 