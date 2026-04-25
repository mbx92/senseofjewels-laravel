@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="space-y-1">
        <p class="text-xs uppercase tracking-[0.25em] text-primary">Order #{{ $order->order_number }}</p>
        <h1 class="display-font text-4xl text-base-content">Pembayaran Manual</h1>
        <p class="text-sm text-base-content/55">Midtrans saat ini nonaktif. Pesanan tetap tersimpan dengan status pembayaran pending.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr,340px] items-start">
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body gap-5">
                <div class="alert alert-info text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"/>
                    </svg>
                    <span>
                        Tim admin akan memverifikasi pembayaran secara manual. Anda bisa mengirim bukti pembayaran melalui WhatsApp store.
                    </span>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Pemesan</span>
                        <span class="text-base-content">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Email</span>
                        <span class="text-base-content">{{ $order->customer_email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base-content/60">Status</span>
                        <span class="badge badge-warning badge-sm">pending</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-primary">
                        Lihat Detail Order
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline">
                        Kembali ke Pesanan
                    </a>
                </div>
            </div>
        </div>

        <div class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h3 class="font-semibold text-sm uppercase tracking-widest text-base-content/50">Ringkasan</h3>
                <ul class="space-y-3 divide-y divide-base-200">
                    @foreach($order->items as $item)
                    <li class="flex items-start justify-between gap-3 pt-3 first:pt-0 text-sm">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-base-content truncate">{{ $item->product_name }}</p>
                            <p class="text-xs text-base-content/45 mt-0.5">Qty {{ $item->quantity }}</p>
                        </div>
                        <span class="text-base-content/80 whitespace-nowrap">@money($item->total)</span>
                    </li>
                    @endforeach
                </ul>

                <div class="pt-3 border-t border-base-200 text-sm space-y-1.5">
                    @if($order->discount_total > 0)
                    <div class="flex justify-between text-success">
                        <span>Diskon</span>
                        <span>- @money($order->discount_total)</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold">
                        <span>Total</span>
                        <span>@money($order->total)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
