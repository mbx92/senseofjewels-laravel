@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Hero Section</h1>
        <p class="text-sm text-base-content/60">Edit konten tampilan utama halaman depan.</p>
    </div>
</div>

@if (session('success'))
    <div role="alert" class="alert alert-success mb-6">
        <span>{{ session('success') }}</span>
    </div>
@endif

<form action="{{ route('admin.hero.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Edit Hero Section</h2>

            {{-- Title --}}
            <div class="form-control">
                <label class="label"><span class="label-text">Judul Utama</span></label>
                <input type="text" name="title" value="{{ old('title', $section?->title) }}"
                    class="input input-bordered w-full @error('title') input-error @enderror"
                    placeholder="e.g. Elegant Jewelry for Every Occasion" required>
                @error('title')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            {{-- Subtitle --}}
            <div class="form-control">
                <label class="label"><span class="label-text">Subtitle</span></label>
                <textarea name="subtitle" rows="3"
                    class="textarea textarea-bordered w-full @error('subtitle') textarea-error @enderror"
                    placeholder="Deskripsi singkat di bawah judul">{{ old('subtitle', $section?->subtitle) }}</textarea>
                @error('subtitle')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            {{-- CTA --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Teks Tombol CTA</span></label>
                    <input type="text" name="cta_text" value="{{ old('cta_text', $section?->cta_text) }}"
                        class="input input-bordered w-full" placeholder="e.g. Explore Products">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Link Tombol CTA</span></label>
                    <input type="text" name="cta_url" value="{{ old('cta_url', $section?->cta_url) }}"
                        class="input input-bordered w-full" placeholder="e.g. /shop">
                </div>
            </div>

            {{-- Background Image --}}
            <div class="form-control">
                <label class="label"><span class="label-text">Background Image</span></label>
                @if ($section?->image_path)
                    <div class="mb-2">
                        <img src="{{ Storage::disk('public')->url($section->image_path) }}"
                            alt="Current hero image"
                            class="h-40 w-full rounded-box object-cover">
                        <p class="mt-1 text-xs text-base-content/50">Gambar saat ini. Upload baru untuk mengganti.</p>
                    </div>
                @endif
                <input type="file" name="background_image" accept="image/*"
                    class="file-input file-input-bordered w-full @error('background_image') file-input-error @enderror">
                <label class="label"><span class="label-text-alt">Maks 2MB · JPG, PNG, WEBP</span></label>
                @error('background_image')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            {{-- Active toggle --}}
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
