<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiscountRequest;
use App\Models\Discount;
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
        return view('admin.discounts.create');
    }

    public function store(DiscountRequest $request): RedirectResponse
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['code']      = $data['code'] ? strtoupper($data['code']) : null;

        Discount::query()->create($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit(Discount $discount): View
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(DiscountRequest $request, Discount $discount): RedirectResponse
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['code']      = $data['code'] ? strtoupper($data['code']) : null;

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
