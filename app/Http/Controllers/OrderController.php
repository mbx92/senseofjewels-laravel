<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['items', 'payment'])
            ->latest('placed_at')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, string $orderNumber): View
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->with(['items.product', 'payment', 'voucher'])
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }
}
