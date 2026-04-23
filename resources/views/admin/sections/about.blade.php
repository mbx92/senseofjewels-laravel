@extends('layouts.admin')

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!document.querySelector('#about-content')) {
            return;
        }

        tinymce.init({
            selector: '#about-content',
            plugins: 'lists link image code',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
            height: 300,
            skin: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide',
            content_css: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default',
        });
    });
</script>
@endpush

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">About Section</h1>
        <p class="text-sm text-base-content/60">Edit konten tentang perusahaan di halaman depan.</p>
    </div>
</div>

<form action="{{ route('admin.about.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Edit About Section</h2>

            <div class="form-control">
                <label class="label"><span class="label-text">Judul</span></label>
                <input type="text" name="title" value="{{ old('title', $section?->title) }}"
                    class="input input-bordered w-full @error('title') input-error @enderror"
                    placeholder="e.g. About Sense of Jewels" required>
                @error('title')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Deskripsi</span></label>
                <textarea id="about-content" name="content"
                    class="textarea textarea-bordered w-full @error('content') textarea-error @enderror"
                    rows="8">{{ old('content', $section?->content) }}</textarea>
                @error('content')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Gambar About</span></label>
                @if ($section?->image_path)
                    <div class="mb-2">
                        <img src="{{ Storage::disk('public')->url($section->image_path) }}"
                            alt="About image"
                            class="h-48 rounded-box object-cover">
                        <p class="mt-1 text-xs text-base-content/50">Gambar saat ini. Upload baru untuk mengganti.</p>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="file-input file-input-bordered w-full @error('image') file-input-error @enderror">
                <label class="label"><span class="label-text-alt">Maks 2MB · JPG, PNG, WEBP</span></label>
                @error('image')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-4">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="toggle toggle-primary"
                        {{ old('is_active', $section?->is_active ?? true) ? 'checked' : '' }}>
                    <span class="label-text">Aktifkan section ini</span>
                </label>
            </div>

            <div class="card-actions justify-end mt-4">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endsection
