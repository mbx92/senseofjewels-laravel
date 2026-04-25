@extends('layouts.account')

@section('account-content')
    <div class="space-y-6">
        @php
            $effectivePaymentStatus = $order->payment?->status ?? $order->payment_status;
            $placedDone = $order->status !== 'cancelled';
            $paidDone = in_array($effectivePaymentStatus, ['paid', 'settlement'], true);
            $fulfilledDone = in_array($order->fulfillment_status, ['processing', 'fulfilled'], true)
                || in_array($order->status, ['shipped', 'delivered', 'completed'], true);
            $completedDone = in_array($order->status, ['delivered', 'completed'], true);
        @endphp
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-1">
                <h1 class="text-3xl font-semibold">{{ __('Order Tracking') }}</h1>
                <p class="text-base-content/70">{{ __('Customers can track order, fulfillment, and payment status using the public order number route.') }}</p>
            </div>
            <div class="badge badge-outline badge-sm w-fit">{{ $order->order_number }}</div>
        </div>

        @guest
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title">{{ __('Filter by email') }}</h2>
                    <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                        <input type="email" name="email" value="{{ request('email') }}" class="input input-bordered flex-1" placeholder="customer@example.com">
                        <button class="btn btn-primary">{{ __('Validate') }}</button>
                    </form>
                </div>
            </div>
        @endguest

        <div class="steps steps-vertical w-full rounded-box border border-base-300 bg-base-100 p-5 sm:p-6 lg:steps-horizontal">
            <div class="step {{ $placedDone ? 'step-primary' : '' }}">{{ __('Placed') }}</div>
            <div class="step {{ $paidDone ? 'step-primary' : '' }}">{{ __('Paid') }}</div>
            <div class="step {{ $fulfilledDone ? 'step-primary' : '' }}">{{ __('Fulfilled') }}</div>
            <div class="step {{ $completedDone ? 'step-primary' : '' }}">{{ __('Completed') }}</div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr,340px]">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title">{{ __('Order Items') }}</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
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
                                        <td>@money($item->total)</td>
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
                        <h2 class="card-title">{{ __('Statuses') }}</h2>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span>{{ __('Order') }}</span>
                                <span class="badge badge-outline">{{ $order->status }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ __('Payment') }}</span>
                                <span class="badge badge-outline">{{ $effectivePaymentStatus }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ __('Fulfillment') }}</span>
                                <span class="badge badge-outline">{{ $order->fulfillment_status }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">{{ __('Payment') }}</h2>
                        <p class="text-sm text-base-content/70">{{ __('Provider') }}: {{ $order->payment?->provider ?? 'midtrans' }}</p>
                        <p class="text-sm text-base-content/70">{{ __('Status') }}: {{ $effectivePaymentStatus }}</p>
                        <div class="divider my-0"></div>
                        <div class="flex items-center justify-between text-base font-semibold">
                            <span>Total</span>
                            <span>@money($order->total)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
