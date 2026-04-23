<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="bali-craft">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin Panel' }} – {{ config('app.name', 'Sense of Jewels') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Nunito:wght@300;400;500&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.dynamic-theme')
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="drawer lg:drawer-open">
            <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

            {{-- Main content area --}}
            <div class="drawer-content flex min-h-screen flex-col bg-base-200">

                {{-- Top navbar --}}
                <div class="flex items-center justify-between h-14 px-5 border-b border-base-300 bg-base-100">
                    {{-- Mobile hamburger --}}
                    <label for="admin-drawer" class="lg:hidden p-2 -ml-2 text-base-content/60 hover:text-base-content cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </label>

                    {{-- Page title / breadcrumb placeholder --}}
                    <span class="text-[11px] uppercase tracking-[0.2em] text-base-content/50 hidden lg:block">
                        Sense of Jewels &nbsp;·&nbsp; Admin
                    </span>

                    {{-- Right: user dropdown --}}
                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}" target="_blank"
                           class="text-[10px] uppercase tracking-widest text-base-content/50 hover:text-base-content transition-colors hidden sm:block">
                            View Site ↗
                        </a>

                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="flex items-center gap-2 cursor-pointer group">
                                <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xs font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="text-[11px] uppercase tracking-widest text-base-content/70 group-hover:text-base-content transition-colors hidden sm:block">
                                    {{ auth()->user()->name ?? 'Admin' }}
                                </span>
                            </label>
                            <ul tabindex="0" class="dropdown-content z-50 mt-3 w-44 bg-base-100 border border-base-300 shadow-lg py-1">
                                <li><a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-[11px] uppercase tracking-widest text-base-content/70 hover:text-base-content hover:bg-base-200 transition-colors">Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-[11px] uppercase tracking-widest text-base-content/70 hover:text-base-content hover:bg-base-200 transition-colors">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Page content --}}
                <main class="flex-1 p-5 lg:p-8">
                    @if (session('success') || session('status'))
                        <div class="mb-6 flex items-center gap-3 bg-success/10 border border-success/30 text-success px-4 py-3 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success') ?? session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex items-center gap-3 bg-error/10 border border-error/30 text-error px-4 py-3 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 flex items-center gap-3 bg-error/10 border border-error/30 text-error px-4 py-3 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>

            {{-- Sidebar --}}
            <div class="drawer-side z-40">
                <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <aside class="flex min-h-full w-72 flex-col bg-neutral text-neutral-content">

                    {{-- Logo --}}
                    <div class="px-6 py-6 border-b border-white/10">
                        <a href="{{ route('admin.dashboard') }}" class="block">
                            <p class="display-font text-2xl text-primary tracking-wide leading-none">Sense of Jewels</p>
                            <p class="text-[9px] uppercase tracking-[0.25em] text-neutral-content/40 mt-1.5">Admin Dashboard</p>
                        </a>
                    </div>

                    {{-- Navigation --}}
                    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-0.5">

                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-none text-[11px] uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                            Dashboard
                        </a>

                        <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">Landing Page</p>

                        @php
                            $landingLinks = [
                                ['label' => 'Hero Section',  'route' => 'admin.hero',               'match' => 'admin.hero*'],
                                ['label' => 'About',         'route' => 'admin.about',              'match' => 'admin.about*'],
                                ['label' => 'Story',         'route' => 'admin.story',              'match' => 'admin.story*'],
                                ['label' => 'Services',      'route' => 'admin.services.index',     'match' => 'admin.services*'],
                                ['label' => 'Portfolio',     'route' => 'admin.portfolio.index',    'match' => 'admin.portfolio*'],
                                ['label' => 'Testimonials',  'route' => 'admin.testimonials.index', 'match' => 'admin.testimonials*'],
                                ['label' => 'Contact Info',  'route' => 'admin.contact-settings',   'match' => 'admin.contact-settings*'],
                            ];
                        @endphp
                        @foreach($landingLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors {{ request()->routeIs($link['match']) ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5' }}">
                            {{ $link['label'] }}
                        </a>
                        @endforeach

                        <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">Commerce</p>

                        @php
                            $commerceLinks = [
                                ['label' => 'Products',    'route' => 'admin.products.index',   'match' => 'admin.products*'],
                                ['label' => 'Categories',  'route' => 'admin.categories.index', 'match' => 'admin.categories*'],
                                ['label' => 'Inventory',   'route' => 'admin.inventory.index',  'match' => 'admin.inventory*'],
                                ['label' => 'Orders',      'route' => 'admin.orders.index',     'match' => 'admin.orders*'],
                                ['label' => 'Discounts',   'route' => 'admin.discounts.index',  'match' => 'admin.discounts*'],
                                ['label' => 'Vouchers',    'route' => 'admin.vouchers.index',   'match' => 'admin.vouchers*'],
                            ];
                        @endphp
                        @foreach($commerceLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors {{ request()->routeIs($link['match']) ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5' }}">
                            {{ $link['label'] }}
                        </a>
                        @endforeach

                        <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">System</p>
                        @can('manage users')
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('admin.users*') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5' }}">
                            Users
                        </a>
                        <a href="{{ route('admin.roles.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('admin.roles*') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5' }}">
                            Roles
                        </a>
                        @endcan
                        <a href="{{ route('admin.media.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('admin.media*') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5' }}">
                            Media Library
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5' }}">
                            Settings
                        </a>
                    </nav>

                    {{-- Sidebar footer --}}
                    <div class="px-6 py-4 border-t border-white/10">
                        <a href="{{ route('home') }}" target="_blank"
                           class="text-[10px] uppercase tracking-widest text-neutral-content/40 hover:text-primary transition-colors">
                            ← View Live Site
                        </a>
                    </div>

                </aside>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
