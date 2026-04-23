<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Request a Snap token for the given order.
     * Returns the snap token string, or throws on failure.
     */
    public function createSnapToken(Order $order): string
    {
        $order->loadMissing(['items', 'payment']);

        $itemDetails = $order->items->map(fn($item) => [
            'id'       => (string) $item->product_id,
            'price'    => (int) $item->unit_price,
            'quantity' => (int) $item->quantity,
            'name'     => mb_substr($item->product_name, 0, 50),
        ])->values()->toArray();

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email'      => $order->customer_email,
                'phone'      => $order->customer_phone ?? '',
            ],
            'callbacks' => [
                'finish' => route('payment.success'),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Persist token to payment record
        if ($order->payment) {
            $order->payment->update(['snap_token' => $snapToken]);
        }

        return $snapToken;
    }

    /**
     * Parse and verify an incoming Midtrans webhook notification.
     * Returns the verified Notification object.
     */
    public function verifyNotification(): Notification
    {
        return new Notification();
    }

    /**
     * Handle the webhook payload: update Order/Payment status, log event.
     */
    public function handleWebhook(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        if (! $orderId) {
            Log::warning('Midtrans webhook: missing order_id', $payload);
            return;
        }

        $order = Order::query()
            ->where('order_number', $orderId)
            ->with('payment')
            ->first();

        if (! $order) {
            Log::warning("Midtrans webhook: order not found [{$orderId}]");
            return;
        }

        $payment = $order->payment ?? Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'midtrans',
            'amount'   => $order->total,
            'currency' => $order->currency ?? 'IDR',
            'status'   => 'pending',
        ]);

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';
        $paymentType       = $payload['payment_type'] ?? null;

        // Verify signature
        $serverKey   = config('midtrans.server_key');
        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $expected    = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (! hash_equals($expected, $payload['signature_key'] ?? '')) {
            Log::warning('Midtrans webhook: invalid signature', ['order' => $orderId]);
            return;
        }

        [$orderStatus, $paymentStatus, $paidAt] = $this->resolveStatuses(
            $transactionStatus,
            $fraudStatus,
        );

        $payment->update(array_filter([
            'status'       => $paymentStatus,
            'payment_type' => $paymentType,
            'payload'      => $payload,
            'paid_at'      => $paidAt,
        ], fn($v) => $v !== null));

        $orderUpdate = ['payment_status' => $paymentStatus];
        if ($orderStatus) {
            $orderUpdate['status'] = $orderStatus;
        }
        if ($paidAt) {
            $orderUpdate['paid_at'] = $paidAt;
        }
        $order->update($orderUpdate);

        // Log the event
        PaymentLog::query()->create([
            'payment_id' => $payment->id,
            'event'      => $transactionStatus,
            'status'     => $paymentStatus,
            'payload'    => $payload,
            'logged_at'  => now(),
        ]);

        // Deduct stock on first successful payment
        if ($paymentStatus === 'paid' && $order->fulfillment_status === 'unfulfilled') {
            app(OrderService::class)->deductStock($order);
        }
    }

    // ---------------------------------------------------------------

    private function resolveStatuses(string $transactionStatus, string $fraudStatus): array
    {
        $paidAt = null;

        match ($transactionStatus) {
            'capture' => $fraudStatus === 'accept'
                ? [$orderStatus, $paymentStatus, $paidAt] = ['processing', 'paid', now()]
                : [$orderStatus, $paymentStatus] = [null, 'fraud_challenge'],
            'settlement' => [$orderStatus, $paymentStatus, $paidAt] = ['processing', 'paid', now()],
            'pending'    => [$orderStatus, $paymentStatus] = [null, 'pending'],
            'deny'       => [$orderStatus, $paymentStatus] = ['cancelled', 'failed'],
            'expire'     => [$orderStatus, $paymentStatus] = ['cancelled', 'expired'],
            'cancel'     => [$orderStatus, $paymentStatus] = ['cancelled', 'cancelled'],
            default      => [$orderStatus, $paymentStatus] = [null, $transactionStatus],
        };

        return [$orderStatus ?? null, $paymentStatus, $paidAt];
    }
}
