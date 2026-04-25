@extends('layouts.account')

@section('account-content')
<div class="space-y-8">

    <div class="space-y-1">
        <a href="{{ route('orders.index') }}" class="text-[10px] uppercase tracking-[0.18em] font-semibold text-base-content/40 hover:text-primary transition-colors">← {{ __('Back to Orders') }}</a>
        <div class="flex items-end justify-between gap-4 flex-wrap">
            <h1 class="display-font text-3xl text-base-content font-normal">{{ $order->order_number }}</h1>
            <span class="text-xs text-base-content/50 pb-1">{{ $order->placed_at?->format('d M Y') }}</span>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success"><span>{{ session('status') }}</span></div>
    @endif

    {{-- Status Steps --}}
    @php
        $steps = ['pending','processing','shipped','delivered','completed'];
        $currentStep = array_search($order->status, $steps);
    @endphp
    @if ($order->status !== 'cancelled')
    <div class="card overflow-x-auto bg-base-100 shadow-sm">
        <div class="card-body">
            <ul class="steps steps-horizontal w-full">
                @foreach ($steps as $i => $step)
                    <li class="step {{ $i <= $currentStep ? 'step-primary' : '' }} capitalize text-sm">{{ $step }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @else
        <div class="alert alert-error"><span>Pesanan ini telah dibatalkan.</span></div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">

        {{-- Order Info --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-base">{{ __('Order Information') }}</h2>
                <div class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <span class="text-base-content/60">{{ __('Date') }}</span>
                        <span>{{ $order->placed_at?->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Status</span>
                        <span class="badge badge-sm capitalize">{{ $order->status }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base-content/60">{{ __('Payment') }}</span>
                        <span class="badge badge-sm capitalize">{{ $order->payment_status }}</span>
                    </div>
                    @if ($order->payment)
                    <div class="flex justify-between">
                        <span class="text-base-content/60">{{ __('Method') }}</span>
                        <span class="capitalize">{{ $order->payment->provider }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Shipping Address --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-base">{{ __('Shipping Address') }}</h2>
                @if ($order->shipping_address)
                    <div class="space-y-1 text-sm">
                        <p class="font-medium">{{ $order->customer_name }}</p>
                        <p>{{ $order->shipping_address['line_1'] ?? '' }}</p>
                        <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['province'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                        <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                        @if (!empty($order->shipping_address['tracking']))
                            <p class="mt-2 rounded bg-base-200 px-2 py-1 font-mono text-xs">
                                Tracking: {{ $order->shipping_address['tracking'] }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base">{{ __('Order Items') }}</h2>
            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">{{ __('Price') }}</th>
                            <th class="text-right">{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $item->product_name }}</div>
                                    <div class="text-xs text-base-content/50">{{ $item->product_sku }}</div>
                                </td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">@money($item->unit_price)</td>
                                <td class="text-right font-semibold">@money($item->total)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="flex justify-end pt-4">
                <div class="text-sm space-y-1 min-w-48">
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Subtotal</span>
                        <span>@money($order->subtotal)</span>
                    </div>
                    @if ($order->discount_total > 0)
                        <div class="flex justify-between text-success">
                            <span>{{ __('Discount') }}</span>
                            <span>- @money($order->discount_total)</span>
                        </div>
                    @endif
                    @if ($order->shipping_total > 0)
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Shipping') }}</span>
                            <span>@money($order->shipping_total)</span>
                        </div>
                    @endif
                    <div class="mt-1 flex justify-between border-t border-base-300 pt-1 text-base font-bold">
                        <span>Total</span>
                        <span>@money($order->total)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
