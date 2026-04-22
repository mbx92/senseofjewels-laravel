<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View
    {
        $cart = Cart::query()
            ->with('items.product')
            ->where('session_id', $request->session()->getId())
            ->first();

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'shipping_address.line_1' => ['required', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:255'],
            'shipping_address.province' => ['required', 'string', 'max:255'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $cart = Cart::query()
            ->with('items.product')
            ->where('session_id', $request->session()->getId())
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $order = Order::query()->create([
            'user_id' => $request->user()?->id,
            'order_number' => 'SOJ-'.Str::upper(Str::random(10)),
            'status' => 'pending',
            'fulfillment_status' => 'unfulfilled',
            'payment_status' => 'pending',
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['shipping_address'],
            'notes' => $validated['notes'] ?? null,
            'subtotal' => $cart->subtotal,
            'discount_total' => $cart->discount_total,
            'shipping_total' => 0,
            'tax_total' => 0,
            'total' => $cart->total,
            'currency' => $cart->currency,
            'placed_at' => now(),
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_total' => 0,
                'tax_total' => 0,
                'total' => $item->line_total,
            ]);
        }

        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'midtrans',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
            'payload' => [
                'integration_status' => 'placeholder',
                'message' => 'Midtrans Snap integration will be connected in the payment module phase.',
            ],
        ]);

        $cart->items()->delete();
        $cart->update([
            'subtotal' => 0,
            'discount_total' => 0,
            'total' => 0,
        ]);

        return redirect()->route('orders.track', $order->order_number)->with('status', 'Order created successfully.');
    }
}
