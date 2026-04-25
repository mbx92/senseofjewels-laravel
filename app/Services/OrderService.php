<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(protected DiscountService $discountService) {}

    /**
     * Create an order from cart items.
     *
     * @param array $address  ['line_1', 'city', 'province', 'postal_code', 'country']
     */
    public function create(
        ?int $userId,
        Cart $cart,
        array $customerData,
        array $address,
        ?int $voucherId = null,
    ): Order {
        $cart->load('items.product');

        ['subtotal' => $subtotal, 'discount_total' => $discountTotal, 'total' => $total]
            = $this->calculateTotals($cart->items, $voucherId);

        $order = Order::query()->create([
            'user_id'            => $userId,
            'voucher_id'         => $voucherId,
            'order_number'       => $this->generateCode(),
            'status'             => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'payment_status'     => 'pending',
            'customer_name'      => $customerData['name'],
            'customer_email'     => $customerData['email'],
            'customer_phone'     => $customerData['phone'] ?? null,
            'shipping_address'   => $address,
            'billing_address'    => $address,
            'notes'              => $customerData['notes'] ?? null,
            'subtotal'           => $subtotal,
            'discount_total'     => $discountTotal,
            'shipping_total'     => 0,
            'tax_total'          => 0,
            'total'              => $total,
            'currency'           => 'IDR',
            'placed_at'          => now(),
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id'     => $item->product_id,
                'product_name'   => $item->product_name,
                'product_sku'    => $item->product_sku,
                'quantity'       => $item->quantity,
                'unit_price'     => $item->unit_price,
                'discount_total' => 0,
                'tax_total'      => 0,
                'total'          => $item->line_total,
            ]);
        }

        // Record voucher usage
        if ($voucherId && $discountTotal > 0) {
            $voucher = Voucher::find($voucherId);
            if ($voucher) {
                VoucherUsage::query()->create([
                    'voucher_id' => $voucher->id,
                    'order_id'   => $order->id,
                    'user_id'    => $userId,
                    'amount'     => $discountTotal,
                    'used_at'    => now(),
                ]);
                $voucher->increment('used_count');
            }
        }

        return $order;
    }

    /** Format: ORD-YYYYMMDD-XXXXX */
    public function generateCode(): string
    {
        do {
            $code = 'ORD-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Order::query()->where('order_number', $code)->exists());

        return $code;
    }

    /**
     * Calculate subtotal, discount_total and total for a collection of cart items.
     */
    public function calculateTotals(Collection $items, ?int $voucherId = null): array
    {
        $subtotal = $items->sum('line_total');
        $discountTotal = 0;

        if ($voucherId) {
            $voucher = Voucher::with('discount')->find($voucherId);
            if ($voucher) {
                $discountTotal = $this->discountService->applyVoucher($voucher, $subtotal);
            }
        }

        return [
            'subtotal'       => $subtotal,
            'discount_total' => $discountTotal,
            'total'          => max(0, $subtotal - $discountTotal),
        ];
    }

    /**
     * Deduct stock and write inventory logs after payment confirmed.
     */
    public function deductStock(Order $order, ?int $adminUserId = null): void
    {
        if (! Setting::boolOf('inventory_enabled', true)) {
            return;
        }

        foreach ($order->items()->with('product')->get() as $item) {
            if (! $item->product) {
                continue;
            }

            $alreadyDeducted = InventoryLog::query()
                ->where('order_item_id', $item->id)
                ->where('type', 'out')
                ->exists();
            if ($alreadyDeducted) {
                continue;
            }

            $before = $item->product->stock;
            $after  = max(0, $before - $item->quantity);

            $item->product->update(['stock' => $after]);

            InventoryLog::query()->create([
                'product_id'   => $item->product_id,
                'user_id'      => $adminUserId,
                'order_item_id' => $item->id,
                'type'         => 'out',
                'quantity'     => $item->quantity,
                'stock_before' => $before,
                'stock_after'  => $after,
                'note'         => "Order #{$order->order_number}",
            ]);
        }
    }
}
