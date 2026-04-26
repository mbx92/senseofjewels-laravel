@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Services</h1>
        <p class="text-sm text-base-content/60">Kelola layanan yang tampil di halaman depan.</p>
    </div>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">+ Tambah Service</a>
</div>

<div class="overflow-x-auto rounded-box border border-base-300 bg-base-100 shadow-sm">
    <table class="table table-zebra w-full">
        <thead>
            <tr class="bg-base-200/70">
                <th>Icon</th>
                <th>Judul</th>
                <th>Summary</th>
                <th>Urutan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
                <tr>
                    <td class="text-xl">{{ $service->icon ?? '—' }}</td>
                    <td class="font-medium">{{ $service->title }}</td>
                    <td class="max-w-xs truncate text-sm text-base-content/70">{{ $service->summary }}</td>
                    <td>{{ $service->sort_order }}</td>
                    <td>
                        @if ($service->is_active)
                            <span class="badge badge-success badge-sm">Aktif</span>
                        @else
                            <span class="badge badge-ghost badge-sm">Nonaktif</span>
                        @endif
                        @if ($service->is_featured)
                            <span class="badge badge-primary badge-sm">Featured</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2" x-data="{ open: false }">
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-info">Edit</a>
                            <button @click="open = true" class="btn btn-sm btn-error">Hapus</button>

                            <dialog class="modal" :class="{ 'modal-open': open }">
                                <div class="modal-box">
                                    <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
                                    <p class="py-4">Hapus service <strong>{{ $service->title }}</strong>? Data tidak dapat dikembalikan.</p>
                                    <div class="modal-action">
                                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST">
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
                    <td colspan="6" class="text-center text-base-content/50 py-8">Belum ada service. <a href="{{ route('admin.services.create') }}" class="link">Tambah sekarang</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
