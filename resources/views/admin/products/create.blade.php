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

                    <div class="form-control">
                        <label class="label"><span class="label-text">Nama Produk <span class="text-error">*</span></span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered @error('name') input-error @enderror" required />
                        @error('name')<span class="label-text-alt text-error mt-1">{{ $message }}</span>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text">SKU <span class="text-error">*</span></span></label>
                            <input type="text" name="sku" value="{{ old('sku') }}" class="input input-bordered @error('sku') input-error @enderror" required />
                            @error('sku')<span class="label-text-alt text-error mt-1">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Kategori</span></label>
                            <select name="category_id" class="select select-bordered">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Deskripsi Singkat</span></label>
                        <textarea name="short_description" rows="2" class="textarea textarea-bordered">{{ old('short_description') }}</textarea>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Deskripsi Lengkap</span></label>
                        <textarea id="description" name="description" rows="6" class="textarea textarea-bordered">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Gambar Produk</h2>
                    <p class="text-sm text-base-content/60">Maks. 8 gambar, masing-masing maks. 2MB. Gambar pertama dijadikan gambar utama.</p>
                    <input type="file" name="images[]" multiple accept="image/*" class="file-input file-input-bordered w-full" />
                    @error('images.*')<span class="label-text-alt text-error mt-1">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Pricing --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Harga & Biaya</h2>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Harga Jual (Rp) <span class="text-error">*</span></span></label>
                        <input type="number" name="price" value="{{ old('price') }}" step="1" min="0" class="input input-bordered @error('price') input-error @enderror" required />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Harga Coret (Rp)</span></label>
                        <input type="number" name="compare_at_price" value="{{ old('compare_at_price') }}" step="1" min="0" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Harga Modal (Rp)</span></label>
                        <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="1" min="0" class="input input-bordered" />
                    </div>
                </div>
            </div>

            {{-- Stock --}}
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Stok & Berat</h2>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Stok Awal</span></label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Alert Stok Minimum</span></label>
                        <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', 5) }}" min="0" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Berat (gram)</span></label>
                        <input type="number" name="weight" value="{{ old('weight') }}" step="0.01" min="0" class="input input-bordered" />
                    </div>
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

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!document.querySelector('#description')) {
            return;
        }

        tinymce.init({
            selector: '#description',
            plugins: 'link lists image code',
            toolbar: 'undo redo | bold italic | bullist numlist | link image | code',
            menubar: false,
            height: 300,
            skin: 'oxide',
        });
    });
</script>
@endpush
