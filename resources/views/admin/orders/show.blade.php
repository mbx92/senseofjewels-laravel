@extends('layouts.admin')

@section('title', 'Pesanan #' . $order->order_number)

@section('content')
<div class="space-y-6">
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        <div class="space-y-1">
            <h1 class="text-2xl font-bold">Pesanan #{{ $order->order_number }}</h1>
            <p class="text-sm text-base-content/60">Detail order ditata ulang agar status, alamat, dan item lebih mudah dipindai.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="badge badge-outline badge-sm w-fit">{{ $order->placed_at?->format('d M Y') }}</span>
        <a href="{{ route('admin.orders.invoice', $order->order_number) }}"
           target="_blank"
           class="btn btn-sm btn-outline gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3M3 17v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2M7 10V7a5 5 0 0 1 10 0v3"/>
            </svg>
            Download Invoice
        </a>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">

    {{-- Update Status --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base">Perbarui Status</h2>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="space-y-4">
                @csrf @method('PATCH')
                <div class="space-y-1.5">
                    <label for="order_status" class="block text-sm font-medium text-base-content/70">Status Pesanan</label>
                    <select id="order_status" name="status" class="select select-bordered select-sm w-full" required>
                        @foreach (['pending','processing','shipped','delivered','completed','cancelled'] as $s)
                            <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="payment_status" class="block text-sm font-medium text-base-content/70">Status Pembayaran</label>
                    <select id="payment_status" name="payment_status" class="select select-bordered select-sm w-full">
                        @foreach (['pending','paid','failed','refunded'] as $p)
                            <option value="{{ $p }}" @selected($order->payment_status === $p)>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-base-content/50">Provider: {{ strtoupper($order->payment?->provider ?? 'manual') }}</p>
                </div>
                <div class="space-y-1.5">
                    <label for="tracking_number" class="block text-sm font-medium text-base-content/70">No. Resi (opsional)</label>
                    <input type="text" name="tracking_number"
                        id="tracking_number"
                        value="{{ $order->shipping_address['tracking'] ?? '' }}"
                        placeholder="JNE123456789"
                        class="input input-bordered input-sm w-full" />
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-full">Simpan</button>
            </form>
        </div>
    </div>

    {{-- Customer Info --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base">Pelanggan</h2>
            <div class="text-sm space-y-1">
                <p class="font-medium">{{ $order->customer_name }}</p>
                <p class="text-base-content/60">{{ $order->customer_email }}</p>
                @if ($order->customer_phone)
                    <p class="text-base-content/60">{{ $order->customer_phone }}</p>
                @endif
                @if ($order->user)
                    <p class="mt-1 text-xs text-base-content/40">User ID: #{{ $order->user->id }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Shipping --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base">Alamat Kirim</h2>
            @if ($order->shipping_address)
                <div class="space-y-1 text-sm">
                    <p>{{ $order->shipping_address['line_1'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['province'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['postal_code'] ?? '' }}, {{ $order->shipping_address['country'] ?? '' }}</p>
                    @if (!empty($order->shipping_address['tracking']))
                        <p class="mt-2 rounded bg-base-200 px-2 py-1 font-mono text-xs">
                            Resi: {{ $order->shipping_address['tracking'] }}
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

@if ($order->notes)
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base">Catatan</h2>
            <p class="text-sm text-base-content/70">{{ $order->notes }}</p>
        </div>
    </div>
@endif
</div>
@endsection
