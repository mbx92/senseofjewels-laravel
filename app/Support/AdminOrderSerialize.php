<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

final class AdminOrderSerialize
{
    /**
     * @return array<string, mixed>
     */
    public static function forShow(Order $order): array
    {
        $order->loadMissing(['items.product', 'user', 'payment', 'voucher']);

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'placed_at' => $order->placed_at?->format('d M Y'),
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'user_id' => $order->user_id,
            'shipping_address' => $order->shipping_address ?? [],
            'subtotal' => (float) $order->subtotal,
            'discount_total' => (float) $order->discount_total,
            'shipping_total' => (float) $order->shipping_total,
            'tax_total' => (float) $order->tax_total,
            'total' => (float) $order->total,
            'currency' => $order->currency,
            'notes' => $order->notes,
            'payment' => $order->payment ? [
                'provider' => $order->payment->provider,
                'status' => $order->payment->status,
            ] : null,
            'voucher' => $order->voucher ? [
                'code' => $order->voucher->code,
            ] : null,
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total' => (float) $item->total,
                'image_url' => (function () use ($item) {
                    $img = $item->product?->images?->sortByDesc('is_primary')->first()
                        ?? $item->product?->images?->first();

                    return $img ? Storage::url($img->path) : null;
                })(),
            ])->values()->all(),
        ];
    }
}
