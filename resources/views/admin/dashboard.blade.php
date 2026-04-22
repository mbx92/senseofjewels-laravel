@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold">Dashboard</h1>
                <p class="text-base-content/70">A quick snapshot of content, catalog, and sales activity.</p>
            </div>
            <div class="badge badge-primary badge-lg badge-outline">Corporate Theme</div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
                <div class="stat-title">Users</div>
                <div class="stat-value text-primary">{{ $stats['users'] }}</div>
                <div class="stat-desc">Registered customer and admin accounts</div>
            </div>
            <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
                <div class="stat-title">Pages</div>
                <div class="stat-value">{{ $stats['pages'] }}</div>
                <div class="stat-desc">CMS-managed landing pages</div>
            </div>
            <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
                <div class="stat-title">Services</div>
                <div class="stat-value">{{ $stats['services'] }}</div>
                <div class="stat-desc">Company profile service items</div>
            </div>
            <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
                <div class="stat-title">Products</div>
                <div class="stat-value text-secondary">{{ $stats['products'] }}</div>
                <div class="stat-desc">Catalog items ready for sale</div>
            </div>
            <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
                <div class="stat-title">Orders</div>
                <div class="stat-value text-accent">{{ $stats['orders'] }}</div>
                <div class="stat-desc">All orders stored in the system</div>
            </div>
            <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
                <div class="stat-title">Pending Orders</div>
                <div class="stat-value text-warning">{{ $stats['pending_orders'] }}</div>
                <div class="stat-desc">Awaiting payment or fulfillment</div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[2fr,1fr]">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <h2 class="card-title">Recent Orders</h2>
                        <div class="badge badge-outline">Scaffolded</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td><span class="badge badge-outline">{{ $order->status }}</span></td>
                                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-base-content/60">No orders yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Next Build Steps</h2>
                    <ul class="steps steps-vertical">
                        <li class="step step-primary">Foundation routes and layouts</li>
                        <li class="step step-primary">Database models and migrations</li>
                        <li class="step">CMS CRUD screens</li>
                        <li class="step">Midtrans Snap checkout</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
