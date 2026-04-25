<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Models\Discount;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = Voucher::query()->with('discount')->latest()->paginate(20);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    public function store(VoucherRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isActive  = $request->boolean('is_active', true);
        $code      = strtoupper($validated['code']);

        // Auto-create the backing Discount rule
        $discount = Discount::query()->create([
            'name'                    => $code,
            'code'                    => $code,
            'type'                    => $validated['discount_type'],
            'value'                   => $validated['discount_value'],
            'applies_to'              => 'all',
            'minimum_order_amount'    => $validated['minimum_order_amount'] ?? 0,
            'maximum_discount_amount' => $validated['maximum_discount_amount'] ?? null,
            'starts_at'               => $validated['starts_at'] ?? null,
            'ends_at'                 => $validated['ends_at'] ?? null,
            'is_active'               => $isActive,
        ]);

        Voucher::query()->create([
            'code'                 => $code,
            'discount_id'          => $discount->id,
            'description'          => $validated['description'] ?? null,
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
            'usage_limit'          => $validated['usage_limit'] ?? null,
            'per_user_limit'       => $validated['per_user_limit'] ?? null,
            'starts_at'            => $validated['starts_at'] ?? null,
            'ends_at'              => $validated['ends_at'] ?? null,
            'is_active'            => $isActive,
            'image_url'            => $validated['image_url'] ?? null,
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(VoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $validated = $request->validated();
        $isActive  = $request->boolean('is_active');
        $code      = strtoupper($validated['code']);

        // Update or recreate the backing Discount rule
        $discountData = [
            'name'                    => $code,
            'code'                    => $code,
            'type'                    => $validated['discount_type'],
            'value'                   => $validated['discount_value'],
            'applies_to'              => 'all',
            'minimum_order_amount'    => $validated['minimum_order_amount'] ?? 0,
            'maximum_discount_amount' => $validated['maximum_discount_amount'] ?? null,
            'starts_at'               => $validated['starts_at'] ?? null,
            'ends_at'                 => $validated['ends_at'] ?? null,
            'is_active'               => $isActive,
        ];

        if ($voucher->discount_id) {
            $voucher->discount()->update($discountData);
        } else {
            $discount = Discount::query()->create($discountData);
            $voucher->discount_id = $discount->id;
        }

        $voucher->update([
            'code'                 => $code,
            'description'          => $validated['description'] ?? null,
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
            'usage_limit'          => $validated['usage_limit'] ?? null,
            'per_user_limit'       => $validated['per_user_limit'] ?? null,
            'starts_at'            => $validated['starts_at'] ?? null,
            'ends_at'              => $validated['ends_at'] ?? null,
            'is_active'            => $isActive,
            'image_url'            => $validated['image_url'] ?? null,
        ]);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        // Also delete the auto-created backing Discount
        $voucher->discount()->delete();
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
