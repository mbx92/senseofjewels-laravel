@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
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
                    <div class="card-body grid gap-4">
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

                        <h3 class="text-lg font-semibold">Shipping Address</h3>
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

                        <div class="card-actions justify-end">
                            <button type="submit" class="btn btn-primary">Create Order</button>
                        </div>
                    </div>
                </form>

                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
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
                        <div role="alert" class="alert alert-info mt-4">
                            <span>Midtrans Snap will plug into the generated <code>payments</code> and <code>payment_logs</code> tables in the next phase.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
