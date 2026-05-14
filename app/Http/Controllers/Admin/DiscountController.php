<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountRequest;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DiscountController extends Controller
{
    public function index(): Response
    {
        $discounts = Discount::query()->latest()->paginate(20)->through(fn (Discount $d) => [
            'id' => $d->id,
            'name' => $d->name,
            'code' => $d->code,
            'type' => $d->type,
            'value' => (float) $d->value,
            'applies_to' => $d->applies_to,
            'is_active' => (bool) $d->is_active,
            'starts_at' => $d->starts_at?->format('Y-m-d'),
            'ends_at' => $d->ends_at?->format('Y-m-d'),
        ]);

        return Inertia::render('Admin/Discounts/Index', ['discounts' => $discounts]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Discounts/Create', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(DiscountRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code'] = $data['code'] ? strtoupper($data['code']) : null;

        if ($data['applies_to'] === 'category') {
            $data['conditions'] = ['category_ids' => array_map('intval', $request->input('category_ids', []))];
        } elseif ($data['applies_to'] === 'product') {
            $data['conditions'] = ['product_ids' => array_map('intval', $request->input('product_ids', []))];
        } else {
            $data['conditions'] = null;
        }

        Discount::query()->create($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit(Discount $discount): Response
    {
        $conditions = $discount->conditions ?? [];
        $categoryIds = $conditions['category_ids'] ?? [];
        $productIds = $conditions['product_ids'] ?? [];

        return Inertia::render('Admin/Discounts/Edit', [
            'discount' => [
                'id' => $discount->id,
                'name' => $discount->name,
                'code' => $discount->code,
                'description' => $discount->description,
                'type' => $discount->type,
                'value' => $discount->value,
                'applies_to' => $discount->applies_to,
                'minimum_order_amount' => $discount->minimum_order_amount,
                'maximum_discount_amount' => $discount->maximum_discount_amount,
                'usage_limit' => $discount->usage_limit,
                'starts_at' => $discount->starts_at?->format('Y-m-d'),
                'ends_at' => $discount->ends_at?->format('Y-m-d'),
                'is_active' => (bool) $discount->is_active,
                'image_url' => $discount->image_url,
                'category_ids' => array_map('strval', (array) $categoryIds),
                'product_ids' => array_map('strval', (array) $productIds),
            ],
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(DiscountRequest $request, Discount $discount): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['code'] = $data['code'] ? strtoupper($data['code']) : null;

        if ($data['applies_to'] === 'category') {
            $data['conditions'] = ['category_ids' => array_map('intval', $request->input('category_ids', []))];
        } elseif ($data['applies_to'] === 'product') {
            $data['conditions'] = ['product_ids' => array_map('intval', $request->input('product_ids', []))];
        } else {
            $data['conditions'] = null;
        }

        $discount->update($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Diskon berhasil dihapus.');
    }
}
