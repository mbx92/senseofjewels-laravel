@extends('layouts.admin')

@section('title', 'Edit Voucher – ' . $voucher->code)

@section('content')
<div class="space-y-6">
<div class="flex items-center gap-3">
    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <div class="space-y-1">
        <h1 class="text-2xl font-bold">Edit Voucher</h1>
        <p class="text-sm text-base-content/60">Perubahan voucher kini mengikuti ritme grid dan padding yang sama dengan create view.</p>
    </div>
</div>

<div class="card bg-base-100 shadow-sm max-w-lg">
    <div class="card-body gap-4">
        <form method="POST" action="{{ route('admin.vouchers.update', $voucher) }}" class="space-y-4">
            @csrf @method('PUT')

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Kode Voucher <span class="text-error">*</span></legend>
                <input type="text" name="code" value="{{ old('code', $voucher->code) }}" class="input w-full uppercase @error('code') input-error @enderror" required />
                @error('code')<p class="fieldset-label text-error">{{ $message }}</p>@enderror
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Diskon Terkait <span class="text-error">*</span></legend>
                <select name="discount_id" class="select w-full" required>
                    <option value="">— Pilih Diskon —</option>
                    @foreach ($discounts as $discount)
                        <option value="{{ $discount->id }}" @selected(old('discount_id', $voucher->discount_id) == $discount->id)>
                            {{ $discount->name }}
                            ({{ $discount->type === 'percent' ? $discount->value . '%' : 'Rp ' . number_format($discount->value, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </fieldset>

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Deskripsi</legend>
                <textarea name="description" rows="2" class="textarea w-full">{{ old('description', $voucher->description) }}</textarea>
            </fieldset>

            @include('admin.components.media-picker', [
                'inputName'    => 'image_url',
                'inputId'      => 'voucher_image_edit',
                'currentValue' => old('image_url', $voucher->image_url ?? ''),
                'label'        => 'Gambar Voucher (Opsional)',
            ])

            <div class="grid gap-4 sm:grid-cols-2">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Min. Order (Rp)</legend>
                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount', $voucher->minimum_order_amount) }}" step="1" min="0" class="input w-full" />
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Batas Penggunaan</legend>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1" class="input w-full" />
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Batas Per User</legend>
                    <input type="number" name="per_user_limit" value="{{ old('per_user_limit', $voucher->per_user_limit) }}" min="1" class="input w-full" />
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Mulai</legend>
                    <input type="date" name="starts_at" value="{{ old('starts_at', $voucher->starts_at?->format('Y-m-d')) }}" class="input w-full" />
                </fieldset>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Berakhir</legend>
                    <input type="date" name="ends_at" value="{{ old('ends_at', $voucher->ends_at?->format('Y-m-d')) }}" class="input w-full" />
                </fieldset>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" id="is_active" value="1" class="toggle toggle-success" @checked(old('is_active', $voucher->is_active)) />
                <label for="is_active" class="text-sm">Aktif</label>
            </div>

            <div class="card-actions justify-end">
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
