<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="bali-craft">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Sense of Jewels') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Nunito:wght@300;400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen bg-base-200">

        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- Left panel: branding --}}
            <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative bg-neutral overflow-hidden flex-col justify-between p-16">
                {{-- Gradient atmosphere --}}
                <div class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/90 to-[#2a1a10]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,rgba(191,160,84,0.18),transparent_55%)]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(143,175,159,0.1),transparent_50%)]"></div>

                {{-- Top logo --}}
                <div class="relative z-10">
                    <a href="{{ route('home') }}" class="display-font text-3xl text-white/90 tracking-wide hover:text-primary transition-colors">
                        Sense of Jewels
                    </a>
                </div>

                {{-- Center quote --}}
                <div class="relative z-10">
                    <p class="text-primary text-[10px] uppercase tracking-[0.3em] mb-6">Admin Portal</p>
                    <h2 class="display-font text-5xl xl:text-6xl text-white leading-[0.95] mb-8">
                        Crafted with<br>
                        <span class="italic font-light text-white/70">intention.</span>
                    </h2>
                    <div class="w-10 h-px bg-primary/50 mb-8"></div>
                    <p class="text-white/50 text-sm font-light leading-relaxed max-w-xs">
                        Manage your jewelry collections, orders, and content from one elegant dashboard.
                    </p>
                </div>

                {{-- Bottom links --}}
                <div class="relative z-10 flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-white/50 hover:text-white text-[10px] uppercase tracking-widest transition-colors">← Back to Site</a>
                    <a href="{{ route('shop.index') }}" class="text-white/50 hover:text-white text-[10px] uppercase tracking-widest transition-colors">Shop</a>
                </div>
            </div>

            {{-- Right panel: form --}}
            <div class="flex-1 flex flex-col items-center justify-center px-6 py-16 bg-base-100">

                {{-- Mobile logo --}}
                <div class="lg:hidden mb-10 text-center">
                    <a href="{{ route('home') }}" class="display-font text-3xl text-base-content">Sense of Jewels</a>
                </div>

                <div class="w-full max-w-sm">
                    {{ $slot }}
                </div>

                <p class="mt-10 text-[10px] text-base-content/40 uppercase tracking-widest">
                    &copy; {{ date('Y') }} Sense of Jewels · Bali
                </p>
            </div>

        </div>

    </body>
</html>
