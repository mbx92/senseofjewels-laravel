<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin Panel' }} - {{ config('app.name', 'Sense of Jewels') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="drawer lg:drawer-open">
            <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

            <div class="drawer-content flex min-h-screen flex-col bg-base-200">
                <div class="navbar border-b border-base-300 bg-base-100 px-4 shadow-sm sm:px-6">
                    <div class="flex-none lg:hidden">
                        <label for="admin-drawer" class="btn btn-square btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </label>
                    </div>
                    <div class="flex-1">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost text-lg font-semibold">
                            CMS Admin Panel
                        </a>
                    </div>
                    <div class="flex-none gap-2">
                        <div class="badge badge-primary badge-outline">corporate</div>
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-outline btn-sm">
                                {{ auth()->user()->name ?? 'Admin' }}
                            </label>
                            <ul tabindex="0" class="menu dropdown-content z-10 mt-3 w-56 rounded-box border border-base-300 bg-base-100 p-2 shadow">
                                <li><a href="{{ route('home') }}">Open Website</a></li>
                                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @if (session('success') || session('status'))
                        <div role="alert" class="alert alert-success mb-6">
                            <span>{{ session('success') ?? session('status') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div role="alert" class="alert alert-error mb-6">
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div role="alert" class="alert alert-error mb-6">
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>

            <div class="drawer-side z-40">
                <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <aside class="flex min-h-full w-80 flex-col bg-base-100 px-4 py-4 text-base-content border-r border-base-300">
                    <!-- Sidebar Header / Logo -->
                    <div class="mb-4 flex items-center gap-2 px-4 pb-4 border-b border-base-300">
                        <div class="flex-1">
                            <h2 class="text-lg font-bold text-primary">Sense of Jewels</h2>
                            <p class="text-xs text-base-content/60">Admin Modules</p>
                        </div>
                    </div>

                    <!-- Sidebar Navigation -->
                    <ul class="menu flex-1 flex-nowrap gap-1 px-0 overflow-y-auto w-full text-base">
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                Dashboard
                            </a>
                        </li>

                        <li class="menu-title mt-4 px-4 text-xs font-semibold uppercase tracking-wider text-base-content/50">Landing Page</li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.hero*') ? 'active' : '' }}" href="{{ route('admin.hero') }}">Hero Section</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.about*') ? 'active' : '' }}" href="{{ route('admin.about') }}">About Section</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.services*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">Services</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.portfolio*') ? 'active' : '' }}" href="{{ route('admin.portfolio.index') }}">Portfolio</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.testimonials*') ? 'active' : '' }}" href="{{ route('admin.testimonials.index') }}">Testimonials</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.contact-settings*') ? 'active' : '' }}" href="{{ route('admin.contact-settings') }}">Contact Info</a>
                        </li>

                        <li class="menu-title mt-4 px-4 text-xs font-semibold uppercase tracking-wider text-base-content/50">Commerce</li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.products*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Categories</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">Inventory</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.discounts*') ? 'active' : '' }}" href="{{ route('admin.discounts.index') }}">Discounts</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.vouchers*') ? 'active' : '' }}" href="{{ route('admin.vouchers.index') }}">Vouchers</a>
                        </li>
                        <li>
                            <a class="gap-3 {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Orders</a>
                        </li>

                        <li class="menu-title mt-4 px-4 text-xs font-semibold uppercase tracking-wider text-base-content/50">System</li>
                        <li class="disabled">
                            <a class="gap-3 text-base-content/40">Users & Roles</a>
                        </li>
                        <li class="disabled">
                            <a class="gap-3 text-base-content/40">Settings</a>
                        </li>
                    </ul>
                </aside>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
