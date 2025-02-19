<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscountController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Discounts/Index', [
            'discounts' => Discount::query()
                ->orderByDesc('created_at')
                ->paginate(10)
                ->through(fn ($discount) => [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'description' => $discount->description,
                    'percentage' => $discount->percentage,
                    'type' => $discount->type,
                    'is_active' => $discount->is_active,
                    'starts_at' => $discount->starts_at?->format('Y-m-d H:i'),
                    'ends_at' => $discount->ends_at?->format('Y-m-d H:i'),
                    'usage_count' => $discount->usage_count,
                    'usage_limit' => $discount->usage_limit,
                    'minimum_order_amount' => $discount->minimum_order_amount,
                    'maximum_discount_amount' => $discount->maximum_discount_amount,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Discounts/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100',
            'type' => 'required|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'rules' => 'nullable|array',
            'stacking_rules' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
        ]);

        Discount::create($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount)
    {
        return Inertia::render('Admin/Discounts/Form', [
            'discount' => [
                'id' => $discount->id,
                'name' => $discount->name,
                'description' => $discount->description,
                'percentage' => $discount->percentage,
                'type' => $discount->type,
                'starts_at' => $discount->starts_at?->format('Y-m-d\TH:i'),
                'ends_at' => $discount->ends_at?->format('Y-m-d\TH:i'),
                'is_active' => $discount->is_active,
                'priority' => $discount->priority,
                'rules' => $discount->rules,
                'stacking_rules' => $discount->stacking_rules,
                'usage_limit' => $discount->usage_limit,
                'minimum_order_amount' => $discount->minimum_order_amount,
                'maximum_discount_amount' => $discount->maximum_discount_amount,
            ],
        ]);
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100',
            'type' => 'required|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'rules' => 'nullable|array',
            'stacking_rules' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
        ]);

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return back()->with('success', 'Discount deleted successfully.');
    }

    public function toggle(Discount $discount)
    {
        $discount->update(['is_active' => !$discount->is_active]);

        return back()->with('success', 
            $discount->is_active ? 'Discount activated successfully.' : 'Discount deactivated successfully.'
        );
    }
} 