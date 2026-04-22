@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div class="space-y-6">
<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Kategori</h1>
        <p class="text-sm text-base-content/60">Kelompokkan katalog dengan hierarki yang tetap rapi di desktop dan mobile.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">+ Tambah Kategori</a>
</div>

<div class="card bg-base-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Induk</th>
                    <th>Produk</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td class="font-medium">{{ $category->name }}</td>
                        <td class="text-sm text-base-content/60">{{ $category->parent?->name ?? '—' }}</td>
                        <td>{{ $category->products_count ?? $category->products()->count() }}</td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            @if ($category->is_active)
                                <span class="badge badge-success badge-sm">Aktif</span>
                            @else
                                <span class="badge badge-ghost badge-sm">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1" x-data="{ open: false }">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-ghost btn-xs">Edit</a>
                                <button @click="open = true" class="btn btn-error btn-outline btn-xs">Hapus</button>

                                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-cloak>
                                    <div class="card bg-base-100 w-80 shadow-xl">
                                        <div class="card-body">
                                            <h3 class="card-title text-base">Hapus Kategori?</h3>
                                            <p class="text-sm">Kategori <strong>{{ $category->name }}</strong> akan dihapus. Pastikan tidak ada produk terhubung.</p>
                                            <div class="card-actions justify-end mt-2">
                                                <button @click="open = false" class="btn btn-ghost btn-sm">Batal</button>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
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
                    <tr><td colspan="6" class="text-center text-base-content/50 py-8">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
