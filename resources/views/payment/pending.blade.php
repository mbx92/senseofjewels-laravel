@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center space-y-6">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-24 h-24 rounded-full bg-warning/10 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        {{-- Heading --}}
        <div class="space-y-2">
            <h1 class="display-font text-4xl text-base-content">{{ __('Waiting for Payment') }}</h1>
            @if($order)
            <p class="text-base-content/60">
                {{ __('Order') }} <span class="font-medium text-base-content">#{{ $order->order_number }}</span> {{ __('has not been confirmed yet.') }}
            </p>
            @else
            <p class="text-base-content/60">{{ __('Your payment is being verified.') }}</p>
            @endif
        </div>

        {{-- Alert --}}
        <div class="alert alert-warning text-sm text-left">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ __('Your payment is being processed. We will send an email notification after confirmation.') }}</span>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if($order)
            <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-warning btn-outline">
                {{ __('Check Order Status') }}
            </a>
            @endif
            <a href="{{ route('home') }}" class="btn btn-outline">
                {{ __('Back to Home') }}
            </a>
        </div>
    </div>
</div>
@endsection
