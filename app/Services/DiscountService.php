<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DiscountService
{
    /**
     * Return the effective price for a product after applying any active discount.
     * Returns null if no active discount applies.
     */
    public function applyProductDiscount(Product $product): ?float
    {
        $now = Carbon::now();

        $discount = Discount::query()
            ->whereDoesntHave('vouchers')
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->where(function ($q) use ($product) {
                $q->where('applies_to', 'all')
                  ->orWhere(function ($q2) use ($product) {
                      $q2->where('applies_to', 'category')
                         ->whereJsonContains('conditions->category_ids', $product->category_id);
                  })
                  ->orWhere(function ($q2) use ($product) {
                      $q2->where('applies_to', 'product')
                         ->whereJsonContains('conditions->product_ids', $product->id);
                  });
            })
            ->orderByDesc('value')
            ->first();

        if (! $discount) {
            return null;
        }

        if ($discount->type === 'percent') {
            $discounted = $product->price * (1 - $discount->value / 100);
        } else {
            $discounted = max(0, $product->price - $discount->value);
        }

        if ($discount->maximum_discount_amount && ($product->price - $discounted) > $discount->maximum_discount_amount) {
            $discounted = $product->price - $discount->maximum_discount_amount;
        }

        return round($discounted, 0);
    }

    /**
     * Validate a voucher code and return the Voucher or an error message.
     */
    public function validateVoucher(string $code, ?int $userId, float $subtotal): array
    {
        $voucher = Voucher::query()->where('code', strtoupper($code))->first();

        if (! $voucher) {
            return ['valid' => false, 'message' => 'Kode voucher tidak ditemukan.'];
        }

        if (! $voucher->is_active) {
            return ['valid' => false, 'message' => 'Voucher tidak aktif.'];
        }

        $now = Carbon::now();

        if ($voucher->starts_at && $voucher->starts_at->gt($now)) {
            return ['valid' => false, 'message' => 'Voucher belum dapat digunakan.'];
        }

        if ($voucher->ends_at && $voucher->ends_at->lt($now)) {
            return ['valid' => false, 'message' => 'Voucher sudah kedaluwarsa.'];
        }

        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            return ['valid' => false, 'message' => 'Batas penggunaan voucher telah habis.'];
        }

        if ($voucher->minimum_order_amount && $subtotal < $voucher->minimum_order_amount) {
            return [
                'valid'   => false,
                'message' => 'Minimum pembelian Rp ' . number_format($voucher->minimum_order_amount, 0, ',', '.') . ' untuk voucher ini.',
            ];
        }

        if ($userId && $voucher->per_user_limit) {
            $usedByUser = VoucherUsage::query()
                ->where('voucher_id', $voucher->id)
                ->where('user_id', $userId)
                ->count();

            if ($usedByUser >= $voucher->per_user_limit) {
                return ['valid' => false, 'message' => 'Anda sudah menggunakan voucher ini.'];
            }
        }

        $discountAmount = $this->applyVoucher($voucher, $subtotal);

        return [
            'valid'           => true,
            'voucher'         => $voucher,
            'discount_amount' => $discountAmount,
            'message'         => 'Voucher berhasil diterapkan.',
        ];
    }

    /**
     * Calculate the monetary discount from a voucher against a subtotal.
     */
    public function applyVoucher(Voucher $voucher, float $subtotal): float
    {
        $discount = $voucher->discount;

        if (! $discount) {
            return 0;
        }

        if ($discount->type === 'percent') {
            $amount = $subtotal * ($discount->value / 100);
        } else {
            $amount = $discount->value;
        }

        if ($discount->maximum_discount_amount) {
            $amount = min($amount, $discount->maximum_discount_amount);
        }

        return round(min($amount, $subtotal), 0);
    }
}
