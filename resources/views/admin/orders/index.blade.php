@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('content')
<div class="space-y-6">
<div class="space-y-1">
    <h1 class="text-2xl font-bold">Pesanan</h1>
    <p class="text-sm text-base-content/60">Filter, status, dan total sekarang punya jarak yang konsisten dengan halaman admin commerce lainnya.</p>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Pesanan / Nama / Email"
        class="input input-bordered input-sm w-56" />

    <select name="status" class="select select-bordered select-sm">
        <option value="">Semua Status</option>
        @foreach (['pending','processing','shipped','delivered','completed','cancelled'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>

    <select name="payment_status" class="select select-bordered select-sm">
        <option value="">Semua Pembayaran</option>
        @foreach (['pending','paid','failed','refunded'] as $p)
            <option value="{{ $p }}" @selected(request('payment_status') === $p)>{{ ucfirst($p) }}</option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    @if (request()->hasAny(['search','status','payment_status']))
        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">Reset</a>
    @endif
</form>

<div class="card bg-base-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="table table-zebra table-sm">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th class="text-right">Total</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="font-mono font-semibold text-xs">{{ $order->order_number }}</td>
                        <td>
                            <div class="text-sm font-medium">{{ $order->customer_name }}</div>
                            <div class="text-xs text-base-content/50">{{ $order->customer_email }}</div>
                        </td>
                        <td class="text-xs text-base-content/60">{{ $order->placed_at?->format('d/m/Y H:i') }}</td>
                        <td class="text-right font-semibold text-sm">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            @php $statusColor = match ($order->status) {
                                'completed'  => 'badge-success',
                                'shipped'    => 'badge-info',
                                'processing' => 'badge-warning',
                                'cancelled'  => 'badge-error',
                                default      => 'badge-ghost',
                            }; @endphp
                            <span class="badge {{ $statusColor }} badge-sm capitalize">{{ $order->status }}</span>
                        </td>
                        <td>
                            @php $payColor = match ($order->payment_status) {
                                'paid'     => 'badge-success',
                                'failed'   => 'badge-error',
                                'refunded' => 'badge-warning',
                                default    => 'badge-ghost',
                            }; @endphp
                            <span class="badge {{ $payColor }} badge-sm capitalize">{{ $order->payment_status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-base-content/50 py-8">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">
        {{ $orders->links() }}
    </div>
</div>
</div>
@endsection
