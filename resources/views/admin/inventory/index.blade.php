@extends('layouts.admin')

@section('title', 'Inventaris')

@section('content')
<x-admin.feature-disabled-wrapper
    :disabled="!$inventoryEnabled"
    title="Fitur ini tidak aktif"
    description="Silakan aktifkan di halaman Settings > Commerce"
>
<div class="space-y-6">
<div class="space-y-1">
    <h1 class="text-2xl font-bold">Inventaris</h1>
    <p class="text-sm text-base-content/60">Satu layar untuk penyesuaian stok dan audit log, dengan jarak antar blok yang lebih konsisten.</p>
</div>
    {{-- Adjustment Form --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-base">Penyesuaian Stok</h2>
            <form method="POST" action="{{ route('admin.inventory.adjust') }}" class="grid items-end gap-4 pt-2 sm:grid-cols-2 xl:grid-cols-4">
                @csrf

                <div class="form-control">
                    <label class="label"><span class="label-text">Produk <span class="text-error">*</span></span></label>
                    <select name="product_id" class="select select-bordered select-sm @error('product_id') select-error @enderror" required>
                        <option value="">— Pilih Produk —</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                {{ $product->name }} (Stok: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<span class="label-text-alt text-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Tipe <span class="text-error">*</span></span></label>
                    <select name="type" class="select select-bordered select-sm" required>
                        <option value="in" @selected(old('type') === 'in')>Masuk (+)</option>
                        <option value="out" @selected(old('type') === 'out')>Keluar (-)</option>
                        <option value="adjustment" @selected(old('type') === 'adjustment')>Set Langsung</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Jumlah <span class="text-error">*</span></span></label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="input input-bordered input-sm @error('quantity') input-error @enderror" required />
                    @error('quantity')<span class="label-text-alt text-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Catatan <span class="text-error">*</span></span></label>
                    <input type="text" name="note" value="{{ old('note') }}" placeholder="Alasan penyesuaian" class="input input-bordered input-sm @error('note') input-error @enderror" required />
                    @error('note')<span class="label-text-alt text-error">{{ $message }}</span>@enderror
                </div>

                <div class="flex justify-end sm:col-span-2 xl:col-span-4">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Penyesuaian</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body pb-0">
            <h2 class="card-title text-base">Riwayat Stok</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra table-sm">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Produk</th>
                        <th>Tipe</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">Sebelum</th>
                        <th class="text-right">Sesudah</th>
                        <th>Catatan</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-xs text-base-content/60 whitespace-nowrap">{{ $log->created_at->format('d/m/y H:i') }}</td>
                            <td class="text-sm font-medium">{{ $log->product?->name ?? '—' }}</td>
                            <td>
                                @php
                                    $typeColor = match($log->type) {
                                        'in'         => 'badge-success',
                                        'out'        => 'badge-error',
                                        'adjustment' => 'badge-info',
                                        default      => 'badge-ghost',
                                    };
                                @endphp
                                <span class="badge {{ $typeColor }} badge-sm capitalize">{{ $log->type }}</span>
                            </td>
                            <td class="text-right font-mono">{{ $log->quantity }}</td>
                            <td class="text-right font-mono text-base-content/60">{{ $log->stock_before }}</td>
                            <td class="text-right font-mono font-semibold">{{ $log->stock_after }}</td>
                            <td class="text-sm text-base-content/70 max-w-48 truncate">{{ $log->note }}</td>
                            <td class="text-xs text-base-content/60">{{ $log->user?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-base-content/50 py-8">Belum ada riwayat stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body pt-2">
            {{ $logs->links() }}
        </div>
    </div>
</div>
</x-admin.feature-disabled-wrapper>
@endsection
