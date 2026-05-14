<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CurrencyService;
use App\Support\PublicOrderResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function tracking(Request $request, CurrencyService $currency): Response
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return Inertia::render('Account/Tracking', [
            'orders' => PublicOrderResource::forAccountTrackingList($orders, $currency),
        ]);
    }

    public function reviews(): Response
    {
        return Inertia::render('Account/Reviews');
    }
}
