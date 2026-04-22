@extends('layouts.app')

@section('title', 'Detail Pesanan – ' . $order->order_number)

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            <h1 class="text-2xl font-bold">Pesanan #{{ $order->order_number }}</h1>
        </div>
        <span class="badge badge-outline badge-sm w-fit">{{ $order->placed_at?->format('d M Y') }}</span>
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
                <h2 class="card-title text-base">Informasi Pesanan</h2>
                <div class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Tanggal</span>
                        <span>{{ $order->placed_at?->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Status</span>
                        <span class="badge badge-sm capitalize">{{ $order->status }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Pembayaran</span>
                        <span class="badge badge-sm capitalize">{{ $order->payment_status }}</span>
                    </div>
                    @if ($order->payment)
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Metode</span>
                        <span class="capitalize">{{ $order->payment->provider }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Shipping Address --}}
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-base">Alamat Pengiriman</h2>
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
            <h2 class="card-title text-base">Item Pesanan</h2>
            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Subtotal</th>
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
                                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-right font-semibold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
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
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($order->discount_total > 0)
                        <div class="flex justify-between text-success">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($order->shipping_total > 0)
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Ongkir</span>
                            <span>Rp {{ number_format($order->shipping_total, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="mt-1 flex justify-between border-t border-base-300 pt-1 text-base font-bold">
                        <span>Total</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
