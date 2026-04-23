@extends('layouts.admin')

@section('title', 'Edit Produk – ' . $product->name)

@section('content')
<div class="space-y-6">
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
        <div class="space-y-1">
            <h1 class="text-2xl font-bold">Edit Produk</h1>
            <p class="text-sm text-base-content/60">Rapikan konten, gambar, dan status produk tanpa kehilangan ritme vertikal form.</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid gap-6 xl:grid-cols-3">

        {{-- Main Info --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">Informasi Dasar</h2>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Nama Produk <span class="text-error">*</span></legend>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="input w-full @error('name') input-error @enderror" required />
                        @error('name')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">SKU <span class="text-error">*</span></legend>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="input w-full @error('sku') input-error @enderror" required />
                            @error('sku')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Kategori</legend>
                            <select name="category_id" class="select w-full">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Deskripsi Singkat</legend>
                        <textarea name="short_description" rows="3" class="textarea w-full">{{ old('short_description', $product->short_description) }}</textarea>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Deskripsi Lengkap</legend>
                        <textarea id="description" name="description" rows="6" class="textarea w-full">{{ old('description', $product->description) }}</textarea>
                    </fieldset>
                </div>
            </div>

            {{-- Existing Images --}}
            @if ($product->images->isNotEmpty())
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Gambar Saat Ini</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($product->images->sortBy('sort_order') as $img)
                            <div class="relative group">
                                <img src="{{ Storage::url($img->path) }}" alt="{{ $img->alt_text }}"
                                    class="w-20 h-20 object-cover rounded border border-base-300 {{ $img->is_primary ? 'ring-2 ring-primary' : '' }}" />
                                @if ($img->is_primary)
                                    <span class="absolute bottom-0 left-0 right-0 text-center text-[9px] bg-primary text-primary-content rounded-b">Utama</span>
                                @endif
                                <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}"
                                    class="absolute top-0 right-0 opacity-0 group-hover:opacity-100 transition-opacity"
                                    onsubmit="return confirm('Hapus gambar ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-error btn-xs p-0 w-5 h-5 min-h-0 rounded-full">×</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Upload New Images --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Tambah Gambar Baru</h2>
                    <p class="text-sm text-base-content/60">Pilih dari media library. Gambar yang sudah ada di atas tidak akan terhapus.</p>
                    @foreach (range(0, 3) as $i)
                        @include('admin.components.media-picker', [
                            'inputName'    => 'media_image_urls[]',
                            'inputId'      => 'product_edit_img_' . $i,
                            'currentValue' => '',
                            'label'        => 'Gambar Baru ' . ($i + 1),
                        ])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Harga & Biaya</h2>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Harga Jual (Rp) <span class="text-error">*</span></legend>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="1" min="0" class="input w-full" required />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Harga Coret (Rp)</legend>
                        <input type="number" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}" step="1" min="0" class="input w-full" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Harga Modal (Rp)</legend>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="1" min="0" class="input w-full" />
                    </fieldset>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Stok & Berat</h2>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Stok</legend>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="input w-full" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Alert Stok Minimum</legend>
                        <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" min="0" class="input w-full" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Berat (gram)</legend>
                        <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" step="0.01" min="0" class="input w-full" />
                    </fieldset>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Status</h2>
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle toggle-success" @checked(old('is_active', $product->is_active)) />
                        <label for="is_active" class="text-sm">Aktif</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_featured" value="0" />
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" class="toggle toggle-accent" @checked(old('is_featured', $product->is_featured)) />
                        <label for="is_featured" class="text-sm">Produk Unggulan</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full">Perbarui Produk</button>
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

    if (textarea.value) {
        quill.root.innerHTML = textarea.value;
    }

    textarea.form.addEventListener('submit', () => {
        textarea.value = quill.root.innerHTML;
    });
});
</script>
@endpush
