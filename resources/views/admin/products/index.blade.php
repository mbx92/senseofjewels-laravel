@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
<div class="space-y-6">
<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Produk</h1>
        <p class="text-sm text-base-content/60">Grid aksi dan tabel produk kini mengikuti ritme vertikal yang sama dengan halaman admin lainnya.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
</div>

<div class="card bg-base-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Stok</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    @php $primary = $product->images->first(); @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="w-10 rounded bg-base-200">
                                        @if ($primary)
                                            <img src="{{ Storage::url($primary->path) }}" alt="{{ $product->name }}" />
                                        @else
                                            <div class="w-10 h-10 flex items-center justify-center text-base-content/30 text-xs">N/A</div>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="font-medium text-sm">{{ $product->name }}</div>
                                    @if ($product->is_featured)
                                        <span class="badge badge-accent badge-xs">Featured</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="font-mono text-xs">{{ $product->sku }}</td>
                        <td class="text-sm">{{ $product->category?->name ?? '-' }}</td>
                        <td class="text-right text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="text-right">
                            <span class="{{ $product->stock <= $product->min_stock_alert ? 'text-error font-bold' : '' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>
                            @if ($product->is_active)
                                <span class="badge badge-success badge-sm">Aktif</span>
                            @else
                                <span class="badge badge-ghost badge-sm">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1" x-data="{ open: false }">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-ghost btn-xs">Edit</a>
                                <button @click="open = true" class="btn btn-error btn-outline btn-xs">Hapus</button>

                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-cloak>
                                    <div class="card bg-base-100 w-80 shadow-xl">
                                        <div class="card-body">
                                            <h3 class="card-title text-base">Hapus Produk?</h3>
                                            <p class="text-sm">Produk <strong>{{ $product->name }}</strong> akan dihapus beserta semua gambarnya.</p>
                                            <div class="card-actions justify-end mt-2">
                                                <button @click="open = false" class="btn btn-ghost btn-sm">Batal</button>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
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
                    <tr><td colspan="7" class="text-center text-base-content/50 py-8">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-0">
        {{ $products->links() }}
    </div>
</div>
</div>
@endsection
