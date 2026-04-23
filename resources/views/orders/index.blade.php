@extends('layouts.account')

@section('account-content')
<div class="space-y-8 w-full">
    <div>
        <h1 class="display-font text-4xl text-base-content font-normal lg:text-5xl">Pesanan Saya</h1>
    </div>

    @if (session('status'))
        <div class="alert alert-success"><span>{{ session('status') }}</span></div>
    @endif

    @if ($orders->isEmpty())
        <div class="card w-full border border-base-300 bg-base-200/70 shadow-sm">
            <div class="card-body min-h-72 items-center justify-center py-10 text-center sm:py-12">
                <p class="mb-4 text-base-content/60">Anda belum memiliki pesanan.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Mulai Belanja</a>
            </div>
        </div>
    @else
        <div class="card w-full border border-base-300 bg-base-100 shadow-sm overflow-hidden">
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
