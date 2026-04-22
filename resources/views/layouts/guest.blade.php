<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Sense of Jewels') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="hero min-h-screen bg-base-200 px-4">
            <div class="hero-content w-full max-w-5xl flex-col gap-10 lg:flex-row lg:items-stretch">
                <div class="max-w-md">
                    <a href="{{ route('home') }}" class="text-sm font-semibold uppercase tracking-[0.35em] text-primary">
                        Sense of Jewels
                    </a>
                    <h1 class="mt-4 text-4xl font-bold leading-tight">
                        Secure access for customers and CMS operators.
                    </h1>
                    <p class="mt-4 text-base-content/70">
                        Authentication is powered by Laravel Breeze, while the interface uses DaisyUI components on the corporate theme.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
                        <a href="{{ route('shop.index') }}" class="btn btn-outline">Browse Shop</a>
                    </div>
                </div>
                <div class="card w-full max-w-lg border border-base-300 bg-base-100 shadow-2xl">
                    <div class="card-body">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
