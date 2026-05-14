<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CurrencyService;
use App\Support\PublicOrderResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderTrackingController extends Controller
{
    public function show(Request $request, string $orderNumber, CurrencyService $currency): Response
    {
        $order = Order::query()
            ->with(['items.product', 'payment'])
            ->where('order_number', $orderNumber)
            ->when(
                $request->filled('email') && ! $request->user(),
                fn ($query) => $query->where('customer_email', $request->string('email')->toString())
            )
            ->firstOrFail();

        return Inertia::render(
            'Orders/Track',
            PublicOrderResource::forTracking(
                $order,
                $currency,
                ! $request->user(),
                $request->get('email'),
            )
        );
    }
}
