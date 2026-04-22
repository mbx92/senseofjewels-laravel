@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Tambah Testimonial</h1>
</div>

<form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Detail Testimonial</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Nama <span class="text-error">*</span></span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="input input-bordered w-full @error('name') input-error @enderror" required>
                    @error('name')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Rating <span class="text-error">*</span></span></label>
                    <select name="rating" class="select select-bordered w-full @error('rating') select-error @enderror" required>
                        <option value="">Pilih Rating</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} ★
                            </option>
                        @endfor
                    </select>
                    @error('rating')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Posisi / Jabatan</span></label>
                    <input type="text" name="position" value="{{ old('position') }}"
                        class="input input-bordered w-full" placeholder="e.g. CEO, Customer">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Perusahaan</span></label>
                    <input type="text" name="company" value="{{ old('company') }}"
                        class="input input-bordered w-full">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Pesan <span class="text-error">*</span></span></label>
                <textarea name="message" rows="4"
                    class="textarea textarea-bordered w-full @error('message') textarea-error @enderror"
                    required maxlength="1000">{{ old('message') }}</textarea>
                @error('message')
                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                @enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Foto</span></label>
                <input type="file" name="photo" accept="image/*"
                    class="file-input file-input-bordered w-full @error('photo') file-input-error @enderror">
                <label class="label"><span class="label-text-alt">Maks 1MB · JPG, PNG, WEBP</span></label>
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
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endsection
