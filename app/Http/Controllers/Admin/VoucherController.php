<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Models\Discount;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class VoucherController extends Controller
{
    public function index(): Response
    {
        $vouchers = Voucher::query()->with('discount')->latest()->paginate(20)->through(fn (Voucher $v) => [
            'id' => $v->id,
            'code' => $v->code,
            'is_active' => (bool) $v->is_active,
            'usage_limit' => $v->usage_limit,
            'discount_type' => $v->discount?->type,
            'discount_value' => $v->discount ? (float) $v->discount->value : null,
        ]);

        return Inertia::render('Admin/Vouchers/Index', ['vouchers' => $vouchers]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Vouchers/Create');
    }

    public function store(VoucherRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isActive = $request->boolean('is_active', true);
        $code = strtoupper($validated['code']);

        $discount = Discount::query()->create([
            'name' => $code,
            'code' => $code,
            'type' => $validated['discount_type'],
            'value' => $validated['discount_value'],
            'applies_to' => 'all',
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
            'maximum_discount_amount' => $validated['maximum_discount_amount'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $isActive,
        ]);

        Voucher::query()->create([
            'code' => $code,
            'discount_id' => $discount->id,
            'description' => $validated['description'] ?? null,
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'per_user_limit' => $validated['per_user_limit'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $isActive,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher): Response
    {
        $voucher->load('discount');

        return Inertia::render('Admin/Vouchers/Edit', [
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'description' => $voucher->description,
                'discount_type' => $voucher->discount?->type ?? 'percent',
                'discount_value' => $voucher->discount ? (float) $voucher->discount->value : 0,
                'maximum_discount_amount' => $voucher->discount?->maximum_discount_amount,
                'minimum_order_amount' => $voucher->minimum_order_amount ?? $voucher->discount?->minimum_order_amount,
                'usage_limit' => $voucher->usage_limit,
                'per_user_limit' => $voucher->per_user_limit,
                'starts_at' => ($voucher->starts_at ?? $voucher->discount?->starts_at)?->format('Y-m-d'),
                'ends_at' => ($voucher->ends_at ?? $voucher->discount?->ends_at)?->format('Y-m-d'),
                'is_active' => (bool) $voucher->is_active,
                'image_url' => $voucher->image_url,
            ],
        ]);
    }

    public function update(VoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validated();
        $isActive = $request->boolean('is_active');
        $code = strtoupper($validated['code']);

        $discountData = [
            'name' => $code,
            'code' => $code,
            'type' => $validated['discount_type'],
            'value' => $validated['discount_value'],
            'applies_to' => 'all',
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
            'maximum_discount_amount' => $validated['maximum_discount_amount'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $isActive,
        ];

        if ($voucher->discount_id) {
            $voucher->discount()->update($discountData);
        } else {
            $discount = Discount::query()->create($discountData);
            $voucher->discount_id = $discount->id;
        }

        $voucher->update([
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'per_user_limit' => $validated['per_user_limit'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $isActive,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->discount()->delete();
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
