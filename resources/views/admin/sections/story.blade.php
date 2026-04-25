@extends('layouts.admin')

@section('content')
@php
    $sv = fn(string $key, $default = null) => $section?->settings[$key] ?? $default;
@endphp

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Story Section</h1>
        <p class="text-sm text-base-content/60">Edit konten Brand Story di halaman depan.</p>
    </div>
</div>

<form action="{{ route('admin.story.update') }}" method="POST" class="pb-24">
    @csrf
    @method('PUT')

    {{-- ── Teks ── --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body space-y-5">
            <h2 class="card-title">Konten</h2>

            <div class="form-control">
                <label class="label"><span class="label-text">Eyebrow Badge <span class="text-base-content/40 font-normal">(mis. "Our Heritage")</span></span></label>
                <input type="text" name="eyebrow" value="{{ old('eyebrow', $sv('eyebrow')) }}"
                       class="input input-bordered w-full @error('eyebrow') input-error @enderror"
                       placeholder="Our Heritage">
                @error('eyebrow')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-control"
                     x-data="{ val: '{{ old('title', $section?->title ?? '') }}' }">
                    <label class="label justify-between">
                        <span class="label-text">Judul (baris pertama)</span>
                        <span class="label-text-alt" :class="val.length > 60 ? 'text-warning' : 'text-base-content/40'">
                            <span x-text="val.length"></span>/80
                        </span>
                    </label>
                    <input type="text" name="title"
                           x-model="val"
                           value="{{ old('title', $section?->title) }}"
                           class="input input-bordered w-full @error('title') input-error @enderror"
                           placeholder="Crafted slowly,"
                           maxlength="80">
                    @error('title')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control"
                     x-data="{ val: '{{ old('subtitle', $section?->subtitle ?? '') }}' }">
                    <label class="label justify-between">
                        <span class="label-text">Judul Italic (baris kedua)</span>
                        <span class="label-text-alt" :class="val.length > 60 ? 'text-warning' : 'text-base-content/40'">
                            <span x-text="val.length"></span>/80
                        </span>
                    </label>
                    <input type="text" name="subtitle"
                           x-model="val"
                           value="{{ old('subtitle', $section?->subtitle) }}"
                           class="input input-bordered w-full @error('subtitle') input-error @enderror"
                           placeholder="worn forever."
                           maxlength="80">
                    @error('subtitle')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Isi Cerita</span></label>
                <textarea name="content" rows="5"
                          class="textarea textarea-bordered w-full @error('content') textarea-error @enderror"
                          placeholder="Tulis cerita brand di sini...">{{ old('content', $section?->content) }}</textarea>
                @error('content')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-control">
                    <label class="label"><span class="label-text">Teks Tombol CTA</span></label>
                    <input type="text" name="cta_text" value="{{ old('cta_text', $section?->cta_text) }}"
                           class="input input-bordered w-full @error('cta_text') input-error @enderror"
                           placeholder="Read The Story">
                    @error('cta_text')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">URL Tombol CTA</span></label>
                    <input type="text" name="cta_url" value="{{ old('cta_url', $section?->cta_url) }}"
                           class="input input-bordered w-full @error('cta_url') input-error @enderror"
                           placeholder="/about">
                    @error('cta_url')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>

        </div>
    </div>

    {{-- ── Gambar ── --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body space-y-5">
            <h2 class="card-title">Gambar</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Gambar Utama <span class="text-base-content/40 font-normal">(4:3, landscape)</span></span></label>
                    @include('admin.components.media-picker', [
                        'inputName'    => 'image_path',
                        'inputId'      => 'story_main_image',
                        'currentValue' => $section?->image_path,
                        'label'        => 'Gambar Utama Story',
                    ])
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Gambar Detail <span class="text-base-content/40 font-normal">(kotak kecil, opsional)</span></span></label>
                    @include('admin.components.media-picker', [
                        'inputName'    => 'secondary_image',
                        'inputId'      => 'story_secondary_image',
                        'currentValue' => $sv('secondary_image'),
                        'label'        => 'Gambar Detail Story',
                    ])
                </div>
            </div>

        </div>
    </div>

    <div class="sticky bottom-0 z-30 bg-base-100/95 backdrop-blur border-t border-base-300 shadow-[0_-4px_24px_rgba(0,0,0,0.08)] -mx-5 lg:-mx-8 px-5 lg:px-8">
        <div class="max-w-5xl mx-auto px-6 py-3">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <label class="label cursor-pointer justify-start gap-4">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="toggle toggle-primary"
                           {{ old('is_active', $section?->is_active ?? true) ? 'checked' : '' }}>
                    <span class="label-text font-medium">Aktifkan story section</span>
                </label>
                <div class="flex gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Semua Perubahan</button>
                </div>
            </div>
        </div>
    </div>

</form>
@endsection
