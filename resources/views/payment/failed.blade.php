@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center space-y-6">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-24 h-24 rounded-full bg-error/10 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        {{-- Heading --}}
        <div class="space-y-2">
            <h1 class="display-font text-4xl text-base-content">{{ __('Payment Failed') }}</h1>
            @if($order)
            <p class="text-base-content/60">
                {{ __('Order') }} <span class="font-medium text-base-content">#{{ $order->order_number }}</span> {{ __('could not be processed.') }}
            </p>
            @else
            <p class="text-base-content/60">{{ __('Payment could not be processed.') }}</p>
            @endif
        </div>

        {{-- Alert --}}
        <div class="alert alert-error text-sm text-left">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ __('Payment was denied or canceled. Please try again or use another payment method.') }}</span>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if($order)
            <a href="{{ route('payment.show', $order->order_number) }}" class="btn btn-primary">
                {{ __('Try Again') }}
            </a>
            <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-outline">
                {{ __('View Order') }}
            </a>
            @else
            <a href="{{ route('shop.index') }}" class="btn btn-primary">{{ __('Back to Shop') }}</a>
            @endif
        </div>
    </div>
</div>
@endsection
