@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-12 max-w-[1400px] pb-40 md:pb-56">
    <!-- Header (Chic Minimal) -->
    <div class="py-12 md:py-20 text-center border-b border-base-300 mb-12">
        <h1 class="display-font text-5xl md:text-6xl text-base-content mb-4 tracking-wide">{{ __('Ready to Wear') }}</h1>
        <p class="text-base-content/60 max-w-lg mx-auto text-lg font-light">Fine jewelry for your daily narrative. Explore our complete collection of modern heirlooms.</p>
    </div>

    <!-- Shop Tools (Horizontal Glamora Style) -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6 text-sm uppercase tracking-widest">
        <div class="flex items-center gap-8 overflow-x-auto w-full md:w-auto pb-4 md:pb-0 no-scrollbar whitespace-nowrap text-xs font-semibold">
            <a href="{{ route('shop.index') }}" class="{{ !request('category') ? 'text-base-content border-b border-base-content' : 'text-base-content/50 hover:text-base-content' }} pb-1 transition-colors">{{ __('All') }}</a>
            @foreach ($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="{{ request('category') === $category->slug ? 'text-base-content border-b border-base-content' : 'text-base-content/50 hover:text-base-content' }} pb-1 transition-colors">{{ $category->name }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('shop.index') }}" class="w-full md:w-64"
              x-data="{ timer: null, autoSubmit(delay = 350) { clearTimeout(this.timer); this.timer = setTimeout(() => this.$refs.form.submit(), delay); } }"
              x-ref="form">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" @input="autoSubmit()"
                       class="w-full border-b border-base-content/20 bg-transparent py-2 text-xs placeholder:text-base-content/40 focus:outline-none focus:border-base-content transition-colors"
                       placeholder="{{ strtoupper(__('Search Collection...')) }}">
                <span class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-base-content/40">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </span>
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    @if($products->isEmpty())
        <div class="py-32 text-center">
            <span class="block text-4xl mb-4 opacity-20">❍</span>
            <p class="text-base-content/50 text-lg font-light mb-6">Our collection is empty based on your current selection.</p>
            <a href="{{ route('shop.index') }}" class="uppercase tracking-widest text-xs font-semibold text-base-content border-b border-base-content pb-1">{{ __('Reset Filters') }}</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-16">
            @foreach ($products as $product)
                @php
                    $waMessage = "Halo Sense of Jewels, saya ingin order {$product->name} (SKU: {$product->sku}). Link: " . route('shop.show', $product->slug);
                    $productWhatsappUrl = \App\Models\Setting::whatsappUrl($waMessage);
                @endphp
                <div class="group flex flex-col relative text-center">
                    <!-- Image -->
                    <div class="aspect-[3/4] bg-base-200 w-full mb-6 block overflow-hidden relative border border-base-300 group-hover:border-primary/40 transition-colors">
                        <a href="{{ route('shop.show', $product->slug) }}" class="absolute inset-0 z-10 block" aria-label="{{ $product->name }}">
                            <span class="sr-only">{{ $product->name }}</span>
                        </a>
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-tr from-base-200 to-base-300 flex items-center justify-center transition-transform duration-1000 group-hover:scale-105">
                                <span class="text-base-content/30 tracking-[0.2em] text-[10px] uppercase">{{ __('View Details') }}</span>
                            </div>
                        @endif
                        @if ($product->is_featured)
                            <div class="absolute top-4 right-4 z-10">
                                <span class="bg-base-100 px-3 py-1 text-[9px] uppercase tracking-widest">{{ __('Bestseller') }}</span>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="absolute bottom-0 left-0 right-0 p-4 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 translate-y-4 transition-all duration-500 z-20">
                            @if($cartEnabled)
                            <form method="POST" action="{{ route('cart.store') }}" class="js-add-to-cart-form">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="js-add-to-cart-btn inline-flex w-full items-center justify-center gap-2 bg-base-100 py-3 text-[10px] font-semibold uppercase tracking-widest text-base-content transition-colors hover:bg-neutral hover:text-white">
                                    <svg class="js-add-to-cart-spinner hidden h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="3"></circle>
                                        <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                                    </svg>
                                    <span class="js-add-to-cart-label">{{ __('Add to Cart') }}</span>
                                </button>
                                <div class="js-add-to-cart-error mt-2 hidden rounded-md bg-red-50 px-2 py-1 text-[10px] font-semibold text-red-700"></div>
                            </form>
                            @elseif($productWhatsappUrl)
                            <a href="{{ $productWhatsappUrl }}" target="_blank" class="block w-full bg-base-100 text-base-content py-3 uppercase tracking-widest text-[10px] font-semibold hover:bg-neutral hover:text-white transition-colors">
                                {{ __('Order via WhatsApp') }}
                            </a>
                            @else
                            <span class="block w-full bg-base-100 text-base-content/40 py-3 uppercase tracking-widest text-[10px] font-semibold">
                                {{ __('WhatsApp is not configured yet') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex flex-col grow pt-1 pb-4">
                        <div class="text-[10px] text-base-content/50 uppercase tracking-widest mb-2">{{ $product->category?->name ?? __('Uncategorized') }}</div>
                        <a href="{{ route('shop.show', $product->slug) }}" class="display-font text-xl text-base-content group-hover:text-primary transition-colors mb-3">
                            {{ $product->name }}
                        </a>
                        <div class="font-light text-base-content/80 mt-1">
                            @if($product->discounted_price)
                                @php $discountPct = round(($product->price - $product->discounted_price) / $product->price * 100); @endphp
                                <span class="text-error font-medium">@money($product->discounted_price)</span>
                                <span class="text-xs text-base-content/40 line-through ml-1">@money($product->price)</span>
                                <span class="ml-1 bg-error text-error-content text-[9px] font-bold px-1.5 py-0.5">-{{ $discountPct }}%</span>
                            @else
                                @money($product->price)
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div class="mt-24 pt-10 border-t border-base-200 flex justify-center">
                {{ $products->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

