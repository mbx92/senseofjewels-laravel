<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todayRevenue = Order::query()
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');

        $todayOrders = Order::query()
            ->whereDate('created_at', today())
            ->count();

        $totalOrders = Order::query()->count();

        $pendingOrders = Order::query()
            ->where('payment_status', 'pending')
            ->count();

        $totalProducts = Product::query()->count();

        $lowStockCount = Product::query()
            ->whereColumn('stock', '<=', 'min_stock_alert')
            ->where('is_active', true)
            ->count();

        $totalCustomers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'customer'))
            ->count();

        $recentOrders = Order::query()->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'todayRevenue',
            'todayOrders',
            'totalOrders',
            'pendingOrders',
            'totalProducts',
            'lowStockCount',
            'totalCustomers',
            'recentOrders',
        ));
    }
}
