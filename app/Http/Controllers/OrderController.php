<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CurrencyService;
use App\Support\PublicOrderResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request, CurrencyService $currency): Response
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['items', 'payment'])
            ->latest('placed_at')
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => $orders->through(fn (Order $order) => PublicOrderResource::tableRow($order, $currency)),
        ]);
    }

    public function show(Request $request, string $orderNumber, CurrencyService $currency): Response
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->with(['items.product', 'payment', 'voucher'])
            ->firstOrFail();

        return Inertia::render('Orders/Show', PublicOrderResource::forOrderShow($order, $currency));
    }
}
