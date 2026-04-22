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
        <div class="min-h-screen bg-base-200">
            <div class="navbar sticky top-0 z-30 border-b border-base-300 bg-base-100/95 px-4 shadow-sm backdrop-blur">
                <div class="navbar-start">
                    <a href="{{ route('home') }}" class="btn btn-ghost px-2 text-lg font-semibold">
                        Sense of Jewels
                    </a>
                </div>
                <div class="navbar-center hidden lg:flex">
                    <ul class="menu menu-horizontal gap-2 px-1">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('shop.index') }}">Shop</a></li>
                        <li><a href="{{ route('home') }}#services">Services</a></li>
                        <li><a href="{{ route('home') }}#portfolio">Portfolio</a></li>
                        <li><a href="{{ route('home') }}#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="navbar-end gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-ghost btn-sm">
                        Cart
                    </a>
                    @auth
                        @php($isAdmin = auth()->user()->hasAnyRole(['super-admin', 'admin']))
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-outline btn-sm">
                                {{ auth()->user()->name }}
                            </label>
                            <ul tabindex="0" class="menu dropdown-content z-[1] mt-3 w-56 rounded-box border border-base-300 bg-base-100 p-2 shadow">
                                <li><a href="{{ route('dashboard') }}">Customer Dashboard</a></li>
                                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                @if ($isAdmin)
                                    <li><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                    @endauth
                </div>
            </div>

            @isset($header)
                <header class="border-b border-base-300 bg-base-100">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div role="alert" class="alert alert-success mb-6">
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="alert alert-error mb-6">
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>

            <footer class="border-t border-base-300 bg-base-100">
                <div class="mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">
                    <div>
                        <p class="footer-title">Sense of Jewels</p>
                        <p class="text-sm text-base-content/70">
                            Company profile, product showcase, and commerce experience built on Laravel, Blade, Alpine.js, and DaisyUI.
                        </p>
                    </div>
                    <div>
                        <p class="footer-title">Modules</p>
                        <ul class="space-y-2 text-sm text-base-content/70">
                            <li>Landing Page CMS</li>
                            <li>Product Catalog & Cart</li>
                            <li>Order & Payment Tracking</li>
                        </ul>
                    </div>
                    <div>
                        <p class="footer-title">Admin Access</p>
                        <p class="text-sm text-base-content/70">
                            Use the dedicated admin entrypoint at
                            <a href="{{ route('admin.login') }}" class="link link-primary">/admin/login</a>.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
