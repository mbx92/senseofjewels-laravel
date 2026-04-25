<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountRequest;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function index(): View
    {
        $discounts = Discount::query()->latest()->paginate(20);

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create(): View
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $products   = Product::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.discounts.create', compact('categories', 'products'));
    }

    public function store(DiscountRequest $request): RedirectResponse
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code']      = $data['code'] ? strtoupper($data['code']) : null;

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

    public function edit(Discount $discount): View
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $products   = Product::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.discounts.edit', compact('discount', 'categories', 'products'));
    }

    public function update(DiscountRequest $request, Discount $discount): RedirectResponse
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['code']      = $data['code'] ? strtoupper($data['code']) : null;

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
