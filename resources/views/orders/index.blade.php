@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="space-y-1">
        <h1 class="text-3xl font-bold">Pesanan Saya</h1>
        <p class="text-sm text-base-content/60">Daftar transaksi Anda tampil dengan ritme yang sama seperti halaman shop dan checkout.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success"><span>{{ session('status') }}</span></div>
    @endif

    @if ($orders->isEmpty())
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body py-14 text-center sm:py-16">
                <p class="mb-4 text-base-content/60">Anda belum memiliki pesanan.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Mulai Belanja</a>
            </div>
        </div>
    @else
        <div class="card bg-base-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="font-mono font-semibold text-sm">{{ $order->order_number }}</td>
                                <td class="text-sm">{{ $order->placed_at?->format('d/m/Y') }}</td>
                                <td class="font-semibold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusColor = match ($order->status) {
                                            'completed'  => 'badge-success',
                                            'shipped'    => 'badge-info',
                                            'processing' => 'badge-warning',
                                            'cancelled'  => 'badge-error',
                                            default      => 'badge-ghost',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusColor }} badge-sm capitalize">{{ $order->status }}</span>
                                </td>
                                <td>
                                    @php
                                        $payColor = match ($order->payment_status) {
                                            'paid'    => 'badge-success',
                                            'failed'  => 'badge-error',
                                            'refunded'=> 'badge-warning',
                                            default   => 'badge-ghost',
                                        };
                                    @endphp
                                    <span class="badge {{ $payColor }} badge-sm capitalize">{{ $order->payment_status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order->order_number) }}"
                                        class="btn btn-ghost btn-xs">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
