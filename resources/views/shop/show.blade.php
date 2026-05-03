@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-12 max-w-[1400px] pb-32">

    {{-- Breadcrumb --}}
    <div class="py-6 border-b border-base-200 mb-10">
        <nav class="text-[11px] uppercase tracking-widest text-base-content/50 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-base-content transition-colors">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-base-content transition-colors">{{ __('Shop') }}</a>
            <span>/</span>
            <span class="text-base-content">{{ $product->name }}</span>
        </nav>
    </div>

    {{-- Main Product Section --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-6 xl:gap-8 mb-20">

        {{-- Left: Image Gallery --}}
        @php
            $galleryImages = $product->images;
            $mainImageUrl = $product->image_url;
        @endphp
        <div class="lg:col-span-8 space-y-4">
            <div class="aspect-[4/5] min-h-[420px] md:min-h-[560px] bg-base-100 border border-base-300 overflow-hidden relative w-full">
                @if($mainImageUrl)
                    <img id="main-product-image"
                         src="{{ $mainImageUrl }}"
                         alt="{{ $product->name }}"
                         class="block h-full w-full object-cover object-center">
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-base-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" class="text-base-content/20"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        <span class="text-base-content/30 text-[10px] uppercase tracking-[0.3em]">{{ __('No Image') }}</span>
                    </div>
                @endif
                @if($product->is_featured)
                <div class="absolute top-4 left-4 z-10">
                    <span class="bg-base-100/90 px-3 py-1 text-[9px] uppercase tracking-widest text-base-content">{{ __('Featured') }}</span>
                </div>
                @endif
            </div>

            <div class="flex gap-3 overflow-x-auto pb-1">
                @foreach($galleryImages as $img)
                <button type="button"
                        onclick="document.getElementById('main-product-image').src='{{ $img->url }}';document.querySelectorAll('.thumb-btn').forEach(b=>b.classList.remove('border-base-content','shadow-[inset_0_0_0_1px_rgba(44,26,14,0.25)]'));this.classList.add('border-base-content','shadow-[inset_0_0_0_1px_rgba(44,26,14,0.25)]');"
                        class="thumb-btn shrink-0 w-20 h-20 md:w-24 md:h-24 border overflow-hidden bg-base-100 transition-colors {{ $loop->first ? 'border-base-content shadow-[inset_0_0_0_1px_rgba(44,26,14,0.25)]' : 'border-base-300 hover:border-base-content/60' }}">
                    <img src="{{ $img->url }}" alt="{{ $product->name }}" class="block w-full h-full object-cover">
                </button>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-6 py-2">
            @php
                $inventoryEnabled = \App\Models\Setting::boolOf('inventory_enabled', true);
                $canPurchase = ! $inventoryEnabled || $product->stock > 0;
                $waMessage = "Halo Sense of Jewels, saya ingin order {$product->name} (SKU: {$product->sku}). Link: " . route('shop.show', $product->slug);
                $productWhatsappUrl = \App\Models\Setting::whatsappUrl($waMessage);
            @endphp

            {{-- Category + Name --}}
            @if($product->category)
            <p class="text-[10px] uppercase tracking-[0.3em] text-base-content/50">{{ $product->category->name }}</p>
            @endif
            <h1 class="display-font text-4xl md:text-5xl text-base-content leading-tight">{{ $product->name }}</h1>

            {{-- Price --}}
            <div class="flex items-baseline gap-3 border-y border-base-200 py-5">
                @if($product->discounted_price)
                    @php $discountPct = round(($product->price - $product->discounted_price) / $product->price * 100); @endphp
                    <span class="display-font text-3xl text-error">@money($product->discounted_price)</span>
                    <span class="display-font text-xl text-base-content/40 line-through">@money($product->price)</span>
                    <span class="bg-error text-error-content text-[9px] font-bold uppercase tracking-widest px-2 py-1">-{{ $discountPct }}%</span>
                @else
                    <span class="display-font text-3xl text-base-content">@money($product->price)</span>
                @endif
                @if($product->weight)
                <span class="text-[11px] text-base-content/40 uppercase tracking-widest">{{ $product->weight }} g</span>
                @endif
            </div>
            @if($product->discounted_price)
            @php $saveAmount = $product->price - $product->discounted_price; @endphp
            <p class="text-[11px] text-success uppercase tracking-widest -mt-3">{{ __('Save') }} @money($saveAmount)</p>
            @endif

            {{-- Short Description --}}
            @if($product->short_description)
            <p class="text-base-content/65 font-light leading-relaxed text-sm md:text-base">{{ $product->short_description }}</p>
            @endif

            <div class="border border-base-200 bg-base-100 p-4 space-y-4">
                <div class="flex items-center gap-2">
                    @if(!$inventoryEnabled)
                        <span class="inline-block w-2 h-2 rounded-full bg-success"></span>
                        <span class="text-[11px] uppercase tracking-widest text-base-content/60">{{ __('Inventory Off') }} · {{ __('Ready to Order') }}</span>
                    @elseif($product->stock > 0)
                        <span class="inline-block w-2 h-2 rounded-full bg-success"></span>
                        <span class="text-[11px] uppercase tracking-widest text-base-content/60">{{ __('In Stock') }} ({{ $product->stock }})</span>
                    @else
                        <span class="inline-block w-2 h-2 rounded-full bg-error"></span>
                        <span class="text-[11px] uppercase tracking-widest text-error/70">{{ __('Out of Stock') }}</span>
                    @endif
                </div>

                @if($canPurchase && $cartEnabled)
                <form method="POST" action="{{ route('cart.store') }}" class="space-y-3 js-add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="flex border border-base-300 w-full sm:w-32 bg-base-100">
                            <button type="button" onclick="let i=this.nextElementSibling;if(i.value>1)i.value--" class="px-4 py-3 text-base-content/60 hover:text-base-content text-lg leading-none transition-colors">−</button>
                            <input type="number" name="quantity" min="1" @if($inventoryEnabled) max="{{ $product->stock }}" @endif value="1"
                                   oninput="const max=this.max?Number(this.max):null;if(max&&Number(this.value)>max){this.value=max;} if(Number(this.value)<1){this.value=1;}"
                                   class="w-full text-center bg-transparent text-sm border-x border-base-300 focus:outline-none">
                            <button type="button" onclick="let i=this.previousElementSibling;@if($inventoryEnabled) if(i.value<{{ $product->stock }}) @endif i.value++" class="px-4 py-3 text-base-content/60 hover:text-base-content text-lg leading-none transition-colors">+</button>
                        </div>
                        <button type="submit" class="js-add-to-cart-btn inline-flex w-full items-center justify-center gap-2 bg-primary px-6 py-3 text-[11px] font-semibold uppercase tracking-widest text-white transition-colors hover:bg-base-content hover:text-base-100 sm:flex-1">
                            <svg class="js-add-to-cart-spinner hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="3"></circle>
                                <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                            <span class="js-add-to-cart-label">{{ __('Add to Cart') }}</span>
                        </button>
                    </div>
                    <div class="js-add-to-cart-error hidden rounded-md bg-red-50 px-3 py-2 text-[11px] font-semibold text-red-700"></div>
                </form>
                @elseif($canPurchase && $productWhatsappUrl)
                <a href="{{ $productWhatsappUrl }}" target="_blank" class="block w-full bg-primary text-center text-white px-6 py-3 uppercase tracking-widest text-[11px] font-semibold hover:bg-base-content hover:text-base-100 transition-colors">
                    {{ __('Order via WhatsApp') }}
                </a>
                @elseif($canPurchase)
                <button disabled class="w-full border border-base-300 py-3 uppercase tracking-widest text-[11px] text-base-content/40 cursor-not-allowed">
                    {{ __('WhatsApp is not configured yet') }}
                </button>
                @else
                <button disabled class="w-full border border-base-300 py-3 uppercase tracking-widest text-[11px] text-base-content/40 cursor-not-allowed">
                    {{ __('Out of Stock') }}
                </button>
                @endif

                <p class="text-[10px] text-base-content/35 uppercase tracking-widest">{{ __('SKU') }}: {{ $product->sku }}</p>
            </div>

        </div>
    </div>

    {{-- Description & Specifications --}}
    <div class="mb-24 border-t border-base-200 pt-12 space-y-12">
        @php
            $description = trim((string) $product->description);
            $descriptionIsHtml = str_contains($description, '<');
        @endphp
        <section class="grid gap-6 lg:grid-cols-[180px,minmax(0,1fr)] lg:gap-10">
            <div>
                <h2 class="text-[11px] uppercase tracking-widest text-base-content/50">{{ __('Description') }}</h2>
            </div>
            <div>
                @if($description !== '')
                <div class="max-w-3xl text-base-content/72 font-light text-sm md:text-base leading-8 [&_p]:mb-4 [&_p:last-child]:mb-0 [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:mb-4 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-2 [&_strong]:font-semibold [&_em]:italic">
                    {!! $descriptionIsHtml ? $description : nl2br(e($description)) !!}
                </div>
                @else
                <p class="text-base-content/40 text-sm font-light">{{ __('No detailed description available for this product.') }}</p>
                @endif
            </div>
        </section>

        @if(!empty($product->specifications))
        <section class="grid gap-6 border-t border-base-200 pt-10 lg:grid-cols-[180px,minmax(0,1fr)] lg:gap-10">
            <div>
                <h2 class="text-[11px] uppercase tracking-widest text-base-content/50">{{ __('Specifications') }}</h2>
            </div>
            <div class="max-w-3xl border border-base-200 bg-base-100">
                @foreach($product->specifications as $label => $value)
                <div class="grid grid-cols-[minmax(0,180px),1fr] gap-4 border-b border-base-200 px-5 py-4 last:border-b-0 max-sm:grid-cols-1 max-sm:gap-2">
                    <span class="text-[11px] uppercase tracking-widest text-base-content/45">{{ \Illuminate\Support\Str::of($label)->replace(['_', '-'], ' ')->headline() }}</span>
                    <span class="text-sm leading-7 text-base-content/80">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->isNotEmpty())
    <div class="border-t border-base-200 pt-16">
        <div class="flex justify-between items-end mb-12">
            <div>
                <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">{{ __('You May Also Like') }}</span>
                <h2 class="display-font text-3xl md:text-4xl text-base-content">{{ __('Related Products') }}</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1 hidden md:block">
                {{ __('View All') }}
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
            @foreach($relatedProducts as $related)
            <a href="{{ route('shop.show', $related->slug) }}" class="group cursor-pointer">
                <div class="aspect-[3/4] bg-base-200 mb-4 relative overflow-hidden border border-base-300 group-hover:border-primary/40 transition-colors">
                    @if($related->image_url)
                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                    <div class="absolute inset-0 bg-gradient-to-t from-base-300/50 to-base-200"></div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-400 z-20">
                        <span class="block w-full bg-base-100/95 text-base-content py-2.5 uppercase tracking-widest text-[9px] font-semibold text-center">
                            {{ __('View Product') }}
                        </span>
                    </div>
                </div>
                <div class="text-center px-1">
                    <h3 class="display-font text-lg text-base-content mb-1 group-hover:text-primary transition-colors">{{ $related->name }}</h3>
                    <p class="text-[11px] text-base-content/60 tracking-widest">@money($related->price)</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

