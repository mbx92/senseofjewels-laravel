<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
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

        return Inertia::render('Admin/Dashboard', [
            'today_revenue_formatted' => 'Rp '.number_format((int) $todayRevenue, 0, ',', '.'),
            'today_orders' => $todayOrders,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'total_customers' => $totalCustomers,
            'recent_orders' => $recentOrders->map(fn (Order $order) => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'status' => $order->status,
                'total_formatted' => 'Rp '.number_format((int) $order->total, 0, ',', '.'),
            ])->values()->all(),
        ]);
    }
}
