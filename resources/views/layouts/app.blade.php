<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="bali-craft">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Sense of Jewels') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Nunito:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- CSS & JS Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.dynamic-theme')
</head>
<body class="antialiased min-h-screen flex flex-col overflow-x-hidden font-sans">

    <!-- ========== STICKY SITE HEADER ========== -->
    <header class="sticky top-0 z-50 w-full">

        <!-- Announcement Bar -->
        <div class="w-full bg-neutral text-neutral-content">
            <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-4 text-[11px] tracking-wide">
                <span class="hidden sm:block">✦ &nbsp; Sense of Jewels Studio &nbsp;·&nbsp; Seminyak, Bali</span>
                <span class="w-full sm:w-auto text-center sm:text-right">Gratis ongkir seluruh Indonesia &nbsp;|&nbsp; Pesan via WhatsApp</span>
            </div>
        </div>

        <!-- Main Navbar -->
        <nav id="main-navbar" class="w-full bg-base-100 border-b border-base-200">

            <!-- Mobile Bar -->
            <div class="lg:hidden flex items-center justify-between h-14 px-4">
                <button id="mobile-menu-btn" class="p-2 text-base-content/70 hover:text-base-content">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ url('/') }}" class="display-font text-2xl text-base-content">Sense of Jewels</a>
                <a href="{{ route('cart.index') }}" class="p-2 text-base-content/70 hover:text-base-content">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </a>
            </div>

            <!-- Mobile Dropdown -->
            <div id="mobile-menu" class="hidden lg:hidden border-t border-base-200 bg-base-100 px-6 pb-5 pt-3">
                <ul class="space-y-1">
                    <li><a href="{{ url('/') }}" class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content hover:text-primary transition-colors">Home</a></li>
                    <li><a href="{{ route('shop.index') }}" class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors">Shop</a></li>
                    <li><a href="{{ route('cart.index') }}" class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors">Cart</a></li>
                    <li><a href="{{ url('/#story') }}" class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors">Story</a></li>
                    <li><a href="{{ url('/#contact') }}" class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Desktop: 3-column grid (nav | logo | icons) -->
            <div class="hidden lg:grid grid-cols-3 items-center h-[72px] max-w-7xl mx-auto px-6 lg:px-8">

                <!-- Left: Navigation Links -->
                <nav class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="text-[11px] uppercase tracking-[0.18em] font-semibold transition-colors {{ request()->is('/') ? 'text-primary' : 'text-base-content/70 hover:text-primary' }}">Home</a>
                    <a href="{{ route('shop.index') }}" class="text-[11px] uppercase tracking-[0.18em] font-semibold transition-colors {{ request()->routeIs('shop.*') ? 'text-primary' : 'text-base-content/70 hover:text-primary' }}">Shop</a>
                    <a href="{{ url('/#story') }}" class="text-[11px] uppercase tracking-[0.18em] font-semibold text-base-content/70 hover:text-primary transition-colors">Story</a>
                    <a href="{{ url('/#contact') }}" class="text-[11px] uppercase tracking-[0.18em] font-semibold text-base-content/70 hover:text-primary transition-colors">Contact</a>
                </nav>

                <!-- Center: Logo -->
                <div class="flex justify-center">
                    <a href="{{ url('/') }}" class="display-font text-[2.4rem] text-base-content hover:text-primary transition-colors leading-none tracking-wide">
                        Sense of Jewels
                    </a>
                </div>

                <!-- Right: Icons -->
                <div class="flex items-center justify-end gap-5">
                    <!-- Search Icon -->
                    <button class="text-base-content/50 hover:text-base-content transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </button>
                    <!-- Account Icon -->
                    @auth
                    <a href="{{ route('profile.edit') }}" class="text-base-content/50 hover:text-base-content transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="text-base-content/50 hover:text-base-content transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </a>
                    @endauth
                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="text-base-content/50 hover:text-base-content transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </a>
                    <!-- WA CTA -->
                    <a href="https://wa.me/6281200000000" target="_blank" class="text-[10px] uppercase tracking-[0.18em] font-bold bg-neutral text-neutral-content px-4 py-2.5 hover:bg-primary hover:text-primary-content transition-colors whitespace-nowrap">
                        Order WA
                    </a>
                    <!-- Language & Currency switcher -->
                    <div class="flex items-center gap-1" x-data>
                        {{-- Language --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="text-[10px] uppercase tracking-[0.14em] text-base-content/50 hover:text-base-content transition-colors flex items-center gap-0.5">
                                {{ strtoupper(app()->getLocale()) }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute right-0 top-full mt-1 z-50 bg-base-100 border border-base-300 shadow-lg min-w-[90px]">
                                @foreach(['id' => 'Bahasa ID', 'en' => 'English'] as $code => $label)
                                <form method="POST" action="{{ route('preferences.locale') }}">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $code }}">
                                    <button type="submit" class="w-full text-left px-3 py-2 text-[10px] uppercase tracking-[0.12em] hover:bg-base-200 transition-colors {{ app()->getLocale() === $code ? 'text-primary font-semibold' : 'text-base-content/70' }}">
                                        {{ $label }}
                                    </button>
                                </form>
                                @endforeach
                            </div>
                        </div>
                        <span class="text-base-content/20 text-xs">|</span>
                        {{-- Currency --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="text-[10px] uppercase tracking-[0.14em] text-base-content/50 hover:text-base-content transition-colors flex items-center gap-0.5">
                                {{ session('currency', 'IDR') }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute right-0 top-full mt-1 z-50 bg-base-100 border border-base-300 shadow-lg min-w-[90px]">
                                @foreach(['IDR' => 'IDR – Rp', 'USD' => 'USD – $', 'SGD' => 'SGD – S$', 'EUR' => 'EUR – €', 'AUD' => 'AUD – A$'] as $code => $label)
                                <form method="POST" action="{{ route('preferences.currency') }}">
                                    @csrf
                                    <input type="hidden" name="currency" value="{{ $code }}">
                                    <button type="submit" class="w-full text-left px-3 py-2 text-[10px] uppercase tracking-[0.12em] hover:bg-base-200 transition-colors {{ session('currency', 'IDR') === $code ? 'text-primary font-semibold' : 'text-base-content/70' }}">
                                        {{ $label }}
                                    </button>
                                </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </nav>
    </header>
    <!-- ========== END SITE HEADER ========== -->

    <!-- Main Content -->
    <main class="grow flex w-full flex-col overflow-x-hidden">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <!-- Footer -->
    <div class="bg-neutral text-neutral-content w-full">
        <footer class="footer p-10 lg:px-16 container mx-auto flex flex-col md:flex-row justify-between gap-8 py-16">
            <aside class="max-w-sm">
                <div class="display-font text-3xl font-bold text-primary mb-2">Sense of Jewels</div>
                <p class="text-sm opacity-80 mt-4 leading-relaxed">
                    Handcrafted jewelry with timeless character and artisan detail.<br>
                    Designed for modern heirlooms since 2019.
                </p>
            </aside>
            
            <nav class="flex flex-col gap-3">
                <h6 class="footer-title text-primary opacity-100 mb-2">Navigation</h6>
                <a href="#home" class="link link-hover text-neutral-content/80">Home</a>
                <a href="#collection" class="link link-hover text-neutral-content/80">Collection</a>
                <a href="#story" class="link link-hover text-neutral-content/80">Brand Story</a>
                <a href="#contact" class="link link-hover text-neutral-content/80">Contact Us</a>
            </nav>
            
            <nav class="flex flex-col gap-3">
                <h6 class="footer-title text-primary opacity-100 mb-2">Social</h6>
                <a href="https://instagram.com/senseofjewels" target="_blank" class="link link-hover flex items-center gap-3 text-neutral-content/80">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    Instagram
                </a>
                <a href="https://wa.me/6281200000000" target="_blank" class="link link-hover flex items-center gap-3 text-neutral-content/80">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    WhatsApp
                </a>
                <a href="mailto:hello@senseofjewels.id" class="link link-hover flex items-center gap-3 text-neutral-content/80">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    Email
                </a>
            </nav>
        </footer>
    </div>
    
    <div class="bg-neutral text-neutral-content px-10 border-t border-neutral-content/10">
        <div class="footer footer-center p-4 container mx-auto pb-8 pt-8">
            <aside>
                <p class="text-neutral-content/60">Copyright © {{ date('Y') }} - All rights reserved by Sense of Jewels</p>
            </aside>
        </div>
    </div>
</body>
</html>
