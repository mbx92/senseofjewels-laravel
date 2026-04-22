@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-semibold">Checkout</h1>
            <p class="text-base-content/70">This is the pre-Midtrans checkout foundation. Customer data, order records, and payment placeholders are already wired.</p>
        </div>

        @if (! $cart || $cart->items->isEmpty())
            <div class="alert">
                <span>Your cart is empty. Add some products first.</span>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-[1fr,340px]">
                <form method="POST" action="{{ route('checkout.store') }}" class="card border border-base-300 bg-base-100 shadow-sm">
                    @csrf
                    <div class="card-body gap-5">
                        <h2 class="card-title">Customer Details</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">Full name</span>
                                </div>
                                <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" class="input input-bordered w-full" required>
                            </label>
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">Email</span>
                                </div>
                                <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" class="input input-bordered w-full" required>
                            </label>
                            <label class="form-control w-full md:col-span-2">
                                <div class="label">
                                    <span class="label-text">Phone</span>
                                </div>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" class="input input-bordered w-full">
                            </label>
                        </div>

                        <h3 class="pt-1 text-lg font-semibold">Shipping Address</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="form-control w-full md:col-span-2">
                                <div class="label">
                                    <span class="label-text">Address line</span>
                                </div>
                                <input type="text" name="shipping_address[line_1]" value="{{ old('shipping_address.line_1', auth()->user()->address_line_1 ?? '') }}" class="input input-bordered w-full" required>
                            </label>
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">City</span>
                                </div>
                                <input type="text" name="shipping_address[city]" value="{{ old('shipping_address.city', auth()->user()->city ?? '') }}" class="input input-bordered w-full" required>
                            </label>
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">Province</span>
                                </div>
                                <input type="text" name="shipping_address[province]" value="{{ old('shipping_address.province', auth()->user()->province ?? '') }}" class="input input-bordered w-full" required>
                            </label>
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">Postal code</span>
                                </div>
                                <input type="text" name="shipping_address[postal_code]" value="{{ old('shipping_address.postal_code', auth()->user()->postal_code ?? '') }}" class="input input-bordered w-full" required>
                            </label>
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">Country</span>
                                </div>
                                <input type="text" name="shipping_address[country]" value="{{ old('shipping_address.country', auth()->user()->country ?? 'Indonesia') }}" class="input input-bordered w-full" required>
                            </label>
                        </div>

                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">Order notes</span>
                            </div>
                            <textarea name="notes" class="textarea textarea-bordered min-h-24 w-full">{{ old('notes') }}</textarea>
                        </label>

                        <div class="card-actions justify-end pt-2">
                            <button type="submit" class="btn btn-primary">Create Order</button>
                        </div>
                    </div>
                </form>

                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body gap-4">
                        <h2 class="card-title">Summary</h2>
                        <ul class="space-y-3 text-sm">
                            @foreach ($cart->items as $item)
                                <li class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-medium">{{ $item->product_name }}</div>
                                        <div class="text-base-content/60">Qty {{ $item->quantity }}</div>
                                    </div>
                                    <span>Rp {{ number_format($item->line_total, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="divider"></div>
                        <div class="flex items-center justify-between text-base font-semibold">
                            <span>Total</span>
                            <span>Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                        {{-- Voucher --}}
                        <div x-data="{ code: '', loading: false, message: '', valid: false, discountFmt: '', total: '' }">
                            <div class="form-control mt-2">
                                <label class="label"><span class="label-text font-medium">Kode Voucher</span></label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="code" placeholder="Masukkan kode voucher"
                                        class="input input-bordered input-sm flex-1" />
                                    <button type="button" class="btn btn-outline btn-sm"
                                        :disabled="loading || !code"
                                        @click="
                                            loading = true; message = ''; valid = false;
                                            fetch('{{ route('checkout.apply-voucher') }}', {
                                                method: 'POST',
                                                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                                                body: JSON.stringify({code})
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
                                    <p class="label-text-alt mt-1" :class="valid ? 'text-success' : 'text-error'" x-text="message"></p>
                                </template>
                            </div>
                            <template x-if="valid">
                                <div class="mt-2 space-y-1 text-sm">
                                    <div class="flex justify-between text-success">
                                        <span>Diskon Voucher</span>
                                        <span x-text="'- ' + discountFmt"></span>
                                    </div>
                                    <div class="flex justify-between font-bold">
                                        <span>Total Bayar</span>
                                        <span x-text="total"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div role="alert" class="alert alert-info mt-4 text-sm">
                            <span>Pembayaran via Midtrans Snap akan diaktifkan pada modul berikutnya.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
