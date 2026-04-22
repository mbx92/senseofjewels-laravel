@extends('layouts.admin')

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!document.querySelector('#portfolio-description')) {
            return;
        }

        tinymce.init({
            selector: '#portfolio-description',
            plugins: 'lists link code',
            toolbar: 'undo redo | bold italic | bullist numlist | link | code',
            height: 250,
        });
    });
</script>
@endpush

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Tambah Portfolio Item</h1>
</div>

<form action="{{ route('admin.portfolio.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Detail Portfolio</h2>

            <div class="form-control">
                <label class="label"><span class="label-text">Judul <span class="text-error">*</span></span></label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="input input-bordered w-full @error('title') input-error @enderror" required>
                @error('title')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Kategori</span></label>
                    <input type="text" name="category" value="{{ old('category') }}"
                        class="input input-bordered w-full" placeholder="e.g. Branding, Web Design">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Nama Klien</span></label>
                    <input type="text" name="client_name" value="{{ old('client_name') }}"
                        class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">URL Proyek</span></label>
                    <input type="url" name="project_url" value="{{ old('project_url') }}"
                        class="input input-bordered w-full @error('project_url') input-error @enderror"
                        placeholder="https://">
                    @error('project_url')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Tanggal Selesai</span></label>
                    <input type="date" name="completed_at" value="{{ old('completed_at') }}"
                        class="input input-bordered w-full">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Deskripsi</span></label>
                <textarea id="portfolio-description" name="description"
                    class="textarea textarea-bordered w-full">{{ old('description') }}</textarea>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Gambar Utama</span></label>
                <input type="file" name="image" accept="image/*"
                    class="file-input file-input-bordered w-full @error('image') file-input-error @enderror">
                <label class="label"><span class="label-text-alt">Maks 2MB · JPG, PNG, WEBP</span></label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Urutan Tampil</span></label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                        class="input input-bordered w-full" min="0">
                </div>
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="toggle toggle-primary" checked>
                    <span class="label-text">Aktif</span>
                </label>
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" class="toggle toggle-secondary">
                    <span class="label-text">Featured</span>
                </label>
            </div>

            <div class="card-actions justify-end mt-4">
                <a href="{{ route('admin.portfolio.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endsection
