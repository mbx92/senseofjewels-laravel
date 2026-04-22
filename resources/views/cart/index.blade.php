@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold">Shopping Cart</h1>
                <p class="text-base-content/70">Session-based cart storage is already wired to authenticated and guest flows.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline">Continue Shopping</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr,320px]">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cart->items as $item)
                                    <tr>
                                        <td>
                                            <div class="font-medium">{{ $item->product_name }}</div>
                                            <div class="text-xs text-base-content/60">{{ $item->product_sku }}</div>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" class="input input-bordered input-sm w-20">
                                                <button class="btn btn-ghost btn-sm">Update</button>
                                            </form>
                                        </td>
                                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-error btn-outline btn-sm">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert">
                                                <span>Your cart is empty.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Order Summary</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Discount</span>
                            <span>Rp {{ number_format($cart->discount_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="divider my-1"></div>
                        <div class="flex items-center justify-between text-base font-semibold">
                            <span>Total</span>
                            <span>Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="card-actions mt-4 justify-end">
                        @if ($cart->items->isEmpty())
                            <button class="btn btn-primary" disabled>Proceed to Checkout</button>
                        @else
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary">Proceed to Checkout</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
