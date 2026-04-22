<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function show(Request $request, string $orderNumber): View
    {
        $order = Order::query()
            ->with(['items.product', 'payment'])
            ->where('order_number', $orderNumber)
            ->when(
                $request->filled('email') && ! $request->user(),
                fn ($query) => $query->where('customer_email', $request->string('email')->toString())
            )
            ->firstOrFail();

        return view('orders.track', compact('order'));
    }
}
