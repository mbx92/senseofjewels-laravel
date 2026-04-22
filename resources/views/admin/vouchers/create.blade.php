@extends('layouts.admin')

@section('title', 'Tambah Voucher')

@section('content')
<div class="space-y-6">
<div class="flex items-center gap-3">
    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Tambah Voucher</h1>
        <p class="text-sm text-base-content/60">Form voucher dirapikan agar blok metadata dan limit penggunaan terasa seimbang.</p>
    </div>
</div>

<div class="card bg-base-100 shadow-sm max-w-lg">
    <div class="card-body gap-4">
        <form method="POST" action="{{ route('admin.vouchers.store') }}" class="space-y-4">
            @csrf

            <div class="form-control">
                <label class="label"><span class="label-text">Kode Voucher <span class="text-error">*</span></span></label>
                <input type="text" name="code" value="{{ old('code') }}" class="input input-bordered uppercase @error('code') input-error @enderror" placeholder="HEMAT50" required />
                @error('code')<span class="label-text-alt text-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Diskon Terkait <span class="text-error">*</span></span></label>
                <select name="discount_id" class="select select-bordered @error('discount_id') select-error @enderror" required>
                    <option value="">— Pilih Diskon —</option>
                    @foreach ($discounts as $discount)
                        <option value="{{ $discount->id }}" @selected(old('discount_id') == $discount->id)>
                            {{ $discount->name }}
                            ({{ $discount->type === 'percent' ? $discount->value . '%' : 'Rp ' . number_format($discount->value, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @error('discount_id')<span class="label-text-alt text-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Deskripsi</span></label>
                <textarea name="description" rows="2" class="textarea textarea-bordered">{{ old('description') }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Min. Order (Rp)</span></label>
                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount') }}" step="1" min="0" class="input input-bordered" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Batas Penggunaan</span></label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" class="input input-bordered" placeholder="Tanpa batas" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Batas Per User</span></label>
                    <input type="number" name="per_user_limit" value="{{ old('per_user_limit') }}" min="1" class="input input-bordered" placeholder="Tanpa batas" />
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

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle toggle-success" @checked(old('is_active', true)) />
                <label for="is_active" class="text-sm">Aktif</label>
            </div>

            <div class="card-actions justify-end">
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
