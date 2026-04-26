@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Portfolio</h1>
        <p class="text-sm text-base-content/60">Kelola item portfolio / galeri proyek.</p>
    </div>
    <a href="{{ route('admin.portfolio.create') }}" class="btn btn-primary btn-sm">+ Tambah Item</a>
</div>

<div class="overflow-x-auto rounded-box border border-base-300 bg-base-100 shadow-sm">
    <table class="table table-zebra w-full">
        <thead>
            <tr class="bg-base-200/70">
                <th>Gambar</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Klien</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>
                        @if ($item->image_path)
                            <img src="{{ Storage::disk('public')->url($item->image_path) }}"
                                alt="{{ $item->title }}"
                                class="h-12 w-16 rounded object-cover">
                        @else
                            <div class="h-12 w-16 rounded bg-base-300 flex items-center justify-center text-xs text-base-content/40">No img</div>
                        @endif
                    </td>
                    <td class="font-medium">{{ $item->title }}</td>
                    <td>
                        @if ($item->category)
                            <span class="badge badge-outline badge-sm">{{ $item->category }}</span>
                        @else
                            <span class="text-base-content/40">—</span>
                        @endif
                    </td>
                    <td class="text-sm text-base-content/70">{{ $item->client_name ?? '—' }}</td>
                    <td>
                        @if ($item->is_active)
                            <span class="badge badge-success badge-sm">Aktif</span>
                        @else
                            <span class="badge badge-ghost badge-sm">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2" x-data="{ open: false }">
                            <a href="{{ route('admin.portfolio.edit', $item) }}" class="btn btn-sm btn-info">Edit</a>
                            <button @click="open = true" class="btn btn-sm btn-error">Hapus</button>

                            <dialog class="modal" :class="{ 'modal-open': open }">
                                <div class="modal-box">
                                    <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
                                    <p class="py-4">Hapus <strong>{{ $item->title }}</strong>? Data tidak dapat dikembalikan.</p>
                                    <div class="modal-action">
                                        <form action="{{ route('admin.portfolio.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Ya, Hapus</button>
                                        </form>
                                        <button class="btn" @click="open = false">Batal</button>
                                    </div>
                                </div>
                                <label class="modal-backdrop" @click="open = false"></label>
                            </dialog>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-base-content/50 py-8">Belum ada item portfolio. <a href="{{ route('admin.portfolio.create') }}" class="link">Tambah sekarang</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
