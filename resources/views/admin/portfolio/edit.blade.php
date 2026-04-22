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
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Edit Portfolio Item</h1>
        <p class="text-sm text-base-content/60">{{ $portfolio->title }}</p>
    </div>
</div>

<form action="{{ route('admin.portfolio.update', $portfolio) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Edit Portfolio</h2>

            <div class="form-control">
                <label class="label"><span class="label-text">Judul <span class="text-error">*</span></span></label>
                <input type="text" name="title" value="{{ old('title', $portfolio->title) }}"
                    class="input input-bordered w-full @error('title') input-error @enderror" required>
                @error('title')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Kategori</span></label>
                    <input type="text" name="category" value="{{ old('category', $portfolio->category) }}"
                        class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Nama Klien</span></label>
                    <input type="text" name="client_name" value="{{ old('client_name', $portfolio->client_name) }}"
                        class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">URL Proyek</span></label>
                    <input type="url" name="project_url" value="{{ old('project_url', $portfolio->project_url) }}"
                        class="input input-bordered w-full @error('project_url') input-error @enderror">
                    @error('project_url')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Tanggal Selesai</span></label>
                    <input type="date" name="completed_at"
                        value="{{ old('completed_at', $portfolio->completed_at?->format('Y-m-d')) }}"
                        class="input input-bordered w-full">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Deskripsi</span></label>
                <textarea id="portfolio-description" name="description"
                    class="textarea textarea-bordered w-full">{{ old('description', $portfolio->description) }}</textarea>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Gambar Utama</span></label>
                @if ($portfolio->image_path)
                    <div class="mb-2">
                        <img src="{{ Storage::disk('public')->url($portfolio->image_path) }}"
                            alt="{{ $portfolio->title }}"
                            class="h-40 rounded-box object-cover">
                        <p class="mt-1 text-xs text-base-content/50">Upload baru untuk mengganti.</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="file-input file-input-bordered w-full @error('image') file-input-error @enderror">
                <label class="label"><span class="label-text-alt">Maks 2MB · JPG, PNG, WEBP</span></label>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Urutan Tampil</span></label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $portfolio->sort_order) }}"
                    class="input input-bordered w-full" min="0">
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="toggle toggle-primary"
                        {{ old('is_active', $portfolio->is_active) ? 'checked' : '' }}>
                    <span class="label-text">Aktif</span>
                </label>
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" class="toggle toggle-secondary"
                        {{ old('is_featured', $portfolio->is_featured) ? 'checked' : '' }}>
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
