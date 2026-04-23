@extends('layouts.admin')

@section('title', 'Tambah Diskon')

@section('content')
<div class="space-y-6">
<div class="flex items-center gap-3">
    <a href="{{ route('admin.discounts.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Tambah Diskon</h1>
        <p class="text-sm text-base-content/60">Atur promosi dengan form dua kolom yang lebih seimbang di berbagai ukuran layar.</p>
    </div>
</div>

<div class="card bg-base-100 shadow-sm max-w-xl">
    <div class="card-body gap-4">
        <form method="POST" action="{{ route('admin.discounts.store') }}" class="space-y-4">
            @csrf

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Nama Diskon <span class="text-error">*</span></legend>
                <input type="text" name="name" value="{{ old('name') }}" class="input w-full @error('name') input-error @enderror" required />
                @error('name')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
            </fieldset>

            <div class="grid gap-4 sm:grid-cols-2">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Kode (opsional)</legend>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="DISKON20" class="input w-full uppercase" />
                    @error('code')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Berlaku Untuk <span class="text-error">*</span></legend>
                    <select name="applies_to" class="select w-full" required>
                        <option value="all" @selected(old('applies_to') === 'all')>Semua Produk</option>
                        <option value="category" @selected(old('applies_to') === 'category')>Kategori</option>
                        <option value="product" @selected(old('applies_to') === 'product')>Produk Tertentu</option>
                    </select>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Tipe Diskon <span class="text-error">*</span></legend>
                    <select name="type" class="select w-full" required>
                        <option value="percent" @selected(old('type') === 'percent')>Persen (%)</option>
                        <option value="fixed" @selected(old('type') === 'fixed')>Nominal (Rp)</option>
                    </select>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Nilai Diskon <span class="text-error">*</span></legend>
                    <input type="number" name="value" value="{{ old('value') }}" step="0.01" min="0" class="input w-full @error('value') input-error @enderror" required />
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Min. Order (Rp)</legend>
                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount') }}" step="1" min="0" class="input w-full" />
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Maks. Diskon (Rp)</legend>
                    <input type="number" name="maximum_discount_amount" value="{{ old('maximum_discount_amount') }}" step="1" min="0" class="input w-full" />
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Batas Penggunaan</legend>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" class="input w-full" placeholder="Tanpa batas" />
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Mulai</legend>
                    <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="input w-full" />
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Berakhir</legend>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="input w-full" />
                </fieldset>
            </div>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Deskripsi</legend>
                <textarea name="description" rows="2" class="textarea w-full">{{ old('description') }}</textarea>
            </fieldset>

            @include('admin.components.media-picker', [
                'inputName'    => 'image_url',
                'inputId'      => 'discount_image_create',
                'currentValue' => old('image_url', ''),
                'label'        => 'Gambar Diskon (Opsional)',
            ])

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle toggle-success" @checked(old('is_active', true)) />
                <label for="is_active" class="text-sm">Aktif</label>
            </div>

            <div class="card-actions justify-end">
                <a href="{{ route('admin.discounts.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
