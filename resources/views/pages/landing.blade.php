@extends('layouts.app')

@section('content')
@php
    $hero    = $sections->get('hero');
    $about   = $sections->get('about');
    $story   = $sections->get('story');
    $contact = $sections->get('contact');
    $h = fn(string $key, string $default = '') => ($hero?->settings[$key] ?? null) ?: $default;
    $s = fn(string $key, string $default = '') => ($story?->settings[$key] ?? null) ?: $default;
    $textPosCss = function(string $pos): string {
        $map = [
            'top-left'      => 'justify-content:flex-start;align-items:flex-start;text-align:left',
            'top-center'    => 'justify-content:flex-start;align-items:center;text-align:center',
            'top-right'     => 'justify-content:flex-start;align-items:flex-end;text-align:right',
            'middle-left'   => 'justify-content:center;align-items:flex-start;text-align:left',
            'middle-center' => 'justify-content:center;align-items:center;text-align:center',
            'middle-right'  => 'justify-content:center;align-items:flex-end;text-align:right',
            'bottom-left'   => 'justify-content:flex-end;align-items:flex-start;text-align:left',
            'bottom-center' => 'justify-content:flex-end;align-items:center;text-align:center',
            'bottom-right'  => 'justify-content:flex-end;align-items:flex-end;text-align:right',
        ];
        return $map[$pos] ?? $map['top-left'];
    };
@endphp

    {{-- ====================================================
         HERO: 3-Panel Glamora Split Layout
         Left (3/5): Large editorial panel
         Right top (2/5): Collection banner
         Right bottom (2/5): Category banner
    ===================================================== --}}
    @php
        $heroSlides = $hero?->settings['hero_slides'] ?? null;
        if (! is_array($heroSlides) || count($heroSlides) === 0) {
            $heroImages = $hero?->settings['hero_images'] ?? null;
            $heroImages = is_array($heroImages) && count($heroImages) > 0
                ? array_values(array_filter($heroImages))
                : ($hero?->image_url ? [$hero->image_url] : []);
            $heroSlides = collect($heroImages)->map(function (string $image, int $index) use ($hero, $h) {
                return [
                    'image' => $image,
                    'title' => $index === 0 ? (string) ($hero?->title ?? '') : '',
                    'subtitle' => $index === 0 ? (string) ($hero?->subtitle ?? '') : '',
                    'description' => $index === 0 ? (string) ($hero?->content ?? '') : '',
                    'cta_text' => $index === 0 ? (string) ($hero?->cta_text ?? '') : '',
                    'cta_url' => $index === 0 ? (string) ($hero?->cta_url ?? route('shop.index')) : route('shop.index'),
                    'text_position' => $index === 0 ? (string) $h('text_position', 'top-left') : 'top-left',
                    'focus_x' => 50,
                    'focus_y' => 50,
                    'zoom' => 100,
                ];
            })->values()->all();
        }
        $heroSlides = collect($heroSlides)->filter(function ($slide) {
            return is_array($slide) && ! empty($slide['image']);
        })->values()->all();
    @endphp
    @if($hero)
    <section id="home" class="grid grid-cols-1 md:grid-cols-5 min-h-[92vh] gap-2 p-2 md:p-3">

        {{-- LEFT PANEL (large) --}}
        <div class="md:col-span-3 relative bg-base-300 min-h-[60vh] md:min-h-0 overflow-hidden group"
             x-data="{ slides: @js($heroSlides), cur: 0 }"
             x-init="slides.length > 1 && setInterval(() => cur = (cur + 1) % slides.length, 5000)">

            {{-- Carousel slides --}}
            <template x-for="(slide, idx) in slides" :key="slide.image + '-' + idx">
                <div class="absolute inset-0 transition-opacity duration-1000"
                     :style="`opacity:${cur===idx?1:0}`">
                    <img :src="slide.image"
                         class="h-full w-full"
                         :style="`object-fit:cover;object-position:${slide.focus_x || 50}% ${slide.focus_y || 50}%;transform:scale(${(slide.zoom || 100) / 100});transform-origin:center;`"
                         alt="">
                </div>
            </template>

            @if(empty($heroSlides))
            {{-- Gradient fallback (no images) --}}
            <div class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/70 to-base-300/60"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_70%,rgba(191,160,84,0.15),transparent_60%)]"></div>
            @endif
            {{-- Dark overlay --}}
            <div class="absolute inset-0 bg-black/40" style="z-index:1;"></div>

            {{-- Carousel dots (rendered server-side, hanya muncul jika >1 gambar) --}}
            @if(count($heroSlides) > 1)
            <div style="position:absolute;right:20px;bottom:20px;z-index:3;display:flex;align-items:center;gap:6px;padding:8px 10px;border-radius:9999px;background:rgba(0,0,0,0.18);backdrop-filter:blur(4px);">
                @foreach($heroSlides as $dotIdx => $_)
                <span @click="cur = {{ $dotIdx }}"
                      role="button"
                      tabindex="0"
                      @keydown.enter.prevent="cur = {{ $dotIdx }}"
                      @keydown.space.prevent="cur = {{ $dotIdx }}"
                      :style="cur === {{ $dotIdx }}
                          ? 'width:18px;height:8px;background:#ffffff;opacity:1;border:1px solid #ffffff;border-radius:9999px;transition:all 0.3s;cursor:pointer;display:inline-block;flex:0 0 auto;box-shadow:0 1px 4px rgba(0,0,0,0.25)'
                          : 'width:8px;height:8px;background:rgba(255,255,255,0.38);opacity:0.95;border:1px solid rgba(255,255,255,0.72);border-radius:9999px;transition:all 0.3s;cursor:pointer;display:inline-block;flex:0 0 auto;box-shadow:0 1px 4px rgba(0,0,0,0.25)'"
                      style="width:{{ $dotIdx === 0 ? '18px' : '8px' }};height:8px;background:{{ $dotIdx === 0 ? '#ffffff' : 'rgba(255,255,255,0.38)' }};border:1px solid {{ $dotIdx === 0 ? '#ffffff' : 'rgba(255,255,255,0.72)' }};border-radius:9999px;transition:all 0.3s;cursor:pointer;display:inline-block;flex:0 0 auto;box-shadow:0 1px 4px rgba(0,0,0,0.25);"></span>
                @endforeach
            </div>
            @endif

            {{-- Text Overlay --}}
            <div class="absolute inset-0 flex flex-col p-6 md:p-8 lg:p-12"
                 style="z-index:2;"
                 x-bind:style="'z-index:2;' + ({
                    'top-left':'justify-content:flex-start;align-items:flex-start;text-align:left',
                    'top-center':'justify-content:flex-start;align-items:center;text-align:center',
                    'top-right':'justify-content:flex-start;align-items:flex-end;text-align:right',
                    'middle-left':'justify-content:center;align-items:flex-start;text-align:left',
                    'middle-center':'justify-content:center;align-items:center;text-align:center',
                    'middle-right':'justify-content:center;align-items:flex-end;text-align:right',
                    'bottom-left':'justify-content:flex-end;align-items:flex-start;text-align:left',
                    'bottom-center':'justify-content:flex-end;align-items:center;text-align:center',
                    'bottom-right':'justify-content:flex-end;align-items:flex-end;text-align:right'
                 }[(slides[cur] && slides[cur].text_position) || 'top-left'] || 'justify-content:flex-start;align-items:flex-start;text-align:left')">
                @if($h('season_badge'))
                <div style="display:inline-flex;align-items:center;gap:6px;margin-bottom:2rem;">
                    <span style="display:block;width:24px;height:1px;background:rgba(255,255,255,0.5);"></span>
                    <span class="text-white uppercase tracking-[0.3em]" style="font-size:10px;opacity:0.85;">{{ $h('season_badge') }}</span>
                </div>
                @endif
                @if($h('eyebrow'))
                <p class="text-white/70 text-[11px] uppercase tracking-[0.25em] mb-3">{{ $h('eyebrow') }}</p>
                @endif
                <h1 x-show="slides[cur] && slides[cur].title" class="display-font text-white leading-tight mb-4" style="font-size:clamp(2rem,4vw,3.5rem)">
                    <span x-text="slides[cur] ? slides[cur].title : ''"></span><br>
                    <span class="italic font-light" x-show="slides[cur] && slides[cur].subtitle" x-text="slides[cur] ? slides[cur].subtitle : ''"></span>
                </h1>
                <p x-show="slides[cur] && slides[cur].description" class="text-white/70 text-sm mb-6 font-light leading-relaxed" style="max-width:22rem" x-text="slides[cur] ? slides[cur].description : ''"></p>
                <a x-show="slides[cur] && slides[cur].cta_text"
                   x-bind:href="(slides[cur] && slides[cur].cta_url) ? slides[cur].cta_url : '{{ route('shop.index') }}'"
                   class="inline-block border border-white/70 text-white text-[10px] uppercase tracking-[0.25em] px-6 py-3 hover:bg-white hover:text-neutral transition-all duration-300">
                    <span x-text="slides[cur] ? slides[cur].cta_text : ''"></span>
                </a>
            </div>
        </div>

        {{-- RIGHT PANELS (stacked 2×) --}}
        <div class="md:col-span-2 flex flex-col gap-2">

            {{-- Top Right --}}
            <div class="relative flex-1 min-h-[40vh] md:min-h-0 bg-base-200 overflow-hidden group cursor-pointer"
                 @if($h('banner1_image')) style="background-image:url('{{ e($h('banner1_image')) }}');background-size:cover;background-position:center;" @endif>
                @if(!$h('banner1_image'))
                <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-[#e8d5b7] to-base-300 group-hover:scale-105 transition-transform duration-700"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(191,160,84,0.12),transparent_50%)]"></div>
                @else
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-colors duration-300"></div>
                @endif

                {{-- Text overlay --}}
                <div class="absolute inset-0 flex flex-col p-8" style="z-index:10;{{ $textPosCss($h('banner1_text_position', 'bottom-left')) }}">
                    @if($h('banner1_label'))
                    <p class="{{ $h('banner1_image') ? 'text-white/60' : 'text-base-content/50' }} text-[10px] uppercase tracking-[0.25em] mb-2">{{ $h('banner1_label') }}</p>
                    @endif
                    @if($h('banner1_title'))
                    <h2 class="display-font {{ $h('banner1_image') ? 'text-white' : 'text-base-content' }} text-3xl md:text-4xl leading-tight mb-6">
                        {{ $h('banner1_title') }}<br>
                        <span class="italic font-light {{ $h('banner1_image') ? 'text-amber-300' : 'text-primary' }}">{{ $h('banner1_subtitle') }}</span>
                    </h2>
                    @endif
                    @if($h('banner1_cta_text'))
                    <a href="{{ $h('banner1_cta_url', route('shop.index')) }}"
                       class="inline-block {{ $h('banner1_image') ? 'border-white/60 text-white hover:bg-white hover:text-neutral' : 'border-base-content/60 text-base-content hover:bg-base-content hover:text-base-100' }} border text-[10px] uppercase tracking-[0.2em] px-6 py-2.5 w-fit transition-all duration-300">
                        {{ $h('banner1_cta_text') }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Bottom Right --}}
            <div class="relative flex-1 min-h-[30vh] md:min-h-0 bg-base-300 overflow-hidden group cursor-pointer"
                 @if($h('banner2_image')) style="background-image:url('{{ e($h('banner2_image')) }}');background-size:cover;background-position:center;" @endif>
                @if(!$h('banner2_image'))
                <div class="absolute inset-0 bg-gradient-to-tl from-neutral/40 via-base-300 to-base-200 group-hover:scale-105 transition-transform duration-700"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_80%,rgba(143,175,159,0.18),transparent_55%)]"></div>
                @else
                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-colors duration-300"></div>
                @endif

                {{-- Text overlay --}}
                <div class="absolute inset-0 flex flex-col p-8" style="z-index:10;{{ $textPosCss($h('banner2_text_position', 'bottom-left')) }}">
                    @if($h('banner2_label'))
                    <p class="{{ $h('banner2_image') ? 'text-white/60' : 'text-base-content/50' }} text-[10px] uppercase tracking-[0.25em] mb-2">{{ $h('banner2_label') }}</p>
                    @endif
                    @if($h('banner2_title'))
                    <h2 class="display-font {{ $h('banner2_image') ? 'text-white' : 'text-base-content' }} text-3xl md:text-4xl leading-tight">
                        {{ $h('banner2_title') }}<br>
                        <span class="italic font-light {{ $h('banner2_image') ? 'text-amber-300' : 'text-primary' }}">{{ $h('banner2_subtitle') }}</span>
                    </h2>
                    @endif
                    @if($h('banner2_cta_text'))
                    <a href="{{ $h('banner2_cta_url', route('shop.index')) }}"
                       class="inline-block mt-6 {{ $h('banner2_image') ? 'border-white/60 text-white hover:bg-white hover:text-neutral' : 'border-base-content/60 text-base-content hover:bg-base-content hover:text-base-100' }} border text-[10px] uppercase tracking-[0.2em] px-6 py-2.5 w-fit transition-all duration-300">
                        {{ $h('banner2_cta_text') }}
                    </a>
                    @endif
                </div>
            </div>

        </div>
    </section>
    @endif

    @if($about)
    <section id="about" class="border-t border-base-200 bg-base-100 py-16 md:py-20">
        <div class="container mx-auto max-w-6xl px-6 lg:px-12">
            <div class="mx-auto max-w-4xl text-center">
                <span class="mb-3 block text-[10px] uppercase tracking-[0.25em] text-primary">{{ __('About Us') }}</span>
                <h2 class="display-font text-4xl text-base-content md:text-5xl">{{ $about->title }}</h2>
                @if($about->content)
                <div class="prose prose-neutral mx-auto mt-6 max-w-3xl text-base-content/70 prose-p:leading-relaxed">
                    {!! $about->content !!}
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    @if($services->isNotEmpty())
    <section id="services" class="border-t border-base-200 bg-base-200 py-20 md:py-24">
        <div class="container mx-auto max-w-7xl px-6 lg:px-12">
            <div class="mb-12 text-center">
                <span class="mb-3 block text-[10px] uppercase tracking-[0.25em] text-primary">{{ __('Our Services') }}</span>
                <h2 class="display-font text-4xl text-base-content md:text-5xl">{{ __('What We Offer') }}</h2>
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach($services as $service)
                    @php
                        $serviceImage = null;
                        if (! empty($service->image_path)) {
                            $serviceImage = (str_starts_with($service->image_path, '/') || str_starts_with($service->image_path, 'http'))
                                ? $service->image_path
                                : \Illuminate\Support\Facades\Storage::disk('public')->url($service->image_path);
                        }
                    @endphp
                    <article class="overflow-hidden border border-base-300 bg-base-100 transition-colors hover:border-primary/40">
                        <div class="relative aspect-[16/10] bg-base-200">
                            @if($serviceImage)
                                <img src="{{ $serviceImage }}" alt="{{ $service->title }}" class="absolute inset-0 h-full w-full object-cover">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-300 to-base-200"></div>
                            @endif
                        </div>
                        <div class="space-y-3 p-5">
                            <h3 class="display-font text-2xl text-base-content">{{ $service->title }}</h3>
                            @if($service->summary)
                                <div class="prose prose-sm max-w-none text-base-content/70 prose-p:my-0 prose-p:leading-relaxed">
                                    {!! $service->summary !!}
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($portfolioItems->isNotEmpty())
    <section id="portfolio" class="border-t border-base-200 bg-base-100 py-20 md:py-24">
        <div class="container mx-auto max-w-7xl px-6 lg:px-12">
            <div class="mb-12 text-center">
                <span class="mb-3 block text-[10px] uppercase tracking-[0.25em] text-primary">{{ __('Portfolio') }}</span>
                <h2 class="display-font text-4xl text-base-content md:text-5xl">{{ __('Selected Works') }}</h2>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($portfolioItems as $item)
                    @php
                        $portfolioImage = null;
                        if (! empty($item->image_path)) {
                            $portfolioImage = (str_starts_with($item->image_path, '/') || str_starts_with($item->image_path, 'http'))
                                ? $item->image_path
                                : \Illuminate\Support\Facades\Storage::disk('public')->url($item->image_path);
                        }
                    @endphp
                    <article class="group overflow-hidden border border-base-300 bg-base-100 transition-colors hover:border-primary/40">
                        <div class="relative aspect-[4/3] bg-base-200">
                            @if($portfolioImage)
                                <img src="{{ $portfolioImage }}" alt="{{ $item->title }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-300 to-base-200"></div>
                            @endif
                        </div>
                        <div class="space-y-2 p-5">
                            <h3 class="display-font text-2xl text-base-content">{{ $item->title }}</h3>
                            @if($item->category)
                                <p class="text-[11px] uppercase tracking-[0.2em] text-base-content/55">{{ $item->category }}</p>
                            @endif
                            @if($item->description)
                                <div class="prose prose-sm max-w-none text-base-content/70 prose-p:my-0 prose-p:leading-relaxed">
                                    {!! $item->description !!}
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ====================================================
         PROMO BANNERS — Active discounts with image
    ===================================================== --}}
    @if($promos->isNotEmpty())
    <section class="py-10 md:py-14 bg-base-100 border-t border-base-200">
        <div class="container mx-auto px-6 lg:px-12 max-w-7xl">

            <div class="text-center mb-8">
                <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-2">{{ __('Special Offers') }}</span>
                <h2 class="display-font text-3xl md:text-4xl text-base-content">{{ __('Active Promotions') }}</h2>
            </div>

            <div class="grid gap-4 {{ $promos->count() === 1 ? 'grid-cols-1 max-w-2xl mx-auto' : ($promos->count() === 2 ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-1 md:grid-cols-3') }}">
                @foreach($promos as $promo)
                <a href="{{ route('shop.index') }}"
                   class="group relative overflow-hidden block min-h-48 md:min-h-56 border border-base-300 hover:border-primary/40 transition-colors duration-300"
                   style="min-height: 190px;">

                    {{-- Background image or gradient fallback --}}
                    @if($promo->image_url)
                    <img src="{{ $promo->image_url }}" alt="{{ $promo->name }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
                    @else
                    <div class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/80 to-base-300 transition-transform duration-700 group-hover:scale-105"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_60%,rgba(191,160,84,0.25),transparent_55%)]"></div>
                    {{-- Decorative pattern --}}
                    <div class="absolute inset-0 opacity-5" style="background-image: repeating-linear-gradient(45deg, #BFA054 0, #BFA054 1px, transparent 0, transparent 50%); background-size: 20px 20px;"></div>
                    {{-- Placeholder icon --}}
                    <div class="absolute right-8 top-1/2 -translate-y-1/2 opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="w-28 h-28 text-primary fill-current">
                            <path d="M32 6 L8 22 L14 42 H50 L56 22 Z M32 6 L20 22 H44 Z M14 42 L18 56 H46 L50 42 Z"/>
                        </svg>
                    </div>
                    @endif

                    {{-- Content --}}
                    <div class="absolute inset-0 flex flex-col justify-center px-4 py-4 md:px-8 md:py-6" style="z-index:10;">
                        {{-- Badge type --}}
                        @if($promo->discount)
                        <span class="mb-1.5 md:mb-2 w-fit bg-primary/90 text-primary-content text-[8px] md:text-[9px] uppercase tracking-[0.15em] md:tracking-[0.2em] px-2 py-1 md:px-3">
                            {{ $promo->discount->type === 'percent' ? __('Discount') . ' ' . number_format($promo->discount->value, 0) . '%' : __('Save') . ' ' . app(\App\Services\CurrencyService::class)->format($promo->discount->value) }}
                        </span>
                        @endif

                        <h3 class="display-font text-white text-xl md:text-3xl leading-tight mb-1">{{ $promo->code }}</h3>

                        @if($promo->description)
                        <p class="text-white/70 text-[11px] md:text-xs font-light mt-0.5 md:mt-1 line-clamp-2">{{ $promo->description }}</p>
                        @endif

                        <div class="mt-2.5 md:mt-4 flex flex-wrap items-center gap-2 md:gap-3 text-[9px] md:text-[10px] text-white/60 uppercase tracking-[0.12em] md:tracking-widest">
                            <span class="border border-white/30 px-2 py-1 md:px-3 font-mono tracking-wider text-white/90">{{ $promo->code }}</span>
                            @if($promo->ends_at)
                            <span>{{ __('Valid until') }} {{ $promo->ends_at->format('d M Y') }}</span>
                            @endif
                            @if($promo->minimum_order_amount > 0)
                            <span>{{ __('Min. order') }} @money($promo->minimum_order_amount)</span>
                            @endif
                        </div>

                        <span class="mt-3 md:mt-5 w-fit border border-white/50 text-white text-[9px] md:text-[10px] uppercase tracking-[0.15em] md:tracking-[0.2em] px-4 py-2 md:px-5 md:py-2.5 group-hover:bg-white group-hover:text-neutral transition-all duration-300">
                            {{ __('Shop Now') }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>

        </div>
    </section>
    @endif

    {{-- ====================================================
         NEW ARRIVALS — 4 product cards
    ===================================================== --}}
    <section id="collection" class="py-20 md:py-28 bg-base-100 border-t border-base-200">
        <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
            <div class="flex flex-col md:flex-row justify-between items-center md:items-end gap-6 mb-14 text-center md:text-left">
                <div>
                    <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">Just Arrived</span>
                    <h2 class="display-font text-4xl md:text-5xl text-base-content">{{ __('New Arrivals') }}</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1 mx-auto md:mx-0">{{ __('View All') }}</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
                @forelse($newArrivals as $product)
                <a href="{{ route('shop.show', $product->slug) }}" class="group cursor-pointer">
                    <div class="aspect-[3/4] bg-base-200 mb-5 relative overflow-hidden border border-base-300 group-hover:border-primary/40 transition-colors">
                        @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                        <div class="absolute inset-0 bg-gradient-to-t from-base-300/50 to-base-200 transition-transform duration-700 group-hover:scale-105"></div>
                        @endif
                        {{-- Badge --}}
                        @if($product->is_featured)
                        <div class="absolute top-3 right-3 z-10">
                            <span class="bg-base-100/90 px-3 py-1 text-[9px] uppercase tracking-widest text-base-content">Featured</span>
                        </div>
                        @endif
                        {{-- Quick add (slide up on hover) --}}
                        <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-400 z-20">
                            <span class="block w-full bg-base-100/95 text-base-content py-2.5 uppercase tracking-widest text-[9px] font-semibold text-center">
                                {{ __('View Product') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-center px-1">
                        <h3 class="display-font text-lg text-base-content mb-1 group-hover:text-primary transition-colors">{{ $product->name }}</h3>
                        @if($product->discounted_price)
                            @php $discountPct = round(($product->price - $product->discounted_price) / $product->price * 100); @endphp
                            <p class="text-[11px] tracking-widest"><span class="text-error">@money($product->discounted_price)</span> <span class="text-base-content/40 line-through">@money($product->price)</span> <span class="bg-error text-error-content text-[9px] font-bold px-1.5 py-0.5">-{{ $discountPct }}%</span></p>
                        @else
                            <p class="text-[11px] text-base-content/60 tracking-widest">@money($product->price)</p>
                        @endif
                    </div>
                </a>
                @empty
                <p class="col-span-4 text-center text-base-content/40 py-8">{{ __('No products yet.') }}</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ====================================================
         FEATURED PRODUCTS — Editorial spotlight
    ===================================================== --}}
    <section class="py-20 md:py-28 bg-base-200 border-y border-base-300">
        <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
                <div>
                    <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">Editor's Pick</span>
                    <h2 class="display-font text-4xl md:text-5xl text-base-content">{{ __('Featured Products') }}</h2>
                    <p class="mt-4 max-w-2xl text-sm md:text-base text-base-content/65 font-light leading-relaxed">
                        {{ __('Kurasi perhiasan pilihan dengan detail paling menonjol, material terbaik, dan karakter desain yang kuat.') }}
                    </p>
                </div>
                <a href="{{ route('shop.index') }}" class="uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1">
                    {{ __('Explore Collection') }}
                </a>
            </div>

            @if($featuredProducts->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
                @foreach($featuredProducts as $product)
                <a href="{{ route('shop.show', $product->slug) }}" class="group cursor-pointer">
                    <div class="aspect-[3/4] bg-base-100 mb-5 relative overflow-hidden border border-base-300 group-hover:border-primary/40 transition-colors">
                        @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                        <div class="absolute inset-0 bg-base-100"></div>
                        @endif
                        <div class="absolute top-3 right-3 z-10">
                            <span class="bg-base-100/90 px-3 py-1 text-[9px] uppercase tracking-widest text-base-content">Featured</span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-400 z-20">
                            <span class="block w-full bg-base-100/95 text-base-content py-2.5 uppercase tracking-widest text-[9px] font-semibold text-center">
                                {{ __('View Product') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-center px-1">
                        <h3 class="display-font text-lg text-base-content mb-1 group-hover:text-primary transition-colors">{{ $product->name }}</h3>
                        @if($product->discounted_price)
                            @php $discountPct = round(($product->price - $product->discounted_price) / $product->price * 100); @endphp
                            <p class="text-[11px] tracking-widest"><span class="text-error">@money($product->discounted_price)</span> <span class="text-base-content/40 line-through">@money($product->price)</span> <span class="bg-error text-error-content text-[9px] font-bold px-1.5 py-0.5">-{{ $discountPct }}%</span></p>
                        @else
                            <p class="text-[11px] text-base-content/60 tracking-widest">@money($product->price)</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="border border-dashed border-base-300 bg-base-100 px-6 py-16 text-center text-base-content/50">
                {{ __('No active featured products yet. Mark products as featured from admin to show them on the landing page.') }}
            </div>
            @endif
        </div>
    </section>

    {{-- ====================================================
         GORGEOUS COLLECTIONS — Circular category circles
    ===================================================== --}}
    <section class="py-20 md:py-28 bg-base-100">
        <div class="container mx-auto px-6 lg:px-12 max-w-7xl">

            {{-- Heading --}}
            <div class="text-center mb-16">
                <span class="block text-accent uppercase tracking-[0.25em] text-[10px] mb-4">Attractive Jewellery</span>
                <h2 class="display-font text-4xl md:text-5xl text-base-content">{{ __('Gorgeous Collections') }}</h2>
            </div>

            {{-- Circles row --}}
            <div class="grid grid-cols-3 md:grid-cols-6 gap-6 md:gap-8">
                @forelse($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                   class="group flex flex-col items-center gap-5">
                    {{-- Circle image --}}
                    <div class="w-full aspect-square rounded-full overflow-hidden bg-base-200 relative ring-1 ring-base-300 group-hover:ring-primary/50 transition-all duration-300 group-hover:shadow-lg group-hover:shadow-primary/10">
                        @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-300 to-[#e0cba8] group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-base-content/20 tracking-widest text-[9px] uppercase">Photo</span>
                        </div>
                        @endif
                    </div>
                    {{-- Label --}}
                    <span class="uppercase tracking-[0.2em] text-[10px] font-semibold text-base-content/70 group-hover:text-base-content transition-colors">{{ $category->name }}</span>
                </a>
                @empty
                <p class="col-span-6 text-center text-base-content/40 py-8">{{ __('No categories yet.') }}</p>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ====================================================
         BRAND STORY — Magazine layout
    ===================================================== --}}
    @if($story)
    <section id="story" class="py-20 md:py-32 bg-base-200">
        <div class="container mx-auto px-6 lg:px-12 max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-16 items-center">
                <div class="md:col-span-5 space-y-8">
                    @if($s('eyebrow'))
                    <span class="block text-primary uppercase tracking-[0.25em] text-[10px]">{{ $s('eyebrow') }}</span>
                    @endif
                    @if($story?->title || $story?->subtitle)
                    <h2 class="display-font text-4xl md:text-5xl text-base-content leading-tight">
                        @if($story?->title){{ $story->title }}@endif
                        @if($story?->subtitle)<br><span class="italic font-light">{{ $story->subtitle }}</span>@endif
                    </h2>
                    @endif
                    <div class="w-12 h-px bg-base-content/20"></div>
                    @if($story?->content)
                    <p class="text-base-content/70 font-light leading-relaxed">{{ $story->content }}</p>
                    @endif
                    @if($story?->cta_text)
                    <a href="{{ $story->cta_url ?: '#' }}" class="inline-block uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1">{{ $story->cta_text }}</a>
                    @endif
                </div>
                <div class="md:col-span-7 relative">
                    @php $storyMainImg = $story?->image_url; @endphp
                    <div class="aspect-[4/3] w-full bg-base-300 overflow-hidden flex items-center justify-center">
                        @if($storyMainImg)
                        <img src="{{ $storyMainImg }}" alt="{{ $story?->title }}" class="w-full h-full object-cover">
                        @else
                        <span class="text-base-content/30 tracking-[0.3em] uppercase text-sm">Artisan Working</span>
                        @endif
                    </div>
                    @php $storySecImg = $s('secondary_image'); @endphp
                    <div class="absolute -bottom-6 -left-6 w-2/5 aspect-square bg-base-100 border border-base-200 hidden md:flex items-center justify-center shadow-xl overflow-hidden">
                        @if($storySecImg)
                        <img src="{{ $storySecImg }}" alt="" class="w-full h-full object-cover">
                        @else
                        <span class="text-base-content/30 tracking-[0.2em] uppercase text-[10px] text-center px-4">Detail Shot</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ====================================================
         CONTACT
    ===================================================== --}}
    @if($contact)
    <section id="contact" class="py-20 md:py-28 bg-base-100 border-t border-base-200">
        <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
            @php
                $contactEmail = trim((string) $settings->get('contact_email', 'hello@senseofjewels.id'));
                $contactPhone = trim((string) $settings->get('contact_phone', '+62 812 0000 0000'));
                $contactAddress = trim((string) $settings->get('contact_address', 'Seminyak, Bali, Indonesia'));
                $contactWhatsapp = preg_replace('/\D+/', '', (string) $settings->get('contact_whatsapp', ''));
                $waLink = $contactWhatsapp ? 'https://wa.me/' . $contactWhatsapp : null;

                $rawMapsEmbed = trim((string) $settings->get('contact_maps_embed', ''));
                $mapsSrc = '';
                if ($rawMapsEmbed !== '') {
                    if (preg_match('/src=["\']([^"\']+)["\']/i', $rawMapsEmbed, $matches)) {
                        $mapsSrc = $matches[1];
                    } else {
                        $mapsSrc = $rawMapsEmbed;
                    }
                }
            @endphp
            <div class="grid gap-8 lg:grid-cols-[1fr,1.15fr]">
                <div class="space-y-6">
                    <div>
                        <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">{{ __('Get in Touch') }}</span>
                        <h2 class="display-font text-4xl md:text-5xl text-base-content">
                            {{ $contact?->title ?? __('Contact Us') }}
                        </h2>
                        <p class="mt-4 text-sm md:text-base text-base-content/65 font-light leading-relaxed">
                            {{ $contact?->content ?? __('We are happy to help with custom requests, product details, and order support.') }}
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-box border border-base-300 bg-base-200/60 p-4">
                            <div class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">{{ __('Email') }}</div>
                            <a href="mailto:{{ $contactEmail }}" class="text-sm font-medium break-all hover:text-primary transition-colors">{{ $contactEmail }}</a>
                        </div>
                        <div class="rounded-box border border-base-300 bg-base-200/60 p-4">
                            <div class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">{{ __('Phone') }}</div>
                            <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}" class="text-sm font-medium hover:text-primary transition-colors">{{ $contactPhone }}</a>
                        </div>
                        <div class="rounded-box border border-base-300 bg-base-200/60 p-4 sm:col-span-2">
                            <div class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">{{ __('Address') }}</div>
                            <div class="text-sm font-medium whitespace-pre-line">{{ $contactAddress }}</div>
                        </div>
                    </div>

                    @if($waLink)
                    <a href="{{ $waLink }}" target="_blank" rel="noreferrer"
                       class="inline-flex w-fit items-center gap-2 border border-base-content/20 px-4 py-2 text-[10px] uppercase tracking-[0.2em] font-semibold text-base-content hover:border-primary hover:text-primary transition-colors">
                        {{ __('Chat WhatsApp') }}
                    </a>
                    @endif

                    @if($mapsSrc)
                    <div class="overflow-hidden rounded-box border border-base-300">
                        <iframe
                            src="{{ $mapsSrc }}"
                            width="100%"
                            height="250"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    @endif
                </div>

                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-xl">{{ __('Send Message') }}</h3>
                        <form action="{{ route('contact.store') }}" method="POST" class="grid gap-3">
                            @csrf
                            <div class="form-control">
                                <input type="text" name="name" value="{{ old('name') }}"
                                       placeholder="{{ __('Your Name') }}"
                                       class="input input-bordered w-full @error('name') input-error @enderror" required>
                                @error('name')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>
                            <div class="form-control">
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="{{ __('Your Email') }}"
                                       class="input input-bordered w-full @error('email') input-error @enderror" required>
                                @error('email')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>
                            <div class="form-control">
                                <input type="text" name="subject" value="{{ old('subject') }}"
                                       placeholder="{{ __('Subject') }}"
                                       class="input input-bordered w-full @error('subject') input-error @enderror" required>
                                @error('subject')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>
                            <div class="form-control">
                                <textarea name="message" rows="5"
                                          placeholder="{{ __('Your Message') }}"
                                          class="textarea textarea-bordered min-h-32 w-full @error('message') textarea-error @enderror"
                                          required>{{ old('message') }}</textarea>
                                @error('message')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Send Message') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ====================================================
         INSTAGRAM GRID
    ===================================================== --}}
    @if(($settings->get('instagram_feed_enabled', '1') === '1'))
    <section class="py-20 bg-base-200 border-t border-base-300">
        <div class="text-center mb-12">
            <a href="https://instagram.com/senseofjewels" target="_blank" class="display-font text-3xl text-base-content hover:text-primary transition-colors">@senseofjewels</a>
        </div>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-0">
            @for($i = 1; $i <= 6; $i++)
            <a href="https://instagram.com/senseofjewels" target="_blank"
               class="aspect-square bg-base-300 relative group flex items-center justify-center border-[0.5px] border-base-100">
                <span class="text-base-content/25 tracking-widest text-[9px] uppercase">Post {{ $i }}</span>
                <div class="absolute inset-0 bg-primary/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                </div>
            </a>
            @endfor
        </div>
    </section>
    @endif

@endsection
