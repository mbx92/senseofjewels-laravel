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

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control sm:col-span-2">
                    <label class="label"><span class="label-text">Nama Diskon <span class="text-error">*</span></span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input input-bordered @error('name') input-error @enderror" required />
                    @error('name')<span class="label-text-alt text-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Kode (opsional)</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="DISKON20" class="input input-bordered uppercase" />
                    @error('code')<span class="label-text-alt text-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Berlaku Untuk</span></label>
                    <select name="applies_to" class="select select-bordered" required>
                        <option value="all" @selected(old('applies_to') === 'all')>Semua Produk</option>
                        <option value="category" @selected(old('applies_to') === 'category')>Kategori</option>
                        <option value="product" @selected(old('applies_to') === 'product')>Produk Tertentu</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Tipe Diskon <span class="text-error">*</span></span></label>
                    <select name="type" class="select select-bordered" required>
                        <option value="percent" @selected(old('type') === 'percent')>Persen (%)</option>
                        <option value="fixed" @selected(old('type') === 'fixed')>Nominal (Rp)</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Nilai Diskon <span class="text-error">*</span></span></label>
                    <input type="number" name="value" value="{{ old('value') }}" step="0.01" min="0" class="input input-bordered @error('value') input-error @enderror" required />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Min. Order (Rp)</span></label>
                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount') }}" step="1" min="0" class="input input-bordered" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Maks. Diskon (Rp)</span></label>
                    <input type="number" name="maximum_discount_amount" value="{{ old('maximum_discount_amount') }}" step="1" min="0" class="input input-bordered" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Batas Penggunaan</span></label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" class="input input-bordered" placeholder="Tanpa batas" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Mulai</span></label>
                    <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="input input-bordered" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Berakhir</span></label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="input input-bordered" />
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Deskripsi</span></label>
                <textarea name="description" rows="2" class="textarea textarea-bordered">{{ old('description') }}</textarea>
            </div>

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
