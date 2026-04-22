@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Edit Testimonial</h1>
        <p class="text-sm text-base-content/60">{{ $testimonial->name }}</p>
    </div>
</div>

<form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Edit Testimonial</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Nama <span class="text-error">*</span></span></label>
                    <input type="text" name="name" value="{{ old('name', $testimonial->name) }}"
                        class="input input-bordered w-full @error('name') input-error @enderror" required>
                    @error('name')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Rating <span class="text-error">*</span></span></label>
                    <select name="rating" class="select select-bordered w-full" required>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>
                                {{ $i }} ★
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Posisi / Jabatan</span></label>
                    <input type="text" name="position" value="{{ old('position', $testimonial->position) }}"
                        class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Perusahaan</span></label>
                    <input type="text" name="company" value="{{ old('company', $testimonial->company) }}"
                        class="input input-bordered w-full">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Pesan <span class="text-error">*</span></span></label>
                <textarea name="message" rows="4"
                    class="textarea textarea-bordered w-full @error('message') textarea-error @enderror"
                    required maxlength="1000">{{ old('message', $testimonial->message) }}</textarea>
                @error('message')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Foto</span></label>
                @if ($testimonial->photo_path)
                    <div class="mb-2 flex items-center gap-3">
                        <div class="avatar">
                            <div class="w-16 rounded-full">
                                <img src="{{ Storage::disk('public')->url($testimonial->photo_path) }}"
                                    alt="{{ $testimonial->name }}">
                            </div>
                        </div>
                        <p class="text-xs text-base-content/50">Foto saat ini. Upload baru untuk mengganti.</p>
                    </div>
                @endif
                <input type="file" name="photo" accept="image/*"
                    class="file-input file-input-bordered w-full @error('photo') file-input-error @enderror">
                <label class="label"><span class="label-text-alt">Maks 1MB · JPG, PNG, WEBP</span></label>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Urutan Tampil</span></label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}"
                    class="input input-bordered w-full" min="0">
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="toggle toggle-primary"
                        {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}>
                    <span class="label-text">Aktif</span>
                </label>
                <label class="label cursor-pointer gap-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" class="toggle toggle-secondary"
                        {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }}>
                    <span class="label-text">Featured</span>
                </label>
            </div>

            <div class="card-actions justify-end mt-4">
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endsection
