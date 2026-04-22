<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
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
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'user', 'payment', 'voucher']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status'          => ['required', 'in:pending,processing,shipped,delivered,completed,cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $update = ['status' => $request->status];

        if ($request->filled('tracking_number')) {
            $shipping             = $order->shipping_address ?? [];
            $shipping['tracking'] = $request->tracking_number;
            $update['shipping_address'] = $shipping;
        }

        $order->update($update);

        return back()->with('success', "Status order #{$order->order_number} diperbarui ke {$request->status}.");
    }
}
