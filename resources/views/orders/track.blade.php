@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-1">
                <h1 class="text-3xl font-semibold">Order Tracking</h1>
                <p class="text-base-content/70">Customers can track order, fulfillment, and payment status using the public order number route.</p>
            </div>
            <div class="badge badge-outline badge-sm w-fit">{{ $order->order_number }}</div>
        </div>

        @guest
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title">Filter by email</h2>
                    <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                        <input type="email" name="email" value="{{ request('email') }}" class="input input-bordered flex-1" placeholder="customer@example.com">
                        <button class="btn btn-primary">Validate</button>
                    </form>
                </div>
            </div>
        @endguest

        <div class="steps steps-vertical w-full rounded-box border border-base-300 bg-base-100 p-5 sm:p-6 lg:steps-horizontal">
            <div class="step {{ in_array($order->status, ['pending', 'processing', 'completed']) ? 'step-primary' : '' }}">Placed</div>
            <div class="step {{ in_array($order->payment_status, ['paid', 'settlement']) ? 'step-primary' : '' }}">Paid</div>
            <div class="step {{ in_array($order->fulfillment_status, ['processing', 'fulfilled']) ? 'step-primary' : '' }}">Fulfilled</div>
            <div class="step {{ $order->status === 'completed' ? 'step-primary' : '' }}">Completed</div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr,340px]">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title">Order Items</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="font-medium">{{ $item->product_name }}</div>
                                            <div class="text-xs text-base-content/60">{{ $item->product_sku }}</div>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Statuses</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span>Order</span>
                                <span class="badge badge-outline">{{ $order->status }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Payment</span>
                                <span class="badge badge-outline">{{ $order->payment_status }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Fulfillment</span>
                                <span class="badge badge-outline">{{ $order->fulfillment_status }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Payment</h2>
                        <p class="text-sm text-base-content/70">Provider: {{ $order->payment?->provider ?? 'midtrans' }}</p>
                        <p class="text-sm text-base-content/70">Status: {{ $order->payment?->status ?? $order->payment_status }}</p>
                        <div class="divider my-0"></div>
                        <div class="flex items-center justify-between text-base font-semibold">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
