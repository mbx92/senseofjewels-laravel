@extends('layouts.admin')

@section('content')
    <div class="space-y-8">

        {{-- Page heading --}}
        <div class="flex flex-col gap-1">
            <p class="text-[10px] uppercase tracking-[0.25em] text-primary">Admin Panel</p>
            <h1 class="display-font text-4xl text-base-content font-normal">Dashboard</h1>
            <p class="text-sm text-base-content/50 font-light">Snapshot of your store's content, catalog, and orders.</p>
        </div>

        {{-- Stat cards --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="bg-base-100 border border-base-300 px-6 py-5">
                <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Pendapatan Hari Ini</p>
                <p class="display-font text-4xl text-primary leading-none mb-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                <p class="text-[11px] text-base-content/40">{{ $todayOrders }} transaksi hari ini</p>
            </div>
            <div class="bg-base-100 border border-base-300 px-6 py-5">
                <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Total Orders</p>
                <p class="display-font text-5xl text-base-content leading-none mb-2">{{ $totalOrders }}</p>
                <p class="text-[11px] text-warning">{{ $pendingOrders }} pending payment</p>
            </div>
            <div class="bg-base-100 border border-base-300 px-6 py-5">
                <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Total Produk</p>
                <p class="display-font text-5xl text-secondary leading-none mb-2">{{ $totalProducts }}</p>
                @if($lowStockCount > 0)
                    <p class="text-[11px] text-error">⚠ {{ $lowStockCount }} stok menipis</p>
                @else
                    <p class="text-[11px] text-base-content/40">Stok aman</p>
                @endif
            </div>
            <div class="bg-base-100 border border-base-300 px-6 py-5">
                <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Total Customers</p>
                <p class="display-font text-5xl text-base-content leading-none mb-2">{{ $totalCustomers }}</p>
                <p class="text-[11px] text-base-content/40">Registered accounts</p>
            </div>
        </div>

        {{-- Bottom row --}}
        <div class="grid gap-6 xl:grid-cols-[2fr,1fr]">

            {{-- Recent Orders --}}
            <div class="bg-base-100 border border-base-300">
                <div class="flex items-center justify-between px-6 py-4 border-b border-base-200">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/60">Recent Orders</p>
                    <a href="{{ route('admin.orders.index') }}" class="text-[10px] uppercase tracking-widest text-primary hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-base-200">
                                <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Order</th>
                                <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Customer</th>
                                <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Status</th>
                                <th class="text-right px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr class="border-b border-base-200 last:border-0 hover:bg-base-200/50 transition-colors">
                                    <td class="px-6 py-3.5 text-xs font-mono text-base-content/70">{{ $order->order_number }}</td>
                                    <td class="px-6 py-3.5 text-xs">{{ $order->customer_name }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="text-[9px] uppercase tracking-widest border border-base-content/20 px-2.5 py-1 text-base-content/60">{{ $order->status }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-base-content/40">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Build progress --}}
            <div class="bg-base-100 border border-base-300">
                <div class="px-6 py-4 border-b border-base-200">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/60">Build Progress</p>
                </div>
                <ul class="px-6 py-5 space-y-4">
                    @php
                        $steps = [
                            ['done' => true,  'label' => 'Foundation routes & layouts'],
                            ['done' => true,  'label' => 'Database models & migrations'],
                            ['done' => false, 'label' => 'CMS CRUD screens'],
                            ['done' => false, 'label' => 'Midtrans Snap checkout'],
                        ];
                    @endphp
                    @foreach($steps as $step)
                    <li class="flex items-center gap-3">
                        <span class="w-4 h-4 shrink-0 flex items-center justify-center rounded-full {{ $step['done'] ? 'bg-primary' : 'border border-base-content/20' }}">
                            @if($step['done'])
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-primary-content" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </span>
                        <span class="text-xs {{ $step['done'] ? 'text-base-content/70' : 'text-base-content/40' }}">{{ $step['label'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
@endsection
