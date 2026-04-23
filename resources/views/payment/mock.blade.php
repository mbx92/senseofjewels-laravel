@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-base-200 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">

        {{-- Mode banner --}}
        <div class="alert alert-warning mb-6 shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z" />
            </svg>
            <div>
                <span class="font-bold">Mode Simulator Lokal</span>
                <p class="text-sm">Midtrans belum dikonfigurasi. Gunakan halaman ini untuk menguji alur pembayaran.</p>
            </div>
        </div>

        {{-- Ringkasan order --}}
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title text-lg">Ringkasan Pesanan</h2>
                <div class="divider my-1"></div>

                <div class="space-y-2 text-sm">
                    @foreach ($order->items as $item)
                    <div class="flex justify-between">
                        <span class="text-base-content/70">{{ $item->product_name ?? $item->name }}
                            <span class="badge badge-ghost badge-sm ml-1">x{{ $item->quantity }}</span>
                        </span>
                        <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="divider my-2"></div>

                <div class="flex justify-between font-bold text-base">
                    <span>Total</span>
                    <span class="text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

                <div class="mt-2 text-xs text-base-content/50">
                    Order # <span class="font-mono">{{ $order->order_number }}</span>
                </div>
            </div>
        </div>

        {{-- Simulator --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg">Simulasi Pembayaran</h2>
                <p class="text-sm text-base-content/60 mb-4">
                    Pilih hasil yang ingin Anda simulasikan. Ini hanya tersedia di lingkungan lokal ketika
                    <code class="kbd kbd-sm text-xs">MIDTRANS_SERVER_KEY</code> belum diisi.
                </p>

                <form method="POST" action="{{ route('payment.mock-simulate') }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    <input type="hidden" name="order_number" value="{{ $order->order_number }}">

                    <div class="grid grid-cols-1 gap-3">

                        {{-- Sukses --}}
                        <button type="submit" name="result" value="success"
                                class="btn btn-success gap-3"
                                :disabled="loading">
                            <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7" />
                            </svg>
                            Simulasi Pembayaran Berhasil
                            <span class="badge badge-outline badge-sm ml-auto">settlement</span>
                        </button>

                        {{-- Pending --}}
                        <button type="submit" name="result" value="pending"
                                class="btn btn-warning gap-3"
                                :disabled="loading">
                            <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Simulasi Pembayaran Pending
                            <span class="badge badge-outline badge-sm ml-auto">pending</span>
                        </button>

                        {{-- Gagal --}}
                        <button type="submit" name="result" value="failed"
                                class="btn btn-error gap-3"
                                :disabled="loading">
                            <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Simulasi Pembayaran Gagal
                            <span class="badge badge-outline badge-sm ml-auto">cancel/deny</span>
                        </button>

                    </div>
                </form>

                <div class="divider text-xs">Cara mendapatkan akun Midtrans asli</div>

                <ol class="steps steps-vertical text-xs text-left gap-1">
                    <li class="step step-neutral">
                        Daftar gratis di
                        <a href="https://dashboard.sandbox.midtrans.com"
                           target="_blank" rel="noopener"
                           class="link link-primary">dashboard.sandbox.midtrans.com</a>
                    </li>
                    <li class="step step-neutral">
                        Buka <strong>Settings → Access Keys</strong> → salin Sandbox Server Key &amp; Client Key
                    </li>
                    <li class="step step-neutral">
                        Isi <code class="kbd kbd-sm">.env</code>:
                        <code class="block font-mono text-xs bg-base-200 rounded p-1 mt-1">
                            MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx<br>
                            MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx
                        </code>
                    </li>
                    <li class="step step-neutral">
                        Jalankan <code class="kbd kbd-sm">php artisan config:clear</code>
                    </li>
                </ol>
            </div>
        </div>

    </div>
</div>
@endsection
