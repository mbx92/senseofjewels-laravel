@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="space-y-6">
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        <div class="space-y-1">
            <h1 class="text-2xl font-bold">Tambah Produk</h1>
            <p class="text-sm text-base-content/60">Susun informasi produk dengan ritme form yang sama di seluruh modul commerce.</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="grid gap-6 xl:grid-cols-3">

        {{-- Main Info --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">Informasi Dasar</h2>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Nama Produk <span class="text-error">*</span></legend>
                        <input type="text" name="name" value="{{ old('name') }}" class="input w-full @error('name') input-error @enderror" required />
                        @error('name')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">SKU <span class="text-error">*</span></legend>
                            <input type="text" name="sku" value="{{ old('sku') }}" class="input w-full @error('sku') input-error @enderror" required />
                            @error('sku')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Kategori</legend>
                            <select name="category_id" class="select w-full">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Deskripsi Singkat</legend>
                        <textarea name="short_description" rows="3" class="textarea w-full">{{ old('short_description') }}</textarea>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Deskripsi Lengkap</legend>
                        <textarea id="description" name="description" rows="6" class="textarea w-full">{{ old('description') }}</textarea>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Spesifikasi</legend>
                        <textarea name="specifications_text" rows="5" class="textarea w-full @error('specifications_text') textarea-error @enderror"
                                  placeholder="Material: Sterling Silver 925&#10;Stone: Zircon&#10;Finish: Polished">{{ old('specifications_text') }}</textarea>
                        <p class="fieldset-label">Satu baris per spesifikasi dengan format: Label: Nilai</p>
                        @error('specifications_text')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
                    </fieldset>
                </div>
            </div>

            {{-- Images --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Gambar Produk</h2>
                    <p class="text-sm text-base-content/60">Pilih hingga 4 gambar dari media library. Gambar pertama dijadikan gambar utama.</p>
                    @foreach (range(0, 3) as $i)
                        @include('admin.components.media-picker', [
                            'inputName'    => 'media_image_urls[]',
                            'inputId'      => 'product_img_' . $i,
                            'currentValue' => '',
                            'label'        => 'Gambar ' . ($i + 1) . ($i === 0 ? ' (Utama)' : ' (Opsional)'),
                        ])
                    @endforeach
                    @error('media_image_urls.*')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Pricing --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Harga & Biaya</h2>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Harga Jual (Rp) <span class="text-error">*</span></legend>
                        <input type="number" name="price" value="{{ old('price') }}" step="1" min="0" class="input w-full @error('price') input-error @enderror" required />
                        @error('price')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Harga Modal (Rp)</legend>
                        <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="1" min="0" class="input w-full" />
                    </fieldset>
                </div>
            </div>

            {{-- Stock --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Stok & Berat</h2>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Stok Awal</legend>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" class="input w-full" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Alert Stok Minimum</legend>
                        <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', 5) }}" min="0" class="input w-full" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Berat (gram)</legend>
                        <input type="number" name="weight" value="{{ old('weight') }}" step="0.01" min="0" class="input w-full" />
                    </fieldset>
                </div>
            </div>

            {{-- Status --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Status</h2>
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle toggle-success" @checked(old('is_active', true)) />
                        <label for="is_active" class="text-sm">Aktif (tampil di toko)</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_featured" value="0" />
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" class="toggle toggle-accent" @checked(old('is_featured')) />
                        <label for="is_featured" class="text-sm">Produk Unggulan</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full">Simpan Produk</button>
        </div>
    </div>
</form>
</div>
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-container { border-bottom-left-radius: .5rem; border-bottom-right-radius: .5rem; }
    .ql-toolbar { border-top-left-radius: .5rem; border-top-right-radius: .5rem; }
    .ql-editor { min-height: 220px; font-size: 14px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.getElementById('description');
    if (!textarea) return;

    const wrapper = document.createElement('div');
    textarea.parentNode.insertBefore(wrapper, textarea);
    textarea.style.display = 'none';

    const quill = new Quill(wrapper, {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean'],
            ],
        },
    });

    // Pre-fill with existing value
    if (textarea.value) {
        quill.root.innerHTML = textarea.value;
    }

    // Sync back before submit
    textarea.form.addEventListener('submit', () => {
        textarea.value = quill.root.innerHTML;
    });
});
</script>
@endpush
