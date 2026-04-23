@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Testimonials</h1>
        <p class="text-sm text-base-content/60">Kelola ulasan pelanggan di halaman depan.</p>
    </div>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm">+ Tambah Testimonial</a>
</div>

<div class="overflow-x-auto">
    <table class="table table-zebra w-full">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($testimonials as $testimonial)
                <tr>
                    <td>
                        @if ($testimonial->photo_path)
                            <div class="avatar">
                                <div class="w-10 rounded-full">
                                    <img src="{{ Storage::disk('public')->url($testimonial->photo_path) }}"
                                        alt="{{ $testimonial->name }}">
                                </div>
                            </div>
                        @else
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-10">
                                    <span class="text-sm">{{ strtoupper(substr($testimonial->name, 0, 1)) }}</span>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td class="font-medium">{{ $testimonial->name }}</td>
                    <td class="text-sm text-base-content/70">
                        {{ $testimonial->position }}
                        {{ $testimonial->company ? '· ' . $testimonial->company : '' }}
                    </td>
                    <td>
                        <div class="rating rating-sm">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" class="mask mask-star-2 bg-orange-400" disabled
                                    {{ $testimonial->rating === $i ? 'checked' : '' }}>
                            @endfor
                        </div>
                    </td>
                    <td>
                        @if ($testimonial->is_active)
                            <span class="badge badge-success badge-sm">Aktif</span>
                        @else
                            <span class="badge badge-ghost badge-sm">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2" x-data="{ open: false }">
                            <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-info">Edit</a>
                            <button @click="open = true" class="btn btn-sm btn-error">Hapus</button>

                            <dialog class="modal" :class="{ 'modal-open': open }">
                                <div class="modal-box">
                                    <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
                                    <p class="py-4">Hapus testimonial dari <strong>{{ $testimonial->name }}</strong>?</p>
                                    <div class="modal-action">
                                        <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST">
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
                    <td colspan="6" class="text-center text-base-content/50 py-8">Belum ada testimonial. <a href="{{ route('admin.testimonials.create') }}" class="link">Tambah sekarang</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
