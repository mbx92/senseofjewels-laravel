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
        $discounts = Discount::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.vouchers.create', compact('discounts'));
    }

    public function store(VoucherRequest $request): RedirectResponse
    {
        $data              = $request->validated();
        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);

        Voucher::query()->create($data);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher): View
    {
        $discounts = Discount::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.vouchers.edit', compact('voucher', 'discounts'));
    }

    public function update(VoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $data              = $request->validated();
        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
