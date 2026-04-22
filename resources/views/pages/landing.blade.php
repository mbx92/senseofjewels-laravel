@extends('layouts.app')

@section('content')

    {{-- ====================================================
         HERO: 3-Panel Glamora Split Layout
         Left (3/5): Large editorial panel
         Right top (2/5): Collection banner
         Right bottom (2/5): Category banner
    ===================================================== --}}
    <section id="home" class="grid grid-cols-1 md:grid-cols-5 min-h-[92vh] gap-2 p-2 md:p-3">

        {{-- LEFT PANEL (large) --}}
        <div class="md:col-span-3 relative bg-base-300 min-h-[60vh] md:min-h-0 overflow-hidden group">
            {{-- Gradient atmosphere (replace with real img later: bg-[url(...)] bg-cover bg-center) --}}
            <div class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/70 to-base-300/60"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_70%,rgba(191,160,84,0.15),transparent_60%)]"></div>

            {{-- Placeholder label (remove when real image is set) --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <span class="text-white/10 tracking-[0.4em] text-xs uppercase">Campaign Image</span>
            </div>

            {{-- Text Overlay --}}
            <div class="absolute inset-0 flex flex-col justify-between p-8 md:p-12 lg:p-16 z-10">
                <div>
                    <span class="text-white/60 text-[10px] uppercase tracking-[0.3em]">New Season 2025</span>
                </div>
                <div class="max-w-sm">
                    <p class="text-white/70 text-[10px] uppercase tracking-[0.25em] mb-4">Artisan Jewelry · Bali</p>
                    <h1 class="display-font text-white text-5xl md:text-6xl lg:text-[5rem] leading-[0.95] mb-6">
                        Timeless<br>
                        <span class="italic font-light">Elegance</span>
                    </h1>
                    <p class="text-white/70 text-sm mb-10 font-light leading-relaxed max-w-xs">
                        Handcrafted fine jewelry designed for the modern everyday.
                    </p>
                    <a href="{{ route('shop.index') }}"
                       class="inline-block border border-white/70 text-white text-[10px] uppercase tracking-[0.25em] px-8 py-3.5 hover:bg-white hover:text-neutral transition-all duration-300">
                        Shop Collection
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT PANELS (stacked 2×) --}}
        <div class="md:col-span-2 flex flex-col gap-2">

            {{-- Top Right --}}
            <div class="relative flex-1 min-h-[40vh] md:min-h-0 bg-base-200 overflow-hidden group cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-[#e8d5b7] to-base-300 group-hover:scale-105 transition-transform duration-700"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(191,160,84,0.12),transparent_50%)]"></div>

                {{-- Placeholder label --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="text-base-content/15 tracking-[0.35em] text-[10px] uppercase">Product Banner</span>
                </div>

                {{-- Text overlay --}}
                <div class="absolute inset-0 flex flex-col justify-end p-8 z-10">
                    <p class="text-base-content/50 text-[10px] uppercase tracking-[0.25em] mb-2">Koleksi Pilihan</p>
                    <h2 class="display-font text-base-content text-3xl md:text-4xl leading-tight mb-6">
                        Emas &amp; Perak<br>
                        <span class="italic font-light text-primary">Artisan Bali</span>
                    </h2>
                    <a href="{{ route('shop.index') }}"
                       class="inline-block border border-base-content/60 text-base-content text-[10px] uppercase tracking-[0.2em] px-6 py-2.5 w-fit hover:bg-base-content hover:text-base-100 transition-all duration-300">
                        Shop Now
                    </a>
                </div>
            </div>

            {{-- Bottom Right --}}
            <div class="relative flex-1 min-h-[30vh] md:min-h-0 bg-base-300 overflow-hidden group cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-tl from-neutral/40 via-base-300 to-base-200 group-hover:scale-105 transition-transform duration-700"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_80%,rgba(143,175,159,0.18),transparent_55%)]"></div>

                {{-- Placeholder label --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="text-base-content/15 tracking-[0.35em] text-[10px] uppercase">Category Banner</span>
                </div>

                {{-- Text overlay --}}
                <div class="absolute inset-0 flex flex-col justify-end p-8 z-10">
                    <p class="text-base-content/50 text-[10px] uppercase tracking-[0.25em] mb-2">Est. 2019</p>
                    <h2 class="display-font text-base-content text-3xl md:text-4xl leading-tight">
                        Cincin &amp;<br>
                        <span class="italic font-light text-primary">Kalung Pilihan</span>
                    </h2>
                </div>
            </div>

        </div>
    </section>

    {{-- ====================================================
         NEW ARRIVALS — 4 product cards
    ===================================================== --}}
    <section id="collection" class="py-20 md:py-28 bg-base-100 border-t border-base-200">
        <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-14">
                <div>
                    <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">Just Arrived</span>
                    <h2 class="display-font text-4xl md:text-5xl text-base-content">New Arrivals</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1">View All</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
                @php
                    $newIn = [
                        ['name' => 'Svara Ring',      'price' => 'Rp 450.000', 'badge' => 'New'],
                        ['name' => 'Aster Necklace',  'price' => 'Rp 850.000', 'badge' => null],
                        ['name' => 'Luna Earrings',   'price' => 'Rp 550.000', 'badge' => 'Bestseller'],
                        ['name' => 'Kirana Cuff',     'price' => 'Rp 350.000', 'badge' => null],
                    ];
                @endphp
                @foreach($newIn as $item)
                <div class="group cursor-pointer">
                    <div class="aspect-[3/4] bg-base-200 mb-5 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-base-300/50 to-base-200 transition-transform duration-700 group-hover:scale-105"></div>
                        {{-- Badge --}}
                        @if($item['badge'])
                        <div class="absolute top-3 left-3 z-10">
                            <span class="bg-base-100/90 px-3 py-1 text-[9px] uppercase tracking-widest text-base-content">{{ $item['badge'] }}</span>
                        </div>
                        @endif
                        {{-- Quick add (slide up on hover) --}}
                        <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-400 z-20">
                            <button class="w-full bg-base-100/95 text-base-content py-2.5 uppercase tracking-widest text-[9px] font-semibold hover:bg-neutral hover:text-white transition-colors">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                    <div class="text-center px-1">
                        <h3 class="display-font text-lg text-base-content mb-1 group-hover:text-primary transition-colors">{{ $item['name'] }}</h3>
                        <p class="text-[11px] text-base-content/60 tracking-widest">{{ $item['price'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
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
                <h2 class="display-font text-4xl md:text-5xl text-base-content">Gorgeous Collections</h2>
            </div>

            {{-- Circles row --}}
            <div class="grid grid-cols-3 md:grid-cols-6 gap-6 md:gap-8">
                @php
                    $collections = [
                        ['label' => 'Rings',      'slug' => 'rings'],
                        ['label' => 'Necklaces',  'slug' => 'necklaces'],
                        ['label' => 'Earrings',   'slug' => 'earrings'],
                        ['label' => 'Bracelets',  'slug' => 'bracelets'],
                        ['label' => 'Pendants',   'slug' => 'pendants'],
                        ['label' => 'Bangles',    'slug' => 'bangles'],
                    ];
                @endphp
                @foreach($collections as $col)
                <a href="{{ route('shop.index', ['category' => $col['slug']]) }}"
                   class="group flex flex-col items-center gap-5">
                    {{-- Circle image placeholder --}}
                    <div class="w-full aspect-square rounded-full overflow-hidden bg-base-200 relative ring-1 ring-base-300 group-hover:ring-primary/50 transition-all duration-300 group-hover:shadow-lg group-hover:shadow-primary/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-300 to-[#e0cba8] group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-base-content/20 tracking-widest text-[9px] uppercase">Photo</span>
                        </div>
                    </div>
                    {{-- Label --}}
                    <span class="uppercase tracking-[0.2em] text-[10px] font-semibold text-base-content/70 group-hover:text-base-content transition-colors">{{ $col['label'] }}</span>
                </a>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ====================================================
         BRAND STORY — Magazine layout
    ===================================================== --}}
    <section id="story" class="py-20 md:py-32 bg-base-200">
        <div class="container mx-auto px-6 lg:px-12 max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-16 items-center">
                <div class="md:col-span-5 space-y-8">
                    <span class="block text-primary uppercase tracking-[0.25em] text-[10px]">Our Heritage</span>
                    <h2 class="display-font text-4xl md:text-5xl text-base-content leading-tight">
                        Crafted slowly,<br><span class="italic font-light">worn forever.</span>
                    </h2>
                    <div class="w-12 h-px bg-base-content/20"></div>
                    <p class="text-base-content/70 font-light leading-relaxed">
                        We partner with master silversmiths in Bali to create jewelry that feels deeply personal. Each piece is shaped, polished, and finished by hand—celebrating the slight imperfections that make it uniquely yours.
                    </p>
                    <a href="#" class="inline-block uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1">Read The Story</a>
                </div>
                <div class="md:col-span-7 relative">
                    <div class="aspect-[4/3] w-full bg-base-300 flex items-center justify-center">
                        <span class="text-base-content/30 tracking-[0.3em] uppercase text-sm">Artisan Working</span>
                    </div>
                    <div class="absolute -bottom-6 -left-6 w-2/5 aspect-square bg-base-100 border border-base-200 hidden md:flex items-center justify-center shadow-xl">
                        <span class="text-base-content/30 tracking-[0.2em] uppercase text-[10px] text-center px-4">Detail Shot</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====================================================
         INSTAGRAM GRID
    ===================================================== --}}
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

@endsection
