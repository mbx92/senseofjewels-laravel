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

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Spesifikasi</legend>
                        <textarea name="specifications_text" rows="5" class="textarea w-full @error('specifications_text') textarea-error @enderror"
                                  placeholder="Material: Sterling Silver 925&#10;Stone: Zircon&#10;Finish: Polished">{{ old('specifications_text', collect($product->specifications ?? [])->map(fn ($value, $label) => $label . ': ' . $value)->implode("\n")) }}</textarea>
                        <p class="fieldset-label">Satu baris per spesifikasi dengan format: Label: Nilai</p>
                        @error('specifications_text')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
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
                                    <span class="absolute bottom-0 left-0 right-0 text-[9px] bg-primary text-primary-content text-center py-0.5">Utama</span>
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
                    <p class="text-sm text-base-content/60">Pilih gambar dari media library. Gambar pertama dari daftar ini jadi utama jika produk belum punya gambar utama.</p>
                    <div x-data="productGalleryPicker()" class="space-y-3">
                        <input type="hidden" name="media_image_urls_json" x-ref="jsonInput" x-effect="$refs.jsonInput.value = JSON.stringify(selected)">

                        <div class="flex flex-wrap gap-2">
                            <template x-for="(url, index) in selected" :key="url">
                                <div class="relative h-20 w-20 overflow-hidden rounded border border-base-300">
                                    <img :src="url" class="h-full w-full object-cover" alt="">
                                    <button type="button" @click="remove(index)"
                                            class="absolute right-1 top-1 h-5 w-5 rounded-full bg-error text-xs leading-none text-white">&times;</button>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/55 py-0.5 text-center text-[9px] text-white" x-text="index === 0 ? 'Utama Baru' : 'Galeri ' + index"></div>
                                </div>
                            </template>
                            <template x-if="selected.length === 0">
                                <div class="flex h-20 w-20 items-center justify-center rounded border border-dashed border-base-300 text-[9px] uppercase tracking-widest text-base-content/40">No Img</div>
                            </template>
                        </div>

                        <button type="button" @click="open = true" class="btn btn-outline btn-sm">Pilih dari Media Library</button>

                        <div x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70">
                            <div class="flex max-h-[90vh] w-full max-w-4xl flex-col bg-base-100 shadow-2xl">
                                <div class="flex items-center justify-between border-b border-base-300 px-6 py-4">
                                    <h3 class="display-font text-2xl">Media Library</h3>
                                    <div class="flex items-center gap-3">
                                        <input type="text" x-model.debounce.300ms="query" placeholder="Search..."
                                               class="w-44 border-b border-base-content/20 bg-transparent py-1.5 text-xs focus:border-primary focus:outline-none">
                                        <button type="button" @click="open = false" class="text-xl text-base-content/50 hover:text-base-content">&times;</button>
                                    </div>
                                </div>
                                <div class="flex-1 overflow-y-auto p-4">
                                    <template x-if="loading">
                                        <div class="py-12 text-center text-sm text-base-content/50">Loading...</div>
                                    </template>
                                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6">
                                        <template x-for="item in mediaItems" :key="item.id">
                                            <button type="button" @click="pick(item.url)"
                                                    :class="selected.includes(item.url) ? 'ring-2 ring-primary ring-offset-1' : 'hover:opacity-80'"
                                                    class="relative aspect-square overflow-hidden bg-base-200 transition-all">
                                                <img x-show="item.is_image" :src="item.url" :alt="item.alt" class="h-full w-full object-cover">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('media_image_urls_json')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
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
function productGalleryPicker(initial = []) {
    return {
        selected: Array.isArray(initial) ? initial : [],
        open: false,
        loading: false,
        mediaItems: [],
        query: '',
        init() {
            this.$watch('open', (val) => { if (val) this.load(); });
            this.$watch('query', () => this.load());
        },
        async load() {
            this.loading = true;
            try {
                const endpoint = `{{ route('admin.media.json', [], false) }}?q=${encodeURIComponent(this.query)}`;
                const res = await fetch(endpoint, {
                    credentials: 'same-origin',
                    mode: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                this.mediaItems = await res.json();
            } finally {
                this.loading = false;
            }
        },
        pick(url) {
            if (this.selected.includes(url)) return;
            this.selected.push(url);
        },
        remove(index) {
            this.selected.splice(index, 1);
        },
    };
}

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
