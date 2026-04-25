@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center space-y-6">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-24 h-24 rounded-full bg-success/10 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        {{-- Heading --}}
        <div class="space-y-2">
            <h1 class="display-font text-4xl text-base-content">{{ __('Payment Successful!') }}</h1>
            @if($order)
            <p class="text-base-content/60">
                {{ __('Order') }} <span class="font-medium text-base-content">#{{ $order->order_number }}</span> {{ __('is being processed.') }}
            </p>
            @else
            <p class="text-base-content/60">{{ __('Your payment has been received.') }}</p>
            @endif
        </div>

        {{-- Alert --}}
        <div class="alert alert-success text-sm text-left">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ __('Payment confirmation has been sent to your email. Your order will be processed shortly.') }}</span>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if($order)
            <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-primary">
                {{ __('View Order Details') }}
            </a>
            @endif
            <a href="{{ route('shop.index') }}" class="btn btn-outline">
                {{ __('Continue Shopping') }}
            </a>
        </div>
    </div>
</div>
@endsection
