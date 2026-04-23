@extends('layouts.admin')

@section('title', 'Edit Kategori – ' . $category->name)

@section('content')
<div class="space-y-6">
<div class="flex items-center gap-3">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Edit Kategori</h1>
        <p class="text-sm text-base-content/60">Perubahan nama, gambar, dan hierarki kategori kini punya ritme yang sama seperti form admin lainnya.</p>
    </div>
</div>

<div class="card bg-base-100 shadow-sm max-w-lg">
    <div class="card-body gap-4">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Nama Kategori <span class="text-error">*</span></legend>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="input w-full @error('name') input-error @enderror" required />
                @error('name')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Kategori Induk</legend>
                <select name="parent_id" class="select w-full">
                    <option value="">— Tidak Ada —</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Deskripsi</legend>
                <textarea name="description" rows="3" class="textarea w-full">{{ old('description', $category->description) }}</textarea>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Gambar</legend>
                @include('admin.components.media-picker', [
                    'inputName'    => 'image_url',
                    'inputId'      => 'cat_image_edit',
                    'currentValue' => old('image_url', $category->image_path ? Storage::url($category->image_path) : ''),
                    'label'        => 'Pilih Gambar dari Library',
                ])
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Urutan Tampil</legend>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" class="input w-28" />
            </fieldset>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle toggle-success" @checked(old('is_active', $category->is_active)) />
                <label for="is_active" class="text-sm">Aktif</label>
            </div>

            <div class="card-actions justify-end pt-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
