@extends('layouts.admin')

@section('title', 'Voucher')

@section('content')
<div class="space-y-6">
<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Voucher</h1>
        <p class="text-sm text-base-content/60">Listing voucher sekarang punya jarak heading, tabel, dan pagination yang setara dengan produk dan diskon.</p>
    </div>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-sm">+ Tambah Voucher</a>
</div>

<div class="card bg-base-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Diskon</th>
                    <th>Terpakai</th>
                    <th>Batas</th>
                    <th>Berlaku s/d</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vouchers as $voucher)
                    <tr>
                        <td class="font-mono font-bold text-sm">{{ $voucher->code }}</td>
                        <td class="text-sm">
                            {{ $voucher->discount?->name ?? '—' }}
                            @if ($voucher->discount)
                                <span class="badge badge-ghost badge-xs ml-1">
                                    {{ $voucher->discount->type === 'percent' ? $voucher->discount->value . '%' : 'Rp ' . number_format($voucher->discount->value, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $voucher->used_count }}</td>
                        <td>{{ $voucher->usage_limit ?? '∞' }}</td>
                        <td class="text-sm text-base-content/60">{{ $voucher->ends_at?->format('d/m/Y') ?? '∞' }}</td>
                        <td>
                            @if ($voucher->is_active)
                                <span class="badge badge-success badge-sm">Aktif</span>
                            @else
                                <span class="badge badge-ghost badge-sm">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1" x-data="{ open: false }">
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-ghost btn-xs">Edit</a>
                                <button @click="open = true" class="btn btn-error btn-outline btn-xs">Hapus</button>

                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-cloak>
                                    <div class="card bg-base-100 w-80 shadow-xl">
                                        <div class="card-body">
                                            <h3 class="card-title text-base">Hapus Voucher?</h3>
                                            <p class="text-sm">Voucher <strong>{{ $voucher->code }}</strong> akan dihapus.</p>
                                            <div class="card-actions justify-end mt-2">
                                                <button @click="open = false" class="btn btn-ghost btn-sm">Batal</button>
                                                <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-error btn-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-base-content/50 py-8">Belum ada voucher.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-0">
        {{ $vouchers->links() }}
    </div>
</div>
</div>
@endsection
