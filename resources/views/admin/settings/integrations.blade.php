@extends('layouts.admin')

@section('content')
<div class="max-w-3xl space-y-8">
    <div class="space-y-1">
        <p class="text-[10px] uppercase tracking-[0.25em] text-primary">System</p>
        <h1 class="display-font text-4xl text-base-content font-normal">Integrations</h1>
        <p class="text-sm text-base-content/50">Kelola status integrasi eksternal untuk alur transaksi.</p>
    </div>

    <form method="POST" action="{{ route('admin.integrations.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card bg-base-100 border border-base-300">
            <div class="card-body gap-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="card-title text-base">Midtrans Payment Gateway</h2>
                        <p class="text-xs text-base-content/50 mt-1">
                            Aktifkan untuk pembayaran online via Midtrans Snap.
                            Jika nonaktif, checkout tetap berjalan dengan metode manual dan status pembayaran pending.
                        </p>
                    </div>
                    <label class="label cursor-pointer gap-3">
                        <input type="hidden" name="midtrans_enabled" value="0">
                        <input type="checkbox"
                               name="midtrans_enabled"
                               value="1"
                               class="toggle toggle-primary"
                               {{ ($settings['midtrans_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                    </label>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 text-xs">
                    <div class="rounded-box border border-base-300 px-3 py-2">
                        <div class="text-base-content/50 uppercase tracking-widest text-[10px] mb-1">Config Status</div>
                        <div class="{{ $midtransConfigured ? 'text-success' : 'text-warning' }}">
                            {{ $midtransConfigured ? 'Configured' : 'Missing MIDTRANS_SERVER_KEY / MIDTRANS_CLIENT_KEY' }}
                        </div>
                    </div>
                    <div class="rounded-box border border-base-300 px-3 py-2">
                        <div class="text-base-content/50 uppercase tracking-widest text-[10px] mb-1">Current Mode</div>
                        <div class="text-base-content">
                            {{ ($settings['midtrans_enabled'] ?? '1') === '1' ? 'Midtrans Enabled' : 'Manual Payment Mode' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <button type="submit" class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-8 py-3 hover:bg-neutral/80 transition-colors">
                Save Integration Settings
            </button>
        </div>
    </form>
</div>
@endsection
