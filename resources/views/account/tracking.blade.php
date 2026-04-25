@extends('layouts.account')

@section('account-content')
<div class="space-y-10">

    {{-- Heading --}}
    <div>
        <h1 class="display-font text-4xl text-base-content font-normal lg:text-5xl">{{ __('Tracking') }}</h1>
    </div>

    {{-- Search by order number --}}
    <div class="border-b border-base-200 pb-10">
        <p class="text-xs text-base-content/50 uppercase tracking-[0.18em] mb-4">{{ __('Track by order number') }}</p>
        <form class="flex flex-col sm:flex-row gap-3 max-w-sm"
              x-data="{ num: '' }"
              @submit.prevent="if(num) window.location.href = '/orders/' + num + '/tracking'">
            <input x-model="num" type="text" placeholder="SOJ-XXXXXXXX"
                   class="flex-1 border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors placeholder:text-base-content/30">
            <button type="submit" class="btn btn-primary btn-sm px-8 self-end">{{ __('Track') }}</button>
        </form>
    </div>

    {{-- Recent orders --}}
    @if($orders->isNotEmpty())
    <div class="space-y-5">
        <p class="text-[10px] uppercase tracking-[0.22em] text-base-content/40">{{ __('Recent Orders') }}</p>
        <div class="divide-y divide-base-200">
            @foreach($orders as $order)
            <div class="flex items-center justify-between py-4 gap-4">
                <div class="space-y-0.5 min-w-0">
                    <p class="font-mono text-sm font-medium text-base-content truncate">{{ $order->order_number }}</p>
                    <p class="text-[11px] text-base-content/50">{{ $order->placed_at?->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    @php
                        $fStatus = $order->fulfillment_status ?? $order->status;
                        $fColor  = match($fStatus) {
                            'fulfilled', 'shipped', 'completed', 'delivered' => 'badge-success',
                            'processing' => 'badge-warning',
                            'cancelled'  => 'badge-error',
                            default      => 'badge-ghost',
                        };
                    @endphp
                    <span class="badge {{ $fColor }} badge-sm capitalize">{{ $fStatus }}</span>
                    <a href="{{ route('orders.track', $order->order_number) }}"
                       class="text-[10px] uppercase tracking-[0.18em] font-semibold text-primary hover:text-primary/70 transition-colors whitespace-nowrap">
                        {{ __('Track') }} →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="py-20 text-center">
        <p class="text-sm text-base-content/40">{{ __('No orders to track yet.') }}</p>
        <a href="{{ route('shop.index') }}"
           class="mt-5 inline-block text-[10px] uppercase tracking-[0.2em] font-semibold text-primary hover:text-primary/70 transition-colors">
            {{ __('Browse Products') }} →
        </a>
    </div>
    @endif

</div>
@endsection
