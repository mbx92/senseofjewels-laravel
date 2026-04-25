@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="display-font text-4xl text-base-content">{{ __('Shopping Cart') }}</h1>
            <p class="text-sm text-base-content/55">{{ __('Review your selected products before proceeding to payment.') }}</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn btn-outline btn-sm self-start sm:self-auto">← {{ __('Continue Shopping') }}</a>
    </div>

    @if(session('status'))
    <div class="alert alert-success text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr,300px] items-start">

        {{-- Cart items table --}}
        <div class="card border border-base-300 bg-base-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="bg-base-200/50">
                        <tr class="text-xs uppercase tracking-widest text-base-content/50">
                            <th class="w-full">Produk</th>
                            <th class="text-center whitespace-nowrap">Jumlah</th>
                            <th class="text-right whitespace-nowrap">Harga Satuan</th>
                            <th class="text-right whitespace-nowrap">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cart->items as $item)
                        <tr class="align-middle">
                            <td>
                                <p class="font-medium text-base-content">{{ $item->product_name }}</p>
                                <p class="text-xs text-base-content/45 mt-0.5">SKU: {{ $item->product_sku }}</p>
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('cart.update', $item) }}" class="inline-flex items-center gap-1">
                                    @csrf @method('PATCH')
                                    <input type="number" name="quantity" min="1" value="{{ $item->quantity }}"
                                        class="input input-bordered input-sm w-16 text-center" />
                                    <button class="btn btn-ghost btn-xs text-base-content/50 hover:text-base-content" title="{{ __('Update') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </button>
                                </form>
                            </td>
                            <td class="text-right whitespace-nowrap text-sm text-base-content/70">
                                @money($item->unit_price)
                            </td>
                            <td class="text-right whitespace-nowrap font-medium">
                                @money($item->line_total)
                            </td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-xs text-error hover:bg-error/10" title="{{ __('Delete') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="py-16 text-center space-y-3">
                                    <span class="block text-5xl opacity-20">🛒</span>
                                    <p class="text-base-content/50">{{ __('Your cart is empty.') }}</p>
                                    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">{{ __('Start Shopping') }}</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Summary --}}
        <div class="card border border-base-300 bg-base-100 lg:sticky lg:top-24">
            <div class="card-body gap-4">
                <h2 class="card-title text-base font-semibold">{{ __('Summary') }}</h2>

                @php
                    $productDiscountTotal = $cart->items->sum(function ($item) {
                        $basePrice = $item->product?->price ?? $item->unit_price;
                        return max(0, ($basePrice - $item->unit_price) * $item->quantity);
                    });
                @endphp
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-base-content/60">
                        <span>{{ __('Subtotal') }}</span>
                        <span>@money($cart->subtotal)</span>
                    </div>
                    @if($productDiscountTotal > 0)
                    <div class="flex justify-between text-success">
                        <span>Diskon Produk</span>
                        <span>- @money($productDiscountTotal)</span>
                    </div>
                    @endif
                    @if($cart->discount_total > 0)
                    <div class="flex justify-between text-success">
                        <span>Diskon Voucher</span>
                        <span>- @money($cart->discount_total)</span>
                    </div>
                    @endif
                    @if($productDiscountTotal + $cart->discount_total > 0)
                    <div class="flex justify-between text-base-content/70 text-xs pt-1">
                        <span>Total Hemat</span>
                        <span>@money($productDiscountTotal + $cart->discount_total)</span>
                    </div>
                    @endif
                    <div class="divider my-1"></div>
                    <div class="flex justify-between font-semibold text-base">
                        <span>{{ __('Total') }}</span>
                        <span>@money($cart->total)</span>
                    </div>
                </div>

                <div class="card-actions pt-2">
                    @if ($cart->items->isEmpty())
                    <button class="btn btn-primary btn-block" disabled>{{ __('Checkout') }}</button>
                    @else
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">
                        {{ __('Proceed to Checkout') }} →
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
