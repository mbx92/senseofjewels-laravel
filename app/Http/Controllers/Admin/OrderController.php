<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
use App\Support\AdminOrderSerialize;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->with('user')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            })
            ->latest('placed_at')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Order $o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'total' => (float) $o->total,
                'currency' => $o->currency,
                'placed_at' => $o->placed_at?->format('Y-m-d H:i'),
            ]);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'status' => $request->string('status')->toString() ?: null,
                'payment_status' => $request->string('payment_status')->toString() ?: null,
                'search' => $request->string('search')->toString() ?: null,
            ],
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['items.product.images', 'user', 'payment', 'voucher']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => AdminOrderSerialize::forShow($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $oldPaymentStatus = $order->payment_status;

        $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,completed,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'payment_status' => ['nullable', 'in:pending,paid,failed,refunded'],
        ]);

        $update = [
            'status' => $request->status,
            'fulfillment_status' => match ($request->status) {
                'pending' => 'unfulfilled',
                'processing', 'shipped' => 'processing',
                'delivered', 'completed' => 'fulfilled',
                'cancelled' => 'cancelled',
                default => $order->fulfillment_status,
            },
        ];

        if ($request->filled('payment_status')) {
            $update['payment_status'] = $request->payment_status;
            $update['paid_at'] = $request->payment_status === 'paid'
                ? ($order->paid_at ?? Carbon::now())
                : null;
        }

        if ($request->filled('tracking_number')) {
            $shipping = $order->shipping_address ?? [];
            $shipping['tracking'] = $request->tracking_number;
            $update['shipping_address'] = $shipping;
        }

        $order->update($update);

        if ($request->filled('payment_status') && $order->payment) {
            $order->payment->update([
                'status' => $request->payment_status,
                'paid_at' => $request->payment_status === 'paid'
                    ? ($order->payment->paid_at ?? Carbon::now())
                    : null,
            ]);
        }

        if (
            $request->input('payment_status') === 'paid'
            && $oldPaymentStatus !== 'paid'
            && Setting::boolOf('inventory_enabled', true)
        ) {
            $this->orderService->deductStock($order->fresh('items.product'), $request->user()?->id);
        }

        return back()->with('success', "Status order #{$order->order_number} diperbarui ke {$request->status}.");
    }
}
