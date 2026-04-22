@extends('layouts.admin')

@section('title', 'Diskon')

@section('content')
<div class="space-y-6">
<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Diskon</h1>
        <p class="text-sm text-base-content/60">Atur promosi dengan tabel yang punya ritme header, isi, dan aksi yang lebih rata.</p>
    </div>
    <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary btn-sm">+ Tambah Diskon</a>
</div>

<div class="card bg-base-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Tipe</th>
                    <th>Nilai</th>
                    <th>Berlaku</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($discounts as $discount)
                    <tr>
                        <td class="font-medium">{{ $discount->name }}</td>
                        <td class="font-mono text-sm">{{ $discount->code ?? '—' }}</td>
                        <td><span class="badge badge-ghost badge-sm capitalize">{{ $discount->type }}</span></td>
                        <td>
                            @if ($discount->type === 'percent')
                                {{ $discount->value }}%
                            @else
                                Rp {{ number_format($discount->value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="text-xs text-base-content/60">
                            @if ($discount->starts_at || $discount->ends_at)
                                {{ $discount->starts_at?->format('d/m/Y') ?? '∞' }}
                                — {{ $discount->ends_at?->format('d/m/Y') ?? '∞' }}
                            @else
                                <span class="text-base-content/40">Tidak terbatas</span>
                            @endif
                        </td>
                        <td>
                            @if ($discount->is_active)
                                <span class="badge badge-success badge-sm">Aktif</span>
                            @else
                                <span class="badge badge-ghost badge-sm">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1" x-data="{ open: false }">
                                <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-ghost btn-xs">Edit</a>
                                <button @click="open = true" class="btn btn-error btn-outline btn-xs">Hapus</button>

                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-cloak>
                                    <div class="card bg-base-100 w-80 shadow-xl">
                                        <div class="card-body">
                                            <h3 class="card-title text-base">Hapus Diskon?</h3>
                                            <p class="text-sm">Diskon <strong>{{ $discount->name }}</strong> akan dihapus.</p>
                                            <div class="card-actions justify-end mt-2">
                                                <button @click="open = false" class="btn btn-ghost btn-sm">Batal</button>
                                                <form method="POST" action="{{ route('admin.discounts.destroy', $discount) }}">
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
                    <tr><td colspan="7" class="text-center text-base-content/50 py-8">Belum ada diskon.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-0">
        {{ $discounts->links() }}
    </div>
</div>
</div>
@endsection
