<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'pages' => Page::query()->count(),
                'services' => Service::query()->count(),
                'products' => Product::query()->count(),
                'orders' => Order::query()->count(),
                'pending_orders' => Order::query()->where('status', 'pending')->count(),
            ],
            'recentOrders' => Order::query()->latest()->take(5)->get(),
        ]);
    }
}
