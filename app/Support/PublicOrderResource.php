<?php

namespace App\Support;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CurrencyService;
use Illuminate\Support\Collection;

final class PublicOrderResource
{
    /**
     * @return array<string, mixed>
     */
    public static function forPaymentPage(Order $order, CurrencyService $currency): array
    {
        $order->loadMissing(['items', 'payment']);

        return [
            'order' => self::orderCore($order, $currency),
            'items' => self::mapItems($order, $currency),
            'routes' => [
                'token' => route('payment.token'),
                'success' => route('payment.success', ['order_id' => $order->order_number]),
                'pending' => route('payment.pending', ['order_id' => $order->order_number]),
                'failed' => route('payment.failed', ['order_id' => $order->order_number]),
            ],
            'midtrans' => [
                'snap_url' => config('midtrans.snap_url'),
                'client_key' => config('midtrans.client_key'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forManualPayment(Order $order, CurrencyService $currency): array
    {
        $order->loadMissing(['items']);

        return [
            'order' => self::orderCore($order, $currency),
            'items' => self::mapItems($order, $currency),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forPaymentStatus(?Order $order, CurrencyService $currency, string $variant): array
    {
        return [
            'variant' => $variant,
            'order' => $order ? self::orderCore($order, $currency) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forOrderShow(Order $order, CurrencyService $currency): array
    {
        $order->loadMissing(['items.product', 'payment', 'voucher']);

        $steps = ['pending', 'processing', 'shipped', 'delivered', 'completed'];
        $currentStep = array_search($order->status, $steps, true);
        if ($currentStep === false) {
            $currentStep = -1;
        }

        return [
            'order' => self::orderDetail($order, $currency),
            'items' => $order->items->map(fn (OrderItem $item) => self::orderItemDetail($item, $currency))->values()->all(),
            'steps' => $steps,
            'current_step_index' => $currentStep,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forTracking(Order $order, CurrencyService $currency, bool $isGuest, ?string $emailQuery): array
    {
        $order->loadMissing(['items', 'payment']);

        $effectivePaymentStatus = $order->payment?->status ?? $order->payment_status;
        $placedDone = $order->status !== 'cancelled';
        $paidDone = in_array($effectivePaymentStatus, ['paid', 'settlement'], true);
        $fulfilledDone = in_array($order->fulfillment_status, ['processing', 'fulfilled'], true)
            || in_array($order->status, ['shipped', 'delivered', 'completed'], true);
        $completedDone = in_array($order->status, ['delivered', 'completed'], true);

        return [
            'order' => self::orderCore($order, $currency),
            'items' => self::mapItems($order, $currency),
            'is_guest' => $isGuest,
            'email_query' => $emailQuery,
            'effective_payment_status' => $effectivePaymentStatus,
            'steps_done' => [
                'placed' => $placedDone,
                'paid' => $paidDone,
                'fulfilled' => $fulfilledDone,
                'completed' => $completedDone,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function forAccountTrackingList(Collection $orders, CurrencyService $currency): array
    {
        return $orders->map(function (Order $order) {
            $fStatus = $order->fulfillment_status ?? $order->status;

            return [
                'order_number' => $order->order_number,
                'placed_at' => $order->placed_at?->format('d M Y'),
                'fulfillment_status' => $fStatus,
                'fulfillment_badge' => self::fulfillmentBadgeClass($fStatus),
            ];
        })->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function orderCore(Order $order, CurrencyService $currency): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'status' => $order->status,
            'fulfillment_status' => $order->fulfillment_status,
            'payment_status' => $order->payment_status,
            'placed_at' => $order->placed_at?->toIso8601String(),
            'placed_at_display' => $order->placed_at?->format('d M Y'),
            'placed_at_long' => $order->placed_at?->format('d M Y, H:i'),
            'total' => (int) $order->total,
            'total_formatted' => $currency->format((int) $order->total),
            'subtotal' => (int) $order->subtotal,
            'subtotal_formatted' => $currency->format((int) $order->subtotal),
            'discount_total' => (int) $order->discount_total,
            'discount_total_formatted' => $currency->format((int) $order->discount_total),
            'shipping_total' => (int) $order->shipping_total,
            'shipping_total_formatted' => $currency->format((int) $order->shipping_total),
            'snap_token' => $order->payment?->snap_token,
            'payment_provider' => $order->payment?->provider,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function tableRow(Order $order, CurrencyService $currency): array
    {
        return [
            'order_number' => $order->order_number,
            'placed_at' => $order->placed_at?->format('d/m/Y'),
            'total_formatted' => $currency->format((int) $order->total),
            'status' => $order->status,
            'status_badge' => self::orderStatusBadgeClass($order->status),
            'payment_status' => $order->payment_status,
            'payment_badge' => self::paymentStatusBadgeClass($order->payment_status),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function orderDetail(Order $order, CurrencyService $currency): array
    {
        $addr = $order->shipping_address ?? [];

        return array_merge(self::orderCore($order, $currency), [
            'shipping_address' => [
                'line_1' => $addr['line_1'] ?? '',
                'city' => $addr['city'] ?? '',
                'province' => $addr['province'] ?? '',
                'postal_code' => $addr['postal_code'] ?? '',
                'country' => $addr['country'] ?? '',
                'tracking' => $addr['tracking'] ?? null,
            ],
            'show_discount' => $order->discount_total > 0,
            'show_shipping' => $order->shipping_total > 0,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private static function orderItemDetail(OrderItem $item, CurrencyService $currency): array
    {
        return [
            'product_name' => $item->product_name,
            'product_sku' => $item->product_sku,
            'quantity' => (int) $item->quantity,
            'unit_price_formatted' => $currency->format((int) $item->unit_price),
            'total_formatted' => $currency->format((int) $item->total),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function mapItems(Order $order, CurrencyService $currency): array
    {
        return $order->items->map(fn (OrderItem $item) => [
            'product_name' => $item->product_name,
            'product_sku' => $item->product_sku,
            'quantity' => (int) $item->quantity,
            'total_formatted' => $currency->format((int) $item->total),
        ])->values()->all();
    }

    private static function orderStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'completed' => 'badge-success',
            'shipped' => 'badge-info',
            'processing' => 'badge-warning',
            'cancelled' => 'badge-error',
            default => 'badge-ghost',
        };
    }

    private static function paymentStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'paid' => 'badge-success',
            'failed' => 'badge-error',
            'refunded' => 'badge-warning',
            default => 'badge-ghost',
        };
    }

    private static function fulfillmentBadgeClass(string $fStatus): string
    {
        return match ($fStatus) {
            'fulfilled', 'shipped', 'completed', 'delivered' => 'badge-success',
            'processing' => 'badge-warning',
            'cancelled' => 'badge-error',
            default => 'badge-ghost',
        };
    }
}
