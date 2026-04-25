@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    {{-- Header --}}
    <div class="space-y-1">
        <p class="text-xs uppercase tracking-[0.25em] text-primary">Order #{{ $order->order_number }}</p>
        <h1 class="display-font text-4xl text-base-content">{{ __('Complete Payment') }}</h1>
        <p class="text-sm text-base-content/55">Klik tombol di bawah untuk membuka halaman pembayaran Midtrans Snap.</p>
    </div>

    {{-- Step --}}
    <ul class="steps steps-horizontal w-full text-xs">
        <li class="step step-primary">Data</li>
        <li class="step step-primary">Konfirmasi</li>
        <li class="step step-primary">Pembayaran</li>
    </ul>

    <div class="grid gap-6 lg:grid-cols-[1fr,340px] items-start">

        {{-- Left: Payment card --}}
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-base">Total Pembayaran</h2>
                        <p class="display-font text-3xl text-base-content">@money($order->total)</p>
                    </div>
                </div>

                <div class="text-sm text-base-content/60 space-y-1">
                    <div class="flex justify-between">
                        <span>Pemesan</span>
                        <span class="text-base-content">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Email</span>
                        <span class="text-base-content">{{ $order->customer_email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode Bayar</span>
                        <span class="text-base-content">Midtrans (semua metode tersedia)</span>
                    </div>
                </div>

                <div class="alert alert-info text-sm gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"/></svg>
                    <span>Setelah klik "Bayar Sekarang", jendela pembayaran Midtrans akan terbuka. Jangan tutup halaman ini.</span>
                </div>

                {{-- Pay Button --}}
                <div x-data="snapPay({
                    orderId: {{ $order->id }},
                    tokenUrl: '{{ route('payment.token') }}',
                    csrfToken: '{{ csrf_token() }}',
                    existingToken: '{{ $order->payment?->snap_token ?? '' }}',
                    successUrl: '{{ route('payment.success', ['order_id' => $order->order_number]) }}',
                    pendingUrl: '{{ route('payment.pending', ['order_id' => $order->order_number]) }}',
                    failedUrl: '{{ route('payment.failed', ['order_id' => $order->order_number]) }}',
                })">
                    <button id="pay-button"
                        class="btn btn-primary btn-block btn-lg"
                        @click="pay()"
                        :disabled="loading">
                        <span class="loading loading-spinner loading-sm" x-show="loading"></span>
                        <span x-text="loading ? '{{ __('Processing...') }}' : '{{ __('Pay Now') }}'"></span>
                    </button>

                    <template x-if="errorMsg">
                        <div class="alert alert-error mt-4 text-sm" x-text="errorMsg"></div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Right: Order items --}}
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body gap-4">
                <h3 class="font-semibold text-sm uppercase tracking-widest text-base-content/50">Isi Order</h3>
                <ul class="space-y-3 divide-y divide-base-200">
                    @foreach($order->items as $item)
                    <li class="flex items-start justify-between gap-3 pt-3 first:pt-0 text-sm">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-base-content truncate">{{ $item->product_name }}</p>
                            <p class="text-xs text-base-content/45 mt-0.5">Qty {{ $item->quantity }}</p>
                        </div>
                        <span class="text-base-content/80 whitespace-nowrap">@money($item->total)</span>
                    </li>
                    @endforeach
                </ul>
                <div class="pt-3 border-t border-base-200 text-sm space-y-1.5">
                    @if($order->discount_total > 0)
                    <div class="flex justify-between text-success">
                        <span>Diskon</span>
                        <span>- @money($order->discount_total)</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold">
                        <span>Total</span>
                        <span>@money($order->total)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Midtrans Snap.js --}}
<script src="{{ config('midtrans.snap_url') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function snapPay({ orderId, tokenUrl, csrfToken, existingToken, successUrl, pendingUrl, failedUrl }) {
    return {
        loading: false,
        errorMsg: '',
        async pay() {
            this.loading = true;
            this.errorMsg = '';
            try {
                let token = existingToken;
                if (!token) {
                    const res = await fetch(tokenUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ order_id: orderId }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.errorMsg = data.error ?? 'Gagal memuat halaman pembayaran.';
                        this.loading = false;
                        return;
                    }
                    token = data.token;
                    existingToken = token; // cache
                }
                this.loading = false;
                snap.pay(token, {
                    onSuccess:  () => { window.location.href = successUrl; },
                    onPending:  () => { window.location.href = pendingUrl; },
                    onError:    () => { window.location.href = failedUrl; },
                    onClose:    () => { /* user closed without paying */ },
                });
            } catch (e) {
                this.errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                this.loading = false;
            }
        }
    };
}
</script>
@endsection
