@extends('layouts.admin')

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!document.querySelector('#service-description')) {
            return;
        }

        tinymce.init({
            selector: '#service-description',
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
        <h1 class="text-2xl font-bold">Edit Service</h1>
        <p class="text-sm text-base-content/60">{{ $service->title }}</p>
    </div>
</div>

<form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Edit Service</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control sm:col-span-2">
                    <label class="label"><span class="label-text">Judul <span class="text-error">*</span></span></label>
                    <input type="text" name="title" value="{{ old('title', $service->title) }}"
                        class="input input-bordered w-full @error('title') input-error @enderror" required>
                    @error('title')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Icon</span></label>
                    <input type="text" name="icon" value="{{ old('icon', $service->icon) }}"
                        class="input input-bordered w-full" placeholder="e.g. 🎨">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Urutan Tampil</span></label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}"
                        class="input input-bordered w-full" min="0">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Summary</span></label>
                <textarea name="summary" rows="2"
                    class="textarea textarea-bordered w-full">{{ old('summary', $service->summary) }}</textarea>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Deskripsi Lengkap</span></label>
                <textarea id="service-description" name="description"
                    class="textarea textarea-bordered w-full">{{ old('description', $service->description) }}</textarea>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Gambar</span></label>
                @if ($service->image_path)
                    <div class="mb-2">
                        <img src="{{ Storage::disk('public')->url($service->image_path) }}"
                            alt="{{ $service->title }}"
                            class="h-32 rounded-box object-cover">
                        <p class="mt-1 text-xs text-base-content/50">Upload baru untuk mengganti.</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="file-input file-input-bordered w-full @error('image') file-input-error @enderror">
                <label class="label"><span class="label-text-alt">Maks 2MB · JPG, PNG, WEBP</span></label>
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="toggle toggle-primary"
                        {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                    <span class="label-text">Aktif</span>
                </label>
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" class="toggle toggle-secondary"
                        {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                    <span class="label-text">Featured</span>
                </label>
            </div>

            <div class="card-actions justify-end mt-4">
                <a href="{{ route('admin.services.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endsection
