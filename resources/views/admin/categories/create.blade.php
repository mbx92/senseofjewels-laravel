@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<div class="space-y-6">
<div class="flex items-center gap-3">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Tambah Kategori</h1>
        <p class="text-sm text-base-content/60">Jaga struktur kategori tetap rapi dengan form yang punya jarak antar field yang konsisten.</p>
    </div>
</div>

<div class="card bg-base-100 shadow-sm max-w-lg">
    <div class="card-body gap-4">
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="form-control">
                <label class="label"><span class="label-text">Nama Kategori <span class="text-error">*</span></span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered @error('name') input-error @enderror" required />
                @error('name')<span class="label-text-alt text-error mt-1">{{ $message }}</span>@enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Kategori Induk</span></label>
                <select name="parent_id" class="select select-bordered">
                    <option value="">— Tidak Ada —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Deskripsi</span></label>
                <textarea name="description" rows="3" class="textarea textarea-bordered">{{ old('description') }}</textarea>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Gambar</span></label>
                <input type="file" name="image" accept="image/*" class="file-input file-input-bordered w-full" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Urutan Tampil</span></label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="input input-bordered w-28" />
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle toggle-success" @checked(old('is_active', true)) />
                <label for="is_active" class="text-sm">Aktif</label>
            </div>

            <div class="card-actions justify-end pt-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
