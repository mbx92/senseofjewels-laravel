@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    {{-- Page Header --}}
    <div class="space-y-1">
        <h1 class="display-font text-4xl text-base-content">Checkout</h1>
        <p class="text-sm text-base-content/55">Lengkapi data pengiriman, lalu selesaikan pembayaran melalui Midtrans.</p>
    </div>

    @if (! $cart || $cart->items->isEmpty())
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"/></svg>
            <span>Keranjang Anda kosong. <a href="{{ route('shop.index') }}" class="underline">Kembali ke toko</a></span>
        </div>
    @else
        <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
            @csrf
        <div class="grid gap-8 lg:grid-cols-[1fr,360px] items-start">

            {{-- Left: Form fields --}}
            <div class="space-y-6">

                {{-- Step indicator --}}
                <ul class="steps steps-horizontal w-full text-xs mb-2">
                    <li class="step step-primary">Data</li>
                    <li class="step step-primary">Konfirmasi</li>
                    <li class="step">Pembayaran</li>
                </ul>

                {{-- Customer info --}}
                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base font-semibold">Informasi Pemesan</h2>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="form-control sm:col-span-2">
                                <label class="label"><span class="label-text">Nama Lengkap <span class="text-error">*</span></span></label>
                                <input type="text" name="customer_name"
                                    value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                    class="input input-bordered w-full @error('customer_name') input-error @enderror" required />
                                @error('customer_name')<p class="label-text-alt text-error mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Email <span class="text-error">*</span></span></label>
                                <input type="email" name="customer_email"
                                    value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                    class="input input-bordered w-full @error('customer_email') input-error @enderror" required />
                                @error('customer_email')<p class="label-text-alt text-error mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Nomor Telepon</span></label>
                                <input type="text" name="customer_phone"
                                    value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                                    placeholder="+62 8xx xxxx xxxx"
                                    class="input input-bordered w-full" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipping address --}}
                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base font-semibold">Alamat Pengiriman</h2>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="form-control sm:col-span-2">
                                <label class="label"><span class="label-text">Alamat Lengkap <span class="text-error">*</span></span></label>
                                <input type="text" name="shipping_address[line_1]"
                                    value="{{ old('shipping_address.line_1', auth()->user()->address_line_1 ?? '') }}"
                                    placeholder="Jl. Contoh No. 1, RT/RW"
                                    class="input input-bordered w-full @error('shipping_address.line_1') input-error @enderror" required />
                                @error('shipping_address.line_1')<p class="label-text-alt text-error mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Kota <span class="text-error">*</span></span></label>
                                <input type="text" name="shipping_address[city]"
                                    value="{{ old('shipping_address.city', auth()->user()->city ?? '') }}"
                                    class="input input-bordered w-full @error('shipping_address.city') input-error @enderror" required />
                                @error('shipping_address.city')<p class="label-text-alt text-error mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Provinsi <span class="text-error">*</span></span></label>
                                <input type="text" name="shipping_address[province]"
                                    value="{{ old('shipping_address.province', auth()->user()->province ?? '') }}"
                                    class="input input-bordered w-full @error('shipping_address.province') input-error @enderror" required />
                                @error('shipping_address.province')<p class="label-text-alt text-error mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Kode Pos <span class="text-error">*</span></span></label>
                                <input type="text" name="shipping_address[postal_code]"
                                    value="{{ old('shipping_address.postal_code', auth()->user()->postal_code ?? '') }}"
                                    class="input input-bordered w-full @error('shipping_address.postal_code') input-error @enderror" required />
                                @error('shipping_address.postal_code')<p class="label-text-alt text-error mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Negara</span></label>
                                <input type="text" name="shipping_address[country]"
                                    value="{{ old('shipping_address.country', auth()->user()->country ?? 'Indonesia') }}"
                                    class="input input-bordered w-full" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-3">
                        <h2 class="card-title text-base font-semibold">Catatan Order</h2>
                        <div class="form-control">
                            <textarea name="notes" rows="3" placeholder="Instruksi khusus pengiriman, dll. (opsional)"
                                class="textarea textarea-bordered min-h-20 w-full">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: Order summary --}}
            <div class="space-y-4 lg:sticky lg:top-24">
                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base font-semibold">Ringkasan Order</h2>

                        <ul class="space-y-3 divide-y divide-base-200">
                            @foreach ($cart->items as $item)
                            <li class="flex items-start justify-between gap-3 pt-3 first:pt-0 text-sm">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-base-content truncate">{{ $item->product_name }}</p>
                                    <p class="text-base-content/50 text-xs mt-0.5">Qty {{ $item->quantity }}</p>
                                </div>
                                <span class="font-light text-base-content/80 whitespace-nowrap">Rp {{ number_format($item->line_total, 0, ',', '.') }}</span>
                            </li>
                            @endforeach
                        </ul>

                        <div class="pt-3 space-y-2 text-sm border-t border-base-200">
                            <div class="flex justify-between text-base-content/60">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($cart->discount_total > 0)
                            <div class="flex justify-between text-success">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($cart->discount_total, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between font-semibold text-base pt-1 border-t border-base-200">
                                <span>Total</span>
                                <span>Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Voucher --}}
                        <div x-data="{ code: '', loading: false, message: '', valid: false, discountFmt: '', total: '' }" class="pt-1">
                            <div class="form-control">
                                <label class="label"><span class="label-text text-xs font-medium uppercase tracking-widest text-base-content/50">Kode Voucher</span></label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="code"
                                        placeholder="Masukkan kode voucher"
                                        class="input input-bordered input-sm flex-1 uppercase"
                                        @keydown.enter.prevent="$refs.applyBtn.click()" />
                                    <button type="button" x-ref="applyBtn"
                                        class="btn btn-outline btn-sm"
                                        :disabled="loading || !code"
                                        @click="
                                            loading = true; message = ''; valid = false;
                                            fetch('{{ route('checkout.apply-voucher') }}', {
                                                method: 'POST',
                                                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                                                body: JSON.stringify({code: code.toUpperCase()})
                                            })
                                            .then(r => r.json())
                                            .then(d => { loading=false; valid=d.valid; message=d.message; if(d.valid){ discountFmt=d.discount_fmt; total=d.total_fmt; } })
                                            .catch(() => { loading=false; message='Terjadi kesalahan.'; });
                                        ">
                                        <span x-show="!loading">Terapkan</span>
                                        <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                    </button>
                                </div>
                                <template x-if="message">
                                    <p class="mt-1 text-xs" :class="valid ? 'text-success' : 'text-error'" x-text="message"></p>
                                </template>
                            </div>
                            <template x-if="valid">
                                <div class="mt-3 space-y-1 text-sm bg-success/10 rounded-lg p-3">
                                    <div class="flex justify-between text-success font-medium">
                                        <span>Diskon Voucher</span>
                                        <span x-text="'- ' + discountFmt"></span>
                                    </div>
                                    <div class="flex justify-between font-bold text-base-content">
                                        <span>Total Bayar</span>
                                        <span x-text="total"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-base-content/40 text-center leading-relaxed px-2">
                    Pembayaran diamankan oleh <span class="font-medium">Midtrans</span>.<br>
                    Anda akan diarahkan ke halaman pembayaran setelah konfirmasi.
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-full btn-lg">
            Konfirmasi & Lanjut ke Pembayaran →
        </button>
        </form>
    @endif
</div>
@endsection
